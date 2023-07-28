# Chargebee Usage based billing example

This demo example shows how to implement Chargebee's usage based billing feature in a [Next.js](https://nextjs.org/) application. We have kept this codebased simple for you to understand and get started with.

## Getting started

### 1. Clone the entire repo

Install dependencies:

```
pnpm i
```


### 2. Create the database

Run the following command to create your SQLite database file. This also creates the `User` table defined in [`prisma/schema.prisma`](./prisma/schema.prisma):

```
pnpm prisma migrate dev --name init
```


### 3. Start the app

```
pnpm dev
```

The app is now running, navigate to [`http://localhost:3000/`](http://localhost:3000/) in your browser to explore its UI.

## Using the REST API

You can access the REST API of the API server directly. It is running on the same host machine and port and can be accessed via the `/api` route (in this case that is `localhost:3000/api/`.

### `GET`

- `/api/generate`: Generate random inspiration quote
  - Headers:
    - `api-key: String` (required): API key assigned to the user
- `/api/usage/${userId}`: Usage report for the user

### `POST`

- `/api/checkout`: Create a user in the db and triggers new checkout session
  - Body:
    - `email: String` (required): User email
- `/webhooks`: Webhook even listener called by Chargebee

## Next steps

- Check out the [Metered Billing](https://www.chargebee.com/docs/2.0/metered_billing.html#configuring-metered-billing)
- Create issues and ask questions on [GitHub](https://github.com/chargebee/chargebee-samples)