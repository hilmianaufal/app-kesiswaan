<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('counseling_cases', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('school_class_id')
                ->nullable()
                ->constrained('school_classes')
                ->nullOnDelete();

            $table->string('case_number', 50)->unique();
            $table->string('title', 200);
            $table->string('problem_category', 50);
            $table->string('priority', 30)->default('medium');
            $table->string('confidentiality_level', 30)
                ->default('confidential');

            $table->text('summary')->nullable();
            $table->string('status', 30)->default('open');

            $table->date('opened_at');
            $table->date('closed_at')->nullable();

            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('problem_category');
            $table->index('priority');
            $table->index('status');
            $table->index('confidentiality_level');
            $table->index(['student_id', 'opened_at']);
            $table->index(['assigned_to', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counseling_cases');
    }
};
