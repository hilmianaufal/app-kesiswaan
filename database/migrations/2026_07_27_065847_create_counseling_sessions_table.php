<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('counseling_sessions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('counseling_case_id')
                ->constrained('counseling_cases')
                ->cascadeOnDelete();

            $table->date('session_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            $table->string('location', 150)->nullable();
            $table->string('method', 30)->default('individual');
            $table->string('attendance_status', 30)
                ->default('scheduled');

            $table->text('agenda');
            $table->text('result')->nullable();
            $table->text('follow_up')->nullable();

            $table->date('next_session_date')->nullable();
            $table->boolean('is_parent_contacted')->default(false);
            $table->text('parent_response')->nullable();

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('session_date');
            $table->index('method');
            $table->index('attendance_status');
            $table->index([
                'counseling_case_id',
                'session_date',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counseling_sessions');
    }
};
