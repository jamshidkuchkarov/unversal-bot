<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('director_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('school_admins', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->json('permissions')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'school_id']);
        });

        Schema::create('school_bots', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->unique()->constrained('schools')->cascadeOnDelete();
            $table->string('bot_token')->nullable();
            $table->string('bot_username')->nullable();
            $table->string('bot_name')->nullable();
            $table->string('webhook_url')->nullable();
            $table->boolean('webhook_set')->default(false);
            $table->text('welcome_text')->nullable();
            $table->text('main_menu_text')->nullable();
            $table->json('menu_buttons')->nullable();
            $table->string('main_channel')->nullable();
            $table->string('main_group')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
        });

        Schema::create('bot_sessions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->bigInteger('telegram_user_id');
            $table->string('telegram_username')->nullable();
            $table->string('telegram_first_name')->nullable();
            $table->string('telegram_last_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('state')->default('idle');
            $table->json('data')->nullable();
            $table->boolean('is_blocked')->default(false);
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->unique(['school_id', 'telegram_user_id']);
            $table->index(['school_id', 'state']);
        });

        Schema::create('bot_messages_log', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->bigInteger('telegram_user_id');
            $table->string('direction');
            $table->text('message_text')->nullable();
            $table->string('message_type')->default('text');
            $table->json('raw_data')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'telegram_user_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bot_messages_log');
        Schema::dropIfExists('bot_sessions');
        Schema::dropIfExists('school_bots');
        Schema::dropIfExists('school_admins');
        Schema::dropIfExists('schools');
    }
};
