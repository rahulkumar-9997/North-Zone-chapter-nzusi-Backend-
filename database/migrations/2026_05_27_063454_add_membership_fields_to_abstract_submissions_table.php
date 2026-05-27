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
        Schema::table('abstract_submissions', function (Blueprint $table) {
            $table->string('nzusi_membership_no')->nullable()->after('supporting_file');
            $table->string('usi_membership_no')->nullable()->after('nzusi_membership_no');
            $table->string('conf_reg_no')->nullable()->after('usi_membership_no');
            $table->string('video_link')->nullable()->after('conf_reg_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('abstract_submissions', function (Blueprint $table) {
            $table->dropColumn([
                'nzusi_membership_no',
                'usi_membership_no',
                'conf_reg_no',
                'video_link'
            ]);
        });
    }
};
