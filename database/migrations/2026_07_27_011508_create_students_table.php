<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_class_id')
                ->nullable()
                ->constrained('school_classes')
                ->nullOnDelete();

            $table->string('nis', 30)->unique();
            $table->string('nisn', 30)->nullable()->unique();
            $table->string('name', 150);
            $table->string('gender', 20);

            $table->string('birth_place', 100)->nullable();
            $table->date('birth_date')->nullable();

            $table->text('address')->nullable();
            $table->string('phone', 25)->nullable();

            $table->string('father_name', 150)->nullable();
            $table->string('mother_name', 150)->nullable();
            $table->string('guardian_name', 150)->nullable();
            $table->string('parent_phone', 25)->nullable();

            $table->string('photo')->nullable();
            $table->unsignedSmallInteger('enrollment_year')->nullable();
            $table->string('status', 30)->default('active');
            $table->text('notes')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('name');
            $table->index('gender');
            $table->index('status');
            $table->index('enrollment_year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
