<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DisabilityType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // ── Relationships ──────────────────────────────────────────

    public function applicationDisabilities(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ApplicationDisability::class);
    }
}