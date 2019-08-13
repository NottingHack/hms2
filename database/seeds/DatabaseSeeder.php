<?php

use Illuminate\Database\Seeder;
use Doctrine\ORM\EntityManagerInterface;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(EntityManagerInterface $entityManager)
    {
        $this->call(UserTableSeeder::class);
        $entityManager->clear();
        $this->call(ProfileTableSeeder::class);
        $entityManager->clear();
        $this->call(AccountTableSeeder::class);
        $entityManager->clear();
        $this->call(PinTableSeeder::class);
        $entityManager->clear();
        $this->call(RfidTagTableSeeder::class);
        $entityManager->clear();
        $this->call(AccessLogTableSeeder::class);
        $entityManager->clear();
        $this->call(BankTransactionTableSeeder::class);
        $entityManager->clear();
        $this->call(RoleUpdateTableSeeder::class);
        $entityManager->clear();
        $this->call(ProductTableSeeder::class);
        $entityManager->clear();
        $this->call(TransactionTableSeeder::class);
        $entityManager->clear();
        $this->call(ToolTableSeeder::class);
        $entityManager->clear();
    }
}
