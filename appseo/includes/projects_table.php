<?php
	include_once('connect_database.php'); 
	include_once('functions.php'); 
?>

<div id="content" class="container col-md-12">
	<?php 
		// create object of functions class
		$function = new functions;
		
		// create array variable to store data from database
		$data = array();
		
		if(isset($_GET['keyword'])){	
			// check value of keyword variable
			$keyword = $function->sanitize($_GET['keyword']);
			$bind_keyword = "%".$keyword."%";
		}else{
			$keyword = "";
			$bind_keyword = $keyword;
		}
			
		if(empty($keyword)){
			$sql_query = "SELECT *
					FROM projects
					ORDER BY name DESC";
		}else{
			$sql_query = "SELECT *
					FROM projects
					WHERE name LIKE ? 
					ORDER BY name DESC";
		}
		
		
		$stmt = $connect->stmt_init();
		if($stmt->prepare($sql_query)) {	
			// Bind your variables to replace the ?s
			if(!empty($keyword)){
				$stmt->bind_param('s', $bind_keyword);
			}
			// Execute query
			$stmt->execute();
			// store result 
			$stmt->store_result();
			$stmt->bind_result($data['id'], 
					$data['name'],
					$data['created_at'],
					$data['created_by'],
					$data['status']
					);
			// get total records
			$total_records = $stmt->num_rows;
		}
			
		// check page parameter
		if(isset($_GET['page'])){
			$page = $_GET['page'];
		}else{
			$page = 1;
		}
						
		// number of data that will be display per page		
		$offset = 10;
						
		//lets calculate the LIMIT for SQL, and save it $from
		if ($page){
			$from 	= ($page * $offset) - $offset;
		}else{
			//if nothing was given in page request, lets load the first page
			$from = 0;	
		}	
		
		if(empty($keyword)){
			$sql_query = "SELECT *
					FROM projects
					ORDER BY name DESC LIMIT ?, ?";
		}else{
			$sql_query = "SELECT *
					FROM projects
					WHERE name LIKE ?
					ORDER BY name DESC LIMIT ?, ?";
		}
		
		$stmt_paging = $connect->stmt_init();
		if($stmt_paging ->prepare($sql_query)) {
			// Bind your variables to replace the ?s
			if(empty($keyword)){
				$stmt_paging ->bind_param('ss', $from, $offset);
			}else{
				$stmt_paging ->bind_param('sss',$bind_keyword, $from, $offset);
			}
			// Execute query
			$stmt_paging ->execute();
			// store result 
			$stmt_paging ->store_result();
			$stmt_paging->bind_result($data['id'], 
					$data['name'],
					$data['created_at'],
					$data['created_by'],
					$data['status']
					);
			// for paging purpose
			$total_records_paging = $total_records; 
		}

		// if no data on database show "No Reservation is Available"
		if($total_records_paging == 0){
	
	?>
	<h1>Project Manager
		<a href="add-projects.php">
			<button class="btn btn-danger">Add New Projects</button>
		</a>
	</h1>
	<hr />
	<?php 
		// otherwise, show data
		}else{
			$row_number = $from + 1;
	?>

	<div class="col-md-12">
		<h1>
			Projects List
			<a href="add-projects.php">
				<button class="btn btn-danger">Add Project</button>
			</a>
		</h1>
		<hr/>
	</div>
	<!-- search form -->
	<form class="list_header" method="get">
		<div class="col-md-12">
			<p class="pholder">Search by Name : </p>
		</div>

		<div class="col-md-3">
			<input type="text" class="form-control" name="keyword" />
		</div>

		<br>
		&nbsp;&nbsp;&nbsp;
		<input type="submit" class="btn btn-primary" name="btnSearch" value="Search" />
	</form>
	<!-- end of search form -->
	
	<br/>
	<div class="col-md-12">
	<table class="table table-hover">
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Create Date</th>
			<th>Create By</th>
			<th>Status</th>
			<th width="20%">Action</th>
		</tr>
	<?php 
		while ($stmt_paging->fetch()){ ?>
			<tr>
				<td><?php echo $data['id'];?></td>
				<td><?php echo $data['name'];?></td>
				<td><?php echo $data['created_at'];?></td>
				<td><?php echo $data['created_by'];?></td>
				<td><?php echo $data['status'];?></td>
				<td>
					<a href="project-detail.php?project_id=<?php echo $data['id'];?>">
					View
					</a>&nbsp;
					<a href="edit-projects.php?id=<?php echo $data['id'];?>">
					Edit
					</a>&nbsp;
					<a href="delete-projects.php?id=<?php echo $data['id'];?>">
					Delete
					</a>
				</td>
			</tr>
		<?php 
		} 
	}
?>
	</table>
	</div>

	<div class="col-md-12">
	<h4>
	
	<?php 
	if($total_records < 1){
		echo "Not found record with keywords <strong style='color:red'>" . $keyword."</strong>";
	}
		// for pagination purpose
		$function->doPages($offset, 'projects.php', '', $total_records, $keyword);?>
	</h4>
	</div>
	<div class="separator"> </div>
</div> 

<?php 
	$stmt->close();
	include_once('close_database.php'); ?>
					
				