<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('family_members', function (Blueprint $table) {
            // 1. Drop the old foreign key constraint first, then the column
            $table->dropForeign(['application_id']);
            $table->dropColumn('application_id');

            // 2. Add the new FK pointing to pwds
            $table->foreignId('pwd_id')
                ->after('id')
                ->constrained('pwds')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('family_members', function (Blueprint $table) {
            $table->dropForeign(['pwd_id']);
            $table->dropColumn('pwd_id');

            $table->foreignId('application_id')
                ->after('id')
                ->constrained('applications');
        });
    }
};