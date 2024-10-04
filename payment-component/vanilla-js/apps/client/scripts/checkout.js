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
            site: "hp-internal-us-test",
            publishableKey: "test_w9wYSMChRqylCiz2bacqUgYgxIFnVfZZ",
        })
        const components = chargebee.components({});
        const onSuccess = (payment_intent) => {
            console.log(payment_intent);
        }
        const onError = (error) => {
            // handle payment errors here
            console.log(error);
        }
        const onPaymentMethodChange = (error) => {
            // handle payment errors here
            console.log(error);
        }
        const paymentComponentOptions = {
            paymentIntentId: json.id,
            layout: {
                type: 'tab',
                showRadioButtons: true,
            },
            paymentMethods: {
                sortOrder: ["paypal_express_checkout", "card"],
                allowed: ["paypal_express_checkout", "card", "google_pay"]
            },
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
            },
        }
        const paymentComponent = components.create(
            'payment',
            paymentComponentOptions,
            {
                onError,
                onSuccess,
                onPaymentMethodChange
            },
        );
        paymentComponent.mount("#payment-component");

        const paymentButtonComponent = components.create(
            'payment-button',
            {},
            {onError},
        );
        paymentButtonComponent.mount("#payment-button-component");
    } catch (error) {
        console.error(error.message);
    }
}
getData();