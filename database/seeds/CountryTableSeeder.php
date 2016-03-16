<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CountryTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $this->command->info('Truncating existing table');
        DB::table('countries')->truncate();
        $this->command->info('Inserting new records in the table');
        DB::table('countries')->insert([
            [
                'country_name' => 'India',
                'slug' => 'india',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $this->command->info('Record inserting compeleted');
    }

}