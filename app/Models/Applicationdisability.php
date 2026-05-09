<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApplicationDisability extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'disability_type_id',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function application(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function disabilityType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DisabilityType::class);
    }
}