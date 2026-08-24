<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ScientificScore;

class ScientificScoreSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'criterion' => 'Clinical/Scientific Relevance',
                'score' => '15',
                'status' => 'active',
            ],
            [
                'criterion' => 'Originality & Innovation',
                'score' => '15',
                'status' => 'active',
            ],
            [
                'criterion' => 'Research Question/Objectives',
                'score' => '10',
                'status' => 'active',
            ],
            [
                'criterion' => 'Methodology & Study Design',
                'score' => '20',
                'status' => 'active',
            ],
            [
                'criterion' => 'Results & Statistical Rigor',
                'score' => '20',
                'status' => 'active',
            ],
            [
                'criterion' => 'Conclusions & Interpretation',
                'score' => '10',
                'status' => 'active',
            ],
            [
                'criterion' => 'Potential Impact',
                'score' => '5',
                'status' => 'active',
            ],
            [
                'criterion' => 'Clarity & Presentation',
                'score' => '5',
                'status' => 'active',
            ],
        ];

        foreach ($data as $item) {
            ScientificScore::firstOrCreate(
                [
                    'criterion' => $item['criterion'],
                ],
                [
                    'score' => $item['score'],
                    'status' => $item['status'],
                ]
            );
        }
    }
}