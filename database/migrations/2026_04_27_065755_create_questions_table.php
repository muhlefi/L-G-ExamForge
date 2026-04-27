<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->text('question_text');
            $table->string('type'); // pilgan, isian, esai
            $table->string('cognitive_level'); // C1-C6
            $table->string('school_level'); // SD, SMP, SMA
            $table->string('topic');
            $table->json('options')->nullable(); // JSON for pilgan
            $table->text('correct_answer');
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
