<?php
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DayTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('day_master')->insert([
            [
                'name' => 'MONDAY',
                'slug' => 'monday',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'TUESDAY',
                'slug' => 'tuesday',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'WEDNESDAY',
                'slug' => 'wednesday',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'THURSDAY',
                'slug' => 'thursday',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'FRIDAY',
                'slug' => 'friday',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'SATURDAY',
                'slug' => 'saturday',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'SUNDAY',
                'slug' => 'sunday',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);
    }
}
