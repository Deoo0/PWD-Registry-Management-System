<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyMember extends Model
{
    protected $fillable = [
        'pwd_id',
        'relationship',
        'last_name',
        'first_name',
        'middle_name',
        'suffix',
    ];

    public function pwd(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Pwd::class);
    }

    /**
     * Full name accessor.
     */
    public function getFullNameAttribute(): string
    {
        $middle = $this->middle_name ? ' ' . $this->middle_name : '';
        $suffix = $this->suffix ? ' ' . $this->suffix : '';
        return "{$this->last_name}, {$this->first_name}{$middle}{$suffix}";
    }
}
