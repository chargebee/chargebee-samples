const http = require('http');
const express = require('express');
var chargebee = require("chargebee");

var cors = require("cors");
const app = express();
const PORT = 3002;

app.use(cors());

app.get('/create_payment_intent', async (req, res) => {
    const site = 'something-test'; // Replace with your site
    const siteApiKey = 'test_xCxVKIPVFmX2bjyJkiNKKTdNiXE8AiEk'; // Replace with your API key

    const url = `https://${site}.devcb.in/api/v2/payment_intents`;
    const amount = 5000;
    const currencyCode = 'USD';
    try {
        const result = await fetch(url, {
            method: 'POST',
            headers: {
                'Authorization': 'Basic ' + btoa(`${siteApiKey}:`),
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                amount: amount,
                currency_code: currencyCode
            })
        })
        const apple = await result.json();  
        res.status(200);
        res.send(apple.payment_intent);
    }
    catch(error){
        res.status(200);
        res.send({
            "id": "6m54BUPZwBR5ATQRXjg3TmsX1DbJFQRxWLsV7kezoY2Twd",
            "status": "inited",
            "amount": 5000,
            "gateway_account_id": "gw_1mklhiAQqsmQwP2F2",
            "expires_at": 1727443227,
            "payment_method_type": "card",
            "created_at": 1727441427,
            "modified_at": 1727441427,
            "updated_at": 1727441427,
            "resource_version": 1727441427076,
            "object": "payment_intent",
            "currency_code": "USD",
            "gateway": "chargebee"
        });
    }
    
});

app.listen(PORT, (error) => {
    if (!error)
        console.log("Server is Successfully Running, and App is listening on port " + PORT)
    else
        console.log("Error occurred, server can't start", error);
}
);



