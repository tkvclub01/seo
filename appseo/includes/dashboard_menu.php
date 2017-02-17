<?php
	include_once('connect_database.php'); 
	include_once('functions.php'); 
?>

<?php

	//Total projects count
	$sql_projects = "SELECT COUNT(*) as num FROM projects";
	$total_projects = mysqli_query($connect, $sql_projects);
	$total_projects = mysqli_fetch_array($total_projects);
	$total_projects = $total_projects['num'];


?>
<div id="content" class="container col-md-12">

<div class="col-md-12">
		<h1>Dashboard</h1>
		<hr/>
	</div>

 		<a href="projects.php">
			<div class="col-sm-6 col-md-2">
	            <div class="thumbnail">    
	              <div class="caption">
	              <center>
	              <img src="images/ic_category.png" width="100" height="100">
	                <h3><?php echo $total_projects;?></h3>
	                <p class="detail">Projects</p>  
	                </center>
	              </div>
	            </div>
	         </div>
	    </a>


        <a href="admin.php">
          <div class="col-sm-6 col-md-2">
            <div class="thumbnail"> 
              <div class="caption">
              <center>
              <img src="images/ic_setting.png" width="100" height="100">
                <h3><br></h3>
                <p class="detail">Setting</p>     
                </center>
              </div>
            </div>
          </div>
        </a>
</div>

<?php include_once('close_database.php'); ?>