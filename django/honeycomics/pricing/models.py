# pricing/models.py

from django.db import models
from django.conf import settings

User = settings.AUTH_USER_MODEL


class Plan(models.Model):
    name = models.CharField(max_length=255)
    description = models.TextField(blank=True)

    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    class Meta:
        ordering = ("-created_at",)

    def __str__(self):
        return self.name


class PlanPrice(models.Model):
    price_id = models.CharField(max_length=255, primary_key=True)
    plan = models.ForeignKey(Plan, on_delete=models.CASCADE)
    price = models.IntegerField()
    currency = models.CharField(max_length=255, default="USD")
    period = models.CharField(max_length=255)

    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    def __str__(self) -> str:
        return f"{self.period} {self.plan.name} {self.currency} plan"


class Subscription(models.Model):
    user = models.ForeignKey(User, on_delete=models.CASCADE)
    subscribed_to = models.ForeignKey(PlanPrice, on_delete=models.CASCADE)

    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    def __str__(self) -> str:
        return f"{self.user.username} {self.subscribed_to.plan.name}"
