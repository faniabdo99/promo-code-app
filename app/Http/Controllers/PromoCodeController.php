<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePromoCodeRequest;
use App\Http\Requests\PromoCodeRedeemRequest;
use App\Models\PromoCode;
use Illuminate\Support\Str;

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
        $user = $request->user();
        $promoCode = PromoCode::where('code', $request->code)->first();
        $promoCode->redeem($user);

        return response()->json([
            'price' => $request->price,
            'promocode_discounted_amount' => $promoCode->calculateDiscount($request->price),
            'final_price' => $request->price - $promoCode->calculateDiscount($request->price),
        ]);
    }
}
