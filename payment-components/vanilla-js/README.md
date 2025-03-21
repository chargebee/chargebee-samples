# Use Payment Components with Advanced Routing

Chargebee.js [Payment Components](https://www.chargebee.com/checkout-portal-docs/payment-components.html) supports the [Advanced Routing](https://www.chargebee.com/checkout-portal-docs/payment-components-advanced-routing.html) feature in Chargebee Billing. If you have multiple [payment gateways](https://www.chargebee.com/docs/payments/2.0/gateway_settings.html) configured, Advanced Routing allows you to direct customer payments through specific gateways based on [rules defined in Billing](https://www.chargebee.com/docs/payments/2.0/advanced-routing.html#rules).

This sample application demonstrates the use of Advanced Routing using Payment Components.

## Run the sample app locally

Follow the steps below to set up the app in your local environment:

1. Clone this repository.
2. Switch to branch `payment-components-advanced-routing`.
3. Copy the file `payment-component/vanilla-js/apps/client/env.js.example` to `env.js` in the same directory.
4. In `payment-component/vanilla-js/apps/client/env.js`:
    - Replace `your-chargebee-subdomain` with the subdomain of your Chargebee [Test site](https://www.chargebee.com/docs/2.0/sites-intro.html#test-site). For example, if your Chargebee Test site is `acme-test.chargebee.com`, then enter `acme-test`.
    - Replace `your-publishable-api-key` with a [publishable API](https://www.chargebee.com/docs/2.0/api_keys.html#types-of-api-keys_publishable-key) key obtained from the Chargebee web app.

5. Copy the file `payment-component/vanilla-js/apps/server/env.js.example` to `env.js` in the same directory.
6. In `payment-component/vanilla-js/apps/server/env.js`:
    - Replace `your-chargebee-subdomain` with the subdomain of your Chargebee Test site. Refer to the example in the previous step.
    - Replace `your-full-access-api-key` with a [full access API](https://www.chargebee.com/docs/2.0/api_keys.html#types-of-api-keys_full-access-key) key obtained from the Chargebee web app.

7. Change the working directory to `payment-component/vanilla-js`.
8. Use [nvm](https://github.com/nvm-sh/nvm/blob/master/README.md) to change the Node.js version to v18.
    ```shell
    nvm use 18
    ```
9. Install dependencies (required only when setting up the app for the first time).
    ```shell
    pnpm i
    ```
10. Start the development servers.
    ```shell
    pnpm run dev
    ```
11. The last two lines of the output should look like this:
    ```shell
    apps/client dev: Checkout page running at http://localhost:8081/
    apps/server dev: API server is running at http://localhost:8082/
    ```
12. Launch the checkout page of the sample app by navigating to `http://localhost:8081/`.

---
