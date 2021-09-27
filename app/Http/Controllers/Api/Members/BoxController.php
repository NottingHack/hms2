<?php

namespace App\Http\Controllers\Api\Members;

use App\Events\Labels\BoxPrint;
use App\Http\Controllers\Controller;
use App\Http\Resources\Members\BoxResource;
use Doctrine\ORM\EntityNotFoundException;
use HMS\Entities\Members\Box;
use HMS\Entities\Snackspace\TransactionType;
use HMS\Entities\User;
use HMS\Factories\Members\BoxFactory;
use HMS\Factories\Snackspace\TransactionFactory;
use HMS\Repositories\Members\BoxRepository;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\Snackspace\TransactionRepository;
use HMS\Repositories\UserRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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
     * @var TransactionRepository
     */
    protected $transactionRepository;

    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;

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
     * Description used for the snackspace transaction.
     *
     * @var string
     */
    protected $transactionDescription = 'Members Box';

    /**
     * Create a new controller instance.
     *
     * @param BoxRepository         $boxRepository
     * @param BoxFactory            $boxFactory
     * @param UserRepository        $userRepository
     * @param MetaRepository        $metaRepository
     * @param TransactionRepository $transactionRepository
     * @param TransactionFactory    $transactionFactory
     */
    public function __construct(
        BoxRepository $boxRepository,
        BoxFactory $boxFactory,
        UserRepository $userRepository,
        MetaRepository $metaRepository,
        TransactionRepository $transactionRepository,
        TransactionFactory $transactionFactory
    ) {
        $this->boxRepository = $boxRepository;
        $this->boxFactory = $boxFactory;
        $this->userRepository = $userRepository;
        $this->metaRepository = $metaRepository;
        $this->transactionRepository = $transactionRepository;
        $this->transactionFactory = $transactionFactory;

        $this->middleware('feature:boxes');
        $this->middleware('can:box.view.self')->only(['index', 'show']);
        $this->middleware('can:box.buy.self')->only(['store']);
        $this->middleware('can:box.issue.all')->only(['issue']);
        $this->middleware('can:box.edit.self')->only(['markInUse', 'markAbandoned', 'markRemoved']);
        $this->middleware(['can:box.printLabel.self', 'feature:label_printer'])->only(['printLabel']);
        $this->middleware('can:box.view.all')->only(['audit']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->user) {
            $user = $this->userRepository->findOneById($request->user);
            if (is_null($user)) {
                throw EntityNotFoundException::fromClassNameAndIdentifier(User::class, ['id' => $request->user]);
            }

            if ($user != Auth::user() && Gate::denies('box.view.all')) {
                throw new AuthorizationException('This action is unauthorized.');
            }
        } else {
            $user = Auth::user();
        }

        $boxes = $this->boxRepository->findByUser($user);

        return BoxResource::collection($boxes);
    }

    /**
     * Show a specific Box.
     *
     * @param Box $box the Box
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Box $box)
    {
        if ($box->getUser() != Auth::user() && Gate::denies('box.view.all')) {
            throw new AuthorizationException('This action is unauthorized.');
        }

        return new BoxResource($box);
    }

    /**
     * Store a newly created box.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'boxUser' => 'sometimes|exists:HMS\Entities\User,id',
        ]);

        if ($request->boxUser) {
            $user = $this->userRepository->findOneById($request->boxUser);
            if (is_null($user)) {
                throw EntityNotFoundException::fromClassNameAndIdentifier(User::class, ['id' => $request->boxUser]);
            }

            if ($user != Auth::user() && Gate::denies('box.issue.all')) {
                throw new AuthorizationException('This action is unauthorized.');
            }
        } else {
            $user = Auth::user();
        }

        $individualLimit = (int) $this->metaRepository->get($this->individualLimitKey);
        $maxLimit = (int) $this->metaRepository->get($this->maxLimitKey);
        $boxCost = (int) $this->metaRepository->get($this->boxCostKey);

        $box = $this->boxFactory->create($user);

        // do we still have space
        $spaceBoxCount = $this->boxRepository->countAllInUse();
        if ($spaceBoxCount >= $maxLimit) {
            $data = [
                'errors' => [
                    [
                        'status' => IlluminateResponse::HTTP_FORBIDDEN,
                        'title'  => 'Forbidden',
                        'detail' => 'Sorry we have no room for any more boxes',
                    ],
                ],
            ];

            return response()->json($data, IlluminateResponse::HTTP_FORBIDDEN);
        }

        // if needed can it still be paid for
        if ($user != Auth::user()) {
            // should be a free issue
            $userBoxCount = $this->boxRepository->countInUseByUser($user);
            if ($userBoxCount >= $individualLimit) {
                $data = [
                    'errors' => [
                        [
                            'status' => IlluminateResponse::HTTP_FORBIDDEN,
                            'title'  => 'Forbidden',
                            'detail' => 'This member has too many boxes already',
                        ],
                    ],
                ];

                return response()->json($data, IlluminateResponse::HTTP_FORBIDDEN);
            }
        } else {
            // check & debit balance
            // do we have enough credit to buy a box?
            if ($user->getProfile()->getBalance() + $boxCost < (-1 * $user->getProfile()->getCreditLimit())) {
                $data = [
                    'errors' => [
                        [
                            'status' => IlluminateResponse::HTTP_FORBIDDEN,
                            'title'  => 'Forbidden',
                            'detail' => 'Sorry you do not have enough credit to buy another box',
                        ],
                    ],
                ];

                return response()->json($data, IlluminateResponse::HTTP_FORBIDDEN);
            }

            // charge this users snackspace account $boxCost
            $boxTransaction = $this->transactionFactory
                ->create(
                    $user,
                    $boxCost,
                    TransactionType::MEMBER_BOX,
                    $this->transactionDescription
                );
            $this->transactionRepository->saveAndUpdateBalance($boxTransaction);
        }

        $this->boxRepository->save($box);

        return (new BoxResource($box))->response()->setStatusCode(IlluminateResponse::HTTP_CREATED);
    }

    /**
     * Print a label for a given box.
     *
     * @param Box $box
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function printLabel(Box $box)
    {
        if ($box->getUser() != Auth::user() && Gate::denies('box.printLabel.all')) {
            throw new AuthorizationException('This action is unauthorized.');
        }

        event(new BoxPrint($box));

        return response()->json([], IlluminateResponse::HTTP_ACCEPTED);
    }

    /**
     * Mark a box in use.
     *
     * @param Box $box
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function markInUse(Box $box)
    {
        $user = $box->getUser();
        if ($user != Auth::user() && Gate::denies('box.edit.all')) {
            throw new AuthorizationException('This action is unauthorized.');
        }

        $individualLimit = (int) $this->metaRepository->get($this->individualLimitKey);
        $maxLimit = (int) $this->metaRepository->get($this->maxLimitKey);

        // check member does not all ready have max number of boxes
        $userBoxCount = $this->boxRepository->countInUseByUser($user);
        if ($userBoxCount >= $individualLimit) {
            if ($box->getUser() == Auth::user()) {
                $message = 'You have too many boxes already';
            } else {
                $message = 'This member has too many boxes already';
            }

            $data = [
                'errors' => [
                    [
                        'status' => IlluminateResponse::HTTP_FORBIDDEN,
                        'title'  => 'Forbidden',
                        'detail' => $message,
                    ],
                ],
            ];

            return response()->json($data, IlluminateResponse::HTTP_FORBIDDEN);
        }

        // do we have space for a box
        $spaceBoxCount = $this->boxRepository->countAllInUse();
        if ($spaceBoxCount >= $maxLimit) {
            $data = [
                'errors' => [
                    [
                        'status' => IlluminateResponse::HTTP_FORBIDDEN,
                        'title'  => 'Forbidden',
                        'detail' => 'Sorry we have no room for any more boxes',
                    ],
                ],
            ];

            return response()->json($data, IlluminateResponse::HTTP_FORBIDDEN);
        }

        $box->setStateInUse();
        $this->boxRepository->save($box);

        return new BoxResource($box);
    }

    /**
     * Mark a box abandoned.
     *
     * @param Box $box
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function markAbandoned(Box $box)
    {
        if ($box->getUser() == Auth::user()) {
            throw new AuthorizationException('This action is unauthorized.');
        }

        if ($box->getUser() != Auth::user() && Gate::denies('box.edit.all')) {
            throw new AuthorizationException('This action is unauthorized.');
        }

        $box->setStateAbandoned();
        $this->boxRepository->save($box);

        return new BoxResource($box);
    }

    /**
     * Mark a box removed.
     *
     * @param Box $box
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function markRemoved(Box $box)
    {
        if ($box->getUser() != Auth::user()) {
            throw new AuthorizationException('This action is unauthorized.');
        }

        $box->setStateRemoved();
        $this->boxRepository->save($box);

        return new BoxResource($box);
    }

    /**
     * View any boxes that are makred INUSE but owned by an Ex member.
     *
     * @return \Illuminate\Http\Response
     */
    public function audit()
    {
        $boxes = $this->boxRepository->paginateInUseByExMember();

        return BoxResource::collection($boxes);
    }
}
