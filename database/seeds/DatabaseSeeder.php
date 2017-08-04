<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserTableSeeder::class);
        $this->call(ProfileTableSeeder::class);
        $this->call(AccountTableSeeder::class);
        $this->call(PinTableSeeder::class);
        $this->call(RfidTagTableSeeder::class);
        $this->call(AccessLogTableSeeder::class);
        $this->call(BankTransactionTableSeeder::class);
    }
}
