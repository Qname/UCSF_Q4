<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php
	$ci = new MY_Controller();
	$ci =& get_instance();
	$ci->load->helper('url');
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>404 Page Not Found</title>
		<link rel="shortcut icon" href="<?php echo base_url();?>assets/smartAdminTemplate/img/favicon/favicon.ico" type="image/x-icon">
		<link rel="icon" href="<?php echo base_url();?>assets/smartAdminTemplate/img/favicon/favicon.ico" type="image/x-icon">
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url();?>assets/smartAdminTemplate/css/bootstrap.min.css">
			<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url();?>assets/smartAdminTemplate/css/404.css">
	</head>
	<body>
		<div class="error-container">
			<div class="error-code">404</div>
			<div class="error-text">page not found</div>
			<div class="error-subtext">Unfortunately we're having trouble loading the page you are looking for. Please wait a moment and try again or use action below.</div>
			<div class="error-actions">                                
				<div class="row">
					<div class="col-md-6">
						<a class="btn btn-info btn-block btn-lg" href="<?php echo base_url();?>glvhome">GL Verification</a>
					</div>
					<div class="col-md-6">
						<button class="btn btn-primary btn-block btn-lg" onClick="history.back();">Previous Page</button>
					</div>
				</div>                                
			</div>
		</div>   
	</body>
</html>