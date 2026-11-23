<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Profile;
use App\Models\Category;
use App\Models\CategoryLimit;
use App\Models\PointsLedger;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RedemptionTest extends TestCase
{
    use RefreshDatabase;

    private function createTestUser(string $type = 'normal'): User
    {
        $profile = Profile::create([
            'user_type' => $type
        ]);

        $user = User::factory()->create([
            'profile_id' => $profile->id
        ]);

        PointsLedger::create([
            'user_id' => $user->id,
            'type' => 'earn',
            'points' => 1000
        ]);

        return $user;
    }

    private function createTestProduct(): Product
    {
        $category = Category::create([
            'name' => 'Electronics'
        ]);

        CategoryLimit::create([
            'category_id' => $category->id,
            'monthly_limit' => 2
        ]);

        return Product::create([
            'category_id' => $category->id,
            'name' => 'Speaker',
            'points_required' => 500
        ]);
    }

    public function test_successful_redemption(): void
    {
        $user = $this->createTestUser();
        $product = $this->createTestProduct();

        $this->postJson('/api/redemptions', [
            'user_id' => $user->id,
            'product_id' => $product->id
        ])->assertCreated();

        $this->assertDatabaseHas('redemptions', [
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);
    }

    public function test_insufficient_points(): void
    {
        $user = $this->createTestUser();
        $product = $this->createTestProduct();

        PointsLedger::create([
            'user_id' => $user->id,
            'type' => 'spend',
            'points' => 800
        ]);

        $this->postJson('/api/redemptions', [
            'user_id' => $user->id,
            'product_id' => $product->id
        ])->assertStatus(422);
    }

    public function test_restricted_user_cannot_redeem(): void
    {
        $user = $this->createTestUser('restricted');
        $product = $this->createTestProduct();

        $this->postJson('/api/redemptions', [
            'user_id' => $user->id,
            'product_id' => $product->id
        ])->assertStatus(422);
    }

    public function test_category_monthly_limit(): void
    {
        $user = $this->createTestUser();
        $product = $this->createTestProduct();

        for ($i = 0; $i < 2; $i++) {
            $this->postJson('/api/redemptions', [
                'user_id' => $user->id,
                'product_id' => $product->id
            ])->assertCreated();
        }

        $this->postJson('/api/redemptions', [
            'user_id' => $user->id,
            'product_id' => $product->id
        ])->assertStatus(422);
    }
}
