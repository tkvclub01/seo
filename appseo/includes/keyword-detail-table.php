<?php
	include_once('connect_database.php'); 
	include_once('functions.php'); 
	error_reporting(0);
?>

<div id="content" class="container col-md-12">
	<?php 
		$total_records = 0;
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
			
		$sql_query = "SELECT *
				FROM data
				WHERE keywords ='".$_REQUEST['keyword']."'
				AND project_id = ".$_GET['project_id']. " 
				AND `search-engine` = '".$_REQUEST['search-engine']."'
				ORDER BY `updated-date` DESC ";
		
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
					$data['keywords'],
					$data['updated-date'],
					$data['search-engine'],
					$data['current-rank'],
					$data['search-engine'],
					$data['previous-rank'],
					$data['url'],
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
		
		$sql_query = "SELECT *
				FROM data
				WHERE keywords ='".$_REQUEST['keyword']."' 
				AND project_id = ". $_GET['project_id']."
				AND `search-engine` = '".$_REQUEST['search-engine']."'
				ORDER BY `updated-date` DESC ";
		
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
					$data['project_id'],
					$data['keywords'],
					$data['updated-date'],
					$data['search-engine'],
					$data['current-rank'],
					$data['previous-rank'],
					$data['url'],
					$data['status']
					);
			// for paging purpose
			$total_records_paging = $total_records; 
		}

		// if no data on database show "No Reservation is Available"
		if($total_records_paging == 0){
	
	?>
	<h1>Project Manager
		<a href="import-data.php">
			<button class="btn btn-danger">Import Data</button>
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
			Keyword: <?php echo $_REQUEST['keyword']?>
		</h1>
	</div>
	<div class="col-md-12">
	<table class="table table-hover">
		<tr>
			<th>Update Date</th>
			<th>Search Engine</th>
			<th>Current Rank</th>
			<th>Change</th>
			<th></th>
			<th>URL</th>
			
		</tr>
	<?php 
		while ($stmt_paging->fetch()){ 
			$sql_get_change = "SELECT `current-rank` FROM `data` WHERE
									keywords = ? and `search-engine`= ?
								AND `updated-date` < (
								SELECT `updated-date` from data as data2 where data2.keywords = ? and data2.`search-engine`= ? ORDER BY data2.`updated-date` DESC LIMIT 1 
								)";
			$dataChange = array();
			$stmt = $connect->stmt_init();
			if($stmt->prepare($sql_get_change)) {
				// Bind your variables to replace the ?s
				$stmt->bind_param('ssss', $data['keywords'],$data['search-engine'],$data['keywords'],$data['search-engine']);
				// Execute query
				$stmt->execute();
				// store result
				$stmt->store_result();
				$stmt->bind_result($dataChange['current-rank']);
				$stmt->fetch();
				$stmt->close();
			}
				$previous_rank = 0;
				
				if(isset($dataChange['current-rank'])){
					$previous_rank = $data['current-rank'] - $dataChange['current-rank'];
				}else{
					$previous_rank = $data['current-rank'];
				}
			?>
			<tr>
				<td><?php echo $data['updated-date'];?></td>
				<td><?php echo $data['search-engine'];?></td>
				<td><?php echo $data['current-rank'];?></td>
				<td><?php echo $previous_rank; ?></td>
				<td><?php 
						if($previous_rank<0){
							echo '<img src="images/down.jpg" />';
						}elseif($previous_rank>0){
							echo '<img src="images/up.jpg" />';
						}
				?></td>
				<td><?php echo $data['url'];?></td>
				
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
	include_once('close_database.php'); ?>
					
				