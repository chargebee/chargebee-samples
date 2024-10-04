'use client'

import React, {ChangeEvent, useCallback, useEffect, useMemo, useRef, useState} from "react";
import {PaymentIntentStoreImpl} from "@/store/payment-intent-store-impl";

const products = [
    {
        id: 1,
        title: 'Early Bird',
        href: '#',
        price: '150',
        color: 'Conference Pass',
        size: 'N/A',
        imageSrc: 'https://img.freepik.com/free-photo/old-green-admission-ticket-isolated-against-white-background_1101-2403.jpg?w=2000&t=st=1728033385~exp=1728033985~hmac=ef05c2eec6e9fdc65aaa11fba2240f9cec9ce27d81a5677f4f2e4eb557881ddb',
        imageAlt: 'Early bird registration pass for the conference.',
    },
    {
        id: 2,
        title: 'Conference T-Shirt',
        href: '#',
        price: '25',
        color: 'ConferenceTee',
        size: 'Large',
        imageSrc: 'https://tailwindui.com/plus/img/ecommerce-images/checkout-page-02-product-01.jpg',
        imageAlt: "Front of the conference t-shirt in blue.",
    },
];
export default function Content() {
    const [country, setCountry] = useState('US');

    const handleCountryChange = useCallback((event: ChangeEvent<HTMLSelectElement>) => {
        setCountry(event.target.value);
    }, []);

    const locale = useMemo(() => {
        switch (country) {
            case "US":
                return "en"
            case "GE":
                return "de"
            case "FR":
                return "fr"
            case "IT":
                return "it"
            case "SP":
                return "es"
            case "PT":
                return "pt"
            default:
                return "en"
        }
    }, [country])

    const component = useRef<any>(null)
    const button = useRef<any>(null)

    const option = useMemo(() => {
        return {
            layout: {
                type: 'tab',
                showRadioButtons: true,
            },
            paymentMethods: {
                sortOrder: ["paypal_express_checkout", "apple_pay", "card"]
            },
            locale: locale,
            style: {
                theme: {
                    hasBackground: 'false',
                    accentColor: "gold",
                    appearance: "light"
                },
                variables: {
                    colorBackground: "#ffff00",
                    spacing: 2,
                    accentIndicator: "#ffff00",
                }
            },
        }
    }, [locale])

    useEffect(() => {
        if (component.current !== null) {
            component.current?.update(option)
        }
    }, [option])

    const retrievePaymentIntent = async () => {
        return await PaymentIntentStoreImpl.create(10, "USD");
    }

    const initializeChargebee = () => {
        return window.Chargebee.init({
            site: process.env
                .NEXT_PUBLIC_CHARGEBEE_SITE as string,
            publishableKey: process.env
                .NEXT_PUBLIC_CHARGEBEE_KEYS_PUBLISHABLE as string
        });
    }

    useEffect(() => {
        const initializePaymentComponent = async () => {
            const [paymentIntent, chargebee] = await Promise.all([retrievePaymentIntent(), initializeChargebee()]);
            const components = chargebee.components({});
            const componentCallbacks = {
                onPaymentMethodChange: () => {
                },
                onSuccess: () => {
                },
                onError: () => {
                },
            }
            const componentOptions = {
                paymentIntent: paymentIntent,
                paymentIntentId: paymentIntent.id,
                ...option
            }

            const thisComponent = components.create(
                'payment',
                componentOptions,
                componentCallbacks,
            );
            thisComponent.mount("#payment-component");
            component.current = thisComponent;

            const buttonOptions = {}

            const buttonCallbacks = {
                onError: () => {
                },
                onClose: () => {
                },
            }
            const thisButton = components.create(
                'payment-button',
                buttonOptions,
                buttonCallbacks
            );
            thisButton.mount("#payment-button-component");
            button.current = thisButton;
        }
        if (component.current === null) {
            initializePaymentComponent();
        }
    }, [option])

    return (
        <main className="mx-auto max-w-7xl px-4 pb-24 pt-16 sm:px-6 lg:px-8">
            <div className="mx-auto max-w-2xl lg:max-w-none">
                <h1 className="sr-only">Checkout</h1>

                <form className="lg:grid lg:grid-cols-2 lg:gap-x-12 xl:gap-x-16">
                    <div>
                        <div>
                            <h2 className="text-lg font-medium text-gray-900">Shipping information</h2>

                            <div className="mt-4 grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
                                <div>
                                    <label htmlFor="first-name" className="block text-sm font-medium text-gray-700">
                                        First name
                                    </label>
                                    <div className="mt-1">
                                        <input
                                            id="first-name"
                                            name="first-name"
                                            type="text"
                                            autoComplete="given-name"
                                            className="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label htmlFor="last-name" className="block text-sm font-medium text-gray-700">
                                        Last name
                                    </label>
                                    <div className="mt-1">
                                        <input
                                            id="last-name"
                                            name="last-name"
                                            type="text"
                                            autoComplete="family-name"
                                            className="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        />
                                    </div>
                                </div>

                                <div className="sm:col-span-2">
                                    <label htmlFor="address" className="block text-sm font-medium text-gray-700">
                                        Address
                                    </label>
                                    <div className="mt-1">
                                        <input
                                            id="address"
                                            name="address"
                                            type="text"
                                            autoComplete="street-address"
                                            className="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label htmlFor="city" className="block text-sm font-medium text-gray-700">
                                        City
                                    </label>
                                    <div className="mt-1">
                                        <input
                                            id="city"
                                            name="city"
                                            type="text"
                                            autoComplete="address-level2"
                                            className="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label htmlFor="country" className="block text-sm font-medium text-gray-700">
                                        Country
                                    </label>
                                    <div className="mt-1">
                                        <select
                                            id="country"
                                            name="country"
                                            value={country}
                                            onChange={handleCountryChange}
                                            autoComplete="country-name"
                                            className="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        >
                                            <option value="US">United States</option>
                                            <option value="GE">Germany</option>
                                            <option value="FR">France</option>
                                            <option value="IT">Italy</option>
                                            <option value="SP">Spain</option>
                                            <option value="PT">Portugal</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div className="mt-10 border-t border-gray-200 pt-10">
                            <h2 className="text-lg font-medium text-gray-900">Payment</h2>
                            <div id="payment-component" className="mt-2"></div>
                        </div>
                    </div>

                    {/* Order summary */}
                    <div className="mt-10 lg:mt-0">
                        <h2 className="text-lg font-medium text-gray-900">Order summary</h2>

                        <div className="mt-4 rounded-lg border border-gray-200 bg-white shadow-sm">
                            <h3 className="sr-only">Items in your cart</h3>
                            <ul role="list" className="divide-y divide-gray-200">
                                {products.map((product) => (
                                    <li key={product.id} className="flex px-4 py-6 sm:px-6">
                                        <div className="flex-shrink-0">
                                            <img alt={product.imageAlt} src={product.imageSrc}
                                                 className="w-20 rounded-md"/>
                                        </div>

                                        <div className="ml-6 flex flex-1 flex-col">
                                            <div className="flex">
                                                <div className="min-w-0 flex-1">
                                                    <h4 className="text-sm">
                                                        <div
                                                            className="font-medium text-gray-700 hover:text-gray-800">
                                                            {product.title}
                                                        </div>
                                                    </h4>
                                                    <p className="mt-1 text-sm text-gray-500">{product.color}</p>
                                                    <p className="mt-1 text-sm text-gray-500">{product.size}</p>
                                                </div>

                                            </div>

                                            <div className="flex flex-1 items-end justify-between pt-2">
                                                <p className="mt-1 text-sm font-medium text-gray-900">{product.price}</p>

                                                <div className="ml-4">
                                                    <label htmlFor="quantity" className="sr-only">
                                                        Quantity
                                                    </label>
                                                    <select
                                                        id="quantity"
                                                        name="quantity"
                                                        className="rounded-md border border-gray-300 text-left text-base font-medium text-gray-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 sm:text-sm"
                                                    >
                                                        <option value={1}>1</option>
                                                        <option value={2}>2</option>
                                                        <option value={3}>3</option>
                                                        <option value={4}>4</option>
                                                        <option value={5}>5</option>
                                                        <option value={6}>6</option>
                                                        <option value={7}>7</option>
                                                        <option value={8}>8</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                ))}
                            </ul>
                            <dl className="space-y-6 border-t border-gray-200 px-4 py-6 sm:px-6">
                                <div className="flex items-center justify-between">
                                    <dt className="text-sm">Subtotal</dt>
                                    <dd className="text-sm font-medium text-gray-900">$64.00</dd>
                                </div>
                                <div className="flex items-center justify-between">
                                    <dt className="text-sm">Taxes</dt>
                                    <dd className="text-sm font-medium text-gray-900">$5.52</dd>
                                </div>
                                <div className="flex items-center justify-between border-t border-gray-200 pt-6">
                                    <dt className="text-base font-medium">Total</dt>
                                    <dd className="text-base font-medium text-gray-900">$75.52</dd>
                                </div>
                            </dl>

                            <div className="border-t border-gray-200 px-4 py-6 sm:px-6">
                                <div id="payment-button-component"/>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    )
}