import { PLAN_NAME } from '@constants/chargebee';
import { getChargebee } from '@lib/chargebee';
import { getRandomInspirationalQuote } from '@lib/quote';
import { PrismaClient } from '@prisma/client';
import { headers } from 'next/headers';
import { NextRequest, NextResponse } from 'next/server';

export async function GET(req: NextRequest) {
  const headersList = headers();

  const apiKey = headersList.get('api-key') as string;

  const prisma = new PrismaClient();

  const user = await prisma.user.findUnique({
    where: {
      apiKey,
    },
  });

  if (!user) {
    return NextResponse.json({ message: 'Invalid API Key' }, { status: 400 });
  }

  const userId = `${user.id}`;

  await getChargebee()
    .usage.create(userId, {
      item_price_id: PLAN_NAME,
      usage_date: Math.floor(Date.now() / 1000),
      quantity: '1',
    })
    .request();

  return NextResponse.json({
    quote: getRandomInspirationalQuote(),
  });
}
