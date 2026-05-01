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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('membership_no')->nullable()->unique();
            $table->string('name')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('password')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('city_name')->nullable();
            $table->string('mobile_no')->nullable();
            $table->foreignId('membership_type_id')->nullable()->constrained('member_types')->cascadeOnDelete();
            $table->date('dob')->nullable();
            $table->enum('preferred_address', ['office', 'residence'])->nullable();
            $table->date('membership_approved_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('login_attempts')->default(0);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('password_changed_at')->nullable();
            $table->timestamps();
            $table->index('mobile_no');
            $table->index('city_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
