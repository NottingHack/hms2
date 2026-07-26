<?php

namespace App\Http\Controllers\Api\Phones;

use App\Http\Controllers\Controller;
use HMS\Entities\Phones\ExtensionType;
use HMS\Repositories\Phones\PhoneExtensionRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AsteriskController extends Controller
{
    /**
     * @var PhoneExtensionRepository
     */
    protected $phoneExtensionRepository;

    /**
     * Create a new Asterisk Controller instance.
     *
     * @param PhoneExtensionRepository $phoneExtensionrepository
     */
    public function __construct(
        PhoneExtensionRepository $phoneExtensionRepository
    ) {
        $this->phoneExtensionRepository = $phoneExtensionRepository;
    }

    /**
     * Obtain DECT/POTS extension mappings.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function dialplan(Request $request)
    {
        $extensions = $this->phoneExtensionRepository->paginateAll(1000);

        $simplified = [];
        foreach ($extensions as $extension) {
            if (! $extension->getMappedNumber()) {
                continue;
            }

            $simplified[] = [
                'extension' => $extension->getExtension(),
                'target' => $extension->getMappedNumber(),
            ];
        }

        return response()->json($simplified, 200);
    }

    /**
     * Obtain SIP extensions and their passwords.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function sipExtensions(Request $request)
    {
        $extensions = $this->phoneExtensionRepository->paginateAll(1000);

        $simplified = [];
        foreach ($extensions as $extension) {
            if ($extension->getType() !== ExtensionType::SIP) {
                continue;
            }

            $simplified[] = [
                'extension' => $extension->getExtension(),
                'password' => $extension->getSipPassword(),
            ];
        }

        return response()->json($simplified, 200);
    }

    /**
     * Map an extension to a specific handset / line.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function mapExtension(Request $request)
    {
        $validated = $request->validate([
            'extension' => 'required|numeric',
            'target' => 'required|numeric',
        ]);

        $extension = $this->phoneExtensionRepository->findOneByExtension($validated['extension']);
        if (! $extension) {
            return response()->json([
                'error' => 'Extension not found',
            ], 404);
        }

        if ($extension->getMappedNumber()) {
            return response()->json([
                'error' => 'Extension already mapped to handset or line',
            ], 409);
        }

        if ($extension->getType() != ExtensionType::DECT && $extension->getType() != ExtensionType::POTS) {
            return response()->json([
                'error' => 'Target number is not acceptable',
            ], 406);
        }

        $extension->setMappedNumber($validated['target']);
        $this->phoneExtensionRepository->save($extension);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
