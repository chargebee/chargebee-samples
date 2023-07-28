import { PLAN_NAME } from '@constants/chargebee';
import { NextRequest, NextResponse } from 'next/server';
import { headers } from 'next/headers';
import { isValidRequest } from '@lib/chargebee';
import { PrismaClient } from '@prisma/client';
import { generateKey } from '@lib/utils';

export async function POST(req: NextRequest) {
  try {
    const headersList = headers();
    const requestIp = headersList.get('x-real-ip') || headersList.get('x-forwarded-for');

    if (process.env.NODE_ENV === 'development' || isValidRequest(requestIp!)) {
      const payload = (await req.json()) as any;
      const eventType = payload.event_type;
      const content = payload.content;

      switch (eventType) {
        case 'subscription_created':
          const { subscription } = content;
          const userId = subscription.customer_id;
          const prisma = new PrismaClient();
          const apiKey = generateKey();

          await prisma.user.update({
            where: {
              id: parseInt(userId),
            },
            data: {
              apiKey,
            },
          });

          await prisma.$disconnect();

          console.log(`💰 New user ${userId} subscribed to plan ${PLAN_NAME} \n API Key: ${apiKey}`);

          break;
      }
      return NextResponse.json({ message: 'Successfully processed' }, { status: 200 });
    } else {
      return NextResponse.json({ error: 'IP Address Not Allowed' }, { status: 405, headers: { Allow: 'POST' } });
    }
  } catch (err) {
    return NextResponse.json({ error: `Webhook Error`, message: (err as any).message }, { status: 400 });
  }
}
