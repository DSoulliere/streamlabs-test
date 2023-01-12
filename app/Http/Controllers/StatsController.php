<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Braintree\Gateway;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\BraintreeService;
use Inertia\Inertia;
use Inertia\Response;

class StatsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, BraintreeService $braintreeService) : Response
    {
        $user =  auth()->user();
        if (empty($user->customer_id)) {
            $user->customer_id = $braintreeService->createCustomer()->id;
            $user->save();
        }
        ;
        return Inertia::render('Stats', [
            'token' => $braintreeService->generateClientToken([
                'customerId' => $user->customer_id
            ]),
            'subscriptionId' => $user->subscription->subscription_id ?? false,
            'planId' => $user->subscription->plan_id ?? null,
        ]);
    }
}
