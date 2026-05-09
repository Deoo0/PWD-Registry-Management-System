<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'username',
        'password',
        'usertype_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'is_active' => 'boolean',
    ];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

    public function isAdmin(): bool
    {
        return $this->usertype_id === 1;
    }
 
    public function isStaff(): bool
    {
        return $this->usertype_id === 2;
    }


    public function usertype(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Usertype::class);
    }
}
