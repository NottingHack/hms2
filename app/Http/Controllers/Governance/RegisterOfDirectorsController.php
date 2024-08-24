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

        $serviceAddress = str(config('branding.company_name'))->append('<br>')
            ->append(config('branding.space_address_1'))->append('<br>')
            ->append(config('branding.space_address_2'))->append('<br>');

        if (config('branding.space_address_3')) {
            $serviceAddress->append(config('branding.space_address_3'))->append('<br>');
        }
        $serviceAddress->append(config('branding.space_city'))->append('<br>');

        if (config('branding.space_county')) {
            $serviceAddress->append(config('branding.space_county'))->append('<br>');
        }
        $serviceAddress->append(config('branding.space_postcode'));

        $viewVars = [
            'registerOfDirectors' => $registerOfDirectors,
            'numPagesTotal' => 999,
            'serviceAddress' => $serviceAddress,
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
