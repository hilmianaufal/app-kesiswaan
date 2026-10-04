<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_permissions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('school_class_id')
                ->nullable()
                ->constrained('school_classes')
                ->nullOnDelete();

            $table->string('permission_type', 30);
            $table->date('permission_date');

            $table->time('start_time')->nullable();
            $table->time('expected_return_time')->nullable();
            $table->time('actual_return_time')->nullable();

            $table->text('reason');
            $table->string('evidence_path')->nullable();

            $table->string('status', 30)->default('pending');
            $table->text('rejection_note')->nullable();

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index('permission_type');
            $table->index('permission_date');
            $table->index('status');
            $table->index(['student_id', 'permission_date']);
            $table->index(['school_class_id', 'permission_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_permissions');
    }
};
