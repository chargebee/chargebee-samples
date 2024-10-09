# Accept a payment

Chargebee payment component is a ready-to-use UI component with input fields and buttons, for building your checkout process. This feature is part of Chargebee.js, which securely tokenizes sensitive data within the component, ensuring that it never interacts with your server.

## Run the sample app locally

Follow the steps below to setup the payment components quickstart app on your local environment.

1. Clone the sample app repository.
2. Change working directory to `payment-component/vanilla-js`
3. Edit `payment-component/vanilla-js/apps/client/env.js` and specify your Chargebee site subdomain and publishable API key.
4. Edit `payment-component/vanilla-js/apps/server/env.js` and specify your Chargebee site subdomain and full access API key.
3. Install dependencies. This is required only when setting up this app for the first time.
   ```shell
   pnpm i
   ```
4. Start development servers.
    ```shell
   pnpm run dev
   ```
5. The last two lines of the output should look as below:
    ```shell
    apps/client dev: Checkout page running at http://localhost:8084/
    apps/server dev: Api server is running at http://localhost:8085/
    ```
6. Launch the checkout page of the sample app by browsing to `http://localhost:8084/`.



