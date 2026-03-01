<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Farm;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        $total = $this->faker->randomFloat(2, 100, 2000);
        $paid = $this->faker->randomFloat(2, 0, $total);

        return [
            'invoice_number' => self::generateInvoiceNumber(),
            'customer_id' => Customer::factory(),
            'farm_id' => Farm::factory(),
            'user_id' => User::factory(),
            'invoice_date' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'total_amount' => $total,
            'paid_amount' => $paid,
            'status' => $paid >= $total ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid'),
        ];
    }

    public static function generateInvoiceNumber(): string
    {
        $latestInvoice = Sale::whereNotNull('invoice_number')
            ->orderByDesc('invoice_number')
            ->first();

        $nextNumber = 1;
        if ($latestInvoice) {
            $lastNumber = (int) substr($latestInvoice->invoice_number, 4); // 'INV-' is 4 chars
            $nextNumber = $lastNumber + 1;
        }

        return 'INV-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
