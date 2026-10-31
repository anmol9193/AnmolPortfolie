<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use App\Support\ChatKnowledge;
use App\Support\ChatSettings;
use App\Support\Content;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatSettingsTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function signInAsAdmin(User $user): static
    {
        $this->actingAs($user);

        return $this->withSession(['admin.verified_user_id' => $user->id, 'admin.password_fingerprint' => hash('sha256', $user->password)]);
    }

    public function test_guests_cannot_read_or_change_chat_settings(): void
    {
        $this->get('/admin/chat')->assertRedirect('/login');
        $this->put('/admin/chat', ['api_key' => 'test-secret'])->assertRedirect('/login');
        $this->delete('/admin/chat')->assertRedirect('/login');
        $this->assertDatabaseMissing('settings', ['key' => ChatSettings::KEY]);
    }

    public function test_admin_can_save_replace_and_remove_an_encrypted_key(): void
    {
        $this->signInAsAdmin(User::factory()->create(['role' => 'super_admin']));
        config(['chat.key' => 'environment-secret']);
        $this->put('/admin/chat', ['api_key' => 'first-secret'])->assertRedirect(route('admin.chat'));
        $stored = Setting::where('key', ChatSettings::KEY)->value('value');
        $this->assertNotSame('first-secret', $stored);
        $this->assertSame('first-secret', Crypt::decryptString($stored));
        $this->assertSame('first-secret', app(ChatSettings::class)->apiKey());
        $this->get('/admin/chat')->assertOk()->assertSee('Saved')->assertDontSee('first-secret')->assertDontSee('environment-secret');
        $this->put('/admin/chat', ['api_key' => 'replacement-secret'])->assertRedirect(route('admin.chat'));
        $this->assertSame('replacement-secret', app(ChatSettings::class)->apiKey());
        $this->assertDatabaseCount('settings', 1);
        $this->delete('/admin/chat')->assertRedirect(route('admin.chat'));
        $this->assertDatabaseMissing('settings', ['key' => ChatSettings::KEY]);
        $this->assertSame('environment-secret', app(ChatSettings::class)->apiKey());
    }

    public function test_invalid_key_is_not_saved_or_flashed_back_to_the_session(): void
    {
        $this->signInAsAdmin(User::factory()->create(['role' => 'super_admin']));
        $this->from('/admin/chat')->put('/admin/chat', ['api_key' => 'not a valid key'])
            ->assertRedirect('/admin/chat')->assertSessionHasErrors('api_key');
        $this->assertArrayNotHasKey('api_key', session()->getOldInput());
        $this->assertDatabaseMissing('settings', ['key' => ChatSettings::KEY]);
        $this->putJson('/admin/chat', ['api_key' => str_repeat('a', 513)])->assertUnprocessable();
        $this->putJson('/admin/chat', ['api_key' => ''])->assertUnprocessable();
    }

    public function test_saved_key_is_used_in_server_request_without_being_exposed(): void
    {
        Content::forget();
        Setting::create(['key' => ChatSettings::KEY, 'value' => Crypt::encryptString('admin-saved-secret')]);
        config(['chat.key' => 'environment-secret']);
        Http::preventStrayRequests();
        Http::fake(['api.scalemax.pro/*' => Http::response(['choices' => [['message' => ['content' => json_encode(['answer' => 'Anmol Saini', 'sources' => ['site.name']])]]]])]);
        $response = $this->postJson('/api/chat', ['message' => 'What is your name?'])->assertOk()->assertJsonPath('answer', 'Anmol Saini');
        $this->assertStringNotContainsString('admin-saved-secret', $response->getContent());
        Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer admin-saved-secret'));
        $this->assertStringNotContainsString('admin-saved-secret', json_encode(app(ChatKnowledge::class)->records()));
        $this->get('/')->assertOk()->assertDontSee('admin-saved-secret');
    }

    public function test_corrupt_saved_key_is_handled_without_leaking_it(): void
    {
        Content::forget();
        Setting::create(['key' => ChatSettings::KEY, 'value' => 'corrupt-secret']);
        Http::preventStrayRequests();
        $this->postJson('/api/chat', ['message' => 'What is your name?'])->assertStatus(503)->assertDontSee('corrupt-secret');
        Http::assertNothingSent();
    }

    public function test_admin_can_check_connection_without_exposing_the_key(): void
    {
        $this->signInAsAdmin(User::factory()->create(['role' => 'super_admin']));
        config(['chat.key' => 'test-secret']);
        Http::preventStrayRequests();
        Http::fake(['api.scalemax.pro/*' => Http::response(['data' => [['id' => config('chat.model')]]])]);
        $this->from('/admin/chat')->post('/admin/chat/test')->assertRedirect('/admin/chat')->assertSessionHas('status', 'Connected to ScaleMax. Your key and model are accessible.');
        Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer test-secret'));
    }

    public function test_invalid_provider_key_is_reported_safely_to_admin(): void
    {
        $this->signInAsAdmin(User::factory()->create(['role' => 'super_admin']));
        config(['chat.key' => 'test-secret']);
        Http::preventStrayRequests();
        Http::fake(['api.scalemax.pro/*' => Http::response(['error' => ['details' => [['reason' => 'API_KEY_INVALID']], 'message' => 'test-secret']], 400)]);
        $this->from('/admin/chat')->post('/admin/chat/test')->assertRedirect('/admin/chat')->assertSessionHasErrors('connection');
        $this->assertStringNotContainsString('test-secret', session('errors')->first('connection'));
        Http::assertSentCount(1);
    }
}
