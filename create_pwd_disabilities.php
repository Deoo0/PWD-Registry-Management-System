<?php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

// Create the pwd_disabilities table with correct structure
Schema::create('pwd_disabilities', function (Blueprint $table) {
    $table->id();
    $table->foreignId('pwd_id')->constrained('pwds')->onDelete('cascade');
    $table->foreignId('disability_type_id')->constrained('disability_type');
    $table->timestamps();
});

echo "pwd_disabilities table created successfully\n";
