<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Role;

class CreateAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::where('slug', 'superadmin')->first();
        DB::table('users')->insert([
                'fname' => 'sagar',
                'lname' => 'acharya',
                'email'=>'admin@gmail.com',
                'username'=>'admin',
                'password'=>bcrypt('admin'),
                'is_active'=>1,
                'status_id'=>1,
                'role_id'=> $role->id,
                'remember_token'=> 'wAYH8YjEJoksLkfBQW9m1ECaxRr5HNUTX4PkehMV',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
        ]);
    }
}
