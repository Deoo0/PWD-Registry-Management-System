<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Residence extends Model
{
    protected $fillable = [
        'house_no_and_street',
        'barangay',
        'municipality',
        'province',
        'region',
    ];

    public function pwds(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Pwd::class);
    }
}