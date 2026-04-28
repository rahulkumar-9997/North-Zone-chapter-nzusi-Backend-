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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('title');
            $table->string('slug')->nullable()->unique();
            $table->string('reading_title')->nullable();
            $table->string('image_file')->nullable();
            $table->string('pdf_file_title')->nullable();
            $table->string('pdf_file')->nullable();
            $table->text('short_content')->nullable();
            $table->longText('long_content')->nullable();
            $table->boolean('status')->default(1);
            $table->unsignedBigInteger('post_user')->nullable();
            $table->unsignedBigInteger('view_count')->default(0);
            $table->timestamps();
            $table->foreign('category_id')->references('id')->on('blog_categories')->onDelete('set null');
            $table->foreign('post_user')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
