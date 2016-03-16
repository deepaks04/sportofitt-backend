<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StateTableSeeder extends Seeder
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
        DB::table('states')->truncate();
        $this->command->info('Inserting new records in the table');
        DB::table('states')->insert([
            [
                'state_name' => 'Maharashtra',
                'slug' => 'maharashtra',
                'country_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $this->command->info('Record inserting compeleted');
    }

}