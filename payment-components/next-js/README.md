This is a [Next.js](https://nextjs.org) project bootstrapped with [`create-next-app`](https://nextjs.org/docs/app/api-reference/cli/create-next-app).

## Getting Started

Follow the steps below to set up the Payment Components Quickstart app in your local environment:

1. Clone this repository.
2. Rename the file `payment-components/next-js/.env.example` to `.env`.
3. In `payment-components/next-js/.env`:
    - Enter your Chargebee test site `NEXT_PUBLIC_CHARGEBEE_SITE` . For example, if your Chargebee Test site is `acme-test.chargebee.com`, then enter `acme-test`.
    - Enter Your Publishable Api key `NEXT_PUBLIC_CHARGEBEE_KEYS_PUBLISHABLE` with a [publishable API](https://www.chargebee.com/docs/2.0/api_keys.html#types-of-api-keys_publishable-key) key obtained from the Chargebee web app.
    - Enter Your Full Access key `NEXT_CHARGEBEE_KEYS_FULL_ACCESS` with a [full access](https://www.chargebee.com/docs/2.0/api_keys.html#types-of-api-keys_full-access-key) key obtained from the Chargebee web app.

4. Change the working directory to `payment-components/next-js`.
5. Use [nvm](https://github.com/nvm-sh/nvm/blob/master/README.md) to change the Node.js version to v18.
    ```shell
    nvm use 18
    ```
6. Install dependencies (required only when setting up the app for the first time).
    ```bash
   npm i
   # or
   pnpm i
    ```
7. Start the development server.
   ```bash
   npm run dev
   # or
   pnpm run dev
   # or
   yarn dev
   # or
   bun dev
   ```

Open [http://localhost:3000](http://localhost:3000) with your browser to see the result.

You can start editing the page by modifying `app/page.tsx`. The page auto-updates as you edit the file.

This project uses [`next/font`](https://nextjs.org/docs/app/building-your-application/optimizing/fonts) to automatically optimize and load [Geist](https://vercel.com/font), a new font family for Vercel.

## Learn More

To learn more about Next.js, take a look at the following resources:

- [Next.js Documentation](https://nextjs.org/docs) - learn about Next.js features and API.
- [Learn Next.js](https://nextjs.org/learn) - an interactive Next.js tutorial.

You can check out [the Next.js GitHub repository](https://github.com/vercel/next.js) - your feedback and contributions are welcome!

## Deploy on Vercel

The easiest way to deploy your Next.js app is to use the [Vercel Platform](https://vercel.com/new?utm_medium=default-template&filter=next.js&utm_source=create-next-app&utm_campaign=create-next-app-readme) from the creators of Next.js.

Check out our [Next.js deployment documentation](https://nextjs.org/docs/app/building-your-application/deploying) for more details.
