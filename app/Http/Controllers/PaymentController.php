<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Payment\CreateOrUpdatePayment;
use App\Http\Services\BraintreeService;

class PaymentController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(CreateOrUpdatePayment $request, BraintreeService $braintreeService)
    {
        $fields = $request->validated();
        $user = auth()->user();

        $customer = $braintreeService->updateCustomer(
            $user->customer_id,
            [
                'paymentMethodNonce' => $fields['paymentMethodNonce']
            ]
        );
        return response()->json($customer->id);

    }
}
