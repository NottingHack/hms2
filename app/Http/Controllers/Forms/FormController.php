<?php

namespace App\Http\Controllers\Forms;

use App\Http\Controllers\Controller;
use HMS\Entities\Forms\Form;
use HMS\Repositories\Forms\FormRepository;
use HMS\Repositories\Forms\FormResponseRepository;
use HMS\Repositories\PermissionRepository;
use HMS\Repositories\RoleRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use LaravelDoctrine\ACL\Permissions\Permission;

class FormController extends Controller
{
    /**
     * Create a new instance of FormController.
     *
     * @param FormRepository $formRepository
     * @param FormResponseRepository $formResponseRepository
     * @param PermissionRepository $permissionRepository
     * @param RoleRepository $roleRepository
     */
    public function __construct(
        protected FormRepository $formRepository,
        protected FormResponseRepository $formResponseRepository,
        protected PermissionRepository $permissionRepository,
        protected RoleRepository $roleRepository
    ) {
        $this->formRepository = $formRepository;
        $this->formResponseRepository = $formResponseRepository;
        $this->permissionRepository = $permissionRepository;
        $this->roleRepository = $roleRepository;

        $this->middleware('feature:forms');
    }

    /**
     * Show the form listing.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $forms = $this->formRepository->paginateAll();

        $formResponseCounts = [];
        foreach ($forms as $form) {
            $formResponseCounts[$form->getId()] = $this->formResponseRepository->countVisibleResponsesForForm($form);
        }

        $userResponseCounts = [];
        foreach ($forms as $form) {
            $userResponseCounts[$form->getId()] = $this->formResponseRepository->countUserResponsesForForm($form, Auth::user());
        }

        return view('forms.index')->with([
            'forms' => $forms,
            'userResponseCounts' => $userResponseCounts,
            'formResponseCounts' => $formResponseCounts,
        ]);
    }

    /**
     * Render a specific form.
     *
     * @param Form $form
     *
     * @return \Illuminate\Http\Response
     */
    public function view(Form $form)
    {
        if (Gate::none([
            'forms.' . $form->getPermissionName() . '.respond',
            'forms.respond',
        ])) {
            flash('You do not have permission to view to ' . $form->getName());

            return redirect()->back();
        }

        if ($form->getMaxResponses() > 0 && $this->formResponseRepository->countUserResponsesForForm($form, Auth::user()) > $form->getMaxResponses()) {
            flash('You have responded to this form more than the permitted number of times.');

            return redirect()->back();
        }

        return view('forms.view')->with('form', $form);
    }

    /**
     * Display responses to a sepcific form.
     *
     * @param Form $form
     *
     * @return \Illuminate\Http\Response
     */
    public function responses(Form $form, Request $request)
    {
        if (Gate::none([
            'forms.' . $form->getPermissionName() . '.viewResponses',
            'forms.viewResponses',
        ])) {
            flash('You do not have permission to view responses to ' . $form->getName());

            return redirect()->back();
        }

        $validatedData = $request->validate([
            'perpage' => 'sometimes|integer',
            'hidden' => 'sometimes|required',
        ]);

        $perPage = array_key_exists('perpage', $validatedData) ? $validatedData['perpage'] : 15;
        $includeHidden = array_key_exists('hidden', $validatedData) && $validatedData['hidden'];

        $formResponses = $this->formResponseRepository->paginateForForm($form, $includeHidden, $perPage);

        $columns = [];
        foreach ($formResponses->items() as $formResponse) {
            // In cases where 'Other' has been selected for a multiple choice, a
            // column suffixed with '-Comment' is added to the response.
            $keys = array_filter(array_keys($formResponse->getResponseJson()), function ($column) {
                return ! str_ends_with($column, '-Comment');
            });

            $columns = array_unique(array_merge($keys, $columns));
        }

        return view('forms.responses')->with([
            'form' => $form,
            'formResponses' => $formResponses,
            'columns' => $columns,
            'perpage' => $perPage,
            'hidden' => $includeHidden,
        ]);
    }

    /**
     * Display the form to make a new form.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function newForm(Request $request)
    {
        if (Gate::denies('forms.edit')) {
            flash('You do not have permission to create new forms');

            return redirect()->back();
        }

        $form = new Form();

        return view('forms.new')->with('form', $form);
    }

    /**
     * Handle the creation of a new form.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function createForm(Request $request)
    {
        if (Gate::denies('forms.edit')) {
            flash('You do not have permission to create new forms');

            return redirect()->back();
        }

        $validatedData = $request->validate([
            'name' => 'required|max:100',
            'jsonDefinition' => 'required|json',
            'maxResponses' => 'integer',
            'notificationKey' => 'sometimes|nullable|string',
            'permissionName' => 'string|required|lowercase|max:32',
        ]);

        $form = new Form();
        $form->setName($validatedData['name']);
        $form->setJsonDefinition(json_decode($request['jsonDefinition'], true));
        $form->setMaxResponses($validatedData['maxResponses']);
        $form->setNotificationkey($validatedData['notificationKey']);
        $form->setPermissionName($validatedData['permissionName']);

        $this->formRepository->save($form);

        $permissionNames = ['respond', 'edit', 'viewResponses'];
        foreach ($permissionNames as $permissionName) {
            $permission = new Permission('forms.' . $form->getPermissionName() . '.' . $permissionName);
            $this->permissionRepository->save($permission);
        }

        return redirect()->route('forms.index');
    }

    /**
     * Display the form to edit a form.
     *
     * @param Form $form
     *
     * @return \Illuminate\Http\Response
     */
    public function editForm(Form $form)
    {
        if (Gate::none([
            'forms.edit',
            'forms.' . $form->getPermissionName() . '.edit',
        ])) {
            flash('You do not have permission to edit ' . $form->getName());

            return redirect()->back();
        }

        return view('forms.edit')->with('form', $form);
    }

    /**
     * Handle the update of a form.
     *
     * @param Form $form
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function updateForm(Form $form, Request $request)
    {
        if (Gate::none([
            'forms.edit',
            'forms.' . $form->getPermissionName() . '.edit',
        ])) {
            flash('You do not have permission to edit ' . $form->getName());

            return redirect()->back();
        }

        $validatedData = $request->validate([
            'name' => 'required|max:100',
            'jsonDefinition' => 'required|json',
            'maxResponses' => 'integer',
            'notificationKey' => 'sometimes|nullable|string',
        ]);

        $form->setName($validatedData['name']);
        $form->setJsonDefinition(json_decode($request['jsonDefinition'], true));
        $form->setMaxResponses($validatedData['maxResponses']);
        $form->setNotificationkey($validatedData['notificationKey']);

        $this->formRepository->save($form);

        return redirect()->route('forms.index');
    }

    /**
     * Display the page for managing a form's permissions.
     *
     * @param Form $form
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function permissions(Form $form, Request $request)
    {
        if (Gate::denies('forms.edit')) {
            flash('You do not have permission to manage the permissions of ' . $form->getName());

            return redirect()->back();
        }

        $roles = $this->roleRepository->findAll();

        $respond = $this->permissionRepository->findOneByName('forms.' . $form->getPermissionName() . '.respond');
        $edit = $this->permissionRepository->findOneByName('forms.' . $form->getPermissionName() . '.edit');
        $viewResponses = $this->permissionRepository->findOneByName('forms.' . $form->getPermissionName() . '.viewResponses');

        return view('forms.permissions')->with([
            'form' => $form,
            'roles' => $roles,
            'permissionRespond' => $respond,
            'permissionEdit' => $edit,
            'permissionViewResponses' => $viewResponses,
        ]);
    }

    /**
     * Handle updates to a form's permissions.
     *
     * @param Form $form
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function updatePermissions(Form $form, Request $request)
    {
        if (Gate::denies('forms.edit')) {
            flash('You do not have permission to manage the permissions of ' . $form->getName());

            return redirect()->back();
        }

        $validatedData = $request->validate([
            'respond' => 'required|sometimes|array|nullable',
            'edit' => 'required|sometimes|array|nullable',
            'viewResponses' => 'required|sometimes|array|nullable',
        ]);

        $respond = $this->permissionRepository->findOneByName('forms.' . $form->getPermissionName() . '.respond');
        $edit = $this->permissionRepository->findOneByName('forms.' . $form->getPermissionName() . '.edit');
        $viewResponses = $this->permissionRepository->findOneByName('forms.' . $form->getPermissionName() . '.viewResponses');

        $roles = $this->roleRepository->findAll();
        foreach ($roles as $role) {
            if (isset($validatedData['respond']) && in_array($role->getName(), $validatedData['respond'])) {
                $role->addPermission($respond);
            } else {
                $role->removePermission($respond);
            }

            if (isset($validatedData['edit']) && in_array($role->getName(), $validatedData['edit'])) {
                $role->addPermission($edit);
            } else {
                $role->removePermission($edit);
            }

            if (isset($validatedData['viewResponses']) && in_array($role->getName(), $validatedData['viewResponses'])) {
                $role->addPermission($viewResponses);
            } else {
                $role->removePermission($viewResponses);
            }

            $this->roleRepository->save($role);
        }

        return redirect()->route('forms.permissions', $form->getId());
    }
}
