<?php
/*
 * Adding ChargeBee php libraries and configuration files.
 */
require_once(dirname(__FILE__) . "/Config.php");
include("./MeterBilling.php");
/*
 * Demo on how to add charge for meter billing customer after
 * receiving Invoice Created event through webhook.
 */
if (isset($_POST)) {
  
  /*
   * Getting the json content from the request.
   */
  if(!checkIfRequestIsFromChargeBee() ){
    return;
  }
  $content = file_get_contents('php://input');
  /* 
   * Assigning the recieved content to ChargeBee Event object.
   */
  $event = ChargeBee_Event::deserialize($content);
  

  
  /*
   * Checking the event type as Invoice Created to add Charge for Meter Billing.
   */
  $eventType = $event->eventType;
  if($eventType == "invoice_created" ) {
	 $invoiceId = $event->content()->invoice()->id;
     $invoiceObj = ChargeBee_Invoice::retrieve($invoiceId)->invoice();
	 if($invoiceObj->status == "pending" ){
       $meterBilling = new MeterBilling();
       $meterBilling->closePendingInvoice($invoiceObj);
	   echo "Invoice has been closed successfully";
     }else {
		 echo "Invoice is not in pending state";
     }
  }
  

}


/* Check if the request is from chargebee. 
 * You can secure the webhook either using
 *   - Basic Authentication
 *   - Or check for specific value in a parameter.
 * For demo purpose we are using the second option though 
 * basic auth is strongly preferred. Also store the key 
 * securely in the server rather than hard coding in code.
 */
function checkIfRequestIsFromChargeBee() {
 if($_REQUEST['webhook_key'] != "DEMO_KEY" ) { 
     header("HTTP/1.0 403 Error");
     return false;
 }
 return true;
}


?>
