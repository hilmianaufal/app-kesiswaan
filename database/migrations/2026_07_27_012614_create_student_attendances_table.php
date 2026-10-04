<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('school_class_id')
                ->nullable()
                ->constrained('school_classes')
                ->nullOnDelete();

            $table->date('attendance_date');
            $table->string('status', 20);

            $table->time('arrival_time')->nullable();
            $table->time('departure_time')->nullable();

            $table->boolean('is_late')->default(false);
            $table->boolean('is_early_leave')->default(false);

            $table->text('note')->nullable();

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(
                ['student_id', 'attendance_date'],
                'student_attendance_unique'
            );

            $table->index(
                ['school_class_id', 'attendance_date'],
                'student_attendance_class_date_index'
            );

            $table->index(
                ['status', 'attendance_date'],
                'student_attendance_status_date_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_attendances');
    }
};
