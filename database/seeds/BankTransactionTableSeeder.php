<?php

use Carbon\Carbon;
use Faker\Generator;
use HMS\Entities\Banking\Account;
use HMS\Entities\Role;
use HMS\Entities\User;
use HMS\Factories\Banking\BankTransactionFactory;
use HMS\Repositories\Banking\AccountRepository;
use HMS\Repositories\Banking\BankRepository;
use Illuminate\Database\Seeder;

class BankTransactionTableSeeder extends Seeder
{
    /**
     * @var AccountRepository
     */
    protected $accountRepository;

    /**
     * @var Bank
     */
    protected $bank;

    /**
     * @var BankTransactionFactory
     */
    protected $bankTransactionFactory;

    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var array
     */
    protected $csvTransactions;

    /**
     * Create a new TableSeeder instance.
     *
     * @param AccountRepository         $accountRepository
     * @param BankRepository            $bankRepository
     * @param BankTransactionFactory    $bankTransactionFactory
     * @param Generator                 $faker
     */
    public function __construct(AccountRepository $accountRepository,
        BankRepository $bankRepository,
        BankTransactionFactory $bankTransactionFactory,
        Generator $faker)
    {
        $this->accountRepository = $accountRepository;
        $this->bank = $bankRepository->find(2);
        $this->bankTransactionFactory = $bankTransactionFactory;
        $this->faker = $faker;
        $this->csvTransactions = [];
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $accounts = $this->accountRepository->findAll();

        foreach ($accounts as $account) {
            $user = $account->getUsers()[0];

            if ($user->hasRoleByName(Role::MEMBER_PAYMENT)) {
                // Gen a record that is less than 2 weeks old so if we run an audit we get some member movememnt
                $ran = rand(1, 3);
                // random number 1 gen bank, 4 gen csv, else none
                if ($ran == 1) {
                    $this->__generateBankTransaction($user, '-3 Weeks');
                } elseif ($ran == 3) {
                    $this->__generateBankCSV($user, '-1 weeks');
                }
            } elseif ($user->hasRoleByName([Role::MEMBER_YOUNG, Role::MEMBER_CURRENT])) {
                // Gen 1-3 records that are over 1.5 months old (history)
                for ($i = 1; $i <= rand(1, 3); $i++) {
                    $this->__generateBankTransaction($user, '-6 months', '-2 months 1 day');
                }

                // now mix of records mostly stay
                $ran = rand(1, 6);
                if ($user->getFirstname() == 'Admin') {
                    // make sure we cant make the make our admin an ex member
                    $this->__generateBankTransaction($user, '-1 week');
                } elseif ($ran == 5) {
                    // some ex just to csv less than 2 weeks (cause ex aproval)
                    $this->__generateBankCSV($user, '-1 weeks');
                } elseif ($ran == 4) {
                    // some warn (between 1.5 and 1 month)
                    $this->__generateBankTransaction($user, '-2 months', '-1 month 14 days');
                    // some also with a csv less that 1 weeks
                    if (rand(1, 3) == 2) {
                        $this->__generateBankCSV($user, '-1 week');
                    }
                } else {
                    $this->__generateBankTransaction($user, '-1 month');
                }
            } elseif ($user->hasRoleByName(Role::MEMBER_EX)) {
                // Gen 1-3 records that are over 2 months old (history)
                for ($i = 1; $i <= rand(1, 3); $i++) {
                    $this->__generateBankTransaction($user, '-6 months', '-2 month 1 day');
                }
                // Gen a record that is less than 2 weeks old so if we run an audit we get some member movememnt
                $ran = rand(1, 4);
                // random number 1 gen bank, 4 gen csv, else none
                if ($ran == 2) {
                    $this->__generateBankTransaction($user, '-3 weeks');
                } elseif ($ran == 4) {
                    $this->__generateBankCSV($user, '-1 weeks');
                }
            }
        }

        $this->__writeDataCSV($this->csvTransactions);
    }

    /**
     * Generate a new BankTransaction.
     *
     * @param User   $user
     * @param string $startDate
     * @param string $endDate
     */
    private function __generateBankTransaction(User $user, string $startDate, $endDate = 'now')
    {
        $bankTransaction = $this->bankTransactionFactory->matchOrCreate(
            $this->bank,
            Carbon::instance($this->faker->dateTimeBetween($startDate, $endDate)),
            $user->getFullname() . ' ' . $user->getAccount()->getPaymentRef() . ' ' . $user->getAccount()->getId(),
            rand(1, 7500)
        );
    }

    /**
     * Generate a new BankTransaction for CSV.
     *
     * @param User   $user
     * @param string $startDate
     * @param string $endDate
     */
    private function __generateBankCSV(User $user, string $startDate, $endDate = 'now')
    {
        $bankTransaction = [
            'transaction_date' => Carbon::instance($this->faker->dateTimeBetween($startDate, $endDate)),
            'description' => $user->getFullname() . ' ' . $user->getAccount()->getPaymentRef() . ' ' . $user->getAccount()->getId(),
            'amount' => rand(1, 7500),
        ];

        $this->csvTransactions[] = $bankTransaction;
    }

    /**
     * Write out bank csv file.
     *
     * @param  array  $bankCSVRecords
     */
    private function __writeDataCSV($bankCSVRecords)
    {
        /* need to write out in the following format
         Transaction Date,Transaction Type,Sort Code,Account Number,Transaction Description,Debit Amount,Credit Amount,Balance,
         29/04/2016,PAY,'77-22-24,13007568,SERVICE CHARGES REF : 196704345 ,18.77,,33203.36
         */

        $data = 'Transaction Date,Transaction Type,Sort Code,Account Number,Transaction Description,Debit Amount,Credit Amount,Balance,';
        $data = $data . "\n";

        $balance = rand(1000, 10000) / 100;

        foreach ($bankCSVRecords as $record) {
            $balance += $record['amount'];

            $data = $data . $record['transaction_date']->format('d/m/Y');
            $data = $data . ",FPI,'77-22-24,13007568,";
            $data = $data . $record['description'];
            $data = $data . ',,';
            $data = $data . $record['amount'];
            $data = $data . ',';
            $data = $data . $balance;
            $data = $data . "\n";
        }

        Storage::disk('local')->put('tsb_bank_test.csv', $data);
    }
}
