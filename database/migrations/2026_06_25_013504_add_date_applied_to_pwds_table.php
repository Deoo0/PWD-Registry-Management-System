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
        Schema::table('pwds', function (Blueprint $table) {
            $table->date('date_applied')->nullable()->after('pwd_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pwds', function (Blueprint $table) {
            $table->date('date_applied')->nullable()->after('pwd_number');
        });
    }
};
