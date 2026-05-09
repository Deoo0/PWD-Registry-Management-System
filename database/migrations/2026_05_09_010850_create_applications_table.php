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
        Schema::create('applications', function (Blueprint $table) {

            $table->id();

            $table->enum('application_type', [
                'New Applicant',
                'Renewal'
            ]);

            $table->string('pwd_number')->nullable();

            $table->date('date_applied');

            $table->string('photo_path')->nullable();

            $table->foreignId('applicant_id')
                ->constrained('applicants');

            $table->foreignId('residence_id')
                ->constrained('residences');

            $table->foreignId('user_id')
                ->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
