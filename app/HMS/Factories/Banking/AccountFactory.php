<?php

namespace HMS\Factories\Banking;

use HMS\Entities\Banking\Account;
use HMS\Repositories\Banking\AccountRepository;

class AccountFactory
{
    /**
     * @var AccountRepository
     */
    protected $accountRepository;

    /**
     * @param AccountRepository $accountRepository
     */
    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    /**
     * Create a new Account with a unique payment refrence.
     *
     * @return Account
     */
    public function createNewAccount()
    {
        return new Account($this->generateUniquePaymentRef());
    }

    /**
     * Generate a payment reference.
     *
     * @return string A unique (at the time of function-call) payment reference.
     */
    protected static function generatePaymentRef()
    {
        // Payment ref is a randomly generates string of 'safechars'
        // Stolen from London Hackspace code
        $safeChars = '2346789BCDFGHJKMPQRTVWXY';
        // We prefix the ref with a string that lets people know it's us
        $prefix = config('hms.account_prefix');
        // Payment references can be up to 18 chars according to:
        // http://www.bacs.co.uk/Bacs/Businesses/BacsDirectCredit/Receiving/Pages/PaymentReferenceInformation.aspx
        $maxRefLength = Account::MAX_REFERENCE_LENGHT;

        $paymentRef = $prefix;

        for ($i = strlen($prefix); $i < $maxRefLength; $i++) {
            $paymentRef .= $safeChars[rand(0, strlen($safeChars) - 1)];
        }

        return $paymentRef;
    }

    /**
     * Generate a unique payment reference.
     *
     * @return string A unique (at the time of function-call) payment reference.
     *
     * @link   http://www.bacs.co.uk/Bacs/Businesses/BacsDirectCredit/Receiving/Pages/PaymentReferenceInformation.aspx
     */
    protected function generateUniquePaymentRef()
    {
        do {
            $paymentRef = $this->generatePaymentRef();
        } while ($this->accountRepository->findOneByPaymentRef($paymentRef) !== null);

        return $paymentRef;
    }
}
