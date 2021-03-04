<?php

namespace App\Http\Controllers\Banking\Bank;

use Exception;
use HMS\Helpers\Features;
use Illuminate\Http\Request;
use HMS\Entities\Banking\Bank;
use Illuminate\Support\Carbon;
use HMS\Entities\Banking\BankType;
use OfxParser\Parser as OfxParser;
use App\Http\Controllers\Controller;
use App\Jobs\Banking\SaveNewOFXTransactionsJob;
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
     * @var Features
     */
    protected $features;

    /**
     * @var OfxParser
     */
    protected $ofxParser;

    /**
     * @param BankTransactionRepository $bankTransactionRepository
     * @param BankTransactionFactory $bankTransactionFactory
     * @param Features $features
     * @param OfxParser $ofxParser
     */
    public function __construct(
        BankTransactionRepository $bankTransactionRepository,
        BankTransactionFactory $bankTransactionFactory,
        Features $features,
        OfxParser $ofxParser
    ) {
        $this->bankTransactionRepository = $bankTransactionRepository;
        $this->bankTransactionFactory = $bankTransactionFactory;
        $this->features = $features;
        $this->ofxParser = $ofxParser;

        $this->middleware('can:bankTransactions.edit')->only(['create', 'store']);
        $this->middleware('can:bankTransactions.ofxUpload')->only(['createViaOfxUpload', 'storeOfx']);
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
            ->matchOrCreate(
                $bank,
                $transactionDate,
                $validatedData['description'],
                $validatedData['amount']
            );

        flash('Bank Transaction \'' . $bankTransaction->getDescription() . '\' created.')->success();

        return redirect()->route('banking.banks.show', $bank->getId());
    }

    /**
     * Show the form for uploading an OFX file.
     *
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function createViaOfxUpload(Bank $bank)
    {
        if (BankType::AUTOMATIC != $bank->getType()) {
            flash('Bank ' . $bank->getName() . ' is not type Automatic. OFX upload not allowed.')
                ->error();

            return redirect()->route('banking.banks.show', $bank->getId());
        } elseif ($this->features->isDisabled('ofx_bank_upload')) {
            flash('OFX upload is disabled.')
                ->error();

            return redirect()->route('banking.banks.show', $bank->getId());
        }

        $latestBankTransaction = $this->bankTransactionRepository->findLatestTransactionByBank($bank);

        return view('banking.banks.transactions.ofx-upload')
            ->with('bank', $bank)
            ->with('latestBankTransaction', $latestBankTransaction);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function storeOfx(Request $request, Bank $bank)
    {
        if (BankType::AUTOMATIC != $bank->getType()) {
            flash('Bank ' . $bank->getName() . ' is not type Automatic. OFX upload not allowed.')
                ->error();

            return redirect()->route('banking.banks.show', $bank->getId());
        } elseif ($this->features->isDisabled('ofx_bank_upload')) {
            flash('OFX upload is disabled.')
                ->error();

            return redirect()->route('banking.banks.show', $bank->getId());
        }

        $validatedData = $request->validate([
            'OfxFile' => [
                'required',
                'file',
                function ($attribute, $value, $fail) {
                    try {
                        $this->ofxParser->loadFromString($value->get());
                    } catch (Exception $e) {
                        $fail('Unable to parse OFX file');
                    }
                },
            ],
        ]);

        SaveNewOFXTransactionsJob::dispatch($bank, utf8_encode($validatedData['OfxFile']->get()));

        flash('Ofx File submitted for processing.')->success();

        return redirect()->route('banking.banks.show', $bank->getId());
    }
}
