<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('level', 20);
            $table->string('major', 100)->nullable();

            $table->foreignId('homeroom_teacher_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('academic_year', 20);
            $table->string('semester', 20)->default('ganjil');
            $table->unsignedInteger('capacity')->default(36);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(
                ['name', 'academic_year', 'semester'],
                'school_classes_unique_name_year_semester'
            );

            $table->index('academic_year');
            $table->index('semester');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_classes');
    }
};
