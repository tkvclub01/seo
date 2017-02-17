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
        <title>Keyword Detail</title>
    </head>
    <body background="images/background/<?php echo $bg_array[$int_random]; ?>">
    	<div id="container">
    		<?php include('includes/menubar.php'); ?>
    		<?php include('includes/keyword-detail-table.php'); ?>
			<?php include('includes/footer.php'); ?>
    	</div>
		
	<script src="css/js/jquery.min.js"></script>
    <script src="css/js/bootstrap.min.js"></script>
    </body>
</html>