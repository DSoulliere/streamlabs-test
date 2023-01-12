<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/inertia-vue3';
import dropin from "braintree-web-drop-in";



const props = defineProps({
    subscriptionId: Number,
    planId: Number,
    token: String,
});


const state = {
    subscriptionId: props.subscriptionId,
    planId: props.planId,
    paymentMethodNonce: null
};

let braintreedropIn = null;

function updateCustomerPayment(nonce) {
    fetch(route('payment'), {
        method: 'POST', // or 'PUT'
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json, text-plain, */*",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": usePage().props.value.csrf
        },
        body: JSON.stringify({
            paymentMethodNonce: nonce
        }),
        credentials: "same-origin",
    })
        .then((response) => response.json())
        .then((data) => {
            console.log('Success:', data);
            alert("You've added a new payment");
        })
        .catch((error) => {
            console.error('Error:', error);
            alert("There seems to have been an error");
        });
};

function updateCustomerSubscription(planId) {
    braintreedropIn.requestPaymentMethod((error, payload) => {
        fetch(route('subscriptions.store'), {
            method: 'POST', // or 'PUT'
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json, text-plain, */*",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": usePage().props.value.csrf
            },
            body: JSON.stringify({
                planId: planId,
                paymentMethodNonce: payload.nonce,
            }),
            credentials: "same-origin",
        })
            .then((response) => response.json())
            .then((data) => {
                console.log('Success:', data);
                alert("You're now subscribed " + data.planName + ". Refresh to see changes");
                state.planId = data.planId;
                state.subscriptionId = data.subscriptionId;
            })
            .catch((error) => {
                console.error('Error:', error);
                alert("There seems to have been an error");
            });
    });


};
function cancelCustomerSubscription() {
    if (state.subscriptionId < 1) {
        alert("You must have a valid subscription to unsubscribe.");
    } else {
        fetch(route('subscriptions.destroy'), {
            method: 'DELETE', // or 'PUT'
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json, text-plain, */*",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": usePage().props.value.csrf
            },
            credentials: "same-origin",
        })
            .then((response) => response.json())
            .then((data) => {
                console.log('Success:', data);
                alert("You're now unsubscribed. Refresh to see changes.");
                state.planId = 0;
                state.subscriptionId = 0;
            })
            .catch((error) => {
                console.error('Error:', error);
                alert("There seems to have been an error");
            });
    }
};




dropin.create({
    authorization: props.token,
    container: '#dropin-container',
    paypal: {
        flow: 'vault'
    }
}, (error, dropinInstance) => {
    if (error) console.error(error);


    braintreedropIn = dropinInstance;
    const form = document.getElementById('payment-form');

    form.addEventListener('submit', event => {
        event.preventDefault();

        braintreedropIn.requestPaymentMethod((error, payload) => {
            if (error) alert('There seems to be an error with the payment method');
            updateCustomerPayment(payload.nonce);
        });
    });
});


</script>

<template>

    <Head title="Stats!" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Advanced Stream Stats</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="flex flex-col">
                        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">

                        <div  class="px-10 m-5">
                            <h3 class="font-medium leading-tight text-3xl mt-0 mb-2 text-blue-600">Add or update your payment method</h3>
                            <form id="payment-form" action="/route/on/your/server" method="post">
                                <!-- Putting the empty container you plan to pass to
                                    `braintree.dropin.create` inside a form will make layout and flow
                                    easier to manage -->
                                    <div id="dropin-container"></div>
                                    <input type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" value="Submit"/>
                                    <input type="hidden" id="nonce" name="payment_method_nonce" />
                                </form>
                            </div>

                            <div  class="px-10 m-5">
                                <h3 class="font-medium leading-tight text-3xl mt-0 mb-2 text-blue-600">
                                    Subscribe and get exciting an exciting new stat!
                                </h3>
                            </div>
                        <div class="flex justify-center">
                            <div class="block rounded-lg shadow-lg bg-white max-w-sm text-center">
                                <div class="py-3 px-6 border-b border-gray-300">
                                    Monthly
                                </div>
                                <div class="p-6">
                                    <h5 class="text-gray-900 text-xl font-medium mb-2">$50 / mo</h5>
                                    <p class="text-gray-700 text-base mb-4">
                                        Instead of paying us every year, pay us  month instead!
                                    </p>
                                    <button type="button" @click="updateCustomerSubscription('monthly')" id="monthlyPayment"
                                        class=" inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out">Subscribe Monthly</button>
                                </div>
                            </div>


                            <div class="block rounded-lg shadow-lg bg-white max-w-sm text-center">
                                <div class="py-3 px-6 border-b border-gray-300">
                                    Yearly
                                </div>
                                <div class="p-6">
                                    <h5 class="text-gray-900 text-xl font-medium mb-2">$500 / y</h5>
                                    <p class="text-gray-700 text-base mb-4">
                                        Pay us more money but just once every 365 days!
                                    </p>
                                    <button type="button" id="yearlyPayment" @click="updateCustomerSubscription('yearly')"
                                        class=" inline-block px-6 py-2.5 bg-green-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg transition duration-150 ease-in-out">Subscribe Yearly</button>
                                </div>
                            </div>

                            <div class="block rounded-lg shadow-lg bg-white max-w-sm text-center">
                                <div class="py-3 px-6 border-b border-gray-300">
                                    Cancel
                                </div>
                                <div class="p-6">
                                    <h5 class="text-gray-900 text-xl font-medium mb-2">$0 / y</h5>
                                    <p class="text-gray-700 text-base mb-4">
                                        You have decided to stop giving us money.
                                    </p>
                                    <button type="button" @click="cancelCustomerSubscription()"
                                        class=" inline-block px-6 py-2.5 bg-red-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-red-700 hover:shadow-lg focus:bg-red-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg transition duration-150 ease-in-out">Button</button>
                                </div>
                            </div>



                        </div>
                        <div class="px-10 m-5">
                            <h3 class="font-medium leading-tight text-3xl mt-0 mb-2 text-blue-600">
                                Advanced stream stat!
                                Current Subscription:

                                <span v-if="state.planId === 1">Monthly. You see kills!</span>
                                <span v-if="state.planId === 2">Yearly. You see kills and  for longer!</span>
                                <span v-if="state.planId < 1">Not Subscribed. You see no kills</span>
                            </h3>
                        </div>
                            <!--If Not Subscribed-->
                            <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8"  v-if="state.planId < 1">
                                <div class="overflow-hidden">
                                    <table class="min-w-full">
                                        <thead class="border-b">
                                            <tr>
                                                <th scope="col"
                                                    class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                    #
                                                </th>
                                                <th scope="col"
                                                    class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                    First
                                                </th>
                                                <th scope="col"
                                                    class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                    Last
                                                </th>
                                                <th scope="col"
                                                    class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                    Handle
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="border-b">
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    1</td>
                                                <td
                                                    class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                    Mark
                                                </td>
                                                <td
                                                    class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                    Otto
                                                </td>
                                                <td
                                                    class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                    @mdo
                                                </td>
                                            </tr>
                                            <tr class="bg-white border-b">
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    2</td>
                                                <td
                                                    class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                    Jacob
                                                </td>
                                                <td
                                                    class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                    Thornton
                                                </td>
                                                <td
                                                    class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                    @fat
                                                </td>
                                            </tr>
                                            <tr class="bg-white border-b">
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    3</td>
                                                <td
                                                    class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                    Larry
                                                </td>
                                                <td
                                                    class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                    Wild
                                                </td>
                                                <td
                                                    class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                    @twitter
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!--If Subscribed-->
                            <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8" v-if="state.planId > 0">
                                <div class="overflow-hidden">
                                    <table class="min-w-full">
                                        <thead class="border-b">
                                            <tr>
                                                <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                    #
                                                </th>
                                                <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                    First
                                                </th>
                                                <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                    Last
                                                </th>
                                                <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                    Handle
                                                </th>

                                                <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                    Kills
                                                </th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="border-b">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    1</td>
                                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                    Mark
                                                </td>
                                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                    Otto
                                                </td>
                                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                    @mdo
                                                </td>

                                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                    120
                                                </td>

                                            </tr>
                                            <tr class="bg-white border-b">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    2</td>
                                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                    Jacob
                                                </td>
                                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                    Thornton
                                                </td>
                                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                    @fat
                                                </td>

                                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                    155
                                                </td>

                                            </tr>
                                            <tr class="bg-white border-b">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    3</td>
                                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                    Larry
                                                </td>
                                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                    Wild
                                                </td>
                                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                    @twitter
                                                </td>

                                                <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                    9001
                                                </td>

                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
