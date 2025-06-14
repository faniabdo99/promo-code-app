<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePromoCodeRequest;
use App\Http\Requests\PromoCodeRedeemRequest;
use App\Models\PromoCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class PromoCodeController extends Controller
{
    /**
     * Store a newly created promo code.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreatePromoCodeRequest $request)
    {
        // Generate a promo code if not provided
        $code = $request->validated('code') ?? strtoupper(Str::random(10));

        // Create the promo code
        $promoCode = PromoCode::create([
            'code' => $code,
            ...$request->validated(),
        ]);

        // Create a promo code-user relation
        if ($request->has('user_ids')) {
            $promoCode->Users()->attach($request->user_ids);
        }

        return response()->json(['message' => 'Promo code created successfully', 'promo_code' => $promoCode], 201);
    }

    /**
     * Redeem a promo code for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function redeem(PromoCodeRedeemRequest $request)
    {
        $promoCode = $this->getPromoCode($request->code);
        $promoCode->redeem(auth()->user());

        return response()->json([
            'price' => $request->price,
            'promocode_discounted_amount' => $promoCode->calculateDiscount($request->price),
            'final_price' => $request->price - $promoCode->calculateDiscount($request->price),
        ]);
    }

    /**
     * Get a promo code from cache or database.
     * 
     * @param string $code The promo code to retrieve
     * @return \App\Models\PromoCode|null Returns the promo code model if found, null otherwise
     */
    private function getPromoCode($code)
    {
        $promo_codes = Cache::remember('promo_codes_', 3600, function(){
            return PromoCode::get();
        });
        return $promo_codes->where('code', $code)->first();
    }
}
