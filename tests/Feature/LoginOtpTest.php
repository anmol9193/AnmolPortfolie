<?php

namespace Tests\Feature;

use App\Mail\LoginOtp;
use App\Models\User;
use App\Support\Content;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LoginOtpTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Content::forget();
    }

    private function requestCode(User $user): string
    {
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertRedirect(route('login.otp'));
        $this->assertGuest();
        $mail = Mail::sent(LoginOtp::class)->last();
        $this->assertInstanceOf(LoginOtp::class, $mail);

        return $mail->code;
    }

    public function test_login_page_requires_password_before_the_otp_screen(): void
    {
        $this->get('/login')->assertOk()->assertSee('name="password"', false)->assertDontSee('Login method');
        $this->get('/login/otp')->assertRedirect(route('login'));
    }

    public function test_code_is_emailed_and_stored_only_as_a_hash(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $code = $this->requestCode($user);
        Mail::assertSent(LoginOtp::class, fn (LoginOtp $mail) => $mail->hasTo($user->email));
        $user->refresh();
        $this->assertMatchesRegularExpression('/\A[0-9]{6}\z/', $code);
        $this->assertNotSame($code, $user->otp_hash);
        $this->assertTrue(Hash::check($code, $user->otp_hash));
        $this->assertStringContainsString($code, (new LoginOtp($code))->render());
        $this->assertArrayNotHasKey('otp_hash', $user->toArray());
        $this->assertStringNotContainsString($code, json_encode(session()->all()));
        $this->get('/login/otp')->assertOk()->assertSee('Verify and sign in')->assertDontSee($code);
    }

    public function test_valid_otp_logs_in_and_is_consumed(): void
    {
        Mail::fake();
        $user = User::factory()->unverified()->create();
        $code = $this->requestCode($user);
        $pending = session('otp');
        $this->post('/login/otp', ['code' => $code])->assertRedirect(route('admin.dashboard'))->assertSessionMissing('otp');
        $this->assertAuthenticatedAs($user);
        $this->get('/admin')->assertOk();
        $this->assertNull($user->fresh()->otp_hash);
        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->post('/logout')->assertRedirect(route('login'));
        $this->withSession(['otp' => $pending])->post('/login/otp', ['code' => $code])->assertSessionHasErrors('code');
        $this->assertGuest();
        Mail::assertSentCount(1);
    }

    public function test_expired_code_cannot_log_in(): void
    {
        Mail::fake();
        $this->freezeTime();
        $code = $this->requestCode(User::factory()->create());
        $this->travel(6)->minutes();
        $this->post('/login/otp', ['code' => $code])->assertSessionHasErrors('code');
        $this->assertGuest();
        Mail::assertSentCount(1);
    }

    public function test_five_wrong_codes_invalidate_the_otp(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $code = $this->requestCode($user);
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/login/otp', ['code' => '000000'])->assertSessionHasErrors('code');
        }
        $this->assertNull($user->fresh()->otp_hash);
        $this->post('/login/otp', ['code' => $code])->assertSessionHasErrors('code');
        $this->assertGuest();
        Mail::assertSentCount(1);
    }

    public function test_resending_a_code_replaces_the_previous_code(): void
    {
        Mail::fake();
        $user = User::factory()->create(['otp_hash' => Hash::make('000000'), 'otp_expires_at' => now()->addMinutes(5)]);
        $newCode = $this->requestCode($user);
        $this->post('/login/otp', ['code' => '000000'])->assertSessionHasErrors('code');
        $this->post('/login/otp', ['code' => $newCode])->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
        Mail::assertSentCount(1);
    }

    public function test_unknown_and_inactive_accounts_do_not_receive_codes(): void
    {
        Mail::fake();
        $user = User::factory()->create(['is_active' => false]);
        $this->post('/login', ['email' => 'unknown@example.com', 'password' => 'password'])->assertSessionHasErrors('email');
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();
        Mail::assertNothingSent();
    }

    public function test_deactivation_after_sending_a_code_prevents_login(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $code = $this->requestCode($user);
        $user->forceFill(['is_active' => false])->save();
        $this->post('/login/otp', ['code' => $code])->assertSessionHasErrors('email');
        $this->assertGuest();
        Mail::assertSentCount(1);
    }

    public function test_resend_is_limited_for_the_same_email_even_from_another_ip(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $this->requestCode($user);
        $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.50'])->post('/login/otp/request')->assertRedirect(route('login.otp'))->assertSessionHasErrors('code');
        Mail::assertSentCount(1);
    }

    public function test_a_code_cannot_be_used_without_the_pending_email_session(): void
    {
        $user = User::factory()->create(['otp_hash' => Hash::make('123456'), 'otp_expires_at' => now()->addMinutes(5)]);
        $this->post('/login/otp', ['code' => '123456'])->assertRedirect(route('login'))->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertNotNull($user->fresh()->otp_hash);
    }

    public function test_pending_email_cannot_be_overridden_by_the_verify_payload(): void
    {
        $first = User::factory()->create(['otp_hash' => Hash::make('123456'), 'otp_expires_at' => now()->addMinutes(5)]);
        $second = User::factory()->create(['otp_hash' => Hash::make('654321'), 'otp_expires_at' => now()->addMinutes(5)]);
        $this->withSession(['otp' => ['user_id' => $second->id, 'email' => $second->email, 'password_fingerprint' => hash('sha256', $second->password), 'expires_at' => now()->addMinutes(10)->timestamp, 'challenge_hash' => $second->otp_hash]])
            ->post('/login/otp', ['email' => $first->email, 'code' => '123456'])->assertSessionHasErrors('code');
        $this->assertGuest();
    }

    public function test_invalid_otp_is_not_flashed_back_to_the_session(): void
    {
        $user = User::factory()->create();
        $this->withSession(['otp' => ['user_id' => $user->id, 'password_fingerprint' => hash('sha256', $user->password), 'expires_at' => now()->addMinutes(10)->timestamp]])
            ->from('/login/otp')->post('/login/otp', ['code' => 'secret-invalid'])->assertSessionHasErrors('code');
        $this->assertArrayNotHasKey('code', session()->getOldInput());
    }

    public function test_mail_failure_clears_the_generated_code_and_keeps_user_logged_out(): void
    {
        $user = User::factory()->create();
        Mail::shouldReceive('to')->once()->with($user->email)->andThrow(new \RuntimeException('Mail transport failed'));
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertSessionHasErrors('email')->assertSessionMissing('otp');
        $this->assertGuest();
        $this->assertNull($user->fresh()->otp_hash);
    }

    public function test_wrong_password_never_sends_an_otp(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $this->post('/login', ['email' => $user->email, 'password' => 'wrong-password'])->assertSessionHasErrors('email')->assertSessionMissing('otp');
        $this->assertGuest();
        Mail::assertNothingSent();
    }

    public function test_email_only_request_cannot_send_a_code_or_create_password_proof(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $this->post('/login/otp/request', ['email' => $user->email])->assertRedirect(route('login'))->assertSessionMissing('otp');
        $this->withSession(['otp.email' => $user->email])->post('/login/otp', ['code' => '123456'])->assertRedirect(route('login'));
        $this->assertGuest();
        Mail::assertNothingSent();
    }

    public function test_password_checked_user_cannot_access_dashboard_before_otp(): void
    {
        Mail::fake();
        $this->requestCode(User::factory()->create());
        $this->get('/admin')->assertRedirect(route('login'));
        $this->assertGuest();
        Mail::assertSentCount(1);
    }

    public function test_pending_verification_is_invalid_after_password_changes(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $code = $this->requestCode($user);
        $user->update(['password' => 'changed-password']);
        $this->post('/login/otp', ['code' => $code])->assertRedirect(route('login'));
        $this->assertGuest();
        Mail::assertSentCount(1);
    }

    public function test_expired_password_proof_cannot_be_extended_by_resending(): void
    {
        Mail::fake();
        $this->freezeTime();
        $this->requestCode(User::factory()->create());
        $this->travel(11)->minutes();
        $this->post('/login/otp/request')->assertRedirect(route('login'))->assertSessionMissing('otp');
        Mail::assertSentCount(1);
    }

    public function test_resend_uses_the_password_checked_account_and_rejects_previous_code(): void
    {
        Mail::fake();
        $this->freezeTime();
        $user = User::factory()->create();
        $this->requestCode($user);
        $oldHash = $user->fresh()->otp_hash;
        $this->travel(61)->seconds();
        $this->post('/login/otp/request', ['email' => 'other@example.com'])->assertRedirect(route('login.otp'));
        Mail::assertSent(LoginOtp::class, 2);
        $this->assertNotSame($oldHash, $user->fresh()->otp_hash);
        $mail = Mail::sent(LoginOtp::class)->last();
        $this->assertTrue($mail->hasTo($user->email));
        $this->post('/login/otp', ['code' => $mail->code])->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }
}
