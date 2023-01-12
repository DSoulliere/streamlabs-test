<?php

namespace App\Http\Controllers;

use App\Models\{ Subscription, Plan };
use App\Http\Requests\Subscription\{StoreOrUpdateSubscription, DestroySubscription};
use App\Http\Services\BraintreeService;

class SubscriptionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrUpdateSubscription $request, BraintreeService $braintreeService)
    {
        $fields = $request->validated();
        $user = auth()->user();
        $plan = Plan::where('plan_id', $fields['planId'])->first();
        $result = $braintreeService->createOrUpdateSubscription(
            $user->subscription->subscription_id ?? null,
            [
                'paymentMethodNonce' => $fields['paymentMethodNonce'],
                'planId' => $plan->plan_id,
            ]
        );
        $braintreeSubscription = json_decode(json_encode($result)); //fix for bizarre issue with 500 being thrown

        if ($braintreeSubscription) {
            $subscription = $user->subscription()->updateOrCreate(
                ['subscription_id' => $braintreeSubscription->id],
                ['plan_id' => $plan->id],
            );

            return response()->json([
                'planId' => $plan->plan_id,
                'planName' => $plan->name,
                'subscriptionId' => $subscription->subscription_id,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroySubscription $request, BraintreeService $braintreeService)
    {
        $user = auth()->user();
        if (!empty($user->subscription->subscription_id)) {
            $braintreeService->cancelSubscription($user->subscription->subscription_id);
            $user->subscription()->delete();
        }
        return response()->json([
            'message' => 'You\'re now unsubscribed'
        ]);
    }
}
