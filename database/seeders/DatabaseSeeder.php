<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        User::truncate();
        Store::truncate();
        Product::truncate();
        Schema::enableForeignKeyConstraints();
        // $this->call(UserTableSeeder::class);
        User::factory()
            ->count(3)
            ->create()
            ->each(function($user) {
            Store::factory()
                    ->count(5)
                    ->create(['user_id' => $user->id])
                    ->each(function($store) {
                        Product::factory()
                            ->count(200)
                            ->create(['store_id' => $store->id]);
                    });
            });
    }
}
