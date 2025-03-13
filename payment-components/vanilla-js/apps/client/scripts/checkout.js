let checkoutData = {
    itemPrices: ["planA","addonA","addonB"],
    shippingCountry: "US",
    billingCountry: "US"
}

let paymentIntent;

getData();

async function getData() {
    const url = "http://localhost:8082/payment-intent";
    try {
        const response = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(checkoutData)
        });
        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }
        paymentIntent = await response.json();
        console.log(paymentIntent.id);
        const chargebee = window.Chargebee.init({
            site: env.site,
            publishableKey: env.publishableKey,
        });

        const componentOptions = {
            locale: "en",
            style: {
                theme: {
                    accentColor: "gold",
                    appearance: "light"
                },
                variables: {
                    spacing: 2,
                    accentIndicator: "#ffff00",
                }
            }
        };

        const paymentComponentOptions = {
            paymentIntent: paymentIntent,
            layout: {
                type: 'accordion',
                showRadioButtons: true,
            },
            paymentMethods: {
                sortOrder: ["card", "paypal_express_checkout", "google_pay", "apple_pay"],
                allowed: ["apple_pay", "paypal_express_checkout", "card", "google_pay"]
            }
        }

        const components = chargebee.components(componentOptions);

        const paymentComponent = components.create(
            'payment',
            paymentComponentOptions,
            {
                onError,
                onSuccess,
                onPaymentMethodChange,
                onButtonClick,
                onClose
            },
        );

        paymentComponent.mount("#payment-component");

        const paymentButtonComponent = components.create(
            'payment-button',
            {},
            {
                onError,
                onClose
            },
        );

        paymentButtonComponent.mount("#payment-button-component");

        getPaymentIntent(paymentIntent.id);

        setTimeout(function(){
            updateIntent(paymentIntent.id)
            paymentComponent.update({
                layout: {
                    type: 'tab',
                }
            })
        },20000)
    } catch (error) {
        console.error(error.message);
    }
}

async function getPaymentIntent(paymentIntentId){
    console.log("getPaymentIntent() called.");
    try{
        const url = `http://localhost:8082/payment-intent/${paymentIntentId}`;
        const response = await fetch(url, {
            method: "GET",
            headers: {
                "Content_Type": "application/json"
            }
        });

        if(!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }

        const tempPaymentIntent = await response.json();
        console.log("`payment_intent.id`: ",tempPaymentIntent);
    }
    catch (error) {
        console.error(error.message);
    }
}

async function updateIntent(paymentIntentId){
    console.log("updateIntent() called.")
    try{
        const url = `http://localhost:8082/payment-intent/${paymentIntentId}`;
        const response = await fetch(url, {
            method: "PUT",
            headers: {
                "Content_Type": "application/json"
            },
            body: JSON.stringify(checkoutData)
        });

        if(!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }

        paymentIntent = await response.json();
    }
    catch (error) {
        console.error(error.message);
    }
}

const onSuccess = async (payment_intent, extra) => {
    const url = "http://localhost:8082/submit";
    console.log(payment_intent, extra);
    try {
        const response = await fetch(url, {
            body: JSON.stringify({payment_intent_id: payment_intent.id}),
            method: "POST",
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }
        const json = await response.json();
        console.log("checkout-complete", json);
    } catch (error) {
        console.error(error.message);
    }
}

const onError = (error) => {
    // Handle payment and payment button errors here.
    console.log(error);
}

const onPaymentMethodChange = (paymentMethod) => {
    // Triggered on first render of the payment component and when user selects a different payment method.
    console.log("Payment method selected: ",paymentMethod);
}

const onButtonClick = () => {
    // Triggered whenever the user attempts to submit the payment.
    // Validate user input or run any critical checks here.
    // Ensure that this function returns within one second.
    // If your checks pass, return a resolved Promise to initiate payment submission.
    return Promise.resolve()
    // If your checks fail, return a rejected Promise with a `reason` to block payment submission. 
    // For example:
    // return Promise.reject(reason);
    // `onError()` is called automatically with `reason` as the argument.
}

const onClose = () => {
    // Triggered when payment or payment button is closed.
    console.log("component closed")
}
