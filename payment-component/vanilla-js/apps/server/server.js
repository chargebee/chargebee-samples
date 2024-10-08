const http = require('http');
const express = require('express');
const cors = require("cors");
const app = express();
const PORT = process.env.PORT || 8085;
const env = require('./env.js');

app.use(cors());

app.post('/payment-intent', async (req, res) => {

    const url = `https://${env.site}.devcb.in/api/v2/payment_intents`;
    const amount = 5000;
    const currencyCode = 'USD';
    try {
        const result = await fetch(url, {
            method: 'POST',
            headers: {
                'Authorization': 'Basic ' + btoa(`${env.apiKey}:`),
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                amount: amount,
                currency_code: currencyCode
            })
        })
        const response = await result.json();
        console.log(response)
        res.status(200);
        res.send(response.payment_intent);
    } catch (error) {
        console.log(error)
        res.status(500);
        res.send(error);
    }
});


app.listen(PORT, () => {
    console.log(`Api Server is running at http://localhost:${PORT}/`);
});



