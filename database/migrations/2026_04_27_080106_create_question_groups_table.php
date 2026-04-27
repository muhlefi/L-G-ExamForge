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
        Schema::create('question_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Kelompok A, Kelompok B
            $table->string('type'); // pilgan, isian, esai
            $table->integer('amount')->default(1);
            $table->integer('options_count')->default(4); // 3, 4, or 5
            $table->string('cognitive_level')->default('C3');
            $table->boolean('with_explanation')->default(false);
            $table->enum('status', ['pending', 'done'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_groups');
    }
};
