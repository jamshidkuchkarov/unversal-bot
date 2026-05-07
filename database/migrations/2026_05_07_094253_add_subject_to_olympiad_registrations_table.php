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
        Schema::table('olympiad_registrations', function (Blueprint $table) {
            $table->string('subject')->nullable()->after('olympiad_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('olympiad_registrations', function (Blueprint $table) {
            $table->dropColumn('subject');
        });
    }
};
