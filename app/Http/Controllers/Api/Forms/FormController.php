<?php

namespace App\Http\Controllers\Api\Forms;

use App\Events\Forms\FormResponseReceived;
use App\Http\Controllers\Controller;
use HMS\Entities\Forms\Form;
use HMS\Entities\Forms\FormResponse;
use HMS\Repositories\Forms\FormRepository;
use HMS\Repositories\Forms\FormResponseRepository;
use HMS\Repositories\Members\ProjectRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\Tools\ToolRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class FormController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param FormRepository $formRepository
     * @param FormResponseRepository $formResponseRepository
     * @param RoleRepository $roleRepository
     * @param ToolRepository $toolRepository
     * @param ProjectRepository $projectRepository
     * @param FormRepository $formRepository
     */
    public function __construct(
        protected FormRepository $formRepository,
        protected FormResponseRepository $formResponseRepository,
        protected RoleRepository $roleRepository,
        protected ToolRepository $toolRepository,
        protected ProjectRepository $projectRepository
    ) {
        $this->formRepository = $formRepository;
        $this->formResponseRepository = $formResponseRepository;
        $this->roleRepository = $roleRepository;
        $this->toolRepository = $toolRepository;
        $this->projectRepository = $projectRepository;

        $this->middleware('feature:forms');
    }

    /**
     * Retrieve a form JSON definition.
     *
     * @param Form $form
     *
     * @return \Illuminate\Http\Response
     */
    public function getModel(Form $form)
    {
        if (Gate::none([
            'forms.' . $form->getPermissionName() . '.respond',
            'forms.respond',
        ])) {
            throw new AuthorizationException('You do not have permission to view this form.');
        }

        $model = $form->getJsonDefinition();

        // Helpers to autofill dropdowns, radio and checkbox groups.
        array_walk_recursive($model, function (&$item, $key) {
            if ($key !== 'choices') {
                return;
            }

            if ($item === 'hms:teams') {
                $item = array_map(function ($role) {
                    return [
                        'value' => $role->getName(),
                        'text' => $role->getDisplayName(),
                    ];
                }, $this->roleRepository->findAllTeams());
            }

            if ($item === 'hms:tools' || $item === 'hms:tools:induction') {
                $tools = $this->toolRepository->findAll();

                if ($item === 'hms:tools:induction') {
                    $tools = array_filter($this->toolRepository->findAll(), function ($tool) {
                        return $tool->isRestricted();
                    });
                }

                $item = array_map(function ($tool) {
                    return $tool->getDisplayName();
                }, $tools);
            }

            if ($item === 'hms:projects') {
                $projects = $this->projectRepository->findByUser(Auth::user());

                $item = array_map(function ($tool) {
                    return [
                        'value' => $tool->getId(),
                        'text' => $tool->getProjectName(),
                    ];
                }, $projects);
            }
        });

        return response()->json($model);
    }

    /**
     * Submit a response to a form.
     *
     * @param Form $form
     *
     * @return \Illuminate\Http\Response
     */
    public function putResponse(Form $form, Request $request)
    {
        if (Gate::none([
            'forms.' . $form->getPermissionName() . '.respond',
            'forms.respond',
        ])) {
            throw new AuthorizationException('You do not have permission to respond to this form.');
        }

        if ($form->getMaxResponses() > 0 && $this->formResponseRepository->countUserResponsesForForm($form, Auth::user()) >= $form->getMaxResponses()) {
            throw new AuthorizationException('You have responded to this form more than the permitted number of times.');
        }

        // We need to find all possible keys for a form.
        $formDefinition = $form->getJsonDefinition();
        $validationRules = [];
        array_walk_recursive($formDefinition, function (&$value, $key) use (&$validationRules) {
            if ($key === 'name') {
                $validationRules[$value] = 'sometimes|required';
                $validationRules[$value . '-Comment'] = 'sometimes|required';
            }
        });

        $validatedData = $request->validate($validationRules);

        $formResponse = new FormResponse();
        $formResponse->setForm($form);
        $formResponse->setResponder(Auth::user());
        $formResponse->setResponseJson($validatedData);
        $formResponse->setHidden(false);

        $this->formResponseRepository->save($formResponse);

        if ($form->getNotificationKey() && array_key_exists($form->getNotificationKey(), $validatedData)) {
            $event = new FormResponseReceived($form, $formResponse);
            $target = $validatedData[$form->getNotificationKey()];

            // There are potentially other targets at some point...
            if (str_starts_with($target, 'team.')) {
                $team = $this->roleRepository->findOneByName($target);
                if ($team) {
                    $event->setRole($team);
                }
            }
            event($event);
        }
    }

    /**
     * Amend a form response with a comment.
     *
     * @param FormResponse $formResponse
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function putComment(FormResponse $formResponse, Request $request)
    {
        $form = $this->formRepository->findOneById($formResponse->getForm()->getId());

        if (Gate::none([
            'forms.' . $form->getPermissionName() . '.viewResponses',
            'forms.viewResponses',
        ])) {
            throw new AuthorizationException('You do not have permission to view this form.');
        }

        $validatedData = $request->validate([
            'comment' => 'present|max:255',
        ]);

        $formResponse->setComment($validatedData['comment']);
        $this->formResponseRepository->save($formResponse);
    }

    /**
     * Hide a response from the default listing.
     *
     * @param FormResponse $formResponse
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function hideResponse(FormResponse $formResponse, Request $request)
    {
        $form = $this->formRepository->findOneById($formResponse->getForm()->getId());

        if (Gate::none([
            'forms.' . $form->getPermissionName() . '.viewResponses',
            'forms.viewResponses',
        ])) {
            throw new AuthorizationException('You do not have permission to view this form.');
        }

        $formResponse->setHidden(true);
        $this->formResponseRepository->save($formResponse);
    }
}
