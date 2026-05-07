<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('olympiads', function (Blueprint $table) {
            $table->json('subjects')->nullable()->after('title');
        });

        // Migrate existing subject data to subjects array
        DB::statement("UPDATE olympiads SET subjects = JSON_ARRAY(subject) WHERE subject IS NOT NULL");

        Schema::table('olympiads', function (Blueprint $table) {
            $table->dropColumn('subject');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('olympiads', function (Blueprint $table) {
            $table->string('subject')->nullable()->after('title');
        });

        // Migrate back: take first subject from array
        DB::statement("UPDATE olympiads SET subject = JSON_UNQUOTE(JSON_EXTRACT(subjects, '$[0]')) WHERE subjects IS NOT NULL");

        Schema::table('olympiads', function (Blueprint $table) {
            $table->dropColumn('subjects');
        });
    }
};
