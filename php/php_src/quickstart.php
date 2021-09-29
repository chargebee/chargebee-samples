
require 'ChargeBee.php';
ChargeBee_Environment::configure("my-domain-test","test_YkiZnDgc1MWyjlWRNBJgHsKCRSSB8cuDlS");
$result = ChargeBee_Customer::create(array(
  "id" => "acme-east",
  "company" => "Acme Eastern",
  "autoCollection" => "on",
  "card" => array(
    "number" => "4111111111111111",
    "cvv" => "100",
    "expiryMonth" => 12,
    "expiryYear" => 2022
    )
  ));
$customer = $result->customer();
$card = $result->card();



$result = ChargeBee_ItemFamily::create(array(
  "id" => "cloud-storage",
  "name" => "Cloud Storage"
  ));
$itemFamily = $result->itemFamily();



$result = ChargeBee_Item::create(array(
  "id" => "silver-plan",
  "name" => "Silver Plan",
  "type" => "plan",
  "item_family_id" => "cloud-storage"
  ));
$item = $result->item();



$result = ChargeBee_ItemPrice::create(array(
  "id" => "silver-plan-USD-monthly",
  "itemId" => "silver-plan",
  "name" => "Silver USD monthly",
  "pricingModel" => "per_unit",
  "price" => 50000,
  "externalName" => "Silver USD",
  "periodUnit" => "month",
  "period" => 1
  ));
$itemPrice = $result->itemPrice();



$result = ChargeBee_Subscription::createWithItems("acme-east",array(
  "subscriptionItems" => array(array(
    "itemPriceId" => "silver-plan-USD-monthly",
    "quantity" => 4))
  ));
$subscription = $result->subscription();
$customer = $result->customer();
$card = $result->card();
$invoice = $result->invoice();
$unbilledCharges = $result->unbilledCharges();
