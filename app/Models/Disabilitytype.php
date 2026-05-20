<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DisabilityType extends Model
{
    use HasFactory;

    protected $table = 'disability_type';

    protected $fillable = ['name'];

    // ── Relationships ──────────────────────────────────────────

    public function pwdDisabilities(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PwdDisability::class);
    }

    /**
     * PWDs that have this disability type.
     */
    public function pwds(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Pwd::class, 'pwd_disabilities');
    }
}