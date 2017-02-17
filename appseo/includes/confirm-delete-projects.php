<?php
	include_once('connect_database.php'); 
?>

<div id="content" class="container col-md-12">
	<?php 
		
		if(isset($_POST['btnDelete'])){
			if(isset($_GET['id'])){
				$ID = $_GET['id'];
			}else{
				$ID = "";
			}
			// delete data from menu table
			$sql_query = "DELETE FROM projects 
					WHERE id = ?";
			
			$stmt = $connect->stmt_init();
			if($stmt->prepare($sql_query)) {	
				// Bind your variables to replace the ?s
				$stmt->bind_param('s', $ID);
				// Execute query
				$stmt->execute();
				// store result 
				$delete_private_result = $stmt->store_result();
				$stmt->close();
			}
				
			// if delete data success back to reservation page
			if($delete_private_result){
				$sql_query_project = "SELECT name FROM projects  WHERE id = ?";
				$data_project_name = '';
				$stmt_project = $connect->stmt_init ();
				if ($stmt_project->prepare ( $sql_query_project )) {
					// Bind your variables to replace the ?s
					$stmt_project->bind_param ( 's', $_GET ['project_id'] );
					// Execute query
					$stmt_project->execute ();
					// store result
					$stmt_project->store_result ();
					$stmt_project->bind_result ( $data_project_name );
					$stmt_project->fetch ();
					$stmt_project->close ();
				}
				$mask = '*'.$data_project_name.'.xlsx';
				array_map('unlink', glob($mask));
				header("location: projects.php");
			}
		}		
		
		if(isset($_POST['btnNo'])){
			header("location: projects.php");
		}
		
	?>
	<h1>Confirm Action</h1>
	<hr />
	<form method="post">
		<p>Are you sure want to delete this project?</p>
		<input type="submit" class="btn btn-primary" value="Delete" name="btnDelete"/>
		<input type="submit" class="btn btn-danger" value="Cancel" name="btnNo"/>
	</form>
	<div class="separator"> </div>
</div>
			
<?php include_once('close_database.php'); ?>