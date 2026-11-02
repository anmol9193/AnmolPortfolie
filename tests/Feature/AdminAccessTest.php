<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\Content;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function signInAsAdmin(User $user): static
    {
        $this->actingAs($user);

        return $this->withSession(['admin.verified_user_id' => $user->id, 'admin.password_fingerprint' => hash('sha256', $user->password)]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        Content::forget();
    }

    public function test_guests_cannot_manage_admins(): void
    {
        $this->get('/admin/users')->assertRedirect(route('login'));
        $this->post('/admin/users', ['name' => 'New', 'email' => 'new@example.com'])->assertRedirect(route('login'));
        $this->assertDatabaseCount('users', 0);
    }

    public function test_super_admin_can_add_an_admin_without_allowing_role_injection(): void
    {
        $owner = User::factory()->create(['role' => 'super_admin']);
        $this->signInAsAdmin($owner)->get('/admin/users')->assertOk()->assertSee('Manage')->assertSee('Add admin');
        $this->post('/admin/users', ['name' => 'Team admin', 'email' => 'TEAM@example.com', 'role' => 'super_admin', 'password' => 'test-password', 'password_confirmation' => 'test-password'])->assertRedirect(route('admin.users'));
        $admin = User::where('email', 'team@example.com')->firstOrFail();
        $this->assertSame('admin', $admin->role);
        $this->assertTrue($admin->is_active);
        $this->assertNotEmpty($admin->password);
    }

    public function test_normal_admin_cannot_manage_other_admins(): void
    {
        $admin = User::factory()->create();
        $other = User::factory()->create();
        $this->signInAsAdmin($admin)->get('/admin/users')->assertForbidden();
        $this->post('/admin/users', ['name' => 'Blocked', 'email' => 'blocked@example.com'])->assertForbidden();
        $this->put('/admin/users/'.$other->id, ['is_active' => false])->assertForbidden();
        $this->delete('/admin/users/'.$other->id)->assertForbidden();
        $this->assertTrue($other->fresh()->is_active);
        $this->assertDatabaseCount('users', 2);
    }

    public function test_owner_can_deactivate_reactivate_and_remove_a_regular_admin(): void
    {
        $owner = User::factory()->create(['role' => 'super_admin']);
        $admin = User::factory()->create(['otp_hash' => Hash::make('123456'), 'otp_expires_at' => now()->addMinutes(5)]);
        $this->signInAsAdmin($owner)->put('/admin/users/'.$admin->id, ['is_active' => false])->assertRedirect(route('admin.users'));
        $this->assertFalse($admin->fresh()->is_active);
        $this->assertNull($admin->fresh()->otp_hash);
        $this->put('/admin/users/'.$admin->id, ['is_active' => true])->assertRedirect(route('admin.users'));
        $this->assertTrue($admin->fresh()->is_active);
        $this->delete('/admin/users/'.$admin->id)->assertRedirect(route('admin.users'));
        $this->assertDatabaseMissing('users', ['id' => $admin->id]);
    }

    public function test_super_admin_cannot_be_deactivated_or_removed(): void
    {
        $owner = User::factory()->create(['role' => 'super_admin']);
        $this->signInAsAdmin($owner)->put('/admin/users/'.$owner->id, ['is_active' => false])->assertForbidden();
        $this->delete('/admin/users/'.$owner->id)->assertForbidden();
        $this->assertTrue($owner->fresh()->is_active);
    }

    public function test_inactive_admin_session_is_logged_out_on_the_next_request(): void
    {
        $admin = User::factory()->create(['is_active' => false]);
        $this->signInAsAdmin($admin)->get('/admin/account')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_profile_update_cannot_escalate_a_role(): void
    {
        $admin = User::factory()->create();
        $this->signInAsAdmin($admin)->put('/admin/account/profile', ['name' => 'Updated', 'email' => $admin->email, 'role' => 'super_admin'])->assertForbidden();
        $this->assertSame('admin', $admin->fresh()->role);
        $this->assertSame($admin->name, $admin->fresh()->name);
    }

    public function test_correct_password_only_starts_verification_and_inactive_accounts_are_rejected(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['password' => 'test-password']);
        $this->post('/login', ['email' => $admin->email, 'password' => 'test-password'])->assertRedirect(route('login.otp'));
        $this->assertGuest();
        $admin->forceFill(['is_active' => false])->save();
        $this->post('/login', ['email' => $admin->email, 'password' => 'test-password'])->assertSessionHasErrors('email');
        $this->assertGuest();
        Mail::assertSentCount(1);
    }

    public function test_admin_creation_validates_email_uniqueness_and_password_confirmation(): void
    {
        $owner = User::factory()->create(['role' => 'super_admin']);
        $this->signInAsAdmin($owner)->post('/admin/users', ['name' => 'Duplicate', 'email' => $owner->email])->assertSessionHasErrors('email');
        $this->post('/admin/users', ['name' => 'New', 'email' => 'new@example.com', 'password' => 'long-password', 'password_confirmation' => 'different'])->assertSessionHasErrors('password');
        $this->assertDatabaseCount('users', 1);
    }

    public function test_non_admin_role_cannot_use_password_login_or_admin_sessions(): void
    {
        $user = User::factory()->create(['role' => 'viewer', 'password' => 'test-password']);
        $this->post('/login', ['email' => $user->email, 'password' => 'test-password'])->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->signInAsAdmin($user)->get('/admin/account')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_old_authenticated_session_without_otp_proof_cannot_access_admin(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin)->get('/admin/account')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_regular_admin_dashboard_only_links_to_the_five_allowed_sections(): void
    {
        $admin = User::factory()->create();
        $response = $this->signInAsAdmin($admin)->get('/admin')->assertOk();
        foreach (['admin.dashboard', 'admin.themes', 'admin.appearance', 'admin.pages', 'admin.messages'] as $route) {
            $response->assertSee('href="'.route($route).'"', false);
        }
        foreach (['admin.chat', 'admin.account', 'admin.users', 'admin.content'] as $route) {
            $response->assertDontSee(route($route));
        }
        $response->assertDontSee('/admin/items/')->assertDontSee('Recently updated')->assertDontSee('Content overview')->assertDontSee('<h2>System</h2>', false);
    }

    public function test_regular_admin_can_open_all_allowed_sections(): void
    {
        $this->signInAsAdmin(User::factory()->create());
        foreach (['/admin', '/admin/themes', '/admin/appearance', '/admin/pages', '/admin/messages'] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_regular_admin_cannot_open_hidden_sections_by_direct_url(): void
    {
        $this->signInAsAdmin(User::factory()->create());
        foreach (['/admin/chat', '/admin/account', '/admin/users', '/admin/content', '/admin/content/hero', '/admin/items/projects', '/admin/items/projects/create'] as $path) {
            $this->get($path)->assertForbidden();
        }
    }

    public function test_regular_admin_cannot_write_to_hidden_sections(): void
    {
        $this->signInAsAdmin(User::factory()->create());
        $this->put('/admin/chat', ['api_key' => 'blocked-secret'])->assertForbidden();
        $this->delete('/admin/chat')->assertForbidden();
        $this->post('/admin/chat/test')->assertForbidden();
        $this->put('/admin/content/hero', ['first_name' => 'Blocked'])->assertForbidden();
        $this->post('/admin/items/projects', ['title' => 'Blocked'])->assertForbidden();
        $this->put('/admin/account/password', ['password' => 'blocked-password'])->assertForbidden();
        $this->assertDatabaseMissing('settings', ['key' => 'chat.api_key']);
    }

    public function test_super_admin_keeps_the_full_dashboard_and_navigation(): void
    {
        $owner = User::factory()->create(['role' => 'super_admin']);
        $response = $this->signInAsAdmin($owner)->get('/admin')->assertOk();
        foreach (['admin.chat', 'admin.account', 'admin.users', 'admin.content'] as $route) {
            $response->assertSee(route($route));
        }
        $response->assertSee('/admin/items/projects')->assertSee('Recently updated')->assertSee('Content overview')->assertSee('<h2>System</h2>', false);
        foreach (['/admin/chat', '/admin/account', '/admin/users', '/admin/content', '/admin/items/projects'] as $path) {
            $this->get($path)->assertOk();
        }
    }
}
