<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Rename applicants -> pwds
        if (Schema::hasTable('applicants')) {
            Schema::rename('applicants', 'pwds');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert pwds -> applicants
        if (Schema::hasTable('pwds')) {
            Schema::rename('pwds', 'applicants');
        }
    }
};