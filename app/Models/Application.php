<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Application extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'application_type',
        'date_applied',
        'photo_path',
        'status',
        'remarks',
        'applicant_id',
        'user_id',
    ];

    protected $casts = [
        'date_applied' => 'date',
    ];

    // Status constants — use these instead of hardcoding strings
    const STATUS_PENDING   = 'pending';
    const STATUS_APPROVED  = 'approved';
    const STATUS_REJECTED  = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    // ── Relationships ──────────────────────────────────────────

    /**
     * The applicant who submitted this application.
     */
    public function applicant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    /**
     * The user (encoder) who processed this application.
     */
    public function encoder(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Pivot records linking this application to disability types.
     */
    public function applicationDisabilities(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ApplicationDisability::class);
    }

    /**
     * Disability types for this application (through the pivot).
     */
    public function disabilities(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            DisabilityType::class,
            ApplicationDisability::class,
            'application_id',   // FK on application_disabilities
            'id',               // FK on disability_types
            'id',               // local key on applications
            'disability_type_id'// local key on application_disabilities
        );
    }

    /**
     * Family members listed on this application.
     */
    public function familyMembers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FamilyMember::class);
    }

    // ── Query Scopes ───────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopeNewApplicant($query)
    {
        return $query->where('application_type', 'New Applicant');
    }

    public function scopeRenewal($query)
    {
        return $query->where('application_type', 'Renewal');
    }
}