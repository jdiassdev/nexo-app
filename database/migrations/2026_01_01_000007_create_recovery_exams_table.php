<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recovery_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['recovery_1', 'recovery_2', 'final']);
            $table->decimal('score', 4, 2)->nullable();
            $table->timestamps();

            $table->unique(['subject_id', 'student_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recovery_exams');
    }
};
