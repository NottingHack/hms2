<?php

namespace App\Http\Controllers\Members;

use HMS\Entities\User;
use Illuminate\Http\Request;
use HMS\Entities\Members\Box;
use App\Events\Labels\BoxPrint;
use App\Http\Controllers\Controller;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\UserRepository;
use HMS\Factories\Members\BoxFactory;
use Doctrine\ORM\EntityNotFoundException;
use HMS\Repositories\Members\BoxRepository;

class BoxController extends Controller
{
    /**
     * @var BoxRepository
     */
    protected $boxRepository;

    /**
     * @var BoxFactory
     */
    protected $boxFactory;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var MetaRepository
     */
    protected $metaRepository;

    /**
     * @var string
     */
    protected $individualLimitKey = 'member_box_individual_limit';

    /**
     * @var string
     */
    protected $maxLimitKey = 'member_box_limit';

    /**
     * @var string
     */
    protected $boxCostKey = 'member_box_cost';

    /**
     * Create a new controller instance.
     *
     * @param BoxRepository  $boxRepository
     * @param BoxFactory     $boxFactory
     * @param UserRepository $userRepository
     */
    public function __construct(BoxRepository $boxRepository,
        BoxFactory $boxFactory,
        UserRepository $userRepository,
        MetaRepository $metaRepository)
    {
        $this->boxRepository = $boxRepository;
        $this->boxFactory = $boxFactory;
        $this->userRepository = $userRepository;
        $this->metaRepository = $metaRepository;

        $this->middleware('can:box.view.self')->only(['index']);
        $this->middleware('can:box.buy.self')->only(['create', 'store']);
        $this->middleware('can:box.issue.all')->only(['issue', 'store']);
        $this->middleware('can:box.edit.self')->only(['markInUse', 'markAbandoned', 'markRemoved']);
        $this->middleware('can:box.printLabel.self')->only(['printLabel']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->user) {
            $user = $this->userRepository->find($request->user);
            if (is_null($user)) {
                throw EntityNotFoundException::fromClassNameAndIdentifier(User::class, ['id' => $request->user]);
            }

            if ($user != \Auth::user() && \Gate::denies('box.view.all')) {
                flash('Unauthorized')->error();

                return redirect()->route('home');
            }
        } else {
            $user = \Auth::user();
        }

        $boxes = $this->boxRepository->paginateByUser($user);
        $boxCost = (int)$this->metaRepository->get($this->boxCostKey);

        return view('members.box.index')
            ->with('user', $user)
            ->with('boxes', $boxes)
            ->with('boxCost', -$boxCost);
    }

    /**
     * Show the form for buying a new box.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = \Auth::user();

        $individualLimit = (int)$this->metaRepository->get($this->individualLimitKey);
        $maxLimit = (int)$this->metaRepository->get($this->maxLimitKey);
        $boxCost = (int)$this->metaRepository->get($this->boxCostKey);

        // check member does not all ready have max number of boxes
        $userBoxCount = $this->boxRepository->countInUseByUser($user);
        if ($userBoxCount >= $individualLimit) {
            flash('You have too many boxes already')->error();

            return redirect()->route('boxes.index');
        }

        // do we have space for a box
        $spaceBoxCount = $this->boxRepository->countAllInUse();
        if ($spaceBoxCount >= $maxLimit) {
            flash('Sorry we have no room for any more boxes')->error();

            return redirect()->route('boxes.index');
        }

        // do we have enought credit to buy a box?
        if ($user->getProfile()->getBalance() + $boxCost < (-1*$user->getProfile()->getCreditLimit())) {
            flash('Sorry you do not have enought credit to buy another box')->error();

            return redirect()->route('boxes.index');
        }

        return view('members.box.buy')
            ->with('boxCost', -$boxCost);
    }

    /**
     * Show the form for issue a new box.
     *
     * @param  User  $user user we are issuing a box for
     * @return \Illuminate\Http\Response
     */
    public function issue(User $user)
    {
        if ($user == \Auth::user()) {
            flash('Can not issue a box to yourself')->error();

            return redirect()->route('boxes.index');
        }

        $individualLimit = (int)$this->metaRepository->get($this->individualLimitKey);
        $maxLimit = (int)$this->metaRepository->get($this->maxLimitKey);

        // check member does not all ready have max number of boxes
        $userBoxCount = $this->boxRepository->countInUseByUser($user);
        if ($userBoxCount >= $individualLimit) {
            flash('This member has too many boxes already')->error();

            return redirect()->route('user.boxes', ['user' => $user->getId()]);
        }

        // even if it's free issue we need to check we have space
        $spaceBoxCount = $this->boxRepository->countAllInUse();
        if ($spaceBoxCount >= $maxLimit) {
            flash('Sorry we have no room for any more boxes')->error();

            return redirect()->route('user.boxes', ['user' => $user->getId()]);
        }

        return view('members.box.issue')
            ->with(['boxUser' => $user]);
    }

    /**
     * Store a newly created box.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'boxUser' => 'sometimes|exists:HMS\Entities\User,id',
        ]);

        if ($request->boxUser) {
            $user = $this->userRepository->find($request->boxUser);
            if (is_null($user)) {
                throw EntityNotFoundException::fromClassNameAndIdentifier(User::class, ['id' => $request->boxUser]);
            }

            if ($user != \Auth::user() && \Gate::denies('box.issue.all')) {
                flash('Unauthorized')->error();

                return redirect()->route('home');
            }
        } else {
            $user = \Auth::user();
        }

        $individualLimit = (int)$this->metaRepository->get($this->individualLimitKey);
        $maxLimit = (int)$this->metaRepository->get($this->maxLimitKey);
        $boxCost = (int)$this->metaRepository->get($this->boxCostKey);

        $box = $this->boxFactory->create($user);

        // do we still have space
        $spaceBoxCount = $this->boxRepository->countAllInUse();
        if ($spaceBoxCount >= $maxLimit) {
            flash('Sorry we have no room for any more boxes')->error();

            return redirect()->route('boxes.index',  ['user' => $user->getId()]);
        }

        // if needed can it still be paid for
        if ($user != \Auth::user()) {
            // should be a free issue
            $userBoxCount = $this->boxRepository->countInUseByUser($user);
            if ($userBoxCount >= $individualLimit) {
                flash('This member has too many boxes already')->error();

                return redirect()->route('user.boxes', $user);
            }
        } else {
            // check & debit balance
            // do we have enought credit to buy a box?
            if ($user->getProfile()->getBalance() + $boxCost < (-1*$user->getProfile()->getCreditLimit())) {
                flash('Sorry you do not have enought credit to buy another box')->error();

                return redirect()->route('boxes.index');
            }

            // charge this users snackspace account $boxCost

        }

        $this->boxRepository->save($box);
        flash('Box created.')->success();

        return redirect()->route('boxes.index', ['user' => $user->getId()]);
    }

    /**
     * print a label for a given box.
     *
     * @param  Box $box
     * @return \Illuminate\Http\Response
     */
    public function printLabel(Box $box)
    {
        if ($box->getUser() != \Auth::user() && \Gate::denies('box.printLabel.all')) {
            flash('Unauthorized')->error();

            return redirect()->route('home');
        }

        event(new BoxPrint($box));
        flash('Label sent to printer.')->success();

        return back();
    }

    /**
     * mark a box in use.
     *
     * @param  Box $box
     * @return \Illuminate\Http\Response
     */
    public function markInUse(Box $box)
    {
        $user = $box->getUser();
        if ($user != \Auth::user() && \Gate::denies('box.edit.all')) {
            flash('Unauthorized')->error();

            return redirect()->route('home');
        }

        $individualLimit = (int)$this->metaRepository->get($this->individualLimitKey);
        $maxLimit = (int)$this->metaRepository->get($this->maxLimitKey);

        // check member does not all ready have max number of boxes
        $userBoxCount = $this->boxRepository->countInUseByUser($user);
        if ($userBoxCount >= $individualLimit) {
            if ($box->getUser() == \Auth::user()){
                flash('You have too many boxes already')->error();
            } else {
                flash('This member has too many boxes already')->error();
            }

            return redirect()->route('boxes.index', ['user' => $user->getId()]);
        }

        // do we have space for a box
        $spaceBoxCount = $this->boxRepository->countAllInUse();
        if ($spaceBoxCount >= $maxLimit) {
            flash('Sorry we have no room for any more boxes')->error();

            return redirect()->route('boxes.index', ['user' => $user->getId()]);
        }

        $box->setStateInUse();
        $this->boxRepository->save($box);
        flash('Box marked in use.')->success();

        return back();
    }

    /**
     * mark a box abandoned.
     *
     * @param  Box $box
     * @return \Illuminate\Http\Response
     */
    public function markAbandoned(Box $box)
    {
        if ($box->getUser() == \Auth::user()) {
            flash('You can not abandoned your own box')->error();

            return redirect()->route('boxes.index');
        }

        if ($box->getUser() != \Auth::user() && \Gate::denies('box.edit.all')) {
            flash('Unauthorized')->error();

            return redirect()->route('home');
        }

        $box->setStateAbandoned();
        $this->boxRepository->save($box);
        flash('Box marked abandoned.')->success();

        return back();
    }

    /**
     * mark a box removed.
     *
     * @param  Box $box
     * @return \Illuminate\Http\Response
     */
    public function markRemoved(Box $box)
    {
        if ($box->getUser() != \Auth::user()) {
            flash('Unauthorized')->error();

            return redirect()->route('home');
        }

        $box->setStateRemoved();
        $this->boxRepository->save($box);
        flash('Box marked removed.')->success();

        return back();
    }
}
