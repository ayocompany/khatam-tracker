<?php

namespace Tests\Unit\Models;

use App\Models\QuranLayout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuranLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_scope_excludes_inactive_layouts(): void
    {
        QuranLayout::factory()->create(['code' => 'active_one', 'is_active' => true]);
        QuranLayout::factory()->create(['code' => 'inactive_one', 'is_active' => false]);

        $active = QuranLayout::query()->active()->get();
        $codes = $active->pluck('code')->toArray();

        $this->assertContains('active_one', $codes);
        $this->assertNotContains('inactive_one', $codes);
    }

    public function test_layout_has_expected_attributes(): void
    {
        $layout = QuranLayout::factory()->create([
            'code'           => 'test_layout',
            'name'           => 'Test Layout',
            'total_pages'    => 100,
            'total_surahs'   => 50,
            'total_verses'   => 5000,
            'lines_per_page' => 15,
            'is_active'      => true,
        ]);

        $this->assertEquals('test_layout', $layout->code);
        $this->assertEquals('Test Layout', $layout->name);
        $this->assertEquals(100, $layout->total_pages);
        $this->assertEquals(50, $layout->total_surahs);
        $this->assertEquals(5000, $layout->total_verses);
        $this->assertEquals(15, $layout->lines_per_page);
        $this->assertTrue($layout->is_active);
    }
}
