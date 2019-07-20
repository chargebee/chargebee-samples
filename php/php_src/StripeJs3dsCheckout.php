<?php
/*
 * Adding ChargeBee php librariesand configuration files.
 */
require_once(dirname(__FILE__) . "/Config.php");
require_once(dirname(__FILE__) . "/ErrorHandler.php");
require_once(dirname(__FILE__) . "/Util.php");

$uri = $_SERVER["REQUEST_URI"];
$requestBody = json_decode(file_get_contents('php://input'), true);
/*
 * Decode the request from post body and call the appropriate function
 * based on the POST endpoint url.
 */
if ($requestBody) {
    if (endsWith($uri, "/confirm_payment")) {
        confirmPayment($requestBody);
    } else if (endsWith($uri, "/checkout")) {
        handleCheckout($requestBody);
    } else {
        header("HTTP/1.0 400 Error");
        include($_SERVER["DOCUMENT_ROOT"]."/error_pages/400.html");
    }
}

/*
 * When client sends a payment method id, then we need to create a payment intent
 * in stripe. For creating a payment intent, we need to specify the exact amount which needs
 * to be put on HOLD. In order to get the estimated amount, we need to call chargebee's
 * create_subscription_estimate api to get the amount.
 * 
 * When client sends a payment intent id, then we need to confirm that payment intent
 * in stripe.
 * 
 * NOTE:
 * While creating payment intent in stripe, make sure to pass the following two parameters
 * with the same values.
 * "capture_method" => "manual", "setup_future_usage" => "off_session"
 */
function confirmPayment($body) {
    try {
        $intent = [];
        \Stripe\Stripe::setApiKey("stripe_api_key");
        if (array_key_exists('payment_method_id', $body)) {
            // Calling chargebee's create_subscription_estimate api
            $estimate = getSubscriptionEstimate($body);
            
            // Creating payment intent in Stripe
            $intent = \Stripe\PaymentIntent::create([
                "payment_method" => $body['payment_method_id'],
                "amount" => $estimate->invoiceEstimate->total,
                "currency" => $estimate->invoiceEstimate->currencyCode,
                "confirm" => "true",
                "confirmation_method" => "manual",
                "capture_method" => "manual",
                "setup_future_usage" => "off_session"
            ]);
            
        } else if (array_key_exists('payment_intent_id', $body)) {
            // Confirming the payment intent in stripe
            $intent = \Stripe\PaymentIntent::retrieve($body['payment_intent_id']);
            $intent = $intent->confirm();
        }
        header('Content-Type: application/json');
        generatePaymentResponse($intent);
    } catch(Exception $e) {
		handleGeneralErrors($e);
    }
}

/*
 * Call chargebee's create_subscription_estimate api to get the estimated amount
 * for current subscription creation.
 */
function getSubscriptionEstimate($body) {
    
    $result = ChargeBee_Estimate::createSubscription(array(
        "billingAddress" => array(
          "line1" => $body['addr'],
          "line2" => $body['extended_addr'],
          "city" => $body['city'],
          "stateCode" => $body['state'],
          "zip" => $body['zip_code'],
          "country" => "US"
          ),
        "subscription" => array(
          "planId" => "basic"
          )
        ));
    
    $estimate = $result->estimate();
    return $estimate;
}

/*
 * Based on the payment intent status, create an appropriate response for client
 * to handle it accordingly.
 * When intent status is 'requires_source_action' or 'requires_action' then client needs
 * to handle extra authentication by calling stripe js function.
 * When intent status is 'requires_capture' then payment intent is ready to be passed into
 * chargebee's endpoint
 */

function generatePaymentResponse($intent) {
    if (($intent['status'] == 'requires_source_action' || $intent['status'] == 'requires_action') &&
        $intent['next_action']['type'] == 'use_stripe_sdk') {
        // Inform the client to handle the action
        print json_encode(array(
            'requires_action' => true,
            'payment_intent_client_secret' => $intent['client_secret']
        ));
    }
    else if ($intent['status'] == 'requires_capture') {
        // The payment didn’t need any additional actions it just needs to be captured
        //  Now can pass this on to chargebee for creating subscription
        print json_encode(array(
            'success' => true,
            'payment_intent_id' => $intent['id']
        ));
    }
    else {
        //  Invalid status
        print json_encode(array(
            'success' => false,
            'error' => $intent['status']
        ));
    }
}


function handleCheckout($body) {
	validateParameters($body);
    try {
        $result = createSubscription($body);
        addShippingAddress($result->subscription(), $result->customer(), $body);
        
        $jsonResp = array();
        /*
         * Forwarding to success page after successful create subscription in ChargeBee.
         */      
        $jsonResp["forward"] = "thankyou.html";
        echo json_encode($jsonResp, true);
        
    } catch(ChargeBee_PaymentException $e) {
   	 	handleTempTokenErrors($e);
    } catch(ChargeBee_InvalidRequestException $e) {
		handleInvalidRequestErrors($e, "plan_id");
    } catch(Exception $e) {
		handleGeneralErrors($e);
    }
}


/* Creates the subscription in ChargeBee using the checkout details and 
 * stripe temporary token provided by stripe.
 */

function createSubscription($body) {
    
    /*
     * Constructing a parameter array for create subscription api. 
     * It will have account information, the temporary token got from Stripe and
     * plan details.
     * For demo purpose a plan with id 'annual' is hard coded.
     * Other params are obtained from request object.
     * Note : Here customer object received from client side is sent directly 
     *        to ChargeBee.It is possible as the html form's input names are 
     *        in the format customer[<attribute name>] eg: customer[first_name] 
     *        and hence the $body["customer"] returns an associative array of the attributes.
     *               
     */
    $createSubscriptionParams = array(
        "planId" => "basic",
        "customer" => $body['customer'],
        "payment_intent" => array(
            "gw_token" => $body['payment_intent_id'],
            "gateway_account_id" => "<stripe_gateway_account_id>"
        )
    );

    /* 
    * Sending request to the chargebee server to create the subscription from 
    * the parameters received. The result will have customer,subscription and 
    * card attributes.
    */
    $result = ChargeBee_Subscription::create($createSubscriptionParams);
    
    return $result;
}

/*
 * Adds the shipping address to an existing subscription. The first name
 * & the last name for the shipping address is got from the customer 
 * account information.
 */
function addShippingAddress($subscription, $customer, $body) {
   /* 
    * Adding address to the subscription for shipping product to the customer.
    * Sends request to the ChargeBee server and adds the shipping address 
    * for the given subscription Id.
    */
    $result = ChargeBee_Address::update(array(
                "subscription_id" => $subscription->id,
                "label" => "shipping_address",
                "first_name" => $customer->firstName,
                "last_name" => $customer->lastName,
                "addr" => $body['addr'],
                "extended_addr" => $body['extended_addr'],
                "city" => $body['city'],
                "state" => $body['state'],
                "zip" => $body['zip_code']
    ));
    $address = $result->address();
    return $address;
}
?>

