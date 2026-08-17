<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abstract_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('abstract_submission_id')->constrained('abstract_submissions')->onDelete('cascade');
            $table->foreignId('assigned_to')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('assigned_at')->useCurrent();
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->timestamps();
            $table->unique(['abstract_submission_id', 'assigned_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abstract_assignments');
    }
};