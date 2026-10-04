<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_achievements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('school_class_id')
                ->nullable()
                ->constrained('school_classes')
                ->nullOnDelete();

            $table->string('title', 200);
            $table->string('category', 50);
            $table->string('level', 50);
            $table->string('rank', 50);
            $table->unsignedInteger('points')->default(0);

            $table->string('event_name', 200)->nullable();
            $table->string('organizer', 200)->nullable();
            $table->string('mentor', 150)->nullable();
            $table->date('achievement_date');
            $table->text('description')->nullable();
            $table->string('certificate_path')->nullable();

            $table->string('status', 30)->default('pending');

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index('achievement_date');
            $table->index('category');
            $table->index('level');
            $table->index('status');
            $table->index(['student_id', 'achievement_date']);
            $table->index(['school_class_id', 'achievement_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_achievements');
    }
};
