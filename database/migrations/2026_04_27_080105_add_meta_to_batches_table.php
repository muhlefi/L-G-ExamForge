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
        Schema::table('batches', function (Blueprint $table) {
            $table->string('school_name')->nullable()->after('id');
            $table->string('class_name')->nullable()->after('school_name');
            $table->string('teacher_name')->nullable()->after('class_name');
            $table->text('material_scope')->nullable()->after('teacher_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->dropColumn(['school_name', 'class_name', 'teacher_name', 'material_scope']);
        });
    }
};
