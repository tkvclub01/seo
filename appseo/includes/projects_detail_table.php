<?php
include_once ('connect_database.php');
include_once ('functions.php');
error_reporting ( 0 );
?>

<div id="content" class="container col-md-12">
	<?php
	$total_records = 0;
	// create object of functions class
	$function = new functions ();
	if (isset ( $_GET ['keyword'] )) {
		// check value of keyword variable
		$keyword = $function->sanitize ( $_GET ['keyword'] );
		$bind_keyword = "%" . $keyword . "%";
	} else {
		$keyword = "";
		$bind_keyword = $keyword;
	}
	$keyword_paging = $keyword;
	$create_at = '';
	$create_at_query = "";
	if (! empty ( $_GET ['created_at'] )) {
		$keyword_paging = $keyword_paging . '&created_at=' . $_GET ['created_at'];
		$create_at = " and d.`updated-date` = '" . $_GET ['created_at'] . "'";
		$create_at_query = " and `updated-date` = '" . $_GET ['created_at'] . "'";
	}
	// create array variable to store data from database
	$data = array ();
	$select_search = 0;
	$my_selected = "";
	$com_selected = "";
	$para_search_engine = '';
	$para_search_engine_query = '';
	if (isset ( $_GET ['search_engines'] )) {
		$search_engines = $_GET ['search_engines'];
		$para_search_engine = ' and ( ';
		$para_search_engine_query = ' and ( ';
		foreach ( $search_engines as $search_engine ) {
			if ($search_engine == "www.google.com") {
				$com_selected = " selected ";
			}
			if ($search_engine == "www.google.com.my") {
				$my_selected = " selected ";
			}
			$keyword_paging = $keyword_paging . '&search_engines%5B%5D=' . $search_engine;
			$para_search_engine = $para_search_engine . " d.`search-engine` = '" . $search_engine . "' or ";
			$para_search_engine_query = $para_search_engine_query . " `search-engine` = '" . $search_engine . "' or ";
			$select_search += 1;
		}
		$para_search_engine = substr ( $para_search_engine, 0, - 3 );
		$para_search_engine = $para_search_engine . " ) ";
		$para_search_engine_query = substr ( $para_search_engine_query, 0, - 3 );
		$para_search_engine_query = $para_search_engine_query . " ) ";
	}
	
	if (empty ( $keyword )) {
		$sql_query = "SELECT  d.* FROM data  as d 
						JOIN ( SELECT `updated-date` as date_tmp FROM DATA WHERE project_id = " . $_GET ['project_id'] . $create_at_query . $para_search_engine_query . " ORDER BY `updated-date` DESC LIMIT 1 ) AS abc
						ON d.`updated-date` = abc.date_tmp
						where d.project_id = " . $_GET ['project_id'] . $create_at . $para_search_engine;
	} else {
		$sql_query = "SELECT  d.* FROM data  as d
						JOIN ( SELECT `updated-date` as date_tmp FROM DATA WHERE project_id = " . $_GET ['project_id'] . $create_at_query . $para_search_engine_query . " ORDER BY `updated-date` DESC LIMIT 1 ) AS abc
						ON d.`updated-date` = abc.date_tmp
						where d.keywords LIKE ? and d.project_id = " . $_GET ['project_id'] . $create_at . $para_search_engine;
	}
	$stmt = $connect->stmt_init ();
	if ($stmt->prepare ( $sql_query )) {
		// Bind your variables to replace the ?s
		if (! empty ( $keyword )) {
			$stmt->bind_param ( 's', $bind_keyword );
		}
		// Execute query
		$stmt->execute ();
		// store result
		$stmt->store_result ();
		$stmt->bind_result ( $data ['id'], $data ['project_id'], $data ['keywords'], $data ['updated-date'], $data ['search-engine'], $data ['current-rank'], $data ['previous-rank'], $data ['url'], $data ['status'] );
		// get total records
		$total_records = $stmt->num_rows;
	}
	
	// check page parameter
	if (isset ( $_GET ['page'] )) {
		$page = $_GET ['page'];
	} else {
		$page = 1;
	}
	
	// number of data that will be display per page
	$offset = 10;
	
	// lets calculate the LIMIT for SQL, and save it $from
	if ($page) {
		$from = ($page * $offset) - $offset;
	} else {
		// if nothing was given in page request, lets load the first page
		$from = 0;
	}
	
	// create array variable to store previous data
	
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
	?>
	<div class="col-md-12">
		<h1>
			Details Project <strong style="color: green;"><?php echo $data_project_name; ?> </strong>
			<a href="import-data.php">
				<button class="btn btn-danger">Import Data</button>
			</a>
			<?php 
				$row_number = $from + 1;
				//$url_export = str_replace('project-detail.php', "export-project.php", "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]&export=1");
				$url_export = "export-project.php?project_id=".$_GET['project_id']."&export=1";
			?>
			<a class="btn btn-success" href=<?php echo $url_export; ?> >Export data</a>
		</h1>
		<hr />
	</div>
	<!-- search form -->
	<form class="list_header" method="get">
		<div class="col-md-12">
			<label>Search by keywords :</label>
		</div>

		<div class="col-md-3">
			<input type="text" class="form-control" name="keyword"
				<?php
				if (isset ( $_GET ['keyword'] )) {
					echo " value='" . $_GET ['keyword'] . "'";
				}
				?> /> <input type="hidden" name="project_id"
				value="<?php echo $_GET['project_id']; ?>" /> <label>Search by
				update date :</label>
			<div class="input-group">
				<div class="input-group-addon">
					<i class="fa fa-calendar"></i>
				</div>
				<input class="form-control" id="date" name="created_at"
					placeholder="YYYY-MM-DD" type="text"
					<?php
					if (isset ( $_GET ['created_at'] )) {
						echo " value='" . $_GET ['created_at'] . "'";
					}
					?> />
			</div>
			<br />
		</div>
		<div class="col-md-3">
			<select multiple class="form-control" name="search_engines[]"
				style="height: 94px;">
				<option value="www.google.com"
					<?php
					echo $com_selected;
					?>>www.google.com</option>
				<option value="www.google.com.my"
					<?php
					echo $my_selected;
					?>>www.google.com.my</option>
			</select>
		</div>
		<div class="col-md-3">
			<a class="btn btn-danger" href="project-detail.php?project_id=<?php echo $_GET['project_id']; ?>" >Reset</a>
			<input type="submit" class="btn btn-primary" name="btnSearch"
				value="Search" />
		</div>
	</form>
	<!-- end of search form -->

	<br />
	<div class="col-md-12">
	<?php if ($total_records == 0) { ?>
		DATA NOT FOUND
	<?php } else {
		?>
		<table class="table table-hover">
			<tr>
				<th>ID</th>
				<th>Keywords</th>
				<th>Update Date</th>
				<th>Search Engine</th>
				<th>Current Rank</th>
				<th>Change</th>
				<th>URL</th>
				<th>Actions</th>
			</tr>
	<?php
		while ( $stmt->fetch () ) {
			$sql_get_change = "SELECT `current-rank` FROM `data` WHERE
									keywords = ? and `search-engine`= ?
								AND `updated-date` < (
								SELECT `updated-date` from data as data2 where data2.keywords = ? and data2.`search-engine`= ? ORDER BY data2.`updated-date` DESC LIMIT 1 
								)";
			$dataChange = array ();
			$stmt_change = $connect->stmt_init ();
			if ($stmt_change->prepare ( $sql_get_change )) {
				// Bind your variables to replace the ?s
				$stmt_change->bind_param ( 'ssss', $data ['keywords'], $data ['search-engine'], $data ['keywords'], $data ['search-engine'] );
				// Execute query
				$stmt_change->execute ();
				// store result
				$stmt_change->store_result ();
				$stmt_change->bind_result ( $dataChange ['current-rank'] );
				$stmt_change->fetch ();
				$stmt_change->close ();
			}
			$previous_rank = 0;
			
			if (isset ( $dataChange ['current-rank'] )) {
				$previous_rank = $data ['current-rank'] - $dataChange ['current-rank'];
			} else {
				$previous_rank = $data ['current-rank'];
			}
			?>
			<tr>
				<td><?php echo $data['id']; ?></td>
				<td><?php echo $data['keywords'];?></td>
				<td><?php echo $data['updated-date'];?></td>
				<td><?php echo $data['search-engine'];?></td>
				<td><?php echo $data['current-rank'];?></td>
				<td><?php echo $previous_rank; ?></td>
				<td><?php echo $data['url'];?></td>
				<td><?php echo "<a href=\"keyword-detail.php?search-engine=".$data['search-engine']."&project_id=".$data['project_id']."&keyword=".$data['keywords']."\"> View </a>"; ?></td>
			</tr>
		<?php
		}
	?>
	</table>
	<?php } ?>
	</div>

	<div class="col-md-12">
		<h4>
	
	<?php
		// for pagination purpose
		$function->doPages ( $offset, 'project-detail.php', '', $total_records, $keyword_paging . "&project_id=" . $_GET ['project_id'] );
	?>
	</h4>
	</div>
	<div class="separator"></div>
</div>

<?php
include_once ('close_database.php');
?>
					
				