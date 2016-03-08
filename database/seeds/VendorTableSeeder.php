<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

use Faker\Factory as Faker;

class VendorTableSeeder extends Seeder
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
        DB::table('users')->truncate();
        DB::table('vendors')->truncate();
        $this->command->info('Inserting new records in the table');
        $faker = Faker::create();
    	foreach (range(1,500) as $index) {
            $user = New App\User();
            $user->fname = $faker->name;
            $user->lname = $faker->lastName;
            $user->email = $faker->email;
            $user->username = $faker->userName;
            $user->is_active = 1;
            $user->status_id = 1;
            $user->role_id = 2;
            $user->password = bcrypt('123456');
            $user->save();
            
            $vendor = new App\Vendor();
            $vendor->business_name = $faker->company;
            $vendor->longitude = $faker->longitude;
            $vendor->latitude = $faker->latitude;
            $vendor->description = $faker->paragraph;
            $vendor->contact = $faker->phoneNumber;
            $vendor->address = $faker->address;
            $vendor->is_processed = 1;
            $vendor->user_id = $user->id;
            $area = App\Area::orderBy(DB::raw('RAND()'))->first();
            $vendor->postcode = $faker->postcode;
            $vendor->area_id = $area->id;
            $vendor->save();
            
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->command->info('Record inserting compeleted');
    }

}