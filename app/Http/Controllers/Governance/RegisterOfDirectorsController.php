<?php

namespace App\Http\Controllers\Governance;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use HMS\Repositories\Governance\RegisterOfDirectorsRepository;

class RegisterOfDirectorsController extends Controller
{
    public function __construct(
        protected RegisterOfDirectorsRepository $registerOfDirectorsRepository
    ) {
        $this->middleware('can:governance.registerOfDirectors.view');
    }

    public function index()
    {
        $registerOfDirectors = $this->registerOfDirectorsRepository->paginateAll();

        return view('governance.registerOfDirectors.index')
            ->with(['registerOfDirectors' => $registerOfDirectors]);
    }

    public function pdf()
    {
        $registerOfDirectors = $this->registerOfDirectorsRepository->findAll();

        $viewVars = [
            'registerOfDirectors' => $registerOfDirectors,
            'numPagesTotal' => 999,
        ];

        // ini_set('memory_limit', '-1');
        $pdf = Pdf::setPaper('a4')
            ->loadView('pdfs.governance.registerOfDirectors', $viewVars);

        $pdf->render();
        $viewVars['numPagesTotal'] = $pdf->getCanvas()->get_page_count();

        $pdf = Pdf::setPaper('a4')
            ->loadView('pdfs.governance.registerOfDirectors', $viewVars);

        return $pdf->stream();
    }
}
