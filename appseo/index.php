<?php 
	ob_start(); 
	session_start();
	
	error_reporting(0);
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
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/custom.css">
        <title>Projects Manager</title>
        <style>
            .login{
              margin-top: 12%;
              margin-left: 2%;
            }
            .login h1{
              padding-bottom: 40px;
            }

        </style>
    </head>

    <body background="images/background/<?php echo $bg_array[$int_random]; ?>">
    	<div id="container">
			<?php include('includes/login_form.php');?>
	        <?php include('includes/footer.php');?>
    	</div>

    <script src="css/js/bootstrap.min.js"></script>
    </body>
</html>