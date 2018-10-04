
<!DOCTYPE html>
<html lang="en-us">
<head>
	<meta charset="utf-8">
	<title>UCSF GL Verification System</title>
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

	<!-- #CSS Links -->
	<!-- Basic Styles -->
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url();?>assets/smartAdminTemplate/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url();?>assets/smartAdminTemplate/css/font-awesome.min.css">

	<!-- SmartAdmin Styles : Caution! DO NOT change the order -->
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url();?>assets/smartAdminTemplate/css/smartadmin-production-plugins.min.css">
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url();?>assets/smartAdminTemplate/css/smartadmin-production.min.css">
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url();?>assets/smartAdminTemplate/css/smartadmin-skins.min.css">

	<!-- SmartAdmin RTL Support -->
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url();?>assets/smartAdminTemplate/css/smartadmin-rtl.min.css">

    <!-- We recommend you use "your_style.css" to override SmartAdmin
         specific styles this will also ensure you retrain your customization with each SmartAdmin update.
         <link rel="stylesheet" type="text/css" media="screen" href="smartAdminTemplate/css/your_style.css"> -->

         <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url();?>assets/smartAdminTemplate/css/ucsf_style.css">

         <!-- #FAVICONS -->
         <link rel="shortcut icon" href="<?php echo base_url();?>assets/smartAdminTemplate/img/favicon/favicon.ico" type="image/x-icon">
         <link rel="icon" href="<?php echo base_url();?>assets/smartAdminTemplate/img/favicon/favicon.ico" type="image/x-icon">

         <!-- #GOOGLE FONT -->
         <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">

         <!--================================================== -->
         <!-- PACE LOADER - turn this on if you want ajax loading to show (caution: uses lots of memory on iDevices)-->
         <script data-pace-options='{ "restartOnRequestAfter": true }' src="<?php echo base_url();?>assets/smartAdminTemplate/js/plugin/pace/pace.min.js"></script>

         <!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->
         <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
         <script>
         	if (!window.jQuery) {
         		document.write('<script src="<?php echo base_url();?>assets/smartAdminTemplate/js/libs/jquery-2.1.1.min.js"><\/script>');
         	}
         </script>

         <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
         <script>
         	if (!window.jQuery.ui) {
         		document.write('<script src="<?php echo base_url();?>assets/smartAdminTemplate/js/libs/jquery-ui-1.10.3.min.js"><\/script>');
         	}
         </script>

         <script>
         	var base_url = "<?php echo base_url(); ?>";
         </script>
     </head>

     <body class="smart-style-0">

     	<header id="header">
     		<div class="col-md-4" style="width:33.3%;"></div>
     		<div class="col-md-4"><h1 class="front-header">General Ledger Verification</h1></div>
     		<div class="col-md-4"><!-- collapse menu button -->					
     		</div>	
     	</header>	

     	<!-- MAIN CONTENT -->
     	<div id="content" class="container">
     		<div class="row">					
     			<div class="col-sm-6 col-md-4 col-md-offset-4">
     				<div class="well no-padding" style="margin-top: 100px;">
     					<div class="smart-form client-form">
     						<?php echo form_open('account/login');?>
     						<header>
     							Sign In
     						</header>
     						<fieldset>								  
     							<section>
     								<label class="label">E-mail</label>
     								<label class="input"> <i class="icon-append fa fa-user"></i>											
     									<input type="email" name="email" id="email" placeholder="email"/>
     									<b class="tooltip tooltip-top-right"><i class="fa fa-user txt-color-teal"></i> Please enter email address/username</b></label>
     								</section>
     								<section>
     									<label class="label">Password</label>
     									<label class="input"> <i class="icon-append fa fa-lock"></i>
     										<input type="password" name="password" id="password" placeholder="**********"/>											
     										<b class="tooltip tooltip-top-right"><i class="fa fa-lock txt-color-teal"></i> Enter your password</b> </label>
     										<div class="note">
     											<a href="#">Forgot password?</a>
     										</div>
     									</section>
     									<section>
     										<label class="checkbox">
     											<input type="checkbox" name="remember" checked="">
     											<i></i>Stay signed in</label>
     										</section>
     									</fieldset>
     									<footer>
     										<input type="submit" value="Sign in" name="submit" class="btn btn-primary"/>									
     									</footer>
     									
     									<?php echo form_close(); ?>		
     								</div>	
     							</div>		
     						</div>
     					</div>
     					
     					<div style="color: #ff0000;text-align: center;">
     						<?php
     						if(isset($message)){
     							echo $message;
     						} 
     						?>
     					</div>
     				</div>		

     			</body>
     			</html>