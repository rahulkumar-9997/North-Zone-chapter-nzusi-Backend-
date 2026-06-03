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
        Schema::table('blogs', function (Blueprint $table) {
            $table->unsignedBigInteger('blog_subcategory_id')
                  ->nullable()
                  ->after('category_id');
            $table->foreign('blog_subcategory_id')
                  ->references('id')
                  ->on('blog_subcategories')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropForeign('blog_subcategory_id');
            $table->dropColumn('blog_subcategory_id');
        });
    }
};
