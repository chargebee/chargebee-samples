import { API_KEY, CHARGEBEE_WEBHOOKS_REQUEST_ORIGINS, SITE_NAME } from '@constants/chargebee';
import chargebee from 'chargebee';


export function getChargebee() {
  chargebee.configure({
    site: SITE_NAME,
    api_key: API_KEY
  })
  return chargebee;
}

export const isValidRequest = (requestIp: string) => {
  return CHARGEBEE_WEBHOOKS_REQUEST_ORIGINS.find((ip) => ip === requestIp);
}