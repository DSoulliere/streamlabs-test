<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Creates a model for the monthly plan
        Plan::firstOrCreate([
            'plan_id' => config('services.braintree.monthly_plan_id'),
            'name' => 'Monthly',
            'price' => config('services.braintree.monthly_plan_price'),
        ]);


        //Creates a model for the yearly plan
        Plan::firstOrCreate([
            'plan_id' => config('services.braintree.yearly_plan_id'),
            'name' => 'Yearly',
            'price' => config('services.braintree.yearly_plan_price'),
        ]);


    }
}
