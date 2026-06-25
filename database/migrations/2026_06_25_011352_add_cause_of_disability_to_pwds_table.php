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
            $table->string('disability_cause_type')->nullable()->after('occupation_other');  // Congenital or Acquired
            $table->string('disability_cause')->nullable()->after('disability_cause_type');  // specific cause
            $table->string('disability_cause_other')->nullable()->after('disability_cause'); // if Others
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pwds', function (Blueprint $table) {
            $table->dropColumn(['disability_cause_type', 'disability_cause', 'disability_cause_other']);
        });
    }
};
