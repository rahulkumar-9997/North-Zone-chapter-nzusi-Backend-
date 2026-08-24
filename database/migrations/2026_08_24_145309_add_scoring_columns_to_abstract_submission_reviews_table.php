<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('abstract_submission_reviews', function (Blueprint $table) {
            $table->boolean('conflict_of_interest')->default(false);
            $table->unsignedTinyInteger('total_score')->nullable();
            $table->foreignId('presentation_type_id')->nullable()->constrained('presentation_types')->nullOnDelete();
            $table->foreignId('presentation_category_id')->nullable()->constrained('presentation_categories')->nullOnDelete();
            $table->string('other_category_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('abstract_submission_reviews', function (Blueprint $table) {
            $table->dropConstrainedForeignId('presentation_type_id');
            $table->dropConstrainedForeignId('presentation_category_id');
            $table->dropColumn(['conflict_of_interest', 'total_score', 'other_category_text']);
        });
    }
};
