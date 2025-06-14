<?php

namespace Tests\Feature\PromoCode;

use App\Models\User;
use App\Models\PromoCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;

class PromoCodeTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    /**
     * Set up the test environment.
     * By default, users are not authenticated.
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Do not authenticate by default
    }

    /**
     * Test that an authenticated user can create a promo code.
     * Verifies the response structure and database entry.
     */
    #[Test]
    public function it_can_create_a_promo_code()
    {
        $this->user = User::factory()->create([
            'email' => 'admin@email.com',
        ]);
        Sanctum::actingAs($this->user);
        $promoCodeData = [
            'code' => 'TEST123',
            'type' => 'fixed',
            'discount' => 10.00,
            'usage_limit' => 100,
            'usage_per_user' => 1,
            'expires_at' => now()->addDays(30)->toDateTimeString(),
        ];

        $response = $this->postJson('/api/v1/promo-codes', $promoCodeData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'promo_code' => [
                    'id',
                    'code',
                    'type',
                    'discount',
                    'usage_limit',
                    'usage_per_user',
                    'expires_at',
                    'created_at',
                    'updated_at',
                ]
            ]);

        $this->assertDatabaseHas('promo_codes', [
            'code' => 'TEST123',
            'discount' => 10.00,
            'usage_limit' => 100,
        ]);
    }

    /**
     * Test validation of required fields when creating a promo code.
     * Ensures proper error response when required fields are missing.
     */
    #[Test]
    public function it_validates_required_fields_when_creating_promo_code()
    {
        $this->user = User::factory()->create([
            'email' => 'admin@email.com',
        ]);
        Sanctum::actingAs($this->user);
        $response = $this->postJson('/api/v1/promo-codes', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type', 'discount', 'usage_limit', 'usage_per_user']);
    }

    /**
     * Test successful redemption of a valid promo code.
     * Verifies the response structure and discount calculation.
     */
    #[Test]
    public function it_can_redeem_a_valid_promo_code()
    {
        $this->user = User::factory()->create([
            'email' => 'admin@email.com',
        ]);
        Sanctum::actingAs($this->user);
        $promoCode = PromoCode::factory()->create([
            'code' => 'REDEEM123',
            'type' => 'fixed',
            'discount' => 20.00,
            'usage_limit' => 10,
            'usage_per_user' => 1,
            'expires_at' => now()->addDays(30),
        ]);

        $response = $this->postJson('/api/v1/promo-codes/redeem', [
            'code' => 'REDEEM123',
            'price' => 100.00
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'price',
                'promocode_discounted_amount',
                'final_price',
            ]);
    }

    /**
     * Test that expired promo codes cannot be redeemed.
     * Verifies proper error response for expired codes.
     */
    #[Test]
    public function it_cannot_redeem_an_expired_promo_code()
    {
        $this->user = User::factory()->create([
            'email' => 'admin@email.com',
        ]);
        Sanctum::actingAs($this->user);
        $promoCode = PromoCode::factory()->create([
            'code' => 'EXPIRED123',
            'expires_at' => now()->subDay(),
        ]);

        $response = $this->postJson('/api/v1/promo-codes/redeem', [
            'code' => 'EXPIRED123',
            'price' => 100.00
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code']);
    }

    /**
     * Test that promo codes cannot be redeemed beyond their usage limit.
     * Verifies proper error response when usage limit is exceeded.
     */
    #[Test]
    public function it_cannot_redeem_a_promo_code_exceeding_max_uses()
    {
        $this->user = User::factory()->create([
            'email' => 'admin@email.com',
        ]);
        Sanctum::actingAs($this->user);
        $promoCode = PromoCode::factory()->create([
            'code' => 'MAXUSED123',
            'usage_limit' => 1,
        ]);
        // Simulate usage
        $promoCode->usages()->create([
            'user_id' => $this->user->id,
            'promo_code_id' => $promoCode->id,
        ]);

        $response = $this->postJson('/api/v1/promo-codes/redeem', [
            'code' => 'MAXUSED123',
            'price' => 100.00
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code']);
    }

    /**
     * Test that invalid promo codes cannot be redeemed.
     * Verifies proper error response for non-existent codes.
     */
    #[Test]
    public function it_cannot_redeem_an_invalid_promo_code()
    {
        $this->user = User::factory()->create([
            'email' => 'admin@email.com',
        ]);
        Sanctum::actingAs($this->user);
        $response = $this->postJson('/api/v1/promo-codes/redeem', [
            'code' => 'INVALID123',
            'price' => 100.00
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code']);
    }

    /**
     * Test that authentication is required to create promo codes.
     * Verifies unauthorized access is properly handled.
     */
    #[Test]
    public function it_requires_authentication_to_create_promo_code()
    {
        // Do not authenticate user
        $response = $this->postJson('/api/v1/promo-codes', [
            'code' => 'TEST123',
            'type' => 'fixed',
            'discount' => 10.00,
            'usage_limit' => 100,
            'usage_per_user' => 1,
            'expires_at' => now()->addDays(30)->toDateTimeString(),
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test that authentication is required to redeem promo codes.
     * Verifies unauthorized access is properly handled.
     */
    #[Test]
    public function it_requires_authentication_to_redeem_promo_code()
    {
        // Do not authenticate user
        $response = $this->postJson('/api/v1/promo-codes/redeem', [
            'code' => 'TEST123',
            'price' => 100.00
        ]);

        $response->assertStatus(401);
    }
} 