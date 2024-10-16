"use server";

import chargebee from "chargebee";
import { NextRequest, NextResponse } from "next/server";
import { PaymentIntent } from "chargebee";

export async function POST(request: NextRequest) {
  try {
    const json = await request.json();

    const url = new URL(process.env
        .NEXT_PUBLIC_CHARGEBEE_APP_URL as string);
    const options = {
      site: process.env
          .NEXT_PUBLIC_CHARGEBEE_SITE as string,
      api_key: process.env
          .NEXT_CHARGEBEE_KEYS_FULL_ACCESS as string,
      port: url.port ? url.port : undefined,
      protocol: url.protocol.replace(":", ""),
      hostSuffix: url.hostname.replace("{site}", ""),
    };
    chargebee.configure(options);

    const amount = Number(json.amount);
    const currencyCode = json.currency_code;
    const paymentMethodType = json.payment_method_type;
    const createInputRequest: PaymentIntent.CreateInputParam = {
      amount: amount,
      currency_code: currencyCode,
    };
    if (paymentMethodType) {
      createInputRequest.payment_method_type = paymentMethodType;
    }
    const result = await chargebee.payment_intent
      .create(createInputRequest)
      .request();

    return NextResponse.json(result.payment_intent);
  } catch (err: unknown) {
    return NextResponse.json(
      {
        error: err,
        code: 500,
      },
      { status: 500 },
    );
  }
}
