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
        'residence_id',
        'photo_path',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
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

    public function latestApplication(): \Illuminate\Database\Eloquent\Relations\HasOne
{
    return $this->hasOne(Application::class)->latestOfMany();
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
}