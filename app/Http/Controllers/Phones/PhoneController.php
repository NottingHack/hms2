<?php

namespace App\Http\Controllers\Phones;

use App\Http\Controllers\Controller;
use HMS\Entities\Phones\ExtensionType;
use HMS\Entities\Phones\PhoneExtension;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\Phones\PhoneExtensionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    }

    /**
     * Phonebook.
     *
     * @return \Illuminate\Http\Response
     */
    public function directory()
    {
        $extensions = $this->phoneExtensionRepository->paginateAll();

        return view('phones.directory')
            ->with(['extensions' => $extensions]);
    }

    /**
     * Users extensions.
     *
     * @return \Illuminate\Http\Response
     */
    public function extensions()
    {
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
        return view('phones.register')
            ->with(['types' => ExtensionType::TYPE_STRINGS]);
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
        $this->validate($request, [
            'extension' => 'required|numeric|unique:HMS\Entities\Phones\PhoneExtension,extension|notin:999,112,111,101',
            'phoneword' => 'sometimes|max:6',
            'type' => 'required|in:SIP,DECT,POTS,CUSTOM',
            'description' => 'required|max:200',
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

        if ($extension->getType() === 'SIP') {
            $extension->generateSipPassword();
        }

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
        if ($extension->getUser() != Auth::user()) {
            flash('Unauthorized')->error();
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
        if ($extension->getUser() != Auth::user()) {
            flash('Unauthorized')->error();
        }

        $this->phoneExtensionRepository->delete($extension);

        return redirect()->route('phones.extensions');
    }
}
