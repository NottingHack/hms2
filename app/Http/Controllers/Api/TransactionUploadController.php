<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Events\Banking\TransactionsUploaded;
use Illuminate\Http\Response as IlluminateResponse;

class TransactionUploadController extends Controller
{
    /**
     * Upload new bank transactions via json.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        /*
         * example JSON for request
         * make sure headers are
         * Accept: application/json
         * Content-Type: application/json
         * body =
         * [
         *     {
         *         "sortCode" : "77-22-24",
         *         "accountNumber" : "13007568",
         *         "date" : "2017-07-17T00:00:00.000Z",
         *         "description" : "Edward Murphy HSNTSBBPRK86CWPV 4",
         *         "amount" : 500
         *     },
         *     {
         *         "sortCode" : "77-22-24",
         *         "accountNumber" : "13007568",
         *         "date" : "2017-07-16T00:00:00.000Z",
         *         "description" : "Gordon Johnson HSNTSB27496WPB2M 53",
         *         "amount" : 700
         *     },
         *     {
         *         "sortCode" : "77-22-24",
         *         "accountNumber" : "13007568",
         *         "date" : "2017-07-16T00:00:00.000Z",
         *         "description" : "BIZSPACE",
         *         "amount" : -238963
         *     }
         * ]
         */

        if (count($request->input()) == 0) {
            // nothing sent
            return response()->json([], IlluminateResponse::HTTP_BAD_REQUEST);
        }

        $this->validate($request, [
            '*.sortCode' => 'sometimes|exists:HMS\Entities\Banking\Bank,sortCode',
            '*.accountNumber' => 'required|exists:HMS\Entities\Banking\Bank,accountNumber',
            '*.date' => 'required|date',
            '*.description' => 'required|string',
            '*.amount' => 'required|integer',
        ]);

        event(new TransactionsUploaded($request->input()));

        return response()->json([], IlluminateResponse::HTTP_ACCEPTED);
    }
}
