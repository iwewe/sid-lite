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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('min_verified')->default(0)->comment('Minimum questions required for verification');
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0)->comment('Display order');
            $table->string('icon')->nullable()->comment('Icon emoji or CSS class');
            $table->timestamps();

            $table->index('code');
            $table->index(['is_active', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
