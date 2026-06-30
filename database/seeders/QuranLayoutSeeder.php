<?php

namespace Database\Seeders;

use App\Models\QuranLayout;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuranLayoutSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $layouts = [
            [
                'code'          => 'madinah_15',
                'name'          => 'Mushaf Madinah (15 baris)',
                'total_pages'   => 604,
                'total_surahs'  => 114,
                'total_verses'  => 6236,
                'lines_per_page' => 15,
                'is_active'     => true,
            ],
            [
                'code'          => 'madinah_13',
                'name'          => 'Mushaf Madinah (13 baris)',
                'total_pages'   => 604,
                'total_surahs'  => 114,
                'total_verses'  => 6236,
                'lines_per_page' => 13,
                'is_active'     => true,
            ],
            [
                'code'          => 'indonesia',
                'name'          => 'Mushaf Indonesia',
                'total_pages'   => 604,
                'total_surahs'  => 114,
                'total_verses'  => 6236,
                'lines_per_page' => 15,
                'is_active'     => true,
            ],
            [
                'code'          => 'pakistan',
                'name'          => 'Mushaf Pakistan / India',
                'total_pages'   => 604,
                'total_surahs'  => 114,
                'total_verses'  => 6236,
                'lines_per_page' => 13,
                'is_active'     => true,
            ],
            [
                'code'          => 'timteng_17',
                'name'          => 'Mushaf Timur Tengah (17 baris)',
                'total_pages'   => 604,
                'total_surahs'  => 114,
                'total_verses'  => 6236,
                'lines_per_page' => 17,
                'is_active'     => true,
            ],
        ];

        foreach ($layouts as $layout) {
            QuranLayout::query()->updateOrCreate(
                ['code' => $layout['code']],
                $layout,
            );
        }
    }
}
