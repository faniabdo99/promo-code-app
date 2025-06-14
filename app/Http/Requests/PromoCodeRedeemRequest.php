<?php

namespace App\Http\Requests;

use App\Models\PromoCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PromoCodeRedeemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Validate the promo code for various conditions.
     *
     * @return string|null Returns error message if validation fails, null if passes
     */
    protected function validatePromoCode(PromoCode $promoCode): ?string
    {
        // Check if promo code is expired
        if ($promoCode->expires_at && $promoCode->expires_at < today()) {
            return 'This promo code has expired.';
        }

        // Check if max usage limit is exceeded (0 means unlimited)
        if ($promoCode->usage_limit > 0 && $promoCode->usages()->count() >= $promoCode->usage_limit) {
            return 'This promo code has reached its maximum usage limit.';
        }

        // Check if user is allowed to use this promo code
        $allowedUsers = $promoCode->Users()->pluck('user_id')->toArray();
        if (! empty($allowedUsers) && ! in_array(Auth::id(), $allowedUsers)) {
            return 'You are not eligible to use this promo code.';
        }

        // Check if user has already used this promo code
        $userUsageCount = $promoCode->usages()->where('user_id', Auth::id())->count();
        if ($promoCode->usage_per_user > 0 && $userUsageCount >= $promoCode->usage_per_user) {
            return 'You can\'t use this promo code more than '.$promoCode->usage_per_user.' times.';
        }

        return null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'exists:promo_codes,code',
                function ($attribute, $value, $fail) {
                    $promoCode = PromoCode::where('code', $value)->first();

                    if (! $promoCode) {
                        return;
                    }

                    $errorMessage = $this->validatePromoCode($promoCode);
                    if ($errorMessage) {
                        $fail($errorMessage);
                    }
                },
            ],
            'price' => 'required|numeric|min:0',
        ];
    }
}
