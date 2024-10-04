const http = require('http');
const express = require('express');
var chargebee = require("chargebee");

var cors = require("cors");
const app = express();
const PORT = process.env.PORT || 8082;

app.use(cors());

app.post('/payment-intent', async (req, res) => {
    const site = 'hp-internal-us-test';
    const siteApiKey = 'test_Tt6Bcdz6dqV6q9042OfLgs01mSgLKnrfW';

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
        const response = await result.json();
        res.status(200);
        res.send(response.payment_intent);
    }
    catch(error){
        console.log(error)
        res.status(500);
        res.send(error);
    }
});


app.listen(PORT, () => {
    console.log(`Api Server is running at http://localhost:${PORT}/`);
});



