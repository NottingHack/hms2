<?php

namespace App\Http\Controllers\Banking;

use Illuminate\Http\Request;
use HMS\Entities\Banking\Bank;
use Illuminate\Validation\Rule;
use HMS\Entities\Banking\BankType;
use App\Http\Controllers\Controller;
use HMS\Factories\Banking\BankFactory;
use HMS\Repositories\Banking\BankRepository;
use HMS\Repositories\Banking\BankTransactionRepository;

class BankController extends Controller
{
    /**
     * @var BankRepository
     */
    protected $bankRepository;

    /**
     * @var BankTransactionRepository
     */
    protected $bankTransactionRepository;

    /**
     * @var BankFactory
     */
    protected $bankFactory;

    /**
     * Create a new controller instance.
     *
     * @param BankRepository $bankRepository
     * @param BankTransactionRepository $bankTransactionRepository
     * @param BankFactory $bankFactory
     */
    public function __construct(
        BankRepository $bankRepository,
        BankTransactionRepository $bankTransactionRepository,
        BankFactory $bankFactory
    ) {
        $this->bankRepository = $bankRepository;
        $this->bankTransactionRepository = $bankTransactionRepository;
        $this->bankFactory = $bankFactory;

        $this->middleware('can:bank.view')->only(['index', 'show']);
        $this->middleware('can:bank.create')->only(['create', 'store']);
        $this->middleware('can:bank.edit')->only(['edit', 'update']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $banks = $this->bankRepository->paginateAll();

        return view('banking.banks.index')
            ->with('banks', $banks);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('banking.banks.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'sortCode' => 'required|string|max:8',
            'accountNumber' => 'required|string|max:8',
            'accountName' => 'required|string|max:100',
            'type' => [
                'required',
                Rule::in(array_keys(BankType::TYPE_STRINGS)),
            ],
        ]);

        $bank = $this->bankFactory->create(
            $validatedData['name'],
            $validatedData['sortCode'],
            $validatedData['accountNumber'],
            $validatedData['accountName'],
            $validatedData['type']
        );

        $this->bankRepository->save($bank);
        flash('Bank \'' . $bank->getName() . '\' created.')->success();

        return redirect()->route('banking.banks.show', $bank->getId());
    }

    /**
     * Display the specified resource.
     *
     * @param  \HMS\Entities\Banking\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function show(Bank $bank)
    {
        $bankTransactions = $this->bankTransactionRepository->paginateByBank($bank);

        return view('banking.banks.show')
            ->with('bank', $bank)
            ->with('bankTransactions', $bankTransactions);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \HMS\Entities\Banking\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function edit(Bank $bank)
    {
        return view('banking.banks.edit')
            ->with('bank', $bank);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \HMS\Entities\Banking\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bank $bank)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'sortCode' => 'required|string|max:8',
            'accountNumber' => 'required|string|max:8',
            'accountName' => 'required|string|max:100',
            'type' => [
                'required',
                Rule::in(array_keys(BankType::TYPE_STRINGS)),
            ],
        ]);

        $bank->setName($validatedData['name']);
        $bank->setSortCode($validatedData['sortCode']);
        $bank->setAccountNumber($validatedData['accountNumber']);
        $bank->setAccountName($validatedData['accountName']);
        $bank->setType($validatedData['type']);

        $this->bankRepository->save($bank);
        flash('Bank \'' . $bank->getName() . '\' updated.')->success();

        return redirect()->route('banking.banks.show', $bank->getId());
    }
}
