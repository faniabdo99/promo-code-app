<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    protected $fillable = [
        'code',
        'type',
        'discount',
        'expires_at',
        'usage_limit',
        'usage_per_user',
        'is_active',
    ];

    /**
     * Get the users that have used the promo code.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function Users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * Get the usages of the promo code.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usages()
    {
        return $this->hasMany(PromoCodeUsage::class);
    }

    public function redeem(User $user)
    {
        $this->usages()->create([
            'user_id' => $user->id,
            'promo_code_id' => $this->id,
        ]);
    }

    public function calculateDiscount($price)
    {
        if ($this->type === 'fixed') {
            return floatval($this->discount);
        }

        return floatval($price * $this->discount / 100);
    }
}
