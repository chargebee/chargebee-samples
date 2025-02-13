'use client'

import React, {ChangeEvent, useCallback, useEffect, useMemo, useRef, useState} from "react";
import {PaymentIntentStoreImpl} from "@/store/payment-intent-store-impl";
import Script from "next/script";

type Components = {
    create: (...args: unknown[]) => { mount: (...args: unknown[]) => unknown, update: (...args: unknown[]) => unknown }
}

type Button = { update: (payload: unknown) => void }
type Component = { update: (payload: unknown) => void }
type ChargebeeInstance = object
declare global {
    interface Window {
        Chargebee: { init: (options: { site: string, publishableKey: string }) => ChargebeeInstance };
    }
}

const products = [
    {
        id: 1,
        title: 'Personal Basic - Monthly',
        price: '150',
        type: 'plan',
    },
    {
        id: 2,
        title: 'Implementation Fee',
        price: '25',
        type: 'charge',
    },
];


const form =  {
    customer : {
        firstName : 'default',
    },
    billingAddress : {
        phone : {
            label : 'Enter Phone',
            required : true
        }
    },
    shippingAddress : {},
    payment : {}
}

const context = {
    cart: {
        lineItems: products.map(product => ({
            id: String(product.id),
            name: product.title,
            description: "",
            type: product.type,
        }))
    },
    customer: {
        firstName : 'chargebee-user'
    }
};


export default function Content() {
    const [cart, setCart] = useState<Record<number, number>>({1: 5, 2: 2})

    // @ts-expect-error Ignoring for now
    const handleQuantityChange = (id: number, event) => {
        const newQuantity = Number(event.target.value);
        const productId = id;
        setCart((prevCart) => ({
            ...prevCart,
            [productId]: newQuantity,
        }));
    };

    const [country, setCountry] = useState('US');
    const handleCountryChange = useCallback((event: ChangeEvent<HTMLSelectElement>) => {
        setCountry(event.target.value);
    }, []);

    const [scriptLoaded, setScriptLoaded] = useState(false);

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
    const component = useRef<Component | null>(null)
    const button = useRef<Button | null>(null)
    const subtotal = useMemo(() => {
        let total = 0;
        for (const [id, quantity] of Object.entries(cart)) {
            const product = products.find((item) => item.id === Number(id))
            total = total += Number(product!.price) * quantity;
        }
        return total;
    }, [cart])
    const [style, setStyle] = useState({
        theme: {
            accentColor: "gold",
            appearance: "light"
        },
        variables: {},
        rules: {}
    });

    const option: object = useMemo(() => {
        let layout = {
            type: 'tab',
            showRadioButtons: false,
        };
        let sortOrder: string[] = [];
        let allowed: string[] | undefined = undefined;
        switch (country) {
            case "US":
                sortOrder = ["card", "paypal_express_checkout", "google_pay"]
                allowed = ["paypal_express_checkout", "apple_pay", "card"]
                layout = {
                    type: 'tab',
                    showRadioButtons: true,
                }
                break;
            case "GE":
                allowed = ["card", "apple_pay"]
                sortOrder = ["apple_pay"]
                layout = {
                    type: 'accordion',
                    showRadioButtons: true,
                }
                break
            case "FR":
                allowed = ["card", "paypal_express_checkout","klarna_pay_now"]
                sortOrder = ["paypal_express_checkout"]
                layout = {
                    type: 'tab',
                    showRadioButtons: false,
                }
                break
            case "IT":
                allowed = ["card"]
                break
            case "PT":
                allowed = ["card", "apple_pay"]
                sortOrder = ["apple_pay"]
                layout = {
                    type: 'accordion',
                    showRadioButtons: true,
                }
                break
            case "SP":
            default:
                sortOrder = ["google_pay", "paypal_express_checkout", "apple_pay"]
                allowed = ["paypal_express_checkout", "google_pay", "card"]
                layout = {
                    type: 'tab',
                    showRadioButtons: true,
                }
        }

        if (subtotal > 1000) {
            allowed = ['card']
        }

        return {
            layout : layout,
            paymentMethods: {
                sortOrder: sortOrder,
                allowed: allowed
            },
            locale: locale,
            style : style,
        }
    }, [country, locale, style, subtotal])

    useEffect(() => {
        if (component.current !== null) {
            component.current?.update(option)
        }
        if(button.current !== null){
            button.current?.update({
                locale : option.locale
            })
        }
    }, [option])

    const retrievePaymentIntent = async () => {
        return await PaymentIntentStoreImpl.create(1000, "EUR");
    }

    const initializeChargebee = () => {
        return window.Chargebee.init({
            site: process.env
                .NEXT_PUBLIC_CHARGEBEE_SITE as string,
            publishableKey: process.env
                .NEXT_PUBLIC_CHARGEBEE_KEYS_PUBLISHABLE as string
        }) as { components: (args: unknown) => Components };
    }

    const onPaymentMethodChange = useCallback((data: string) => {
        const thisStyle = {
            theme: {
                accentColor: "gold",
                appearance: "light"
            },
            variables: {
                defaultFontFamily: 'Courier, monospace;',
                colorBackground: 'rgb(224 231 255)',
                spacing: 2,
            },
            rules: {
                ".g-RadioCardsItem": {
                    background:
                        "linear-gradient(150deg, transparent 60%, var(--gray-9) 100%)",
                },
                ".g-Section:where(.g-size-1)": {
                    "padding-top": "0px",
                    "padding-bottom": "0px"
                }
            }
        }
        switch (data) {
            case "card":
                thisStyle.theme.accentColor = "blue"
                thisStyle.rules[".g-RadioCardsItem"] = {
                    background: "linear-gradient(150deg, transparent 60%, rgba(255, 94, 77, 1) 100%)",
                };
                break;
            case "paypal_express_checkout":
                thisStyle.theme.accentColor = "yellow"
                thisStyle.rules[".g-RadioCardsItem"] = {
                    background: "linear-gradient(150deg, transparent 60%, rgba(0, 204, 255, 1) 100%)",
                };
                break;
            case "google_pay":
                thisStyle.theme.appearance = "purple"
                thisStyle.rules[".g-RadioCardsItem"] = {
                    background: "linear-gradient(150deg, transparent 60%, rgba(34, 193, 195, 1) 100%)",
                };
                break;
            case "apple_pay":
                thisStyle.theme.appearance = "jade"
                thisStyle.rules[".g-RadioCardsItem"] = {
                    background: "linear-gradient(150deg, transparent 60%, rgba(138, 43, 226, 1) 100%)",
                };
                break;
        }

        setStyle((prevState) => ({
            ...prevState,
            ...thisStyle,
        }));

    }, [])


    useEffect(() => {
        const initializePaymentComponent = async () => {
            const [paymentIntent, chargebee] = await Promise.all([retrievePaymentIntent(), initializeChargebee()]);
            const components = chargebee.components({});
            if (component.current === null) {
                const componentOptions = {
                    paymentIntent: {id : paymentIntent.id},
                    form : form,
                    context : context,
                    ...option
                }
                const componentCallbacks = {
                    onPaymentMethodChange,
                    onSuccess: (data: never) => {
                        console.log("GTA", data)
                    },
                    onError: (data: never) => {
                        console.log("GTA", data)
                    },
                    onButtonClick : () => {
                        return Promise.resolve()
                    }
                }

                const thisComponent = components.create(
                    'payment',
                    componentOptions,
                    componentCallbacks,
                );
                thisComponent.mount("#payment-component");
                component.current = thisComponent;
            }
            if (button.current === null) {
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
        }

        if (scriptLoaded && (component.current === null || button.current === null)) {
            initializePaymentComponent();
        }
    }, [onPaymentMethodChange, option, scriptLoaded])

    return (
        <main className="mx-auto max-w-7xl px-4 pb-24 pt-8 sm:px-6 lg:px-8">
            <Script
                onLoad={() => {
                    setScriptLoaded(true);
                    console.log("Script loaded successfully!");
                }}
                onError={() => {
                    console.error("Error loading script.");
                }}
                src={process.env
                    .NEXT_PUBLIC_CHARGEBEE_JS_URL as string} async></Script>
            <div className="mx-auto max-w-2xl lg:max-w-none">
                <h1 className="sr-only">Checkout</h1>

                <form className="lg:grid lg:grid-cols-2 lg:gap-x-12 xl:gap-x-16">
                    <div>
                        <div>
                            <h2 className="text-lg font-medium text-gray-900">Shipping information</h2>

                            <div className="mt-2 grid grid-cols-1 gap-y-3 sm:grid-cols-2 sm:gap-x-4">
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
                                            value={"Chargebee"}
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
                                            value={"Saas"}
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
                        <div className="mt-5 border-t border-gray-200 pt-4">
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
                                    <li key={product.id} className="flex py-6 ">
                                        <div className="flex flex-1 flex-col">
                                            <div className="flex">
                                                <div className="min-w-0 flex-1">
                                                    <div className="ml-4 flex flex-1 flex-col justify-between sm:ml-6">
                                                        <div
                                                            className="relative pr-9 sm:grid sm:grid-cols-2 sm:gap-x-6 sm:pr-0">
                                                            <div>
                                                                <div className="flex justify-between">
                                                                    <h3 className="text-sm">
                                                                        <div
                                                                            className="font-medium text-gray-700 hover:text-gray-800">
                                                                            {product.title}
                                                                        </div>
                                                                    </h3>
                                                                </div>
                                                                <div className="mt-1 flex text-sm">
                                                                  <span
                                                                      className="inline-flex items-center rounded-md bg-green-100 px-2 py-1 text-xs font-medium text-green-700">
                                                                    {product.type}
                                                                  </span>
                                                                </div>
                                                                <p className="mt-1 text-sm font-medium text-gray-900">{cart[product.id]} x
                                                                    ${product.price}.00</p>
                                                            </div>

                                                            <div className="mt-4 sm:mt-0 sm:pr-9">
                                                                <label htmlFor={`quantity-${product.id}`}
                                                                       className="sr-only">
                                                                    Quantity, {product.id}
                                                                </label>
                                                                <select
                                                                    id={`quantity-${product.id}`}
                                                                    name={`quantity-${product.id}`}
                                                                    value={cart[product.id] || 1}
                                                                    onChange={(event) => {
                                                                        handleQuantityChange(product.id, event)
                                                                    }}
                                                                    className="max-w-full rounded-md border border-gray-300 py-1.5 text-left text-base font-medium leading-5 text-gray-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 sm:text-sm"
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
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                ))}
                            </ul>
                            <dl className="space-y-6 border-t border-gray-200 px-4 py-6 sm:px-6">
                                <div className="flex items-center justify-between">
                                    <dt className="text-sm">Subtotal</dt>
                                    <dd className="text-sm font-medium text-gray-900">${subtotal}.00</dd>
                                </div>
                                <div className="flex items-center justify-between">
                                    <dt className="text-sm">Taxes</dt>
                                    <dd className="text-sm font-medium text-gray-900">${subtotal * (5 / 100)}</dd>
                                </div>
                                <div className="flex items-center justify-between border-t border-gray-200 pt-6">
                                    <dt className="text-base font-medium">Total</dt>
                                    <dd className="text-base font-medium text-gray-900">${subtotal + (subtotal * (5 / 100))}</dd>
                                </div>
                            </dl>

                            <div className="border-t border-gray-200 px-4 py-6 sm:px-6">
                                <div style={{"height": "100px"}} id="payment-button-component"/>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    )
}
