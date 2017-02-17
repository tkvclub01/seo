<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Checking keyword rank</title>
	<style type="text/css">
		body,h1,h2,h3,p,quote,small,form,input,ul,li,ol,label{
			margin:0px;
			padding:5px;
		}

		body{
			color:#555555;
			font-size:13px;
			background: url(img/dark_wood_texture.jpg) #282828;
			font-family:Arial, Helvetica, sans-serif;
		}

		.clear{
			clear:both;
		}

		#main-container{
			width:500px;
			margin:30px auto;
		}

		#form-container{
			background-color:#f5f5f5;
			padding:15px;
			
			-moz-border-radius:12px;
			-khtml-border-radius: 12px;
			-webkit-border-radius: 12px;
			border-radius:12px;
		}

		a:hover{
			text-decoration:underline;
		}

		h1{
			color:#777777;
			font-size:22px;
			font-weight:normal;
			text-transform:uppercase;
			margin-bottom:5px;
		}

		h2{
			font-weight:normal;
			font-size:10px;
			
			text-transform:uppercase;
			
			color:#aaaaaa;
			margin-bottom:15px;
			
			border-bottom:1px solid #eeeeee;
			margin-bottom:15px;
			padding-bottom:10px;
		}
		h3{
			font-weight:bold;
			font-size:18px;	
		}		

		label{
			text-transform:uppercase;
			font-size:10px;
			font-family:Tahoma,Arial,Sans-serif;
			margin:0px,0px,5px,0px;
		}

    </style>
</head>

<body>
<div id="main-container">
<div id="form-container">
<h1>Report keywords rank</h1>
<form action="report.php" method="post" id="report">
		<p>Google Url:</p>
		<select name="googleUrl" style="width:330px">
			<option value="www.google.com.my">www.google.com.my</option>
			<option value="www.google.com">www.google.com</option>
			<option value="www.google.co.uk">www.google.co.uk</option>
			<option value="www.google.co.kr">www.google.co.kr</option>
			<option value="www.google.com.sg">www.google.com.sg</option>
		</select>
        <p>Domain: <i>(eg: domain.com)</i></p>
        <input name="domain" value=""   size="50"/>
        <p>Keywords: <i>(note: keywords seperate by enter key)</i></p>
		<textarea name="keywords" rows="6" cols="55"></textarea>
		<input name="reportdate" type="hidden" value="<?php echo date('Y-m-d');?>"/>
		<input type="submit" name="report" value="Report Page"/>
</form>
</div>
</div>


<br style="clear:both" />

    <div id="result">
        
    </div>

</div>

</body>
</html>
