<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePromoCodeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => 'string|max:255|unique:promo_codes',
            'type' => 'required|in:fixed,percentage',
            'discount' => 'required|numeric|min:0',
            'expires_at' => 'date',
            'usage_limit' => 'required|integer|min:0',
            'usage_per_user' => 'required|integer|min:0',
            'user_ids' => 'array',
            'user_ids.*' => 'exists:users,id',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.string' => 'The promo code must be a string.',
            'code.max' => 'The promo code cannot be longer than 255 characters.',
            'code.unique' => 'This promo code already exists.',
            'type.required' => 'Please specify the type of discount (fixed or percentage).',
            'type.in' => 'The discount type must be either fixed or percentage.',
            'discount.required' => 'Please specify the discount amount.',
            'discount.numeric' => 'The discount must be a number.',
            'discount.min' => 'The discount cannot be negative.',
            'expires_at.date' => 'Please provide a valid expiration date.',
            'usage_limit.required' => 'Please specify the usage limit.',
            'usage_limit.integer' => 'The usage limit must be a whole number.',
            'usage_limit.min' => 'The usage limit cannot be negative.',
            'usage_per_user.required' => 'Please specify the usage limit per user.',
            'usage_per_user.integer' => 'The usage limit per user must be a whole number.',
            'usage_per_user.min' => 'The usage limit per user cannot be negative.',
            'user_ids.array' => 'User IDs must be provided as an array.',
            'user_ids.*.exists' => 'One or more selected users do not exist.',
        ];
    }
}
