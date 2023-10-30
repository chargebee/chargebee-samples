# pricing/views.py

import json
import chargebee
from .models import Plan, PlanPrice, Subscription
from django.views.generic import View, DetailView, ListView
from django.contrib.auth.mixins import LoginRequiredMixin
from django.http import JsonResponse, HttpResponseForbidden
from django.utils.decorators import method_decorator
from django.views.decorators.csrf import csrf_exempt
from django.contrib.auth.models import User

# US region IP addresses
ALLOWED_IPS = [
    '3.209.65.25',
    '3.215.11.205',
    '3.228.118.137',
    '3.229.12.56',
    '3.230.149.90',
    '3.231.198.174',
    '3.231.48.173',
    '3.233.249.52',
    '18.210.240.138',
    '18.232.249.243',
    '34.195.242.184',
    '34.206.183.55',
    '35.168.199.245',
    '52.203.173.152',
    '52.205.125.34',
    '52.45.227.101',
    '54.158.181.196',
    '54.163.233.122',
    '54.166.107.32',
    '54.84.202.184',
]


class PlanView(LoginRequiredMixin, ListView):
    model = Plan
    context_object_name = "plans"
    template_name = "pricing/plans.html"


class PlanDetailView(LoginRequiredMixin, DetailView):
    model = Plan
    context_object_name = "plan"
    template_name = "pricing/plan_detail.html"

    def get_context_data(self, **kwargs):
        context = super(PlanDetailView, self).get_context_data()
        context["plan_prices"] = PlanPrice.objects.filter(plan=self.get_object())
        return context


class CreateCBSubscriptionView(View):
    def post(self, request, *args, **kwargs):
        try:
            price_id = request.POST.get("price_id")
            # Configure Chargebee
            chargebee.configure('CHARGEBEE_API_KEY', 'CHARGEBEE_SITE')

            # Use the received price_id in the checkout request
            result = chargebee.HostedPage.checkout_new_for_items(
                {
                    "subscription_items": [
                        {"item_price_id": price_id},
                    ],
                    "customer": {
                        "first_name": request.user.first_name,
                        "last_name": request.user.last_name,
                        "email": request.user.email,
                    }
                }
            )

            # Extract hosted_page from the result
            hosted_page = result._response["hosted_page"]
            # Return the hosted_page data as JSON response
            return JsonResponse(hosted_page)
        except json.JSONDecodeError:
            return JsonResponse(
                {"error": "Invalid JSON data in the request."}, status=400
            )


@method_decorator(csrf_exempt, name="dispatch")
class WebhookView(View):
    def post(self, request, *args, **kwargs):
        try:
            remote_ip = request.META.get('REMOTE_ADDR', None)
            if remote_ip in ALLOWED_IPS:
                event = json.loads(request.body)
                if event["event_type"] == "subscription_created":
                    content = event["content"]
                    email = content["customer"]["email"]
                    user = User.objects.get(email=email)
                    item_price_id = content["subscription"]["subscription_items"][0]["item_price_id"]
                    item_price = PlanPrice.objects.get(price_id=item_price_id)
                    Subscription.objects.create(user=user, subscribed_to=item_price)
                    return JsonResponse({"status": "success"}, status=200)
                else:
                    print("Unhandled event type")
                    return JsonResponse({"status": "success"}, status=200)
            else:
                return HttpResponseForbidden("Access denied: Request from unauthorized IP.")
        except Exception as e:
            return JsonResponse({"error": str(e)}, status=500)


class SubscriptionView(LoginRequiredMixin, ListView):
    model = Subscription
    template_name = 'pricing/subscription.html'
    context_object_name = 'subscription'

    def get_context_data(self, **kwargs):
        context = super().get_context_data(**kwargs)
        context['subscription'] = Subscription.objects.filter(user=self.request.user).order_by('-created_at').first()
        return context
