# pricing/admin.py

from django.contrib import admin
from .models import Plan, PlanPrice

admin.site.register(Plan)
admin.site.register(PlanPrice)
