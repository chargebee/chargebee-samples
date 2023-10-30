# pricing/urls.py

from django.urls import path
from .views import PlanView, PlanDetailView, CreateCBSubscriptionView, WebhookView, SubscriptionView

app_name = "pricing"

urlpatterns = [
    path("plans", PlanView.as_view(), name="plans"),
    path("plan/<int:pk>/", PlanDetailView.as_view(), name="plan-detail"),
    path(
        "api/generate_checkout_new_url",
        CreateCBSubscriptionView.as_view(),
    ),
    path("webhooks", WebhookView.as_view()),
    path("subscription", SubscriptionView.as_view(), name="subscription"),

]
