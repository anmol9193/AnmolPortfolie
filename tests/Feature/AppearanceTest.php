<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\Content;
use App\Support\Theme;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AppearanceTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Theme::forget();
        Content::forget();
    }

    protected function tearDown(): void
    {
        Theme::forget();
        Content::forget();
        parent::tearDown();
    }

    private function signIn(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin)->withSession(['admin.verified_user_id' => $admin->id, 'admin.password_fingerprint' => hash('sha256', $admin->password)]);
    }

    public function test_appearance_page_offers_three_color_combinations_and_custom_controls(): void
    {
        $this->signIn();
        $this->get('/admin/appearance')->assertOk()->assertSee('3-color combinations')
            ->assertSee('Ocean')->assertSee('Sunset')->assertSee('Aurora')->assertSee('Forest')->assertSee('Rose')->assertSee('Midnight Gold')
            ->assertSee('Choose individual colors')->assertSee('name="accent"', false)->assertSee('name="bg"', false);
    }

    public function test_saving_a_combination_applies_all_three_colors_to_the_public_site(): void
    {
        $this->signIn();
        $this->from('/admin/appearance')->put('/admin/appearance', ['mode' => 'dark', 'combination' => 'aurora', 'accent' => '#000000', 'bg' => '#ffffff'])->assertRedirect('/admin/appearance');
        $this->assertDatabaseHas('settings', ['key' => 'theme_combination', 'value' => 'aurora']);
        $this->assertDatabaseHas('settings', ['key' => 'theme_bg', 'value' => '#161225']);
        $this->assertDatabaseHas('settings', ['key' => 'theme_accent', 'value' => '#a78bfa']);
        $this->assertDatabaseHas('settings', ['key' => 'theme_accent2', 'value' => '#2dd4bf']);
        $this->assertSame('Aurora', Theme::current()['color_label']);
        $this->get('/')->assertOk()->assertSee('--accent:#a78bfa', false)->assertSee('--accent-2:#2dd4bf', false)->assertSee('--bg:#161225', false);
        $this->get('/admin/appearance')->assertOk()->assertSee('value="aurora" checked', false);
    }

    public function test_switching_a_combination_to_light_uses_its_complete_light_palette(): void
    {
        $this->signIn();
        $this->put('/admin/appearance', ['mode' => 'dark', 'combination' => 'ocean'])->assertRedirect();
        $this->put('/admin/appearance', ['mode' => 'light', 'combination' => 'ocean'])->assertRedirect();
        $theme = Theme::current();
        $this->assertSame('light', $theme['mode']);
        $this->assertSame('#edf5fc', $theme['bg']);
        $this->assertSame('#1d4ed8', $theme['accent']);
        $this->assertSame('#0f766e', $theme['accent2']);
        $this->post('/logout')->assertRedirect(route('login'));
        $this->get('/login')->assertOk()->assertSee('--bg:#edf5fc', false)->assertSee('--accent:#1d4ed8', false);
    }

    public function test_individual_custom_colors_clear_the_selected_combination(): void
    {
        $this->signIn();
        $this->put('/admin/appearance', ['mode' => 'dark', 'combination' => 'aurora'])->assertRedirect();
        $this->put('/admin/appearance', ['mode' => 'dark', 'combination' => 'individual', 'color' => 'custom', 'accent' => '#123456', 'accent2' => '#654321', 'background' => 'custom', 'bg' => '#fafafa'])->assertRedirect();
        $theme = Theme::current();
        $this->assertSame('individual', $theme['combination']);
        $this->assertSame('Custom', $theme['color_label']);
        $this->assertSame('#123456', $theme['accent']);
        $this->assertSame('#654321', $theme['accent2']);
        $this->assertSame('light', $theme['mode']);
    }

    public function test_existing_appearance_requests_without_a_combination_still_work(): void
    {
        $this->signIn();
        $this->put('/admin/appearance', ['mode' => 'light', 'color' => 'orange', 'background' => 'default'])->assertRedirect();
        $this->assertSame('individual', Theme::current()['combination']);
        $this->assertSame('#ec4a1c', Theme::current()['accent']);
    }

    public function test_unknown_combinations_and_invalid_custom_colors_are_rejected(): void
    {
        $this->signIn();
        $this->put('/admin/appearance', ['mode' => 'dark', 'combination' => 'unknown'])->assertSessionHasErrors('combination');
        $this->assertDatabaseMissing('settings', ['key' => 'theme_combination']);
        $this->put('/admin/appearance', ['mode' => 'dark', 'combination' => 'individual', 'color' => 'custom', 'accent' => 'red;display:none', 'background' => 'default'])->assertSessionHasErrors('accent');
        $this->assertDatabaseMissing('settings', ['key' => 'theme_accent']);
    }

    public function test_guests_cannot_change_the_appearance(): void
    {
        $this->put('/admin/appearance', ['mode' => 'dark', 'combination' => 'aurora'])->assertRedirect(route('login'));
        $this->assertDatabaseMissing('settings', ['key' => 'theme_combination']);
    }
}
