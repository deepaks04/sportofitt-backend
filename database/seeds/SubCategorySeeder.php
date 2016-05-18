<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SubCategorySeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('TRUNCATE TABLE sub_categories');
        $this->command->info('Table truncated and inserting records...');
        DB::table('sub_categories')->insert([
            [
                'name' => 'Badminton',
                'slug' => 'badminton',
                'root_category_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Boxing',
                'slug' => 'boxing',
                'root_category_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Cricket',
                'slug' => 'cricket',
                'root_category_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Football',
                'slug' => 'football',
                'root_category_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Golf',
                'slug' => 'golf',
                'root_category_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'squash',
                'slug' => 'squash',
                'root_category_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Table Tennis',
                'slug' => 'table-tennis',
                'root_category_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'tennis',
                'slug' => 'tennis',
                'root_category_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Crossfit',
                'slug' => 'crossfit',
                'root_category_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Dancercise',
                'slug' => 'dancercise',
                'root_category_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Gym',
                'slug' => 'gym',
                'root_category_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Judo',
                'slug' => 'judo',
                'root_category_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Karate',
                'slug' => 'kjarate',
                'root_category_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Kickboxing',
                'slug' => 'kickboxing',
                'root_category_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'MMA',
                'slug' => 'mma',
                'root_category_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Pilates',
                'slug' => 'pilates',
                'root_category_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Spinning',
                'slug' => 'spinning',
                'root_category_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Swimming',
                'slug' => 'swimming',
                'root_category_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Taekwondo',
                'slug' => 'taekwondo',
                'root_category_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Yoga',
                'slug' => 'yoga',
                'root_category_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Zumba',
                'slug' => 'Zumba',
                'root_category_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Accupressure',
                'slug' => 'accupressure',
                'root_category_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Aromatherapy',
                'slug' => 'aromatherapy',
                'root_category_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Ayurveda',
                'slug' => 'ayurveda',
                'root_category_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Body Treatment',
                'slug' => 'body-treatment',
                'root_category_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Day Spa',
                'slug' => 'day-spa',
                'root_category_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Deep Tissue Massage',
                'slug' => 'deep-tissue-massage',
                'root_category_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Face Treatment',
                'slug' => 'face-treatment',
                'root_category_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Foot Massage',
                'slug' => 'foot-massage',
                'root_category_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Hair Treatment',
                'slug' => 'hair-treatment',
                'root_category_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Reflexology',
                'slug' => 'reflexology',
                'root_category_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Shiatsu Massage',
                'slug' => 'shiatsu-massage',
                'root_category_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Stone Massage',
                'slug' => 'stone-massage',
                'root_category_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Swedish Massage',
                'slug' => 'swedish-massage',
                'root_category_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Thai Massage',
                'slug' => 'thai-massage',
                'root_category_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Horse Riding',
                'slug' => 'horse-riding',
                'root_category_id' => 4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Paintball',
                'slug' => 'paintball',
                'root_category_id' => 4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Paragliding',
                'slug' => 'paragliding',
                'root_category_id' => 4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Rappelling',
                'slug' => 'rappelling',
                'root_category_id' => 4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Rock Climbing',
                'slug' => 'rock-climbing',
                'root_category_id' => 4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Scuba Diving',
                'slug' => 'scuba-diving',
                'root_category_id' => 4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Trekking',
                'slug' => 'trekking',
                'root_category_id' => 4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Water Rafting',
                'slug' => 'water-rafting',
                'root_category_id' => 4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Athletics',
                'slug' => 'athletics',
                'root_category_id' => 1,    
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Fencing',
                'slug' => 'fencing',
                'root_category_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Archery',
                'slug' => 'archery',
                'root_category_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);
        $this->command->info('Inserting of records completed...');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

}