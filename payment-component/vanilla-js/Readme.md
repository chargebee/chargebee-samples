# Collect payment using payment component

Chargebee payment component is a ready-to-use UI component with input fields and buttons, for building your checkout process. This feature is part of Chargebee.js, which securely tokenizes sensitive data within the component, ensuring that it never interacts with your server.

## Run the sample app locally

Follow the steps below to setup the payment components quickstart app on your local environment.

1. Clone this repository.
2. Rename the file `payment-component/vanilla-js/apps/client/env.js.example` to `env.js`.
3. In `payment-component/vanilla-js/apps/client/env.js`:
    - Replace `your-chargebee-subdomain` with the subdomain of your Chargebee [Test site](tk). For example, if your Chargebee Test site is `acme-test.chargebee.com`, then provide `acme-test`.
    - Replace `your-publishable-api-key` with a [publishable API](tk) key obtained from the Chargebee web app.

4. Rename the file `payment-component/vanilla-js/apps/server/env.js.example` to `env.js`.
3. In `payment-component/vanilla-js/apps/server/env.js`:
    - Replace `your-chargebee-subdomain` with the subdomain of your Chargebee Test site. See example in the previous step.
    - Replace `your-full-access-api-key` with a [full access API](tk) key obtained from the Chargebee web app.

2. Change working directory to `payment-component/vanilla-js`
3. Use [nvm](https://github.com/nvm-sh/nvm/blob/master/README.md) to change the Node.js version to v18.
    ```shell
    nvm use 18
    ```
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
    apps/client dev: Checkout page running at http://localhost:8081/
    apps/server dev: API server is running at http://localhost:8082/
    ```
6. Launch the checkout page of the sample app by browsing to `http://localhost:8081/`.

---

