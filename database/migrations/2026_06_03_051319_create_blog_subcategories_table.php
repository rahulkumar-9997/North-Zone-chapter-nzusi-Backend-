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
        Schema::create('blog_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_category_id')
                  ->constrained('blog_categories')
                  ->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->nullable()->unique();
            $table->text('short_content')->nullable();
            $table->string('image')->nullable();
            $table->boolean('status')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_subcategories');
    }
};
