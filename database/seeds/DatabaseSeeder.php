<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call(UserTableSeeder::class);
        $this->call(RoleTableSeeder::class);
        $this->call(CityTableSeeder::class);
        $this->call(AreaTableSeeder::class);
        $this->call(StatusTableSeeder::class);
        $this->call(RootCategorySeeder::class);
        $this->call(SubCategorySeeder::class);
        $this->call(PackageTypeSeeder::class);
        $this->call(DayTableSeeder::class);


        Model::reguard();
    }
}
