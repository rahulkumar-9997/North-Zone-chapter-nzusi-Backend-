<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PresentationCategory;

class PresentationCategorySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'name' => 'Uro-oncology',
                'status' => 'active',
            ],
            [
                'name' => 'Endourology / Stone Disease',
                'status' => 'active',
            ],
            [
                'name' => 'Urodynamics / Female Urology',
                'status' => 'active',
            ],
            [
                'name' => 'Reconstructive Urology',
                'status' => 'active',
            ],
            
            [
                'name' => 'Andrology / Sexual Medicine',
                'status' => 'active',
            ],
            [
                'name' => 'Pediatric Urology',
                'status' => 'active',
            ],
            [
                'name' => 'Renal Transplantation',
                'status' => 'active',
            ],
            [
                'name' => 'Laparoscopy / Minimally Invasive Surgery',
                'status' => 'active',
            ],
            [
                'name' => 'Trauma / Emergency Urology',
                'status' => 'active',
            ],
            [
                'name' => 'Infection / Inflammation',
                'status' => 'active',
            ],
            [
                'name' => 'Other: ',
                'status' => 'active',
            ],
        ];

        foreach ($data as $item) {
            PresentationCategory::firstOrCreate(
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