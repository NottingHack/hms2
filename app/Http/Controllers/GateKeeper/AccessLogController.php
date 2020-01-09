<?php

namespace App\Http\Controllers\GateKeeper;

use Carbon\Carbon;
use HMS\Entities\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use HMS\Repositories\GateKeeper\AccessLogRepository;

class AccessLogController extends Controller
{
    protected $accessLogRepository;

    public function __construct(AccessLogRepository $accessLogRepository)
    {
        $this->accessLogRepository = $accessLogRepository;

        $this->middleware('can:profile.view.all');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validatedData = $request->validate([
            'fromDate' => 'date_format:Y-m-d',
        ]);

        if (array_key_exists('fromDate', $validatedData)) {
            $fromDate = new Carbon($validatedData['fromDate']);
            $fromDate->startOfDay();
            $accessLogs = $this->accessLogRepository->paginateBetweeenAccessTimes($fromDate, Carbon::now(), 50);
        } else {
            $accessLogs = $this->accessLogRepository->paginateAll(50);
            $fromDate = $this->accessLogRepository->findFirst()->getAccessTime();
        }

        return view('gateKeeper.accessLogs.index')
            ->with('fromDate', $fromDate)
            ->with('accessLogs', $accessLogs);
    }

    /**
     * Display a listing of the resource.
     *
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function indexByUser(User $user)
    {
        $accessLogs = $this->accessLogRepository->paginateByUser($user);

        return view('gateKeeper.accessLogs.index_by_user')
            ->with('user', $user)
            ->with('accessLogs', $accessLogs);
    }
}
