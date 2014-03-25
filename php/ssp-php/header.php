<?php
  require_once(dirname(__FILE__) . '/../php_src/Config.php');
  require_once(dirname(__FILE__) . '/../php_src/Util.php');
  $subscriptionId = null;
  $customerId = null;
  if( authenticate() ) {
     $subscriptionId = getSubscriptionId();
     $customerId = getCustomerId();
  } else {
     header("Location: /ssp-php/");
     return;
  }
?>
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
        <title>Self Service Portal - ChargeBee</title>
        <link href="/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" media="all">
        <link href="/assets/css/ssp-core.css" rel="stylesheet" type="text/css">

        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.6.2/html5shiv.js"></script>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.js"></script>
        <script type="text/javascript" src="http://malsup.github.io/jquery.form.js"></script>
        <script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js"></script>
        <!--[if lt IE 9]>
        <script src="//html5sshiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        <!--script src="../chargebee/respond.min.js"></script-->
    </head>
    <body>
        <div class="navbar navbar-static-top">
            <div class="container">
                <div class="navbar-header">
                    <img src="/assets/images/logo.png" alt="ChargeBee SSP" class="navbar-brand">
                </div>
            </div>
        </div>
        <div class="container"> 
             <p class="lead">
                    Self Service Portal
             </p>
             <div class="ajax-loader" style="display: none">
                <img src="/assets/images/loader.gif" class="center-img">
                <div id="lightbox"></div>
             </div>
