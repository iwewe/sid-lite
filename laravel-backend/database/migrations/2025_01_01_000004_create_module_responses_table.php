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
        Schema::create('module_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warga_id')->constrained('warga')->onDelete('cascade');
            $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
            $table->json('responses')->comment('JSON: {question_code: value}');
            $table->integer('verification_score')->default(0)->comment('Count of required questions filled');
            $table->boolean('is_verified')->default(false)->comment('Computed: score >= module.min_verified');
            $table->foreignId('submitted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->index('warga_id');
            $table->index('module_id');
            $table->index('is_verified');
            $table->index('submitted_at');
            $table->unique(['warga_id', 'module_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_responses');
    }
};
