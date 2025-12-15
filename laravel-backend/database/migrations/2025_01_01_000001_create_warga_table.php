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
        Schema::create('warga', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 16)->unique();
            $table->string('nama');
            $table->string('dusun')->nullable();
            $table->string('rw', 10)->nullable();
            $table->string('rt', 10)->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_kk', 16)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('telepon', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for search performance
            $table->index('nik');
            $table->index('nama');
            $table->index(['dusun', 'rw', 'rt']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warga');
    }
};
