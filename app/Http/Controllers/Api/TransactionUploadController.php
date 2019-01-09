<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Events\Banking\TransactionsUploaded;
use Illuminate\Http\Response as IlluminateResponse;

class TransactionUploadController extends Controller
{
    /**
     * upload new bank transacions via json.
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        /*
         * example JSON for request
         * [
         *     {
         *         "sortCode" : "77-22-24",
         *         "accountNumber" : "13007568",
         *         "date" : "2017-07-17",
         *         "description" : "Edward Murphy HSNTSBBPRK86CWPV 4",
         *         "amount" : 500
         *     },
         *     {
         *         "sortCode" : "77-22-24",
         *         "accountNumber" : "13007568",
         *         "date" : "2017-07-16",
         *         "description" : "Gordon Johnson HSNTSB27496WPB2M 53",
         *         "amount" : 700
         *     },
         *     {
         *         "sortCode" : "77-22-24",
         *         "accountNumber" : "13007568",
         *         "date" : "2017-07-16",
         *         "description" : "BIZSPACE",
         *         "amount" : -2389.63
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
