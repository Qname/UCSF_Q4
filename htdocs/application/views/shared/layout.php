<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
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
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url();?>assets/sumoselect/sumoselect.min.css">

    <!-- We recommend you use "your_style.css" to override SmartAdmin
         specific styles this will also ensure you retrain your customization with each SmartAdmin update.
    <link rel="stylesheet" type="text/css" media="screen" href="smartAdminTemplate/css/your_style.css"> -->

    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url();?>assets/smartAdminTemplate/css/ucsf_style.css">

    <!-- #FAVICONS -->
    <link rel="shortcut icon" href="<?php echo base_url();?>assets/smartAdminTemplate/img/favicon/favicon.ico" type="image/x-icon">
    <link rel="icon" href="<?php echo base_url();?>assets/smartAdminTemplate/img/favicon/favicon.ico" type="image/x-icon">

    <!-- #GOOGLE FONT -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">

    <!--================================================== -->
    <!-- PACE LOADER - turn this on if you want ajax loading to show (caution: uses lots of memory on iDevices)-->
    <script data-pace-options='{ "restartOnRequestAfter": true }' src="<?php echo base_url();?>assets/smartAdminTemplate/js/plugin/pace/pace.min.js"></script>

    <!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script>
        if (!window.jQuery) {
            document.write('<script src="<?php echo base_url();?>assets/smartAdminTemplate/js/libs/jquery-2.1.1.min.js"><\/script>');
        }
    </script>

    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
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

<!-- #HEADER -->
<header id="header">
    <?php $this->load->view("shared/header");?>
</header>
<!-- END HEADER -->

<!-- LEFT  -->
<aside id="left-panel">
    <?php $this->load->view("shared/left");?>
</aside>
<!-- END LEFT  -->

<!-- MAIN PANEL -->
<div id="main" role="main">
    <?php $this->load->view($template);?>
</div>
<!-- END MAIN PANEL -->

<!-- #PAGE FOOTER -->
<div class="page-footer">
    <?php $this->load->view("shared/footer");?>
</div>
<!-- END FOOTER -->

<!-- IMPORTANT: APP CONFIG -->
<script src="<?php echo base_url();?>assets/smartAdminTemplate/js/app.config.js"></script>

<!-- JS TOUCH : include this plugin for mobile drag / drop touch events-->
<script src="<?php echo base_url();?>assets/smartAdminTemplate/js/plugin/jquery-touch/jquery.ui.touch-punch.min.js"></script>

<!-- BOOTSTRAP JS -->
<script src="<?php echo base_url();?>assets/smartAdminTemplate/js/bootstrap/bootstrap.min.js"></script>

<!-- CUSTOM NOTIFICATION -->
<script src="<?php echo base_url();?>assets/smartAdminTemplate/js/notification/SmartNotification.min.js"></script>

<!-- JARVIS WIDGETS -->
<script src="<?php echo base_url();?>assets/smartAdminTemplate/js/smartwidgets/jarvis.widget.min.js"></script>

<!-- EASY PIE CHARTS -->
<script src="<?php echo base_url();?>assets/smartAdminTemplate/js/plugin/easy-pie-chart/jquery.easy-pie-chart.min.js"></script>

<!-- SPARKLINES -->
<script src="<?php echo base_url();?>assets/smartAdminTemplate/js/plugin/sparkline/jquery.sparkline.min.js"></script>

<!-- JQUERY VALIDATE -->
<script src="<?php echo base_url();?>assets/smartAdminTemplate/js/plugin/jquery-validate/jquery.validate.min.js"></script>

<!-- JQUERY MASKED INPUT -->
<script src="<?php echo base_url();?>assets/smartAdminTemplate/js/plugin/masked-input/jquery.maskedinput.min.js"></script>

<!-- JQUERY SELECT2 INPUT -->
<script src="<?php echo base_url();?>assets/smartAdminTemplate/js/plugin/select2/select2.min.js"></script>

<!-- JQUERY UI + Bootstrap Slider -->
<script src="<?php echo base_url();?>assets/smartAdminTemplate/js/plugin/bootstrap-slider/bootstrap-slider.min.js"></script>

<!-- browser msie issue fix -->
<script src="<?php echo base_url();?>assets/smartAdminTemplate/js/plugin/msie-fix/jquery.mb.browser.min.js"></script>

<!-- FastClick: For mobile devices -->
<script src="<?php echo base_url();?>assets/smartAdminTemplate/js/plugin/fastclick/fastclick.min.js"></script>

<!--[if IE 8]>
<h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>
<![endif]-->

<!-- MAIN APP JS FILE -->
<script src="<?php echo base_url();?>assets/smartAdminTemplate/js/app.min.js"></script>

<script type="text/javascript">
    pageSetUp();
</script>


<script src="<?php echo base_url();?>assets/smartAdminTemplate/js/plugin/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url();?>assets/smartAdminTemplate/js/plugin/datatables/dataTables.colVis.min.js"></script>
<script src="<?php echo base_url();?>assets/smartAdminTemplate/js/plugin/datatables/dataTables.tableTools.min.js"></script>
<script src="<?php echo base_url();?>assets/smartAdminTemplate/js/plugin/datatables/dataTables.bootstrap.min.js"></script>
<script src="<?php echo base_url();?>assets/smartAdminTemplate/js/plugin/datatable-responsive/datatables.responsive.min.js"></script>

<script src="<?php echo base_url();?>assets/sumoselect/jquery.sumoselect.min.js"></script>
<script src="<?php echo base_url();?>assets/js/ucsf_common.js"></script>

<?php echo put_footer(); ?>
</body>

</html>