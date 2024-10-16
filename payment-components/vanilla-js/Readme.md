# Collect payment using payment components

The Chargebee payment components are ready-to-use UI components that includes input fields and buttons, designed to help you build your checkout process. This feature is part of Chargebee.js and securely tokenizes sensitive data within the component, ensuring that the data never interacts with your server.

## Run the sample app locally

Follow the steps below to set up the Payment Components Quickstart app in your local environment:

1. Clone this repository.
2. Rename the file `payment-component/vanilla-js/apps/client/env.js.example` to `env.js`.
3. In `payment-component/vanilla-js/apps/client/env.js`:
    - Replace `your-chargebee-subdomain` with the subdomain of your Chargebee [Test site](https://www.chargebee.com/docs/2.0/sites-intro.html#test-site). For example, if your Chargebee Test site is `acme-test.chargebee.com`, then enter `acme-test`.
    - Replace `your-publishable-api-key` with a [publishable API](https://www.chargebee.com/docs/2.0/api_keys.html#types-of-api-keys_publishable-key) key obtained from the Chargebee web app.

4. Rename the file `payment-component/vanilla-js/apps/server/env.js.example` to `env.js`.
5. In `payment-component/vanilla-js/apps/server/env.js`:
    - Replace `your-chargebee-subdomain` with the subdomain of your Chargebee Test site. Refer to the example in the previous step.
    - Replace `your-full-access-api-key` with a [full access API](https://www.chargebee.com/docs/2.0/api_keys.html#types-of-api-keys_full-access-key) key obtained from the Chargebee web app.

6. Change the working directory to `payment-component/vanilla-js`.
7. Use [nvm](https://github.com/nvm-sh/nvm/blob/master/README.md) to change the Node.js version to v18.
    ```shell
    nvm use 18
    ```
8. Install dependencies (required only when setting up the app for the first time).
    ```shell
    pnpm i
    ```
9. Start the development servers.
    ```shell
    pnpm run dev
    ```
10. The last two lines of the output should look like this:
    ```shell
    apps/client dev: Checkout page running at http://localhost:8081/
    apps/server dev: API server is running at http://localhost:8082/
    ```
11. Launch the checkout page of the sample app by navigating to `http://localhost:8081/`.

---
