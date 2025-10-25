<?php

namespace Database\Factories;

use App\Enums\TransactionStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_id' => Str::random(3) . Carbon::now()->timestamp,
            'user_id' => 1,
            'address' => fake()->address(),
            'food_id' => 1,
            'price' => 25000,
            'quantity' => 4,
            'total' => 100000,
            'payment_proof' => 'dummy-txn.jpg',
            'status' => TransactionStatus::PENDING_PAYMENT->value
        ];
    }
}
