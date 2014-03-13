<?php
  session_start();
  if( isset($_SESSION['subscription_id'] ) ) {
    header("Location: subscription");
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
    <body id="cb-ssp-login">
        <div class="navbar navbar-static-top">
            <div class="container">
                <div class="navbar-header">
                    <img src="/assets/images/logo.png" alt="ChargeBee SSP" class="navbar-brand img-responsive">
                </div>
            </div>
        </div>
        <div class="container"> 
<div class="row">
	<div class=" col-sm-6 col-sm-offset-3">
        <div class="panel panel-success">
            <div class="panel-heading h3">Login</div>
            <div class="panel-body">
                <form class="form-horizontal" action="/ssp-php/login" method="post" >
                    <div class="form-group">
                      <label class="col-sm-4 control-label">Subscription Id</label>
                      <div class="col-sm-8">
                        <input type="text" name="subscription_id" class="form-control" placeholder="Enter User Name" value='john@acmeinc.com'>
                        <small class="text-danger"></small>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-4 control-label">Password</label>
                      <div class="col-sm-8">
                        <input type="password" name="password" class="form-control" placeholder="Enter Password" value="thisismypassword">
                        <small class="text-danger"></small>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-4 control-label">&nbsp;</label>                    
                        <div class="col-sm-8">
                            <input type="submit" class="btn btn-success" value="Login">
                            <?php if( $_GET["login"]== "failed" ) { ?>
                                <br><span class="text-danger login-err">
                                    Subscription Id not found
                                </span>
                            <?php } ?>
                        </div>
                  	</div>
                </form>
            </div>
          </div>
  	</div>
</div>
    <h6 class="text-center text-muted clearfix">
                <span class="text-muted">&copy; Honey Comics. All Rights Reserved.</span><br><br>
                Powered by <a href="//www.chargebee.com" target="_blank">
            <img src="/assets/images/cb-footer.png" alt="Powered by ChargeBee"></a>
            </h6>
        </div>
    </body>
</html>
