<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacancies', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->string('title');
            $table->string('category')->default('teacher');
            $table->string('subject')->nullable();
            $table->longText('description')->nullable();
            $table->longText('requirements')->nullable();
            $table->longText('conditions')->nullable();
            $table->decimal('salary_min', 12, 2)->nullable();
            $table->decimal('salary_max', 12, 2)->nullable();
            $table->string('salary_note')->nullable();
            $table->date('deadline')->nullable();
            $table->string('work_schedule')->nullable();
            $table->enum('status', ['draft', 'published', 'closed', 'archived'])->default('draft');
            $table->integer('views_count')->default(0);
            $table->boolean('announced_to_channel')->default(false);
            $table->string('announcement_message_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'status']);
            $table->index('category');
        });

        Schema::create('vacancy_applications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('vacancy_id')->nullable()->constrained('vacancies')->nullOnDelete();
            $table->enum('application_type', ['current', 'reserve'])->default('current');
            $table->foreignId('school_id')->constrained('schools');
            $table->foreignId('bot_session_id')->nullable()->constrained('bot_sessions')->nullOnDelete();
            $table->bigInteger('telegram_user_id')->nullable();
            $table->string('full_name');
            $table->string('phone');
            $table->string('telegram_contact')->nullable();
            $table->string('email')->nullable();
            $table->integer('age')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('address')->nullable();
            $table->text('experience')->nullable();
            $table->integer('experience_years')->nullable();
            $table->text('education')->nullable();
            $table->text('certificates')->nullable();
            $table->text('skills')->nullable();
            $table->text('achievements')->nullable();
            $table->text('about_self')->nullable();
            $table->string('subject')->nullable();
            $table->string('cv_file_path')->nullable();
            $table->string('photo_file_path')->nullable();
            $table->enum('status', ['pending', 'reviewing', 'invited', 'hired', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->text('response_text')->nullable();
            $table->boolean('response_sent')->default(false);
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index(['vacancy_id', 'status']);
            $table->index('school_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacancy_applications');
        Schema::dropIfExists('vacancies');
    }
};
