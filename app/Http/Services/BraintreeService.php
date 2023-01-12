<?php

namespace App\Http\Services;

use Braintree\Gateway;
use InvalidArgumentException;

class BraintreeService
{
    private $gateway;

    public function __construct(array $config)
    {
        $this->gateway = new Gateway($config);
    }

    /**
     * Generate a client token for client-side authentication
     *
     *
     * @return Result\Successful|Result\Error
     */
    public function generateClientToken(array $params = [])
    {
        $clientToken = $this->gateway->clientToken()->generate($params);

        return $clientToken;
    }

    /**
     * Create a customer with the provided information
     *
     * @param array $params containing the request parameters
     *
     * @return Result\Successful|Result\Error
     */
    public function createCustomer(array $params = [])
    {
        $customer = $this->gateway->customer()->create($params);

        return $customer->customer;
    }


    /**
     * Create a customer with the provided information
     *
     * @param array $customerId customer id
     *
     * @return Result\Successful|Result\Error
     */
    public function getCustomer($customerId)
    {
        $customer = $this->gateway->customer()->find($customerId);

        return $customer;
    }



    /**
     * Update a customer with the provided information
     *
     * @param string $customerId to be updated
     * @param array $params containing the request parameters
     *
     * @return Result\Successful|Result\Error
     */
    public function updateCustomer($customerId, array $params)
    {
        $customer = $this->gateway->customer()->update($customerId, $params);

        return $customer->customer;
    }


    /**
     * Create a payment method for a customer
     *
     * @param array $params containing the request parameters
     *              ['customerId'] REQUIRED
     *                  The id of the customer
     *
     * @return Result\Successful|Result\Error
     */
    public function createPaymentMethod(array $params)
    {
        $paymentMethod = $this->gateway->paymentMethod()->create($params);

        return $paymentMethod;
    }


    /**
     * Update a payment method for a customer
     *
     * @param string $token   payment method identifier
     * @param array $params containing the request parameters
     *              ['customerId'] REQUIRED
     *                  The id of the customer
     *
     * @return Result\Successful|Result\Error
     */
    public function updatePaymentMethod($token, array $params)
    {
        $paymentMethod = $this->gateway->paymentMethod()->update($token, $params);

        return $paymentMethod;
    }


    /**
     * Delete a payment method for a customer
     *
     * @param string $token   payment method identifier
     *
     * @return Result\Successful|Result\Error
     */
    public function deletePaymentMethod($token)
    {
        $paymentMethod = $this->gateway->paymentMethod()->delete($token);

        return $paymentMethod;
    }



    /**
     * Create a subscription for the customer associated with the nonce or token
     *
     * @param array $params containing the request parameters
     *              ['paymentMethodNonce'] REQUIRED WITHOUT paymentMethodToken
     *                  token given from vault
     *              ['paymentMethodToken'] REQUIRED WITHOUT paymentMethodNonce
     *                  nonce generated from drop-in UI
     *              ['planId'] REQUIRED
     *                  The plan ID as defined in settings
     *
     * @return Result\Successful|Result\Error
     */
    public function createSubscription(array $params)
    {
        $subscription = $this->gateway->subscription()->create($params);

        return $subscription;
    }


    /**
     * Update a specific subscription for a customer
     *
     * @param string $subscriptionId the ID of the subscription to be updated
     * @param array $params containing the request parameters* @param string $subscriptionId the ID of the subscription to be updated
     *              ['planId'] The plan ID as defined in settings
     *
     * @return Subscription|Exception\NotFound
     */
    public function createOrUpdateSubscription($subscriptionId = null, array $params)
    {
        if (empty($subscriptionId)) {
            $subscription = $this->gateway->subscription()->create($params);
        } else {
            $subscription = $this->gateway->subscription()->update($subscriptionId, $params);
        }

        return $subscription->subscription;
    }


    /**
     * Cancel a specific subscription for a customer
     *
     * @param string $subscriptionId the ID of the subscription to be updated
     * @param array $params containing the request parameters* @param string $subscriptionId the ID of the subscription to be updated
     *              ['planId'] The plan ID as defined in settings
     *
     * @return Subscription|Exception\NotFound
     */
    public function cancelSubscription($subscriptionId)
    {
        $subscription = $this->gateway->subscription()->cancel($subscriptionId);

        return $subscription;
    }
}
