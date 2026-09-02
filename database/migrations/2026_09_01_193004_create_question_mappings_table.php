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
        Schema::create('question_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('table_name'); // e.g., 'alumnis'
            $table->string('column_name'); // e.g., 'F1'
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->timestamps();

            // Ensure a column is mapped to only one question
            $table->unique(['table_name', 'column_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_mappings');
    }
};
