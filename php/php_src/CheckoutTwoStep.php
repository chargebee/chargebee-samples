<?php
/*
 * Adding ChargeBee php libraries and configuration files.
 */
require_once(dirname(__FILE__) . "/Config.php");
require_once(dirname(__FILE__) . "/Util.php");
require_once(dirname(__FILE__) . "/ErrorHandler.php");

/*
 * Demo on how to create a Subscription using ChargeBee Checkout New Hosted Page 
 * API and add Shipping Address to the subscription after successful create 
 * Subscription in ChargeBee(Two step checkout process using pass thru content).
 */
$uri = $_SERVER["REQUEST_URI"];
if (endsWith($uri,"/first_step")){
   firstStep();
} else if(endsWith(substr($uri,0,strpos($uri,"?")),"/redirect_handler")){
   redirectHandler();
} else {
   customError400();
}

/*
 * When a Checkout New API is called, the customer is redirected to a Hosted 
 * Checkout Page. And while calling this API, the shipping address can be passed 
 * as 'pass thru content'. After checkout, the customer is redirected to the 
 * Return URL, while doing this 'pass thru content' can be retrived using 
 * the hosted page ID.
 */
function firstStep() {
  validateParameters($_POST);
  $planId = "basic";
  
  $passThrough = array( "address"=>$_POST['addr'],
                        "extended_addr"=> $_POST['extended_addr'],
                        "city" => $_POST['city'],
                        "state"=> $_POST['state'],
                        "zip_code"=> $_POST['zip_code']
                      );
   
   try {
     /*
      * Calling ChargeBee Checkout new Hosted Page API to checkout a new subscription
   	  * by passing plan id the customer would like to subscribe and also passing customer 
      * first name, last name, email and phone details. The resposne returned by ChargeBee
      * has hosted page url and the customer will be redirected to that url.
      * 
      * Note: Parameter embed(Boolean.TRUE) can be shown in iframe
      *       whereas parameter embed(Boolean.FALSE) can be shown as seperate page.
      * Note : Here customer object received from client side is sent directly 
      *        to ChargeBee.It is possible as the html form's input names are 
      *        in the format customer[<attribute name>] eg: customer[first_name] 
      *        and hence the $_POST["customer"] returns an associative array of the attributes.              
      */
      
      $result = Chargebee_HostedPage::CheckoutNew(array("subscription"=>array("planId"=>$planId),
                                                    	"customer"=> $_POST['customer'],
                                                    	"embed" => "false",
                                                    	"passThruContent"=>json_encode($passThrough) ));
      
      
      $redirectUrl = $result->hostedPage()->url;
	  $jsonResponse = array("forward" => $redirectUrl);
	  print json_encode($jsonResponse, true);
      
  } catch(ChargeBee_InvalidRequestException $e) {
	  handleInvalidRequestErrors($e,"subscription[plan_id]");
  } catch(Exception $e) {
      handleGeneralErrors($e);
  }
}

/* The request will have hosted page id and state of the checkout   
 * which helps in getting the details of subscription created using 
 * ChargeBee checkout hosted page.
 */
function redirectHandler(){
   $hostedPageId = $_GET['id'];
  
    
     /* Requesting ChargeBee server about the Hosted page state and 
      * getting the details of the created subscription.
      */
     $result = ChargeBee_HostedPage::retrieve($hostedPageId);
     $hostedPage = $result->hostedPage();
	 /*
	  * Checking the state of the hosted page. If the state is not "succeeded",
	  * then cusotmer checkout is considered as failed.
	  */
     if( $hostedPage->state != "succeeded" ) {
        header("HTTP/1.0 400 Error");
        include($_SERVER["DOCUMENT_ROOT"]."/error_pages/400.html");
        return;
     }
	 
     $subscriptionId = $hostedPage->content()->subscription()->id;
     addShippingAddress($subscriptionId, $result);
     header("Location: thankyou?subscription_id=".URLencode($subscriptionId));
}

/*
 * Shipping address for the subscription is added after successful create 
 * subscription using ChargeBee Hosted Page API. The shipping address is passed 
 * as pass thru content during Hosted Page API Call and after successful create 
 * subscription the pass thru content is retrieved and using Address API 
 * shipping address is added.
 */
function addShippingAddress($subscriptionId, $result) {
  
  $passThru = $result->hostedPage()->passThruContent;
  $shippingAddress = json_decode($passThru,true);
  $result = ChargeBee_Address::update( array( "label" => "Shipping Address",
                                              "subscriptionId" => $subscriptionId,
                                              "addr" => $shippingAddress['address'],
                                              "extended_addr" => $shippingAddress['extended_addr'],
                                              "city" => $shippingAddress['city'],
                                              "state" => $shippingAddress['state'],
                                              "zip" => $shippingAddress['zip_code']
                                      ));
  
}


?>
