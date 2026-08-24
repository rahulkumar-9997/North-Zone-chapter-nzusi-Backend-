<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PresentationType;

class PresentationTypeSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'name' => 'Best Video – Robotic',
                'status' => 'active',
            ],
            [
                'name' => 'Best Video – Non-Robotic',
                'status' => 'active',
            ],
            [
                'name' => 'Best Podium',
                'status' => 'active',
            ],
            [
                'name' => 'Podium',
                'status' => 'active',
            ],
            [
                'name' => 'Moderated Poster',
                'status' => 'active',
            ],
            [
                'name' => 'Unmoderated Poster',
                'status' => 'active',
            ],
        ];

        foreach ($data as $item) {
            PresentationType::firstOrCreate(
                [
                    'name' => $item['name'],
                ],
                [
                    'status' => $item['status'],
                ]
            );
        }
    }
}