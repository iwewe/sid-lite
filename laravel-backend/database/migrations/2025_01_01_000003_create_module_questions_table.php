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
        Schema::create('module_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
            $table->string('code', 50)->comment('Question code, e.g., b3r301a');
            $table->text('question');
            $table->enum('field_type', ['select', 'text', 'number', 'date', 'textarea'])->default('select');
            $table->json('options')->nullable()->comment('For select type: [{value: "1", label: "Option 1"}]');
            $table->boolean('is_required')->default(false);
            $table->integer('order')->default(0);
            $table->text('help_text')->nullable()->comment('Additional help/hint for the question');
            $table->timestamps();

            $table->index('module_id');
            $table->index('code');
            $table->index(['module_id', 'order']);
            $table->unique(['module_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_questions');
    }
};
