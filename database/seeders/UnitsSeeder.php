<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Block;
use App\Models\Unit;

class UnitsSeeder extends Seeder
{
    public function run()
    {
        $blocks = Block::pluck('id')->toArray();

        $units = [
            'Introduction to the Subject',
            'Core Concepts and Theories',
            'Practical Applications',
            'Advanced Topics and Case Studies'
        ];

        foreach ($blocks as $blockId) {
            foreach ($units as $index => $unitTitle) {
                Unit::create([
                    'block_id' => $blockId,
                    'title' => $unitTitle,
                    'summary' => 'This unit covers ' . strtolower($unitTitle) . ' in detail.',
                    'position' => $index + 1,
                    'moodle_unit_id' => null, // سيتم ملؤه عند التكامل مع Moodle
                    'visibility' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}
