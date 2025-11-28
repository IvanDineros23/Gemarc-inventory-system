<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DeliverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $productId = DB::table('products')->value('id');

        if ($productId) {
            DB::table('deliveries')->insert([
                'product_id' => $productId,
                'date' => Carbon::now(),
                'qty' => 1,
                'remarks' => 'Seeded sample delivery',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
