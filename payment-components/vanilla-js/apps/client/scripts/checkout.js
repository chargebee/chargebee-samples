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

//Button click event listeners.
const firstBtn = document.querySelector('#first-btn');
const secondBtn = document.querySelector('#second-btn');
firstBtn.addEventListener('click',() => checkout(0));
secondBtn.addEventListener('click',() => checkout(1));

//Executes when user clicks any of the checkout buttons.
async function checkout(index){
    console.log(`CheckoutData: `,index)
    try{
        if(!paymentIntent){
            initializeChargebee();
            await createPaymentIntent(index);
            createPaymentComponent(index);
        }
        else{
            await getPaymentIntent(paymentIntent.id);
            console.log("`payment_intent.status`: ",paymentIntent.status)
            switch(paymentIntent.status){
                case 'inited':
                case 'in_progress':
                    await updatePaymentIntent(paymentIntent.id,index);
                    updatePaymentComponent(index);
                    break;
                case 'authorized':
                    //Caution! `payment_intent` is authorized and cannot be updated via API. 
                    //Depending on the payment gateway and payment method, the payment may have been collected at this stage.
                    //If collected, a refund will be initiated in 30 minutes so long as the `payment_intent` is not consumed.
                    //If you still want to proceed, warn the user and start over with a new `payment_intent`.
                    break;
                case 'consumed':
                    //Caution! `payment_intent` has been consumed and the payment has been collected.
                    //This indicates that a subscription or charge has been created in Chargebee Billing.
                    //Inform the user and provision the service.
                    break;
                case 'expired':    
                    //It has been 30 minutes since the `payment_intent` was created.
                    //Start over with a new `payment_intent`.
                    await createPaymentIntent(index);
                    updatePaymentComponent(index);
            }
        }
    } catch (error) {
        console.error(error.message);
    }
}

//Payment Component wrappers

function initializeChargebee() {
    try {
        chargebee = window.Chargebee.init({
            site: env.site,
            publishableKey: env.publishableKey
        });
    }
    catch(error) {
        console.error(error.message);
    }
}

function createPaymentComponent(index) {
    components = chargebee.components({});
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

async function updatePaymentComponent(index) {
    setPaymentComponentOptions(index);
    paymentComponent.update(paymentComponentOptions);
}

function setPaymentComponentOptions(index) {
    switch(index){
        case 0:
            paymentComponentOptions = {
                paymentIntent: paymentIntent,
                form: {
                    customer: {
                      firstName: {
                        required: true
                      },
                      lastName: "default"
                    }
                },
                layout: {
                    type: 'accordion',
                    showRadioButtons: true,
                },
                paymentMethods: {
                    sortOrder: ["card"]
                },
                context: {
                    cart: {
                        lineItems: [{ id: "plan-a", type: "plan" }] // Advanced Routing variable.
                    },
                    customer: {
                        firstName: "Jane",
                        lastName: "Doe",
                        billingAddress: {
                          firstName: "Jane",
                          lastName: "Doe",
                          phone: "555-123-4567",
                          addressLine1: "123 Main St",
                          addressLine2: "Apt 4B",
                          addressLine3: "",
                          city: "Springfield",
                          state: "Illinois",
                          stateCode: "IL",
                          countryCode: "US", // Advanced Routing variable.
                          zip: "62701"
                        },
                        shippingAddress: {
                            firstName: "Jane",
                            lastName: "Doe",
                            phone: "555-123-4567",
                            addressLine1: "123 Main St",
                            addressLine2: "Apt 4B",
                            addressLine3: "",
                            city: "Springfield",
                            state: "Illinois",
                            stateCode: "IL",
                            countryCode: "US", // Advanced Routing variable.
                            zip: "62701"
                        }
                    }
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
                },
                context: {
                    cart: {
                        lineItems: [{ id: "plan-b", type: "plan" }], // Advanced Routing variable.
                    },
                    customer: {
                        firstName: "Erika",
                        lastName: "Mustermann",
                        billingAddress: { 
                            "firstName": "Erika",
                            "lastName": "Mustermann",
                            "phone": "634-067-4573",
                            "addressLine1": "Arster Hemm 59",
                            "city": "Bremen",
                            "stateCode": "ON",
                            "countryCode": "DE", // Advanced Routing variable.
                            "zip": "28279"
                        },
                        shippingAddress: {
                            "firstName": "Erika",
                            "lastName": "Mustermann",
                            "phone": "634-067-4573",
                            "addressLine1": "Arster Hemm 59",
                            "city": "Bremen",
                            "stateCode": "ON",
                            "countryCode": "DE", // Advanced Routing variable.
                            "zip": "28279"
                          }
                    }
                }
            }
    }  
}

//Payment Intent wrappers
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

//Payment Component callbacks

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
