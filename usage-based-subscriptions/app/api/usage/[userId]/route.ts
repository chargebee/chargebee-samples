import { NextRequest, NextResponse } from 'next/server';
import { getChargebee } from '@lib/chargebee';

export async function GET(req: NextRequest, { params }: { params: { userId: string } }) {
  const userId = params.userId;

  const result = await getChargebee().usage.list({
    subscription_id: {
      is: userId
    },
    limit: 100
  }).request();

  return NextResponse.json(result);
}
