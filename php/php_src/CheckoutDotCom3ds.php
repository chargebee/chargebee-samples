<?php
require __DIR__ . '/vendor/autoload.php';
require_once(__DIR__ . '/vendor/chargebee/chargebee-php/lib/ChargeBee.php');

use Checkout\CheckoutApi;
use Checkout\Models\Payments\ThreeDs;
use Checkout\Models\Payments\TokenSource;
use Checkout\Models\Payments\Payment;
use Checkout\Library\Exceptions\CheckoutHttpException;

class CheckoutDotCom3DS
{
    public $token;
    public $paymentSourceID;
    public $waitDuration;

    /**
     * @return mixed
     */
    public function getWaitDuration(): int
    {
        return $this->waitDuration;
    }

    /**
     * @param mixed $waitDuration in seconds
     */
    public function setWaitDuration($waitDuration): void
    {
        $this->waitDuration = $waitDuration;
    }

    /**
     * @return CheckoutApi
     */
    public function getCheckout(): CheckoutApi
    {
        return $this->checkout;
    }

    /**
     * @param CheckoutApi $checkout
     */
    public function setCheckout(CheckoutApi $checkout): void
    {
        $this->checkout = $checkout;
    }
    public Payment $payment;
    public Payment $checkPayment;
    private CheckoutApi $checkout;
    
    public function __construct()
    {
        ChargeBee_Environment::configure("<chargebee-site-name>", "<chargebee-api-key>");
        $this->initCheckoutApi();
    }

    private function initCheckoutApi()
    {
        // Add the Secret Key in the checkout-sdk-php/src/config.ini file
        $this->checkout = new CheckoutApi();
    }
    
    /**
     * @return mixed
     */
    public function getPaymentSourceID()
    {
        return $this->paymentSourceID;
    }

    /**
     * @param mixed $paymentSourceID
     */
    public function setPaymentSourceID($paymentSourceID): void
    {
        $this->paymentSourceID = $paymentSourceID;
    }

    /**
     * @return Payment
     */
    public function getPayment(): Payment
    {
        return $this->payment;
    }

    /**
     * @param Payment $payment
     */
    public function setPayment(Payment $payment): void
    {
        $this->payment = $payment;
    }

    /**
     * @return Payment
     */
    public function getCheckPayment(): Payment
    {
        return $this->checkPayment;
    }

    /**
     * @param Payment $checkPayment
     */
    public function setCheckPayment(Payment $checkPayment): void
    {
        $this->checkPayment = $checkPayment;
    }

    function getPaymentSource($token)
    {
        $this->$token = $token;
        // Create a payment method instance with card details
        $token = new TokenSource($token);
        // Prepare the payment parameters
        $payment = new Payment($token, 'USD');
        $payment->capture = false;
        $payment->threeDs = new ThreeDs(true);
        $payment->amount = 10000; // = 100.00
        // Send the request and retrieve the response
        try {
            $cdc = $this->getCheckout();
            $response = $cdc->payments()->request($payment);
//            $this -> $paymentSourceID = $response -> id;
            $this->setPaymentSourceID($response->id);

            echo "Payment Intent ID: " . $this -> getPaymentSourceID() . PHP_EOL;
            
            echo "Complete 3DS Flow here: " . $response->getRedirection() . PHP_EOL;
            
        } catch (CheckoutHttpException $che) {
            echo "Checkout.com Error: " . $che->getErrors()[0] . PHP_EOL;
            echo "Possibly the token has expired or it has been used." . PHP_EOL;
            exit();
        }
        $this->setPayment($response);
        return $this->payment;
    }


    function checkIfPaymentIsAuthorized(): bool
    {
//        $cdc = $this->getCheckout();
//        $this->payment = $cdc->payments()->details($this->paymentSourceID);
        $this -> setCheckPayment($this -> getCheckout() -> payments() -> details($this ->paymentSourceID));
        $currentPaymentStatus = $this->checkPayment -> status;
        echo "Current Payment Status: " . $currentPaymentStatus . PHP_EOL;
//        if ((strcasecmp($currentPaymentStatus, "Authorized") != 0) || (strcasecmp($currentPaymentStatus, "Card Verified") != 0)){
        if (($currentPaymentStatus === "Authorized") || ($currentPaymentStatus === "Card Verified")){
            return true;
        }
        return false;
    }


    function createChargebeeCustomer($paymentIntentID)
    {
        try {
            $result = ChargeBee_Subscription::create(array(
                "planId" => "<chargebee-plan-id>",
                "autoCollection" => "on",
                "billingAddress" => array(
                    "firstName" => "John",
                    "lastName" => "Doe",
                    "line1" => "PO Box 9999",
                    "city" => "Walnut",
                    "state" => "California",
                    "zip" => "91789",
                    "country" => "US"
                ),
                "customer" => array(
                    "firstName" => "John",
                    "lastName" => "Doe",
                    "email" => "john@user.com"
                ),
                "paymentIntent" => array(
                    "gatewayAccountId" => "<checkout.com-gateway-id>",
                    "gwToken" => $paymentIntentID
                )
            ));
            $subscription = $result->subscription();
        } catch (ChargeBee_PaymentException $cbe) {
            echo "Chargebee Payment Error: " . $cbe->getApiErrorCode() . PHP_EOL;
            exit();
        }
        return $subscription->id;
    }
}

$checkoutDotCom3DS = new CheckoutDotCom3DS();
$checkoutDotCom3DS -> setWaitDuration(15);
$paymentSource = $checkoutDotCom3DS->getPaymentSource("<checkout.com-client-token>");
$paymentIntentID = $paymentSource->id;

while (!$checkoutDotCom3DS->checkIfPaymentIsAuthorized()) {
    echo 'Payment has not been authorized yet'. PHP_EOL;
    echo sprintf('If you have just authorized the payment, kindly wait for %d seconds', $checkoutDotCom3DS -> getWaitDuration()) . PHP_EOL;
    sleep($checkoutDotCom3DS -> getWaitDuration());
}

echo("Chargebee Subscription ID: " . $checkoutDotCom3DS->createChargebeeCustomer($paymentIntentID) . PHP_EOL);
