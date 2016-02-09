    

<?php
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class RootCategorySeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('root_categories')->truncate();
        $this->command->info('Table truncated and inserting records...');
        DB::table('root_categories')->insert([
            [
                'name' => 'SPORT',
                'slug' => 'sport',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'FITNESS',
                'slug' => 'fitness',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'THERAPY',
                'slug' => 'therapy',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'OUTDOOR',
                'slug' => 'outdoor',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);
         $this->command->info('Inserting of records completed...');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
