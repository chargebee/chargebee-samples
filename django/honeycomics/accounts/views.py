# accounts/views.py

from django.urls import reverse_lazy
from django.views.generic.edit import CreateView
from .forms import UserForm


class SignUpView(CreateView):
    form_class = UserForm
    success_url = reverse_lazy("login")
    template_name = "registration/signup.html"
