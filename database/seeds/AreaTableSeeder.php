<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AreaTableSeeder extends Seeder
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
        DB::table('areas')->truncate();
        $this->command->info('Inserting new records in the table');
        DB::table('areas')->insert([
            [
                'name' => 'Aundh',
                'city_id' => 1,
                'latitude' => 18.5576982,
                'longitude' => 73.7898289,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Baner',
                'city_id' => 1,
                'latitude' => 18.5599707,
                'longitude' => 73.7663066,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Bavdhan',
                'city_id' => 1, 'latitude' => 18.5046396,'longitude' => 73.7474528, 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Camp',
                'city_id' => 1, 'latitude' => 18.5088621, 'longitude' => 73.8155604,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Chinchwad',
                'city_id' => 1, 'latitude' => 18.6426343, 'longitude' => 73.7589452,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Hadapsar',
                'city_id' => 1, 'latitude' => 18.4972508, 'longitude' => 73.9043991,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Deccan',
                'city_id' => 1, 'latitude' => 18.5164905, 'longitude' => 73.8283536,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Kondhwa',
                'city_id' => 1, 'latitude' => 18.4677572, 'longitude' => 73.8762474,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Kharadi',
                'city_id' => 1, 'latitude' => 18.5527177, 
                'longitude' => 73.9278969,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Katraj',
                'city_id' => 1, 
                'latitude' => 18.4442473, 
                'longitude' => 73.8451703,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'KarveNagar',
                'city_id' => 1,
                'latitude' => 18.4891663, 
                'longitude' => 73.8129169,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Kalyani Nagar',
                'city_id' => 1,
                'latitude' => 18.5476792, 
                'longitude' => 73.8927272,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Koregaon Park',
                'city_id' => 1,
                'latitude' => 18.5373008, 
                'longitude' => 73.8858393,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Kothrud',
                'city_id' => 1, 
                'latitude' => 18.5073551, 
                'longitude' => 73.7871018,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Pashan',
                'city_id' => 1,
                'latitude' => 18.5331543,
                 'longitude' => 73.7526183,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Pimple Saudagar',
                'city_id' => 1,
                'latitude' => 18.5936142,
                'longitude' => 73.7851593, 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Pimpri',
                'city_id' => 1,
                'latitude' => 18.6323494,
                'longitude' => 73.7757029,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Senapati Bapat Road',
                'city_id' => 1, 
                'latitude' => 18.530475,
                'longitude' => 73.8277488,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Sinhagad Road',
                'city_id' => 1,
                'latitude' => 18.4536216, 
                'longitude' => 73.7942354,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Viman Nagar',
                'city_id' => 1, 
                'latitude' => 18.5670558, 
                'longitude' => 73.9084194,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Wadgaon Sheri',
                'city_id' => 1, 
                'latitude' => 18.5500037, 
                'longitude' => 73.9028833,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Wagholi',
                'city_id' => 1,
                'latitude' => 18.5741428, 
                'longitude' => 73.962115,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Wakad',
                'city_id' => 1, 'latitude' => 18.5985832, 
                'longitude' => 73.7436413,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Wanowrie',
                'city_id' => 1, 'latitude' => 18.4969287,
                'longitude' => 73.8857188,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Parvati',
                'city_id' => 1, 'latitude' => 18.4591045,
                'longitude' => 73.8351287,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $this->command->info('Record inserting compeleted');
    }

}