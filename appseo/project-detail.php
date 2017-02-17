<?php
	ob_start();
	// start session
	session_start();
	
	// set time for session timeout
	$currentTime = time() + 25200;
	$expired = 3600;
	
	// if session not set go to login page
	if(!isset($_SESSION['user'])){
		header("location:index.php");
	}
	
	// if current time is more than session timeout back to login page
	if($currentTime > $_SESSION['timeout']){
		session_destroy();
		header("location:index.php");
	}
	
	// destroy previous session timeout and create new one
	unset($_SESSION['timeout']);
	$_SESSION['timeout'] = $currentTime + $expired;

	$dir = "images/background/";
	// Open a directory, and read its contents
	$bg_array  = array();
	if (is_dir($dir)){
		if ($dh = opendir($dir)){
			while (($file = readdir($dh)) !== false){
				if(strlen($file)>3){
					array_push($bg_array, $file);
				}
			}
			closedir($dh);
		}
	}
	$int_random = rand(0,count($bg_array)-1);
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" href="css/font-awesome.min.css">
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/custom.css">
         <!--formden.js communicates with FormDen server to validate fields and submit via AJAX -->
		<script type="text/javascript" src="https://formden.com/static/cdn/formden.js"></script>
		
		<!-- Special version of Bootstrap that is isolated to content wrapped in .bootstrap-iso -->
		<link rel="stylesheet" href="https://formden.com/static/cdn/bootstrap-iso.css" />
		
		<!--Font Awesome (added because you use icons in your prepend/append)-->
		<link rel="stylesheet" href="https://formden.com/static/cdn/font-awesome/4.4.0/css/font-awesome.min.css" />
		
		<!-- Inline CSS based on choices in "Settings" tab -->
		<style>.bootstrap-iso .formden_header h2, .bootstrap-iso .formden_header p, .bootstrap-iso form{font-family: Arial, Helvetica, sans-serif; color: black}.bootstrap-iso form button, .bootstrap-iso form button:hover{color: white !important;} .asteriskField{color: red;}</style>
		<style type="text/css">
		.switch {
		  position: relative;
		  display: inline-block;
		  width: 60px;
		  height: 34px;
		}
		
		.switch input {display:none;}
		
		.switch .slider {
		  position: absolute;
		  cursor: pointer;
		  top: 0;
		  left: 0;
		  right: 0;
		  bottom: 0;
		  background-color: #ccc;
		  -webkit-transition: .4s;
		  transition: .4s;
		}
		
		.switch .slider:before {
		  position: absolute;
		  content: "";
		  height: 26px;
		  width: 26px;
		  left: 4px;
		  bottom: 4px;
		  background-color: white;
		  -webkit-transition: .4s;
		  transition: .4s;
		}
		
		.switch input:checked + .slider {
		  background-color: #2196F3;
		}
		
		.switch input:focus + .slider {
		  box-shadow: 0 0 1px #2196F3;
		}
		
		.switch input:checked + .slider:before {
		  -webkit-transform: translateX(26px);
		  -ms-transform: translateX(26px);
		  transform: translateX(26px);
		}
		
		/* Rounded sliders */
		.switch .slider.round {
		  border-radius: 34px;
		}
		
		.switch .slider.round:before {
		  border-radius: 50%;
		}
		</style>
        <title>Projects Detail</title>
    </head>
    <body background="images/background/<?php echo $bg_array[$int_random]; ?>">
    	<div id="container">
    		<?php include('includes/menubar.php'); ?>
    		<?php include('includes/projects_detail_table.php'); ?>
			<?php include('includes/footer.php'); ?>
    	</div>
		
    <script src="css/js/jquery.min.js"></script>
    <script src="css/js/bootstrap.min.js"></script>	
    <!-- Extra JavaScript/CSS added manually in "Settings" tab -->
	<!-- Include jQuery -->
	<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
	
	<!-- Include Date Range Picker -->
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>
	
	<script>
		$(document).ready(function(){
			var date_input=$('input[name="created_at"]'); //our date input has the name "date"
			var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
			date_input.datepicker({
				format: 'yyyy-mm-dd',
				container: container,
				todayHighlight: true,
				autoclose: true,
			})
		})
	</script>
    </body>
</html>