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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');            
            $table->string('slug')->unique()->nullable();
            $table->string('main_image')->nullable();
            $table->longText('content')->nullable();
            $table->string('route_name')->unique()->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('pages')->cascadeOnDelete();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('template')->default('default');
            $table->boolean('show_in_sidebar')->default(false);
            $table->timestamps();
            $table->index('parent_id');
            $table->index('is_active');
            $table->index('order');
            $table->index('template');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
