<?php

namespace App\Http\Controllers\Banking\Bank;

use Illuminate\Http\Request;
use HMS\Entities\Banking\Bank;
use Illuminate\Support\Carbon;
use HMS\Entities\Banking\BankType;
use App\Http\Controllers\Controller;
use HMS\Factories\Banking\BankTransactionFactory;
use HMS\Repositories\Banking\BankTransactionRepository;

class BankBankTransactionController extends Controller
{
    /**
     * @var BankTransactionRepository
     */
    protected $bankTransactionRepository;

    /**
     * @var BankTransactionFactory
     */
    protected $bankTransactionFactory;

    /**
     * @param BankTransactionRepository $bankTransactionRepository
     * @param BankTransactionFactory $bankTransactionFactory
     */
    public function __construct(
        BankTransactionRepository $bankTransactionRepository,
        BankTransactionFactory $bankTransactionFactory
    ) {
        $this->bankTransactionRepository = $bankTransactionRepository;
        $this->bankTransactionFactory = $bankTransactionFactory;

        $this->middleware('can:bankTransactions.edit');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function create(Bank $bank)
    {
        if (BankType::AUTOMATIC == $bank->getType()) {
            flash('Bank ' . $bank->getName() . ' is type Automatic. Transactions can not be enter manually.')
                ->error();

            return redirect()->route('banking.banks.show', $bank->getId());
        }

        return view('banking.banks.transactions.create')
            ->with('bank', $bank);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Bank $bank)
    {
        if (BankType::AUTOMATIC == $bank->getType()) {
            flash('Bank ' . $bank->getName() . ' is type Automatic. Transactions can not be enter manually.')
                ->error();

            return redirect()->route('banking.banks.show', $bank->getId());
        }

        $validatedData = $request->validate([
            'transactionDate' => 'required|date',
            'description' => 'required|string|max:512',
            'amount' => 'required|integer',
        ]);

        $transactionDate = new Carbon($validatedData['transactionDate']);

        $bankTransaction = $this->bankTransactionFactory
            ->create(
                $bank,
                $transactionDate,
                $validatedData['description'],
                $validatedData['amount']
            );

        // now see if we already have this transaction on record? before saving it
        $bankTransaction = $this->bankTransactionRepository->findOrSave($bankTransaction);

        flash('Bank Transaction \'' . $bankTransaction->getDescription() . '\' created.')->success();

        return redirect()->route('banking.banks.show', $bank->getId());
    }
}
