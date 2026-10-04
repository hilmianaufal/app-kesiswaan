<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_development_notes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('school_class_id')
                ->nullable()
                ->constrained('school_classes')
                ->nullOnDelete();

            $table->date('note_date');
            $table->string('category', 50);
            $table->string('type', 30);
            $table->string('title', 200);
            $table->text('description');
            $table->text('follow_up')->nullable();
            $table->string('follow_up_status', 30)->default('pending');
            $table->boolean('is_private')->default(false);

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('note_date');
            $table->index('category');
            $table->index('type');
            $table->index('follow_up_status');
            $table->index(['student_id', 'note_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_development_notes');
    }
};
