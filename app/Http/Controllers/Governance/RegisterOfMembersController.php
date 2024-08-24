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

        $records = collect($registerOfMembers)->map(fn ($record) => [
            'fullname' => $record->getFullname(),
            'startedAt' => $record->getStartedAt()->toDateString(),
            'endedAt' => $record->getEndedAt()?->toDateString(),
        ]);

        unset($registerOfMembers);
        gc_collect_cycles();

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
            'registerOfMembers' => $records,
            'numPagesTotal' => 999,
            'serviceAddress' => $serviceAddress,
        ];

        ini_set('memory_limit', '-1');
        $pdf = Pdf::setPaper('a4')
            ->loadView('pdfs.governance.registerOfMembers', $viewVars);
        $pdf->render();
        $viewVars['numPagesTotal'] = $pdf->getCanvas()->get_page_count();
        unset($pdf);
        gc_collect_cycles();

        $pdf = Pdf::setPaper('a4')
            ->loadView('pdfs.governance.registerOfMembers', $viewVars);

        return $pdf->stream();
    }
}
