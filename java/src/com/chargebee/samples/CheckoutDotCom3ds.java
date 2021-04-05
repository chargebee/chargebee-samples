package com.chargebee.samples;

import java.awt.*;
import java.io.IOException;
import java.net.URI;
import java.net.URISyntaxException;
import java.util.concurrent.ExecutionException;

import com.chargebee.Environment;
import com.chargebee.Result;
import com.chargebee.models.*;
import com.chargebee.models.enums.AutoCollection;
import com.checkout.CheckoutApi;
import com.checkout.CheckoutApiException;
import com.checkout.CheckoutApiImpl;
import com.checkout.CheckoutValidationException;
import com.checkout.common.Currency;
import com.checkout.common.ErrorResponse;
import com.checkout.payments.*;

public class CheckoutDotCom3ds {
    private String paymentSourceID;

    public CheckoutDotCom3ds() {
        
        /* Set false for Production Checkout.com account */
        this.api = CheckoutApiImpl.create("<checkout.com-secret-key>", true, "<checkout.com-public-key>");
        Environment.configure("<chargebee-site-name>", "<chargebee-api-key>");
        
        this.paymentSourceResponse = null;
        this.redirectURL = null;
        this.waitDuration = 18000;
        this.paymentSourceID = null;
        this.checkedPayment = null;
        this.payment = null;
    }
    private String token;
    private PaymentResponse paymentSourceResponse;
    private CheckoutApi api;
    private String redirectURL;
    private GetPaymentResponse checkedPayment;
    public PaymentProcessed payment;
    /*Set the interval between subsequent checks of the Payment Method's status */
    public void setWaitDuration(int waitDuration) {
        this.waitDuration = waitDuration;
    }

    public int waitDuration;
    
    /* Returns a Processed payment source. The Payment request is created */
    public PaymentProcessed CreateProcessedPaymentSource(String token){
        TokenSource tokenSource = new TokenSource(token);
        PaymentRequest<TokenSource> paymentRequest = PaymentRequest.fromSource(tokenSource, Currency.USD, 10000L);
        paymentRequest.setCapture(false);
        paymentRequest.setThreeDS(ThreeDSRequest.from(true));
        PaymentResponse response;

        try {
            response = this.api.paymentsClient().requestAsync(paymentRequest).get();
            this.paymentSourceID = response.isPending() ? response.getPending().getId() : response.getPayment().getId();

            /* Keep checking whether the payment has been approved */
            if (response.isPending() && response.getPending().requiresRedirect()) {
                redirect(response);
            }
            else if (response.getPayment().isApproved()){
                paymentSuccessful(response.getPayment());
            }
            else if (response.getPayment().getStatus().equals("Declined")){
                paymentDeclined();
            }
            else System.out.println("Unknown error occurred.");
        } catch (CheckoutValidationException e) {
            validationError(e.getError());
        } catch (CheckoutApiException e) {
            System.out.println("Payment request failed with status code " + e.getApiResponseInfo());
            throw e;
        } catch (ExecutionException e) {
            System.out.println("Checkout.com Error: " + e.getCause().toString());
            System.exit(0);
        } catch (InterruptedException e) {
            e.printStackTrace();
        } catch (IOException e) {
            e.printStackTrace();
        } catch (URISyntaxException e) {
            e.printStackTrace();
        }
        return this.payment;
    }
    
    private void validationError(ErrorResponse error) {
        System.out.println(error.toString());

    }

    private void paymentSuccessful(PaymentProcessed payment) {
        System.out.println("Payment has been processed");
    }

    public void redirect(PaymentResponse paymentResponse) throws InterruptedException, URISyntaxException, IOException {
        /* Redirect the user to the Authorization Flow URL */
        this.redirectURL = paymentResponse.getPending().getRedirectLink().getHref();
        Desktop.getDesktop().browse(new URI(redirectURL));
        /* Keep checking whether the payment has been authorized or not. */
        while (!isPaymentApproved(this.paymentSourceID)){
            System.out.println("Payment hasn't been approved yet.");
            System.out.println("Kindly navigate to the Checkout.com 3DS Authorization page which is " +
                    "currently open on your default browser");
            System.out.println(String.format("If you have just authorized the payment, kindly wait for %s seconds\n",this.waitDuration/1000));
            Thread.sleep(this.waitDuration);
        }
    }
    
    /* Fetches the current instance of the Payment Source */
    public GetPaymentResponse getPayment(){
        GetPaymentResponse checkedPayment = null;
        try {
            checkedPayment = this.api.paymentsClient().getAsync(this.paymentSourceID).get();
            System.out.println(String.format("Payment ID: %s Status: %s",checkedPayment.getId(), checkedPayment.getStatus()));
        } catch (InterruptedException | ExecutionException e) {
            e.printStackTrace();
        }
        return checkedPayment;
    }

    public boolean isPaymentApproved(String payment_id){
        GetPaymentResponse getPaymentResponse = getPayment();
        if (getPaymentResponse.getStatus().equals("Declined"))
        {
            paymentDeclined();
        }
        /* Check whether the payment is Authorized */
        else return getPaymentResponse.isApproved();
        return false;
    }
    
    private void paymentDeclined() {
        /* Exit the program if the payment is declined*/
        System.out.println("Payment has been declined by Checkout.com");
        System.exit(0);
    }
    
    public static String createChargebeeSubscription(String paymentMethodID) {
        Result result = null;
        try {
            result = Subscription.create()
                    .planId("<chargebee-plan-id>")
                    .autoCollection(AutoCollection.ON)
                    .customerFirstName("John")
                    .customerLastName("Doe")
                    .customerEmail("john@user.com")
                    .billingAddressFirstName("John")
                    .billingAddressLastName("Doe")
                    .billingAddressLine1("PO Box 9999")
                    .billingAddressCity("Walnut")
                    .billingAddressState("California")
                    .billingAddressZip("91789")
                    .billingAddressCountry("US")
                    .paymentIntentGatewayAccountId("<checkout.com-gateway-id>")
                    .paymentIntentGwToken(paymentMethodID)
                    .request();
        } catch (Exception e) {
            e.printStackTrace();
        }
        Subscription subscription = result.subscription();
        System.out.println(String.format("Chargebee Subscription \'%s\' created for Checkout.com Payment ID \'%s\'",subscription.id(), paymentMethodID));
        return subscription.id();
    }
    
    public static void main(String[] args) throws IOException, InterruptedException, URISyntaxException {
        /* Create an instance of the class */
        CheckoutDotCom3ds checkoutDotCom3DS = new CheckoutDotCom3ds();
        /* Return a PaymentResponse from a token ID */
        try {
            checkoutDotCom3DS.CreateProcessedPaymentSource("<checkout.com-client-token>");
        } catch (CheckoutValidationException e){
            System.out.println(e.getCause().toString());
            System.exit(0);
        } catch (Exception e){
            e.printStackTrace();
        }
        /* Create a Chargebee subscription */
        createChargebeeSubscription(checkoutDotCom3DS.paymentSourceID);
    }

}