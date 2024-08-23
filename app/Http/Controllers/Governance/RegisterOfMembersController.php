<?php

namespace App\Http\Controllers\Governance;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use HMS\Repositories\Governance\RegisterOfMembersRepository;

class RegisterOfMembersController extends Controller
{
    public function __construct(
        protected RegisterOfMembersRepository $registerOfMembersRepository
    ) {
        $this->middleware('can:governance.registerOfMembers.view');
    }

    public function index()
    {
        $registerOfMembers = $this->registerOfMembersRepository->paginateAll();

        return view('governance.registerOfMembers.index')
            ->with(['registerOfMembers' => $registerOfMembers]);
    }

    public function pdf()
    {
        $registerOfMembers = $this->registerOfMembersRepository->findAll();

        $viewVars = [
            'registerOfMembers' => $registerOfMembers,
            'numPagesTotal' => 999,
        ];

        // ini_set('memory_limit', '-1');
        $pdf = Pdf::setPaper('a4')
            ->loadView('pdfs.governance.registerOfMembers', $viewVars);
        $pdf->render();
        $viewVars['numPagesTotal'] = $pdf->getCanvas()->get_page_count();

        $pdf = Pdf::setPaper('a4')
            ->loadView('pdfs.governance.registerOfMembers', $viewVars);

        return $pdf->stream();
    }
}
