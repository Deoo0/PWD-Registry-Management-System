<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PwdDisability extends Model
{
    use HasFactory;

    protected $fillable = [
        'pwd_id',
        'disability_type_id',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function pwd(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Pwd::class);
    }

    public function disabilityType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DisabilityType::class);
    }
}