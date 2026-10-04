<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_violations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('school_class_id')
                ->nullable()
                ->constrained('school_classes')
                ->nullOnDelete();

            $table->foreignId('violation_type_id')
                ->nullable()
                ->constrained('violation_types')
                ->nullOnDelete();

            /*
             * Snapshot disimpan agar data historis tidak berubah
             * ketika master jenis pelanggaran diperbarui.
             */
            $table->string('violation_name', 150);
            $table->string('category', 30);
            $table->unsignedInteger('points')->default(0);

            $table->date('violation_date');
            $table->string('location', 150)->nullable();
            $table->text('chronology');
            $table->text('action_taken')->nullable();
            $table->string('follow_up_status', 30)->default('pending');
            $table->text('notes')->nullable();

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('violation_date');
            $table->index('category');
            $table->index('follow_up_status');
            $table->index(['student_id', 'violation_date']);
            $table->index(['school_class_id', 'violation_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_violations');
    }
};
