<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FlushDB extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Database flush';

    /**
     *
     * @var array
     */
    protected $tableNames = array();

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->tableNames = array('areas', 'available_facilities', 'bank_details','billing_info',
            'booked_packages', 'booked_timings', 'cities', 'countries', 'customers', 'day_master', 'duration_master',
            'facility_images', 'migrations', 'multiple_sessions', 'opening_hours', 'orders', 'package_child', 'package_types',
            'password_resets', 'pre_guest_users', 'roles', 'root_categories', 'session_bookings', 'session_package', 'states',
            'status', 'sub_categories', 'users', 'user_body_stats', 'vendors', 'vendor_images');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($this->tableNames as $tableName) {
            \DB::statement('TRUNCATE TABLE ' . $tableName);
        }

        \DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->info('Database has been flushed');
    }

}
