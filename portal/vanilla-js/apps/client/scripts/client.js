let chargebee, chargebeePortal

console.log("Checkout.js")

window.addEventListener("DOMContentLoaded", function() {
    document.getElementById("sdas-btn").addEventListener("click", function() {
        console.log("sdas-btn clicked.")
        Chargebee.tearDown()
        chargebee = window.Chargebee.init({
            site: env.site0,
            isItemsModel: true,
        })
        console.log("chargebee initialized to sdas-test.")
        console.log(chargebee)
        let xyz = new Promise((resolve, reject) => {
            console.log("Requesting portal session.")
            fetch("http://localhost:8082/portal_sessions0", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json", // Set the appropriate content type
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        return reject(new Error("Network response was not ok: " + response.statusText));
                    }
                    return response.json()
                })
                .then(data => {
                    console.log("response data", data)
                    chargebee.setPortalSession(data)
                    console.log("Done.")
                    console.log(chargebee)
                    console.log("Calling createChargebeePortal()")
                    chargebeePortal = chargebee.createChargebeePortal()
                    console.log("Done.")
                    console.log("chargebeePortal", chargebeePortal)
                    console.log("Calling chargebeePortal.open()")
                    chargebeePortal.open()
                    console.log("Done.")
                })
                .catch(error => {
                    reject(new Error("There was a problem with the fetch operation: " + error));
                });
        });
        console.log("Portal session:")
        console.log(xyz)
        console.log("Calling setPortalSession().")
    })

    document.getElementById("johnplus3-btn").addEventListener("click", function() {
        console.log("johnplus3-btn clicked.")
        Chargebee.tearDown()
        chargebee = window.Chargebee.init({
            site: env.site1,
            isItemsModel: true,
        })
        console.log("chargebee initialized to johnplus3-test.")
        console.log(chargebee)
        let xyz = new Promise((resolve, reject) => {
            console.log("Requesting portal session.")
            fetch("http://localhost:8082/portal_sessions1", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json", // Set the appropriate content type
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        return reject(new Error("Network response was not ok: " + response.statusText));
                    }
                    return response.json()
                })
                .then(data => {
                    console.log("response data")
                    console.log(data)
                    console.log("Calling setPortalSession().")
                    chargebee.setPortalSession(data)
                    console.log("Done.")
                    console.log(chargebee)
                    console.log("Calling createChargebeePortal()")
                    chargebeePortal = chargebee.createChargebeePortal()
                    console.log("Done.")
                    console.log(chargebeePortal)
                    console.log("Calling chargebeePortal.open()")
                    chargebeePortal.open()
                    console.log("Done.")
                })
                .catch(error => {
                    reject(new Error("There was a problem with the fetch operation: " + error));
                });
        });
        console.log("Portal session:")
        console.log(xyz)
        console.log("Calling setPortalSession().")
    })

    document.getElementById("teardown-btn").addEventListener("click",function() {
        Chargebee.tearDown()
        console.log("Teardown.")
        console.log(chargebee)
    })

    
})

