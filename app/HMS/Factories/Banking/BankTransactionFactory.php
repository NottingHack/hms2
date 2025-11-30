<?php

namespace HMS\Factories\Banking;

use Carbon\Carbon;
use HMS\Entities\Banking\Account;
use HMS\Entities\Banking\Bank;
use HMS\Entities\Banking\BankTransaction;
use HMS\Entities\Snackspace\TransactionType;
use HMS\Factories\Snackspace\TransactionFactory as SnackspaceTransactionFactory;
use HMS\Helpers\Features;
use HMS\Repositories\Banking\AccountRepository;
use HMS\Repositories\Banking\BankTransactionRepository;
use HMS\Repositories\Snackspace\TransactionRepository as SnackspaceTransactionRepository;
use HMS\Repositories\UserRepository;

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
     * @var SnackspaceTransactionFactory
     */
    protected $snackspaceTransactionFactory;

    /**
     * @var SnackspaceTransactionRepository
     */
    protected $snackspaceTransactionRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var Features
     */
    protected $features;

    /**
     * @param BankTransactionRepository $bankTransactionRepository
     * @param AccountRepository $accountRepository
     * @param SnackspaceTransactionFactory $snackspaceTransactionFactory
     * @param SnackspaceTransactionRepository $snackspaceTransactionRepository
     * @param UserRepository $userRepository
     * @param Features $features
     */
    public function __construct(
        BankTransactionRepository $bankTransactionRepository,
        AccountRepository $accountRepository,
        SnackspaceTransactionFactory $snackspaceTransactionFactory,
        SnackspaceTransactionRepository $snackspaceTransactionRepository,
        UserRepository $userRepository,
        Features $features
    ) {
        $this->bankTransactionRepository = $bankTransactionRepository;
        $this->accountRepository = $accountRepository;
        $this->snackspaceTransactionFactory = $snackspaceTransactionFactory;
        $this->snackspaceTransactionRepository = $snackspaceTransactionRepository;
        $this->userRepository = $userRepository;
        $this->features = $features;
    }

    /**
     * Function to instantiate a new BankTransaction from given params.
     *
     * @param Bank $bank
     * @param Carbon $transactionDate
     * @param string $description
     * @param int $amount
     * @param null|Account $account
     *
     * @return BankTransaction
     */
    public function matchOrCreate(
        Bank $bank,
        Carbon $transactionDate,
        string $description,
        int $amount,
        ?Account $account = null
    ) {
        $bankTransaction = $this->bankTransactionRepository->findOneByBankAndDateAndDescriptionAndAmount(
            $bank,
            $transactionDate,
            $description,
            $amount
        );

        if (! is_null($bankTransaction)) {
            return $bankTransaction;
        }

        $_bankTransaction = new BankTransaction();
        $_bankTransaction->setBank($bank);
        $_bankTransaction->setTransactionDate($transactionDate);
        $_bankTransaction->setDescription($description);
        $_bankTransaction->setAmount($amount);

        $prefix = config('hms.account_prefix');
        $pattern = '/' . $prefix . '\S{' . strval(Account::MAX_REFERENCE_LENGHT - strlen($prefix)) . '}/';

        if ($account) {
            $_bankTransaction->setAccount($account);
        } elseif (preg_match($pattern, $description, $matches) == 1) {
            $account = $this->accountRepository->findOneByPaymentRef($matches[0]);
            $_bankTransaction->setAccount($account);
        } elseif ($this->features->isEnabled('match_legacy_ref')
            && preg_match(config('hms.account_legacy_regex'), $description, $matches) == 1) {
            $account = $this->accountRepository->findOneByLegacyRef($matches[0]);
            $_bankTransaction->setAccount($account);
        }

        if (preg_match('/SNACK(?>SPACE)?-?(?>CU|EX)(\d+)([a-zA-Z]{1,2})/', $description, $matches) == 1) {
            // do we have a user for this id?
            $user = $this->userRepository->findOneById((int) $matches[1]);

            if ($user) {
                // double check initials
                $initials = $user->getFirstname()[0] . $user->getLastname()[0];
                $initials = preg_replace('/[^-a-zA-Z0-9]/', '', $initials);

                if (strtoupper($initials) == strtoupper($matches[2])) {
                    // OK matched to a user id and there initials
                    // lets make a new Snackspace Transaction and link it
                    $stringAmount = money($amount, 'GBP');
                    $description = 'Bank Transfer : ' . $stringAmount;

                    $snackspaceTransaction = $this->snackspaceTransactionFactory
                        ->create(
                            $user,
                            $amount,
                            TransactionType::BANK_PAYMENT,
                            'Bank Transfer : ' . $stringAmount
                        );

                    $snackspaceTransaction = $this->snackspaceTransactionRepository
                        ->saveAndUpdateBalance($snackspaceTransaction);

                    $_bankTransaction->setTransaction($snackspaceTransaction);
                }
            }
        }

        $this->bankTransactionRepository->save($_bankTransaction);

        return $_bankTransaction;
    }
}
