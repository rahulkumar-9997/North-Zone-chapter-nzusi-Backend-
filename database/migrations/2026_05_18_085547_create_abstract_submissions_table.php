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
        Schema::create('abstract_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_user')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('institution')->nullable();
            $table->string('designation')->nullable();
            $table->string('city')->nullable();
            $table->string('presentation_type')->nullable();
            $table->string('topic_category')->nullable();
            $table->string('abstract_title')->nullable();
            $table->text('authors')->nullable();
            $table->string('corresponding_author')->nullable();
            $table->longText('abstract_body')->nullable();
            $table->string('supporting_file')->nullable();
            $table->enum('status', [
                'pending',
                'approved',
                'rejected'
            ])->default('pending');
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();
            $table->foreign('post_user')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abstract_submissions');
    }
};
