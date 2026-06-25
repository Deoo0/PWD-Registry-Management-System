<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pwd extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'last_name',
        'first_name',
        'middle_name',
        'suffix',
        'date_of_birth',
        'sex',
        'civil_status_id',
        'educational_attainment_id',
        'occupation_id',
        'mobile_no',
        'email',
        'pwd_number',
        'is_4ps_beneficiary',
        'residence_id',
        'photo_path',
        'occupation_other',
        'disability_cause_type',
        'disability_cause',
        'disability_cause_other',
        'date_applied',
    ];

    protected $casts = [
        'date_of_birth'      => 'date',
        'date_applied'  => 'date',
        'is_4ps_beneficiary' => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function civilStatus(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CivilStatus::class);
    }

    public function educationalAttainment(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(EducationalAttainment::class);
    }

    public function occupation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Occupation::class);
    }

    public function residence(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Residence::class);
    }

    public function disabilities(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(DisabilityType::class, 'pwd_disabilities','pwd_id','disability_type_id');
    }
    public function familyMembers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FamilyMember::class);
    }
    
    // ── Accessors ──────────────────────────────────────────────

    /**
     * Full name in "Last, First Middle" format.
     */
    public function getFullNameAttribute(): string
    {
        $middle = $this->middle_name ? ' ' . $this->middle_name : '';
        $suffix = $this->suffix ? ' ' . $this->suffix : '';
        return "{$this->last_name}, {$this->first_name}{$middle}{$suffix}";
    }

    /**
     * Age computed from date_of_birth.
     */
    public function getAgeAttribute(): int
    {
        return $this->date_of_birth->age;
    }
    public function getLatestDisabilitiesAttribute(): string
    {
    // $this->disabilities is your existing BelongsToMany relationship
    return $this->disabilities->pluck('name')->implode(', ') ?: '—';
    }
    // app/Models/Pwd.php

    public function getExpiryDateAttribute(): ?\Carbon\Carbon
    {
        return $this->date_applied?->addYears(5);
    }

    public function getIdStatusAttribute(): string
    {
        if (!$this->date_applied) return 'unknown';
        $expiry = $this->expiry_date;
        if ($expiry->isPast())                          return 'expired';
        if (now()->diffInDays($expiry) <= 180) return 'expiring';
        return 'valid';
    }
}