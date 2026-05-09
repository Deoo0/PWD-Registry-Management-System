<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->enum('status', [
                        'pending',
                        'approved',
                        'rejected',
                        'cancelled'
                    ])
                  ->default('pending')
                  ->after('date_applied');

            $table->text('remarks')
                  ->nullable()
                  ->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['status', 'remarks']);
        });
    }
};