<?php

namespace Tests\Feature;

use App\Models\QuranLayout;
use App\Models\User;
use App\Repositories\Contracts\KhatamProgramRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class SettingsLayoutTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private QuranLayout $layoutMadinah;
    private QuranLayout $layoutIndonesia;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(KhatamProgramRepositoryInterface::class, function (MockInterface $mock) {
            $mock->shouldIgnoreMissing();
        });

        $this->layoutMadinah = QuranLayout::factory()->create([
            'code' => 'test_madinah_15',
            'name' => 'Mushaf Madinah (15 baris)',
            'lines_per_page' => 15,
        ]);
        $this->layoutIndonesia = QuranLayout::factory()->create([
            'code' => 'test_indonesia',
            'name' => 'Mushaf Indonesia',
            'lines_per_page' => 15,
        ]);
        // inactive layout — should not appear
        QuranLayout::factory()->create([
            'code' => 'test_inactive_layout',
            'name' => 'Inactive',
            'is_active' => false,
        ]);

        $this->user = User::factory()->create([
            'quran_layout_code' => $this->layoutMadinah->code,
        ]);
    }

    public function test_guest_cannot_access_settings(): void
    {
        $this->get(route('settings'))
            ->assertRedirect(route('signin'));
    }

    public function test_settings_page_shows_layouts_and_current(): void
    {
        $this->actingAs($this->user)
            ->get(route('settings'))
            ->assertInertia(fn ($page) => $page
                ->component('Settings')
                ->has('layouts')                      // at least test + seeded active layouts
                ->where('currentLayout.code', 'test_madinah_15')
            );
    }

    public function test_inactive_layouts_excluded_from_settings(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('settings'));

        $response->assertInertia(fn ($page) => $page
            ->component('Settings')
            ->has('layouts')
        );

        $pageData = $response->baseResponse->original->getData();
        $layouts = $pageData['page']['props']['layouts'] ?? [];
        $codes = collect($layouts)->pluck('code')->toArray();

        $this->assertNotContains('test_inactive_layout', $codes);
    }

    public function test_update_layout_success(): void
    {
        $this->actingAs($this->user)
            ->post(route('settings.updateLayout'), [
                'layout_code' => $this->layoutIndonesia->code,
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->user->refresh();
        $this->assertEquals($this->layoutIndonesia->code, $this->user->quran_layout_code);
    }

    public function test_update_layout_with_invalid_code_fails(): void
    {
        $this->actingAs($this->user)
            ->post(route('settings.updateLayout'), [
                'layout_code' => 'nonexistent_code',
            ])
            ->assertSessionHasErrors('layout_code');
    }

    public function test_update_layout_with_inactive_code_fails(): void
    {
        $this->actingAs($this->user)
            ->post(route('settings.updateLayout'), [
                'layout_code' => 'test_inactive_layout',
            ])
            ->assertSessionHasErrors('layout_code');
    }

    public function test_update_layout_guest_redirect(): void
    {
        $this->post(route('settings.updateLayout'), [
            'layout_code' => 'test_madinah_15',
        ])->assertRedirect(route('signin'));
    }

    public function test_dashboard_redirects_to_onboarding_when_no_active_program(): void
    {
        // User has no khatam program — but we need to be authenticated
        $this->actingAs($this->user)
            ->get(route('dashboard'))
            ->assertRedirect(route('onboarding'));
    }
}
