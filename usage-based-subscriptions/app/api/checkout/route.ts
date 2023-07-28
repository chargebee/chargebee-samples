import { NextRequest, NextResponse } from 'next/server';
import { HostedPage } from 'chargebee';
import { PrismaClient } from '@prisma/client';
import { getChargebee } from '@lib/chargebee';
import { PLAN_NAME } from '@constants/chargebee';

export async function POST(req: NextRequest) {

  const { email } = await req.json() as { email: string };
  const prisma = new PrismaClient();

  const user = await prisma.user.create({
    data: {
      email
    },
  });

  await prisma.$disconnect();

  const payload: HostedPage.CheckoutNewForItemsInputParam = {
    subscription: {
      id: user.id,
    },
    subscription_items: [
      {
        item_price_id: PLAN_NAME,
      },
    ],
    customer: {
      id: user.id,
      email
    },
  };

  const result = await getChargebee().hosted_page.checkout_new_for_items(payload).request();

  return NextResponse.json(result.hosted_page);
}
