<?php
/*
 * Adding ChargeBee php librariesand configuration files.
 */
require_once(dirname(__FILE__) . "/Config.php");
require_once(dirname(__FILE__) . "/ErrorHandler.php");
require_once(dirname(__FILE__) . "/Util.php");

if ($_POST) {
	validateParameters($_POST);
    try {
        $result = createSubscription();
        addShippingAddress($result->subscription(), $result->customer());
        $jsonResp = array();
        
        /*
         * Forwarding to success page after successful create subscription in ChargeBee.
         */
        $queryParameters = "name=" . urlencode($result->customer()->firstName) 
                            . "&planId=" . urlencode($result->subscription()->planId);        
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
function createSubscription() {
    
    /*
     * Constructing a parameter array for create subscription api. 
     * It will have account information, the temporary token got from Stripe and
     * plan details.
     * For demo purpose a plan with id 'annual' is hard coded.
     * Other params are obtained from request object.
     * Note : Here customer object received from client side is sent directly 
     *        to ChargeBee.It is possible as the html form's input names are 
     *        in the format customer[<attribute name>] eg: customer[first_name] 
     *        and hence the $_POST["customer"] returns an associative array of the attributes.
     *               
     */
    $createSubscriptionParams = array(
        "planId" => "basic",
        "customer" => $_POST['customer'],
        "card" => array(
            "tmp_token" => $_POST['stripeToken']
    ));

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
function addShippingAddress($subscription, $customer) {
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
                "addr" => $_POST['addr'],
                "extended_addr" => $_POST['extended_addr'],
                "city" => $_POST['city'],
                "state" => $_POST['state'],
                "zip" => $_POST['zip_code']
    ));
    $address = $result->address();
    return $address;
}
?>

