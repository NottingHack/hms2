<?php

namespace App\Http\Controllers\Api;

use App\Events\DovecotPushReceived;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DovecotPushController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $validatedData = $request->validate([
            'user' => 'sometimes|string',
            'folder' => 'sometimes|string',
            'event' => 'sometimes|string',
            'from' => 'sometimes|string',
            'subject' => 'sometimes|string',
            'snippet' => 'sometimes|string',
            'messages' => 'sometimes|numeric',
            'unseen' => 'sometimes|numeric',
        ]);

        broadcast(new DovecotPushReceived(
            $validatedData['user'],
            $validatedData['folder'],
            $validatedData['event'],
            $validatedData['from'],
            $validatedData['subject'],
            $validatedData['snippet'],
            $validatedData['messages'],
            $validatedData['unseen'],
        ));

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
