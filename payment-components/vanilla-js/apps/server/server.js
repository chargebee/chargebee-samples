const http = require('http');
const express = require('express');
const cors = require("cors");
const bodyParser = require('body-parser');

const app = express();
const PORT = process.env.PORT || 8082;
const env = require('./env.js');


app.use(cors());
app.use(bodyParser.json());

app.post('/submit', async (req, res) => {
    const paymentIntentId = req.body.payment_intent_id;
    try {
        const createCustomerUri = `https://${env.site}.chargebee.com/api/v2/customers`;
        const createCustomerResult = await fetch(createCustomerUri, {
            method: 'POST',
            headers: {
                'Authorization': 'Basic ' + btoa(`${env.apiKey}:`),
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({})
        })
        const customerResponse = await createCustomerResult.json();
        const customer = customerResponse.customer;

        const createSubscriptionUri = `https://${env.site}.chargebee.com/api/v2/customers/` + customer.id + '/subscription_for_items';
        const createSubscriptionResult = await fetch(createSubscriptionUri, {
            method: 'POST',
            headers: {
                'Authorization': 'Basic ' + btoa(`${env.apiKey}:`),
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                'subscription_items[item_price_id][0]': 'Non-Zero-Dollar-Plan-USD-Monthly',
                'subscription_items[quantity][0]': '1',
                'payment_intent[id]': paymentIntentId
            })
        })

        const subscriptionResponse = await createSubscriptionResult.json();
        const subscription = subscriptionResponse.subscription;

        res.status(200);
        res.send({customer: customer, subscription: subscription});
    } catch (error) {
        console.log(error)
        res.status(500);
        res.send(error);
    }
});

app.get('/payment-intent/:paymentIntentId', async (req, res) => {
    const paymentIntentId = req.params.paymentIntentId;
    const url = `https://${env.site}.chargebee.com/api/v2/payment_intents/${paymentIntentId}`;
    try {
        const result = await fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': 'Basic ' + btoa(`${env.apiKey}:`),
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
        const response = await result.json();
        console.log("Get intent \n",response);
        res.status(200);
        res.send(response.payment_intent);
    } catch (error) {
        res.status(500);
        console.log("Get intent error: ",error);
        res.send(error);
    }
});


app.post('/payment-intent', async (req, res) => {

    const url = `https://${env.site}.chargebee.com/api/v2/payment_intents`;
    const checkoutData = req.body;
    try {
        const result = await fetch(url, {
            method: 'POST',
            headers: {
                'Authorization': 'Basic ' + btoa(`${env.apiKey}:`),
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                amount: calculateAmount(checkoutData),
                currency_code: getCurrencyCode(checkoutData)
            })
        })
        const response = await result.json();
        console.log("Create intent \n",response)
        res.status(200);
        res.send(response.payment_intent);
    } catch (error) {
        console.log(error)
        res.status(500);
        res.send(error);
    }
});

app.put('/payment-intent/:paymentIntentId', async (req, res) => {
    const paymentIntentId = req.params.paymentIntentId;
    const checkoutData = req.body;
    const url = `https://${env.site}.chargebee.com/api/v2/payment_intents/${paymentIntentId}`;
    try {
        const result = await fetch(url, {
            method: 'POST',
            headers: {
                'Authorization': 'Basic ' + btoa(`${env.apiKey}:`),
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                amount: calculateAmountv2(checkoutData),
                currency_code: getCurrencyCode(checkoutData)
            })
        })
        const response = await result.json();
        console.log("Update intent \n",response);
        res.status(200);
        res.send(response.payment_intent);
    } catch (error) {
        res.status(500);
        console.log("Update intent error ",error);
        res.send(error);
    }
});

function calculateAmount(checkoutData){
    return 5000;
}

function calculateAmountv2(checkoutData){
    return 6000;
}

function getCurrencyCode(checkoutData){
    return "USD";
}


app.listen(PORT, () => {
    console.log(`Api Server is running at http://localhost:${PORT}/`);
});



