<?php

namespace App\Http\Controllers;

use HMS\Entities\Role;
use HMS\Views\LowPayer;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\Banking\BankTransactionRepository;

class CSVDownloadController extends Controller
{
    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var BankTransactionRepository
     */
    protected $bankTransactionRepository;

    /**
     * Create a new controller instance.
     *
     * @param RoleRepository $roleRepository
     * @param BankTransactionRepository $bankTransactionRepository
     */
    public function __construct(
        RoleRepository $roleRepository,
        BankTransactionRepository $bankTransactionRepository
    ) {
        $this->roleRepository = $roleRepository;

        $this->middleware('can:profile.view.all');
        $this->bankTransactionRepository = $bankTransactionRepository;
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
            'Expires' => '0',
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
            'Expires' => '0',
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

    /**
     * Download a csv of members payments that have change.
     *
     * @return \Illuminate\Http\Response
     */
    public function paymentChange()
    {
        $headers = [
            'Content-type' => 'text/csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $currentRole = $this->roleRepository->findOneByName(Role::MEMBER_CURRENT);
        $currentUsers = $currentRole->getUsers();

        $csvData = collect();

        foreach ($currentUsers as $user) {
            $transactions = $this->bankTransactionRepository->paginateByAccount($user->getAccount());

            $jointCount = count($user->getAccount()->getUsers());

            if ($transactions->count() > 1) {
                $newAmount = $transactions[0]->getAmount();
                $oldAmount = $transactions[1]->getAmount();

                if ($jointCount > 1) {
                    $newAmount /= $jointCount;
                    $oldAmount /= $jointCount;
                }

                $joint = $jointCount > 1 ? 'Yes' : '';

                if ($newAmount != $oldAmount) {
                    $lastestTransactionDate = $transactions[0]->getTransactionDate();

                    $csvData[] = [
                        'Name' => $user->getFullname(),
                        'User Id' => $user->getId(),
                        'Last Payment Date' => $lastestTransactionDate->toDateString(),
                        'New Amount' => $newAmount / 100,
                        'Old Amount' => $oldAmount / 100,
                        'Joint' => $joint,
                        'Account Id' => $user->getAccount()->getId(),
                        'Difference' => ($newAmount - $oldAmount) / 100,
                    ];
                }
            }
        }

        $callback = function () use ($csvData) {
            $file = fopen('php://output', 'w');

            fputcsv($file, array_keys($csvData->first()));
            foreach ($csvData as $row) {
                fputcsv(
                    $file,
                    $row
                );
            }
            fclose($file);
        };

        return response()->streamDownload($callback, 'payment-change-' . date('d-m-Y-H:i:s') . '.csv', $headers);
    }
}
