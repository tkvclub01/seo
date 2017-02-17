<?php
	include_once('connect_database.php'); 
	include_once('functions.php'); 
	
?>
<div id="content" class="container col-md-12">
	<?php 
		$error = array();
		$error1 = "";
		if(isset($_POST['btnAdd'])){
			$name = $_POST['name'];
			$created_at = $_POST['created_at'];
			$created_by = $_POST['created_by'];
			$status = 0;
			if($_POST['status'] =="on"){
				$status = 1;
			}
			if(empty($name)){
				$error['name'] = " <span class='label label-danger'>Must Insert Name!</span>";
			}
			if(empty($created_at)){
				$error['created_at'] = " <span class='label label-danger'>Must Be Choice Create Date!</span>";
			}
			
			if(empty($created_by)){
				$error['created_by'] = " <span class='label label-danger'>Must Be Input Create By!</span>";
			}
			
			if(!empty($name)){
				
				// insert new data to menu table
				$sql_query = "INSERT INTO projects (name,created_at,created_by,status)
						VALUES(?,?,?,?)";
				
				$stmt = $connect->stmt_init();
				if($stmt->prepare($sql_query)) {	
					// Bind your variables to replace the ?s
					$stmt->bind_param('ssss', 
								$name,
								$created_at,
								$created_by,
								$status
								);
					// Execute query
					$stmt->execute();
					// store result 
					$result = $stmt->store_result();
					$stmt->close();
				}
				if($result){
					$error1 = " <h4><div class='alert alert-success'>
														* New Project success added.
														<a href='projects.php'>
														<i class='fa fa-check fa-lg'></i>
														</a></div>
												  </h4>";
				}else{
					$error1 = " <span class='label label-danger'>Failed add Projects</span>";
				}
			}
			
		}

		if(isset($_POST['btnCancel'])){
			header("location: projects.php");
		}

	?>
	<div class="col-md-12">
		<h1>Add Project</h1>
		<?php 
			echo $error1;
		?>
		<hr />
	</div>
	
	<div class="col-md-5">
		<form method="post"
			enctype="multipart/form-data">
			<label>Project Name (EX: www.vdato.com): </label><?php echo isset($error['name']) ? $error['name'] : '';?>
			<input type="text" class="form-control" name="name"/>
			<br/>
			<label>Create Date :</label><?php echo isset($error['created_at']) ? $error['created_at'] : '';?>
			<div class="input-group">
		        <div class="input-group-addon">
		         	<i class="fa fa-calendar"></i>
		        </div>
	        <input class="form-control" id="date" name="created_at" placeholder="YYYY-MM-DD" type="text"/>
	       </div>
	       <br/>
			<label>Create By :</label><?php echo isset($error['created_by']) ? $error['created_by'] : '';?>
			<input type="text" class="form-control" name="created_by"/>
			<br/>
			<label>Status</label>
			<br/>
			<label class="switch">
			  <input type="checkbox" checked="checked" name="status">
			  <div class="slider round"></div>
			</label>
			<br/><br/>
			<input type="submit" class="btn-primary btn" value="Submit" name="btnAdd"/>
			<input type="reset" class="btn-warning btn" value="Clear"/>
			<input type="submit" class="btn-danger btn" value="Cancel" name="btnCancel"/>
		</form>
	</div>

	<div class="separator"> </div>
</div>
	
<?php include_once('close_database.php'); ?>