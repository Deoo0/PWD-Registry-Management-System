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
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('suffix')->nullable();

            $table->date('date_of_birth');

            $table->enum('sex', ['Male', 'Female']);

            $table->foreignId('civil_status_id')
                ->constrained('civil_status');
                
            $table->foreignId('educational_attainment_id')
                ->constrained('educational_attainments');

            $table->string('mobile_no', 20)->nullable();

            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
