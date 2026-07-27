<?php

namespace App\Http\Controllers\Phones;

use App\Http\Controllers\Controller;
use HMS\Entities\Phones\ExtensionCategory;
use HMS\Entities\Phones\ExtensionType;
use HMS\Entities\Phones\PhoneExtension;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\Phones\PhoneExtensionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PhoneController extends Controller
{
    /**
     * @var PhoneExtensionRepository
     */
    protected $phoneExtensionRepository;

    /**
     * @var MetaRepository
     */
    protected $metaRepository;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        PhoneExtensionRepository $phoneExtensionRepository,
        MetaRepository $metaRepository
    ) {
        $this->phoneExtensionRepository = $phoneExtensionRepository;
        $this->metaRepository = $metaRepository;

        $this->middleware('feature:phones');
    }

    /**
     * Phonebook.
     *
     * @return \Illuminate\Http\Response
     */
    public function directory(Request $request)
    {
        if (! Gate::allows('phones.view.directory.all') && ! Gate::allows('phones.view.directory.limited')) {
            flash('Unauthorized')->error();

            return redirect()->route('home');
        }

        $extensions = [];
        if (isset($request->category) && isset(ExtensionCategory::TYPE_STRINGS[$request->category])) {
            $extensions = $this->phoneExtensionRepository->paginateByCategory($request->category, 100);
        } else {
            $extensions = $this->phoneExtensionRepository->paginateAll(100);
        }

        return view('phones.directory')
            ->with([
                'extensions' => $extensions,
                'categories' => ExtensionCategory::TYPE_STRINGS,
            ]);
    }

    /**
     * Users extensions.
     *
     * @return \Illuminate\Http\Response
     */
    public function extensions()
    {
        if (! Gate::allows('phones.view.self')) {
            flash('Unauthorized')->error();

            return redirect()->route('home');
        }
        $user = Auth::user();

        $extensions = $this->phoneExtensionRepository->paginateByUser($user);

        return view('phones.numbers')
            ->with(['extensions' => $extensions]);
    }

    /**
     * Page to register a new extension.
     *
     * @return \Illuminate\Http\Response
     */
    public function registerExtension()
    {
        if (! Gate::allows('phones.edit.self') && ! Gate::allows('phones.edit.all')) {
            flash('Unauthorized')->error();

            return redirect()->route('phones.extensions');
        }

        return view('phones.register')
            ->with([
                'types' => ExtensionType::TYPE_STRINGS,
                'categories' => ExtensionCategory::TYPE_STRINGS,
                'extension' => new PhoneExtension(),
            ]);
    }

    /**
     * Page to edit an existing extension.
     *
     * @return \Illuminate\Http\Response
     */
    public function editExtension(PhoneExtension $extension)
    {
        if (! ($extension->getUser() == Auth::user() && Gate::allows('phones.edit.self')) &&
            ! Gate::allows('phones.edit.all')) {
            flash('Unauthorized')->error();

            return redirect()->route('phones.extensions');
        }

        return view('phones.edit')
            ->with([
                'types' => ExtensionType::TYPE_STRINGS,
                'categories' => ExtensionCategory::TYPE_STRINGS,
                'extension' => $extension,
            ]);
    }

    /**
     * Handler for extension creation.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function createExtension(Request $request)
    {
        if (! Gate::allows('phones.edit.self') && ! Gate::allows('phones.edit.all')) {
            flash('Unauthorized')->error();

            return redirect()->route('phones.extensions');
        }

        $this->validate($request, [
            'extension' => 'required|numeric|unique:HMS\Entities\Phones\PhoneExtension,extension|notin:999,112,111,101',
            'phoneword' => 'sometimes|max:6',
            'type' => 'required|in:SIP,DECT,POTS,CUSTOM',
            'description' => 'required|max:200',
            'category' => 'required|in:MEMBER,AREA,SERVICE',
        ], [
            'extension.notin' => 'Registering emergency service numbers is not a good idea, and not allowed here.',
            'extension.unique' => 'The number you are trying to register is already in use.',
            'extension.numeric' => 'Phone numbers are numeric, hence the name.',
        ]);

        $extension = new PhoneExtension();
        $extension->setExtension($request->extension);
        $extension->setPhoneword($request->phoneword);
        $extension->setUser(Auth::user());
        $extension->setDescription($request->description);
        $extension->setType($request->type);
        $extension->setCategory($request->category);
        $extension->setHidden(isset($request->hidden));

        if ($extension->getType() === 'SIP') {
            $extension->generateSipPassword();
        }

        $this->phoneExtensionRepository->save($extension);

        return redirect()->route('phones.extensions');
    }

    /**
     * Handler for extension updates.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function updateExtension(Request $request, PhoneExtension $extension)
    {
        if (! ($extension->getUser() == Auth::user() && Gate::allows('phones.edit.self')) &&
            ! Gate::allows('phones.edit.all')) {
            flash('Unauthorized')->error();

            return redirect()->route('phones.extensions');
        }

        $this->validate($request, [
            'phoneword' => 'sometimes|max:6',
            'description' => 'required|max:200',
            'category' => 'required|in:MEMBER,AREA,SERVICE',
        ]);

        $extension->setPhoneword($request->phoneword);
        $extension->setDescription($request->description);
        $extension->setCategory($request->category);
        $extension->setHidden(isset($request->hidden));

        $this->phoneExtensionRepository->save($extension);

        return redirect()->route('phones.extensions');
    }

    /**
     * Extension setup guide.
     *
     * @param PhoneExtension $extension
     *
     * @return \Illuminate\Http\Response
     */
    public function setup(PhoneExtension $extension)
    {
        if (! ($extension->getUser() == Auth::user() && Gate::allows('phones.edit.self'))) {
            flash('Unauthorized')->error();

            return redirect()->route('phones.extensions');
        }

        return view('phones.setup')
            ->with([
                'extension' => $extension,
                'sip_server' => $this->metaRepository->get('phones_sip_server'),
                'dect_registration_pin' => $this->metaRepository->get('phones_dect_registration_pin'),
                'link_prefix' => $this->metaRepository->get('phones_link_dial_prefix'),
            ]);
    }

    public function deleteExtension(PhoneExtension $extension)
    {
        if (! ($extension->getUser() == Auth::user() && Gate::allows('phones.edit.self')) &&
            ! Gate::allows('phones.edit.all')) {
            flash('Unauthorized')->error();

            return redirect()->route('phones.extensions');
        }

        $this->phoneExtensionRepository->delete($extension);

        return redirect()->route('phones.extensions');
    }
}
