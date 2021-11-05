<?php

use Doctrine\Common\Collections\Collection;
use HMS\Entities\Role;
use HMS\Entities\Snackspace\Transaction;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\Snackspace\ProductRepository;
use HMS\Repositories\Snackspace\TransactionRepository;
use Illuminate\Database\Seeder;

class TransactionTableSeeder extends Seeder
{
    /**
     * @var TransactionRepository
     */
    protected $transactionRepository;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var Product[]
     */
    protected $products;

    /**
     * Create a new TableSeeder instance.
     *
     * @param TransactionRepository $transactionRepository
     * @param RoleRepository $roleRepository
     * @param ProductRepository $productRepository
     */
    public function __construct(
        TransactionRepository $transactionRepository,
        RoleRepository $roleRepository,
        ProductRepository $productRepository
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->roleRepository = $roleRepository;
        $this->products = $productRepository->findAll();
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currentMembers = $this->roleRepository->findOneByName(Role::MEMBER_CURRENT)->getUsers();
        $youngMembers = $this->roleRepository->findOneByName(Role::MEMBER_YOUNG)->getUsers();
        $exMembers = $this->roleRepository->findOneByName(Role::MEMBER_EX)->getUsers();

        $this->generateTransactionsForUsers($currentMembers);
        $this->generateTransactionsForUsers($youngMembers);
        $this->generateTransactionsForUsers($exMembers);
    }

    /**
     * @param  Collection $users
     */
    private function generateTransactionsForUsers(Collection $users)
    {
        foreach ($users as $user) {
            if (rand(1, 2) == 2) {
                $t = entity(Transaction::class, 'payment')->make(['user' => $user, 'products' => $this->products]);
                $this->transactionRepository->saveAndUpdateBalance($t);

                $vends = rand(1, 3);
                for ($i = 0; $i < $vends; $i++) {
                    $t = entity(Transaction::class, 'vend')->make(['user' => $user, 'products' => $this->products]);
                    $this->transactionRepository->saveAndUpdateBalance($t);
                }
            }
        }
    }
}
