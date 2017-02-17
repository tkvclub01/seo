<?php
	include_once('connect_database.php'); 
	include_once('functions.php'); 
?>
<div id="content" class="container col-md-12">
	<?php 
		if(isset($_GET['id'])){
			$ID = $_GET['id'];
		}else{
			$ID = "";
		}
		$error = array();
		$error1 = "";
		if(!empty($_POST['btnEdit'])){
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
				$sql_query = "UPDATE projects set name=?,created_at=?,created_by=?,status=?
						where id = ".$ID;
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
					$update_result = $stmt->store_result();
					$stmt->close();
				}
				
				// check update result
				if($update_result){
					$error['update_projects'] = " <h4><div class='alert alert-success'>
														* Projects  updated success.
														<a href='projects.php'>
														<i class='fa fa-check fa-lg'></i>
														</a></div>
												  </h4>";
				}else{
					$error['update_projects'] = " <span class='label label-danger'>Failed to update Project.</span>";
				}
			}
				
		}
			
		// create array variable to store previous data
		$data = array();
		
		$sql_query = "SELECT * FROM projects  WHERE id = ?";
		
		$stmt = $connect->stmt_init();
		if($stmt->prepare($sql_query)) {	
			// Bind your variables to replace the ?s
			$stmt->bind_param('s', $ID);
			// Execute query
			$stmt->execute();
			// store result 
			$stmt->store_result();
			$stmt->bind_result($data['id'], 
					$data['name'],
					$data['create_at'],
					$data['create_by'],
					$data['status']
					);
			$stmt->fetch();
			$stmt->close();
		}

		if(isset($_POST['btnCancel'])){
			header("location: projects.php");
		}
		
	?>
	<div class="col-md-12">
		<h1>Edit Category</h1>
		<?php echo isset($error['update_projects']) ? $error['update_projects'] : '';?>
		<hr />
	</div>
	
	<div class="col-md-5">
		<form method="post"
			enctype="multipart/form-data">
			<label>Project Name :</label><?php echo isset($error['name']) ? $error['name'] : '';?>
			<input type="text" class="form-control" name="name" value="<?php echo $data['name'];?>"/>
			<br/>
			<label>Create Date :</label><?php echo isset($error['created_at']) ? $error['created_at'] : '';?>
			<div class="input-group">
		        <div class="input-group-addon">
		         	<i class="fa fa-calendar"></i>
		        </div>
	        <input class="form-control" id="date" name="created_at" placeholder="YYYY-MM-DD" type="text" value="<?php echo $data['create_at'];?>"/>
	       </div>
	       <br/>
			<label>Create By :</label><?php echo isset($error['created_by']) ? $error['created_by'] : '';?>
			<input type="text" class="form-control" name="created_by" value="<?php echo $data['create_by'];?>"/>
			<br/>
			<label>Status</label>
			<br/>
			<label class="switch">
			  <input type="checkbox"   <?php  if($data['status']==1){ ?> checked="checked" <?php  }  ?>  name="status" />
			  <div class="slider round"></div>
			</label>
			<br/><br/>
			<input type="submit" class="btn-primary btn" value="Update" name="btnEdit"/>
			<input type="submit" class="btn-danger btn" value="Cancel" name="btnCancel"/>
		</form>
	</div>

	<div class="separator"> </div>
</div>
	
<?php include_once('close_database.php'); ?>