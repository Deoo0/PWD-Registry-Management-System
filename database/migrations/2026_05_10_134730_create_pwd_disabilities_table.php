<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pwd_disabilities', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pwd_id')
                ->constrained('pwds')
                ->onDelete('cascade');

            $table->foreignId('disability_type_id')
                ->constrained('disability_type')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pwd_disabilities');
    }
};