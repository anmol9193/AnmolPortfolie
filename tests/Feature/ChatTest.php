<?php

namespace Tests\Feature;

use App\Http\Controllers\ChatController;
use App\Models\PortfolioItem;
use App\Support\ChatKnowledge;
use App\Support\Content;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Content::forget();
        config(['chat.key' => 'test-secret', 'chat.origin' => 'http://localhost']);
        Http::preventStrayRequests();
    }

    public function test_greeting_and_small_talk_receive_different_ai_replies_with_history(): void
    {
        Http::fake(['api.scalemax.pro/*' => Http::sequence()
            ->push(['choices' => [['message' => ['content' => json_encode(['answer' => 'Hi! How can I help?', 'sources' => []])]]]])
            ->push(['choices' => [['message' => ['content' => json_encode(['answer' => 'I am ready to help you explore the portfolio.', 'sources' => []])]]]])]);
        $this->postJson('/api/chat', ['message' => 'hii'])->assertOk()->assertJsonPath('answer', 'Hi! How can I help?');
        $this->postJson('/api/chat', ['message' => 'How are you'])->assertOk()->assertJsonPath('answer', 'I am ready to help you explore the portfolio.');
        $requests = Http::recorded();
        $this->assertCount(4, $requests[1][0]['messages']);
        $this->assertSame('hii', $requests[1][0]['messages'][1]['content']);
        $this->assertSame('https://api.scalemax.pro/token/v1/chat/completions', $requests[1][0]->url());
        $this->assertTrue($requests[1][0]->hasHeader('Authorization', 'Bearer test-secret'));
        $this->assertSame(0.1, $requests[1][0]['temperature']);
        $this->assertCount(4, session('chat.history'));
    }

    public function test_project_answer_and_follow_up_use_public_context(): void
    {
        $project = PortfolioItem::create(['type' => 'projects', 'data' => ['title' => 'Atlas', 'description' => 'A Laravel job portal.'], 'position' => 0]);
        Http::fake(['api.scalemax.pro/*' => Http::response(['choices' => [['message' => ['content' => json_encode(['answer' => 'Atlas is a Laravel job portal.', 'sources' => ['projects.'.$project->id]])]]]])]);
        $this->postJson('/api/chat', ['message' => 'Tell me about Atlas'])->assertOk()->assertJsonPath('answer', 'Atlas is a Laravel job portal.');
        $this->postJson('/api/chat', ['message' => 'Tell me more about it'])->assertOk()->assertJsonPath('answer', 'Atlas is a Laravel job portal.');
        Http::assertSent(fn ($request) => str_contains($request['messages'][count($request['messages']) - 1]['content'], 'Atlas'));
        Http::assertSentCount(2);
    }

    public function test_unknown_question_is_refused_without_outbound_request(): void
    {
        $this->postJson('/api/chat', ['message' => 'Explain quantum physics'])->assertOk()->assertJsonPath('answer', ChatController::REFUSAL);
        Http::assertNothingSent();
    }

    public function test_invalid_sources_are_not_accepted(): void
    {
        Http::fake(['api.scalemax.pro/*' => Http::response(['choices' => [['message' => ['content' => json_encode(['answer' => 'Invented facts', 'sources' => ['private.secret']])]]]])]);
        $this->postJson('/api/chat', ['message' => 'What is your name?'])->assertOk()->assertJsonPath('answer', ChatController::REFUSAL);
        Http::assertSentCount(1);
    }

    public function test_provider_error_does_not_leak_key_or_upstream_details(): void
    {
        Http::fake(['api.scalemax.pro/*' => Http::response(['error' => ['message' => 'test-secret internal details']], 400)]);
        $this->postJson('/api/chat', ['message' => 'hello'])->assertStatus(503)->assertJsonPath('message', 'The AI connection needs attention. Please contact the site owner.')->assertDontSee('test-secret')->assertDontSee('internal details');
        Http::assertSentCount(1);
    }

    public function test_connection_failure_is_handled(): void
    {
        Http::fake(['api.scalemax.pro/*' => Http::failedConnection()]);
        $this->postJson('/api/chat', ['message' => 'hello'])->assertStatus(503)->assertJsonPath('message', 'Could not connect to the assistant. Please try again shortly.');
        Http::assertSentCount(1);
    }

    public function test_key_is_not_sent_to_an_unsupported_endpoint(): void
    {
        config(['chat.base_url' => 'https://untrusted.example/v1']);
        $this->postJson('/api/chat', ['message' => 'hello'])->assertStatus(503);
        Http::assertNothingSent();
    }

    public function test_invalid_input_and_foreign_origins_are_rejected(): void
    {
        $this->postJson('/api/chat', ['message' => str_repeat('x', 1001)])->assertUnprocessable();
        $this->postJson('/api/chat', ['message' => '   '])->assertUnprocessable();
        $this->withHeader('Origin', 'https://foreign.example')->postJson('/api/chat', ['message' => 'Hello'])->assertForbidden();
        Http::assertNothingSent();
    }

    public function test_rate_limit_is_per_ip(): void
    {
        for ($attempt = 0; $attempt < 10; $attempt++) {
            $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.99'])->postJson('/api/chat', ['message' => 'zzzzzzzz'])->assertOk();
        }
        $this->postJson('/api/chat', ['message' => 'zzzzzzzz'])->assertStatus(429);
        $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.100'])->postJson('/api/chat', ['message' => 'zzzzzzzz'])->assertOk();
        Http::assertNothingSent();
    }

    public function test_public_widget_has_no_old_greeting_loop_or_secrets(): void
    {
        $this->get('/')->assertOk()->assertSee('portfolio-chat')->assertSee('Chat with me')->assertDontSee("bubble('Hii', 'bot')", false)->assertDontSee('Next question')->assertSee('Clear chat')->assertDontSee('test-secret');
    }

    public function test_every_reply_has_one_answerable_question_without_repeats(): void
    {
        $first = $this->postJson('/api/chat', ['message' => 'Explain quantum physics'])->assertOk()->assertJsonCount(1, 'suggestions')->json('suggestions.0');
        $second = $this->postJson('/api/chat', ['message' => 'Explain astronomy'])->assertOk()->assertJsonCount(1, 'suggestions')->json('suggestions.0');
        $this->assertNotSame($first, $second);
        $this->assertNotEmpty(app(ChatKnowledge::class)->retrieve($first));
        $this->assertNotEmpty(app(ChatKnowledge::class)->retrieve($second));
        Http::assertNothingSent();
    }

    public function test_clear_chat_removes_chat_memory_and_preserves_other_session_data(): void
    {
        $this->withSession(['chat.history' => [['role' => 'user', 'parts' => [['text' => 'hello']]]], 'chat.used' => ['A question'], 'chat.suggestion' => 'A question', 'unrelated' => 'keep'])
            ->deleteJson('/api/chat')->assertOk()->assertExactJson(['cleared' => true])
            ->assertSessionMissing('chat.history')->assertSessionMissing('chat.used')->assertSessionMissing('chat.suggestion')->assertSessionHas('unrelated', 'keep');
        Http::assertNothingSent();
    }

    public function test_foreign_origin_cannot_clear_chat_memory(): void
    {
        $this->withSession(['chat.suggestion' => 'Keep this'])
            ->withHeader('Origin', 'https://foreign.example')->deleteJson('/api/chat')->assertForbidden()->assertSessionHas('chat.suggestion', 'Keep this');
        Http::assertNothingSent();
    }
}
