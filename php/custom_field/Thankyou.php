<?php
require_once('../partials/header.php');
/*
 * Adding ChargeBee php libraries
 */
require_once(dirname(__FILE__) . "/../php_src/Config.php");


$subscriptionId = $_GET["subscription_id"];
$result = ChargeBee_Subscription::retrieve($subscriptionId);
$dob = $result->customer()->cfDateOfBirth;
$comicsType = $result->customer()->cfComicsType;


?>

<div class="jumbotron text-center">
    	<h2><span class="text-muted">Congrats! You've successfully</span> signed up <span class="text-muted">to Honey Comics.</span></h2>
        <h4 class="text-muted"><?php echo $comicsType ?> comics will be delivered to your email address.</h4>
        <h3> Expect a surprise on your birthday (<?php echo date('d-M',$dob) ?>) :) </h3>
        <h1>Thank You!</h1>
</div>

<?php
require_once('../partials/footer.php');
?>
