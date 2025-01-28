const http = require('http');
const express = require('express');
const cors = require("cors");
const bodyParser = require('body-parser');

const app = express();
const PORT = process.env.PORT || 8082;
const env = require('./env.js');


app.use(cors());
app.use(bodyParser.json());

app.post('/portal_sessions0', async (req, res) => {

    const url = `https://${env.site0}.chargebee.com/api/v2/portal_sessions`;
    try {
        const result = await fetch(url, {
            method: 'POST',
            headers: {
                'Authorization': 'Basic ' + btoa(`${env.apiKey0}:`),
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                'customer[id]': '169vzeUb5T6nN30X'
            })
        })
        const response = await result.json();
        console.log(response)
        res.status(200);
        res.send(response.portal_session);
    } catch (error) {
        console.log(error)
        res.status(500);
        res.send(error);
    }
});

app.post('/portal_sessions1', async (req, res) => {

    const url = `https://${env.site1}.chargebee.com/api/v2/portal_sessions`;
    try {
        const result = await fetch(url, {
            method: 'POST',
            headers: {
                'Authorization': 'Basic ' + btoa(`${env.apiKey1}:`),
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                'customer[id]': '16CRfyUb3M0kfceQ'
            })
        })
        const response = await result.json();
        console.log(response)
        res.status(200);
        res.send(response.portal_session);
    } catch (error) {
        console.log(error)
        res.status(500);
        res.send(error);
    }
});




app.listen(PORT, () => {
    console.log(`Api Server is running at http://localhost:${PORT}/`);
});



