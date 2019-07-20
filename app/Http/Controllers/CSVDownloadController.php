<?php

namespace App\Http\Controllers;

use HMS\Entities\Role;
use HMS\Views\LowPayer;
use Illuminate\Http\Request;
use HMS\Repositories\RoleRepository;

class CSVDownloadController extends Controller
{
    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * Create a new controller instance.
     *
     * @param RoleRepository $roleRepository
     */
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;

        $this->middleware('can:profile.view.all');
    }

    /**
     * Display a listing of the available downloads.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.csv_download');
    }

    /**
     * Download a csv of current member email addresses.
     *
     * @return \Illuminate\Http\Response
     */
    public function currentMemberEmails()
    {
        $headers = [
            'Content-type' => 'text/csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $members = $this->roleRepository->findOneByName(Role::MEMBER_CURRENT)->getUsers();

        $callback = function () use ($members) {
            $file = fopen('php://output', 'w');

            foreach ($members as $member) {
                fputcsv(
                    $file,
                    [$member->getEmail()]
                );
            }
            fclose($file);
        };
        return response()->streamDownload($callback, 'currentMemberEmails-' . date('d-m-Y-H:i:s') . '.csv', $headers);
    }

    /**
     * Download a csv of members payments.
     *
     * @return \Illuminate\Http\Response
     */
    public function lowPayers()
    {
        $headers = [
            'Content-type' => 'text/csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $lowPayers = LowPayer::all();

        $callback = function () use ($lowPayers) {
            $file = fopen('php://output', 'w');

            fputcsv($file, array_keys($lowPayers->first()->toArray()));
            foreach ($lowPayers as $lowPayer) {
                fputcsv(
                    $file,
                    $lowPayer->toArray()
                );
            }
            fclose($file);
        };

        return response()->streamDownload($callback, 'low-payers-' . date('d-m-Y-H:i:s') . '.csv', $headers);
    }
}
