<?php

namespace Database\Seeders;

use App\Models\Food;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $foods = [
            ["name" => "Ayam Geprek Bakar", "price" => 25000, "image" => "menu1.jpeg"],
            ["name" => "Ayam Geprek Kremes", "price" => 25000, "image" => "menu2.png"],
            ["name" => "Ayam Geprek Super", "price" => 25000, "image" => "menu3.jpg"],
            ["name" => "Ayam Geprek Krispi", "price" => 25000, "image" => "menu4.png"],
        ];

        User::factory()->create([
            'name' => 'John',
            'email' => 'john@example.com',
        ]);

        foreach ($foods as $food) {
            Food::factory()->create([
                'name' => $food['name'],
                'price' => $food['price'],
                'image' => $food['image'],
            ]);
        }

    }
}
