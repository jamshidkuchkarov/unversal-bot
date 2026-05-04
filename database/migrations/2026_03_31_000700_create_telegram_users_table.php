<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegram_users', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('telegram_id');
            $table->string('chat_id')->index();
            $table->string('username')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('language_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_subscribed')->default(false);
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamps();

            $table->unique(['school_id', 'telegram_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_users');
    }
};
