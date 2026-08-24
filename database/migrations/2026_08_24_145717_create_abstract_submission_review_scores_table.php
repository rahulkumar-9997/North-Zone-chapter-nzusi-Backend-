<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abstract_submission_review_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('abstract_submission_review_id');
            $table->foreign(
                'abstract_submission_review_id',
                'review_scores_review_fk'
            )->references('id')
            ->on('abstract_submission_reviews')
            ->cascadeOnDelete();
            $table->foreignId('scientific_score_id');
            $table->foreign(
                'scientific_score_id',
                'review_scores_scientific_fk'
            )
            ->references('id')
            ->on('scientific_score')
            ->cascadeOnDelete();
            $table->unsignedTinyInteger('score');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abstract_submission_review_scores');
    }
};