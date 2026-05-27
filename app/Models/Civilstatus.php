<?php

// ── CivilStatus ────────────────────────────────────────────────────────────
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CivilStatus extends Model
{
    use HasFactory;
    
    protected $fillable = ['name'];
    
    protected $table = 'civil_status';

    public function pwds(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Pwd::class);
    }
}