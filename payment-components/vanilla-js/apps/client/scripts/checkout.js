const checkoutData = [{
    plan: "plan-a",
    shippingCountry: "US",
    billingCountry: "US"
},
{
    plan: "plan-b",
    shippingCountry: "DE",
    billingCountry: "DE"
}]

let paymentIntent, 
    chargebee, 
    componentOptions, 
    components, 
    paymentComponentOptions, 
    paymentComponent, 
    paymentButtonComponent;

//Event listeners.
const firstBtn = document.querySelector('#first-btn');
const secondBtn = document.querySelector('#second-btn');
firstBtn.addEventListener('click',() => payAndSubscribe(0));
secondBtn.addEventListener('click',() => payAndSubscribe(1));

//Executes when user clicks any of the checkout buttons.
async function payAndSubscribe(index){
    console.log(`CheckoutData: `,index)
    try{
        if(!paymentIntent){
            await initializeChargebee();
            await createPaymentIntent(index);
            createPaymentComponent(index);
        }
        else{
            updatePaymentComponent(index);
        }
    } catch (error) {
        console.error(error.message);
    }
}

//Payment Intent functions
async function createPaymentIntent(index) {
    console.log(`CreateIntent() called.\nIndex:${index}\nData: ${checkoutData}`)
    const url = "http://localhost:8082/payment-intent";
    try {
        const response = await fetch(url,{
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(checkoutData[index])
        });
        if (!response.ok)
            throw new Error(`Response status: ${response.status}`);
        paymentIntent = await response.json();
        console.log(`Payment Intent Created: `,paymentIntent.id);
    }
    catch (error) {
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

        paymentIntent = await response.json();
        console.log("`payment_intent.id`: ",paymentIntent.id);
    }
    catch (error) {
        console.error(error.message);
    }
}

async function updatePaymentIntent(paymentIntentId,index){
    console.log(`updateIntent() called.\nIndex:${index}\nData: ${JSON.stringify(checkoutData[index])}`)
    try{
        const url = `http://localhost:8082/payment-intent/${paymentIntentId}`;
        const response = await fetch(url, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(checkoutData[index])
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

//Chargebee functions

async function initializeChargebee() {
    try {
        chargebee = await window.Chargebee.init({
            site: env.site,
            publishableKey: env.publishableKey
        });
    }
    catch(error) {
        console.error(error.message);
    }
}

//Chargebee Payment Components functions

function createPaymentComponent(index) {
    componentOptions = {
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

    components = chargebee.components(componentOptions);
    setPaymentComponentOptions(index);
    paymentComponent = components.create(
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
    paymentButtonComponent = components.create(
        'payment-button',
        {},
        {
            onError,
            onClose
        },
    ); 

    paymentButtonComponent.mount("#payment-button-component");
}

function setPaymentComponentOptions(index) {
    switch(index){
        case 0:
            paymentComponentOptions = {
                paymentIntent: paymentIntent,
                layout: {
                    type: 'accordion',
                    showRadioButtons: true,
                },
                paymentMethods: {
                    sortOrder: ["card"]
                }
            }
            break;
        case 1:
            paymentComponentOptions = {
                paymentIntent: paymentIntent,
                layout: {
                    type: 'tab',
                    showRadioButtons: false,
                },
                paymentMethods: {
                    sortOrder: ["card"]
                }
            }
    }  
}

async function updatePaymentComponent(index) {
    await getPaymentIntent(paymentIntent.id);
    if(paymentIntent.status != "authorized"){ //Allow changes only if payment has not been collected.
        console.log("`payment_intent.status`: ",paymentIntent.status)
        await updatePaymentIntent(paymentIntent.id,index);
        setPaymentComponentOptions(index);
        // console.log("Foobar")
        paymentComponent.update(paymentComponentOptions)
    }
    else{
        // Warning, payment may have been collected or is being collected!
    } 
}

//Chargebee Payment Components callbacks

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
