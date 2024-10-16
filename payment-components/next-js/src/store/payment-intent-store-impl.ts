import {PaymentIntent} from "chargebee";

export class PaymentIntentStoreImpl {

    public static async create(
        amount: number,
        currency_code: string,
    ): Promise<PaymentIntent> {
        const URL = `${window.location.protocol}//${window.location.hostname}:${window.location.port}/api`;
        const response = await fetch(`${URL}/payment-intents`, {
            method: "POST",
            body: JSON.stringify({amount, currency_code}),
        });
        if (!response.ok) {
            throw new Error("Failed to get payment intent");
        }
        return await response.json();
    }
}
