/**
*
* This is a basic node js server Created
* for showing a demo on how to implement razorpay
* in chargebee application
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
 * Used to create chargebee's estimation on subscription for items
 */
app.post('/api/create_estimate', (req, res) => {
    let data = qs.stringify(req.body);
    let requestOptions = {
        method: 'POST',
        headers: {
            "Authorization": CB_AUTH_HEADER,
            "Content-Type": "application/x-www-form-urlencoded"
        },
        data: data,
        url: `https://${process.env.SITE_ID}.chargebee.com/api/v2/customers/${process.env.CB_CUSTOMER_ID}/create_subscription_for_items_estimate`
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
 * Used to create customer on razorpay
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
 * Used to create order on razorpay
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
 * Used to create subscription in chargebee site
 */
app.post('/api/create_subscription', (req, res) => {
    let data = qs.stringify(req.body);
    let requestOptions = {
        method: 'POST',
        headers: {
            "Authorization": CB_AUTH_HEADER,
            "Content-Type": "application/x-www-form-urlencoded"
        },
        data: data,
        url: `https://${process.env.SITE_ID}.chargebee.com/api/v2/customers/${process.env.CB_CUSTOMER_ID}/subscription_for_items`
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