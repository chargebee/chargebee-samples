/**
*
* This is a simple Node.js application
 * which contains a set of endpoints
 * to integrate Razorpay payment gateway
 * with Chargebee using their APIs.
 * Refer to this tutorial [https://www.chargebee.com/tutorials/razorpay-js-integration-with-chargebee-api.html] for detailed steps.
* 
*/
require('dotenv').config();
const express = require('express'),
    cors = require('cors'),
    qs = require('qs'),
    axios = require('axios');

const app = express()
app.use(cors())
app.use(express.json());
app.use(express.urlencoded());


/**
 * Env Variables
 */
 const CB_AUTH_TOKEN = Buffer.from(process.env.CB_API_KEY + ":").toString('base64');
 const CB_AUTH_HEADER = `Basic ${CB_AUTH_TOKEN}`;

 const RP_AUTH_TOKEN = Buffer.from(process.env.RAZORPAY_KEY + ":" + process.env.RAZORPAY_KEY_SECRET).toString('base64');
 const RP_AUTH_HEADER = `Basic ${RP_AUTH_TOKEN}`;


/**
 * [ @Chargebee ]
 * Used to create a new customer in Chargebee App(Product Catalog 2.0)
 * Refer to this API [https://apidocs.chargebee.com/docs/api/customers#create_a_customer].
 */
app.post('/api/create_cb_customer', (req, res) => {
    let data = qs.stringify(req.body);
    let requestOptions = {
        method: 'POST',
        headers: {
            "Authorization": CB_AUTH_HEADER,
            "Content-Type": "application/x-www-form-urlencoded"
        },
        data: data,
        url: `https://${process.env.SITE_ID}.chargebee.com/api/v2/customers`
    };

    axios(requestOptions)
        .then(function (response) {
            res.status(response.status).json(response.data);
        })
        .catch(function (error) {
            console.log(error.response.data);
        });
})


/**
 * [ @Chargebee ]
 * Used to create an estimate for a subscription(Product Catalog 2.0)
 * Refer to this API [https://apidocs.chargebee.com/docs/api/estimates?prod_cat_ver=2&lang=curl#estimate_for_creating_a_subscription].
 */
app.post('/api/create_estimate', (req, res) => {
    let data = qs.stringify(req.body);
    let customerId = req.body.cb_customer_id
    let requestOptions = {
        method: 'POST',
        headers: {
            "Authorization": CB_AUTH_HEADER,
            "Content-Type": "application/x-www-form-urlencoded"
        },
        data: data,
        url: `https://${process.env.SITE_ID}.chargebee.com/api/v2/customers/${customerId}/create_subscription_for_items_estimate`
    };

    axios(requestOptions)
        .then(function (apiResult) {
            res.status(apiResult.status).json(apiResult.data);
        })
        .catch(function (error) {
            console.log(error.response.data);
        });
})

/**
 * [ @Razorpay ]
 * Used to create a customer in Razorpay.
 * Refer to this API [https://razorpay.com/docs/api/customers/#create-a-customer].
 */
app.post('/api/create_razorpay_customer', (req, res) => {
    let data = req.body;
    let requestOptions = {
        method: 'POST',
        headers: {
            "Authorization": RP_AUTH_HEADER,
            "Content-Type": "application/json"
        },
        data: data,
        url: `https://api.razorpay.com/v1/customers`
    };

    axios(requestOptions)
        .then(function (apiResult) {
            res.status(apiResult.status).json(apiResult.data);
        })
        .catch(function (error) {
            console.log(error.response.data);
        });
})

/**
 * [ @Razorpay ]
 * Used to create an order in Razorpay.
 * Refer to this API [https://razorpay.com/docs/api/orders/#create-an-order].
 */
app.post('/api/create_razorpay_order', (req, res) => {
    let data = req.body;
    let requestOptions = {
        method: 'POST',
        headers: {
            "Authorization": RP_AUTH_HEADER,
            "Content-Type": "application/json"
        },
        data: data,
        url: `https://api.razorpay.com/v1/orders`
    };

    axios(requestOptions)
        .then(function (apiResult) {
            res.status(apiResult.status).json(apiResult.data);
        })
        .catch(function (error) {
            console.log(error.response.data);
        });
})

/**
 * [ @Chargebee ]
 * Used to create a subscription(Product Catalog 2.0) for an existing customer within Chargebee
 * Refer to this API [https://apidocs.chargebee.com/docs/api/subscriptions#create_subscription_for_items].
 */
app.post('/api/create_subscription', (req, res) => {
    let data = qs.stringify(req.body);
    let customerId = req.body.cb_customer_id;
    let requestOptions = {
        method: 'POST',
        headers: {
            "Authorization": CB_AUTH_HEADER,
            "Content-Type": "application/x-www-form-urlencoded"
        },
        data: data,
        url: `https://${process.env.SITE_ID}.chargebee.com/api/v2/customers/${customerId}/subscription_for_items`
    };

    axios(requestOptions)
        .then(function (apiResult) {
            res.status(apiResult.status).json(apiResult.data);
        })
        .catch(function (error) {
            console.log(error.response.data);
        });
})

/**
 * dummy base route
 */
app.get('/', (req, res) => {
    res.send("Chargebee's razorpay integration demo site")
});

app.listen(process.env.PORT, () => {
    console.log(`Chargebee demo server running on port ${process.env.PORT}`);
});