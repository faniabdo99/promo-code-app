<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if the user is an admin.
     *
     * @description This is a simplified solution to check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->email == 'admin@email.com';
    }

    /**
     * Get the promo codes that the user has used.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function PromoCodes()
    {
        return $this->belongsToMany(PromoCode::class)->withTimestamps();
    }

    /**
     * Get the usages of the promo code.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function promoCodeUsages()
    {
        return $this->hasMany(PromoCodeUsage::class);
    }
}
