<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admissions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->string('title');
            $table->string('academic_year', 20);
            $table->json('target_classes');
            $table->json('admission_options')->nullable();
            $table->text('description')->nullable();
            $table->text('requirements')->nullable();
            $table->json('required_documents')->nullable();
            $table->integer('quota')->nullable();
            $table->integer('accepted_count')->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['draft', 'published', 'closed', 'completed'])->default('draft');
            $table->boolean('announced_to_channel')->default(false);
            $table->string('announcement_message_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'status']);
            $table->index('academic_year');
        });

        Schema::create('admission_applications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('admission_id')->constrained('admissions')->cascadeOnDelete();
            $table->foreignId('school_id')->constrained('schools');
            $table->foreignId('bot_session_id')->nullable()->constrained('bot_sessions')->nullOnDelete();
            $table->bigInteger('telegram_user_id')->nullable();
            $table->string('student_full_name')->nullable();
            $table->date('student_birth_date')->nullable();
            $table->enum('student_gender', ['male', 'female'])->nullable();
            $table->integer('target_class');
            $table->string('target_variant')->nullable();
            $table->string('education_language', 20)->nullable();
            $table->string('previous_school')->nullable();
            $table->string('parent_full_name');
            $table->string('parent_phone');
            $table->string('parent_phone_2')->nullable();
            $table->enum('parent_relation', ['father', 'mother', 'guardian'])->default('father');
            $table->string('address')->nullable();
            $table->text('transition_reason')->nullable();
            $table->json('documents')->nullable();
            $table->enum('status', ['pending', 'reviewing', 'accepted', 'rejected', 'waitlist'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index(['admission_id', 'status']);
            $table->index('school_id');
            $table->index('student_birth_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_applications');
        Schema::dropIfExists('admissions');
    }
};
