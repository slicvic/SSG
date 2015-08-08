<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class CompaniesTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('companies')->delete();

        DB::table('companies')->insert([
            [
                'id' => 1,
                'name' => 'Lantigua Lab',
                'shipping_address_id' => 1,
                'billing_address_id' => 2
            ]
        ]);
    }
}
