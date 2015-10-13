<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class duration_master extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('duration_master')->insert([
            [
                'time'=> '15 Minutes',
                'actual_time' => 15,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'time'=> '30 Minutes',
                'actual_time' => 30,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'time'=> '45 Minutes',
                'actual_time' => 45,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'time'=> '60 Minutes',
                'actual_time' => 60,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'time'=> '75 Minutes',
                'actual_time' => 75,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'time'=> '90 Minutes',
                'actual_time' => 90,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'time'=> '105 Minutes',
                'actual_time' => 105,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'time'=> '120 Minutes',
                'actual_time' => 120,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'time'=> '135 Minutes',
                'actual_time' => 135,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'time'=> '150 Minutes',
                'actual_time' => 150,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'time'=> '165 Minutes',
                'actual_time' => 165,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'time'=> '180 Minutes',
                'actual_time' => 180,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
