const onSuccess = async (payment_intent, extra) => {
    const url = "http://localhost:8082/submit";
    console.log(payment_intent, extra);
    try {
        const response = await fetch(url, {
            body: JSON.stringify({payment_intent_id: payment_intent.id}), // Convert to JSON string.
            method: "POST",
            headers: {
                'Content-Type': 'application/json' // Set the content type to JSON.
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
    // Triggered when there is a change in payment method.
    console.log(paymentMethod);
}

const onClose = () => {
    // Triggered when payment or payment button is closed.
    console.log("component closed")
}

async function getData() {
    const url = "http://localhost:8082/payment-intent";
    try {
        const response = await fetch(url, {
            method: "POST",
        });
        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }
        const json = await response.json();
        console.log(json.id);
        const chargebee = window.Chargebee.init({
            site: env.site,
            publishableKey: env.publishableKey,
        });

        const componentOptions = {
            locale: "fr",
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
            paymentIntent: json,
            layout: {
                type: 'tab',
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
    } catch (error) {
        console.error(error.message);
    }
}

getData();
