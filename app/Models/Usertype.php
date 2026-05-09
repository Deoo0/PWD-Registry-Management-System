<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usertype extends Model
{
    protected $table = 'usertype';

    protected $fillable = [
        'name',
    ];

    public function users(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class);
    }
}