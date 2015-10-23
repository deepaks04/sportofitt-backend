<?php
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PackageTypeSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('package_types')->insert([
            [
                'name' => 'PACKAGE',
                'slug' => 'package',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'SESSION',
                'slug' => 'session',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);
    }
}
