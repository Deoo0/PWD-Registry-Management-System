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
                $table->boolean('is_4ps_beneficiary')->default(false)->after('pwd_number');
            });
        }

    public function down(): void
        {
            Schema::table('pwds', function (Blueprint $table) {
                $table->dropColumn('is_4ps_beneficiary');
            });
        }
};
