<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EducationalAttainment extends Model
{
    use HasFactory;
    
    protected $fillable = ['name'];
    
    protected $table = 'educational_attainments';

    public function pwds(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Pwd::class);
    }
}