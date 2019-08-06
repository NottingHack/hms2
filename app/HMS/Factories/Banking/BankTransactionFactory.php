
<?php

namespace HMS\Factories\Banking;

use Carbon\Carbon;
use HMS\Entities\Banking\Bank;
use HMS\Repositories\UserRepository;
use HMS\Entities\Banking\BankTransaction;
use HMS\Entities\Snackspace\TransactionType;
use HMS\Repositories\Banking\AccountRepository;
use HMS\Factories\Snackspace\TransactionFactory;
use HMS\Repositories\Snackspace\TransactionRepository;
use HMS\Repositories\Banking\BankTransactionRepository;

class BankTransactionFactory
{
    /**
     * @var BankTransactionRepository
     */
    protected $bankTransactionRepository;

    /**
     * @var AccountRepository
     */
    protected $accountRepository;

    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var TransactionRepository
     */
    protected $transactionRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @param BankTransactionRepository $bankTransactionRepository
     * @param AccountRepository $accountRepository
     * @param TransactionFactory $transactionFactory
     * @param TransactionRepository $transactionRepository
     * @param UserRepository $userRepository
     */
    public function __construct(
        BankTransactionRepository $bankTransactionRepository,
        AccountRepository $accountRepository,
        TransactionFactory $transactionFactory,
        TransactionRepository $transactionRepository,
        UserRepository $userRepository
    ) {
        $this->bankTransactionRepository = $bankTransactionRepository;
        $this->accountRepository = $accountRepository;
        $this->transactionFactory = $transactionFactory;
        $this->transactionRepository = $transactionRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Function to instantiate a new BankTransaction from given params.
     *
     * @param Bank $bank
     * @param Carbon $transactionDate
     * @param string $description
     * @param int $amount
     */
    public function create(Bank $bank, Carbon $transactionDate, string $description, int $amount)
    {
        $_bankTransaction = new BankTransaction();
        $_bankTransaction->setBank($bank);
        $_bankTransaction->setTransactionDate($transactionDate);
        $_bankTransaction->setDescription($description);
        $_bankTransaction->setAmount($amount);

        if (preg_match('/HSNTSB\S{10}/', $description, $matches) == 1) {
            $account = $this->accountRepository->findOneByPaymentRef($matches[0]);
            $_bankTransaction->setAccount($account);
        }

        if (preg_match('/SNACK(?>SPACE)?-?(?>CU|EX)(\d+)([a-zA-Z]{1,2})/', $description, $matches) == 1) {
            // do we have a user for this id?
            $user = $this->userRepository->findOneById($matches[1]);

            if ($user) {
                // double check initials
                $initials = $user->getFirstname()[0] . $user->getLastname()[0];
                $initials = preg_replace('/[^-a-zA-Z0-9]/', '', $initials);

                if ($initials == $matches[2]) {
                    // OK matched to a user id and there initials
                    // lets make a new Snackspace Transaction and link it
                    $stringAmount = money_format('%n', $amount / 100);
                    $description = 'Bank Transfer : ' . $stringAmount;

                    $snackspaceTransaction = $this->transactionFactory->create(
                        $user,
                        $amount,
                        TransactionType::BANK_PAYMENT,
                        'Bank Transfer : ' . $stringAmount
                    );

                    $snackspaceTransaction = $this->transactionRepository->saveAndUpdateBalance($snackspaceTransaction);

                    $_bankTransaction->setTransaction($snackspaceTransaction);
                }
            }
        }

        return $_bankTransaction;
    }
}
