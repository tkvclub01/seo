<?php
include_once ('connect_database.php');
include_once ('functions.php');
include_once 'lib/PHPExcel/IOFactory.php';
include_once ('lib/PHPExcel.php');
include 'lib/PHPExcel/Writer/Excel2007.php';
error_reporting ( 1 );
?>

<div id="content" class="container col-md-12">
	<?php
	$total_records_export = 0;
	// create object of functions class
	$function = new functions ();
	$keyword_paging = '';
	
	
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
	
	$total_records = 0;
	if (isset ( $_GET ['keyword'] )) {
		// check value of keyword variable
		$keyword = $function->sanitize ( $_GET ['keyword'] );
		$bind_keyword = "%" . $keyword . "%";
	} else {
		$keyword = "";
		$bind_keyword = $keyword;
	}
	$create_at = '';
	$create_at_query = "";
	if (! empty ( $_GET ['created_at'] )) {
		$create_at = " and d.`updated-date` = '" . $_GET ['created_at'] . "'";
		$create_at_query = " and `updated-date` = '" . $_GET ['created_at'] . "'";
	}
	// create array variable to store data from database
	$data = array ();
	$search_engine = "www.google.com.my";

	$sql_query = "SELECT * from `data` where project_id=" . $_GET ['project_id'] .  " and `search-engine`='". $search_engine."'";
	$stmt = $connect->stmt_init ();
	
	$file_name = date("Y-m-d")."_keywords_ranking_".$data_project_name.".xlsx";
	$objPHPExcel = new PHPExcel();
	$is_file_exists = false;
	if (file_exists ( 'templates/' . $file_name )) {
		$is_file_exists = true;
		unlink ( 'templates/' . $file_name );
	}
	// Set properties
	//echo date('H:i:s') . " Set properties<br />";
	$objPHPExcel->getProperties()->setCreator("DATO-TANDT");
	$objPHPExcel->getProperties()->setLastModifiedBy("TANDT");
	$objPHPExcel->getProperties()->setTitle("Keywords Ranking ". $data_project_name);
	$objPHPExcel->getProperties()->setSubject("Keywords Ranking ". $data_project_name);
	$objPHPExcel->getProperties()->setDescription("File report ranking keywords for project ". $data_project_name);
	$objPHPExcel->setActiveSheetIndex(0);
	
	// Add some data
	$objPHPExcel->getActiveSheet ()->getColumnDimension('A')->setWidth(25);
	$objPHPExcel->getActiveSheet()->setCellValue('A1', $data_project_name);
	
	$style_font_heading = array (
			'font' => array (
					'bold' => true,
					'color' => array (
							'rgb' => '000000'
					),
					'size' => 11,
					'name' => 'Arial'
			),
			'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'ffff00')
			),
			'borders' => array(
					'allborders' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
					)
			)
	);
	$style_font_border = array(
			'borders' => array(
					'allborders' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
					)
			)
	);
	$style_font_first_column = array(
			'font' => array (
					'bold' => true,
					'color' => array (
							'rgb' => 'ff0000'
					),
					'size' => 11,
					'name' => 'Arial'
			)
	);
	
	$objPHPExcel->getActiveSheet ()->getStyle ( 'A2' )->applyFromArray ( $style_font_heading );
	$objPHPExcel->getActiveSheet ()->setCellValue("A2","Keywords");
	$objPHPExcel->getActiveSheet ()->getStyle ( 'A2' )->applyFromArray ( $style_font_heading );
	
	$data_date = array();
	$total_get_date = 0;
	$query_get_date = "SELECT `updated-date` from `data` where project_id = ". $_GET['project_id'] ." and `search-engine`='www.google.com.my' GROUP BY `updated-date` DESC";
	$stmt_get_date = $connect->stmt_init ();
	if ($stmt_get_date->prepare ( $query_get_date )) {
		// Execute query
		$stmt_get_date->execute ();
		// store result
		$stmt_get_date->store_result ();
		$stmt_get_date->bind_result ( $data_date ['updated-date'] );
		// get total records
		$total_get_date = $stmt_get_date->num_rows;
	}
	
	$highestColumm = $objPHPExcel->getActiveSheet()->getHighestColumn(); // e.g. "EL"
	$highestRow =1;
	
	$date_array_new = array();
	while ( $stmt_get_date->fetch () ) {
		array_push($date_array_new, $data_date['updated-date']);
	}
	

	$columnLetter = PHPExcel_Cell::stringFromColumnIndex($total_get_date+1);
	//$colIndex = PHPExcel_Cell::columnIndexFromString("C");
	
	$dataset = array();
	for ($row = 2; $row <= 2; $row++) {
		for ($column = 'B'; $column != $columnLetter; $column++) {
			$cell_date = $objPHPExcel->getActiveSheet()->getCell($column . $row);
			$colIndex = PHPExcel_Cell::columnIndexFromString($column);
			for($i=0; $i < $total_get_date ; $i++){
				if($colIndex == $i+2){
					$cell_date->setValue($date_array_new[$i]);
					$j=2;
					$sql_query1 = $sql_query . " and `updated-date` = '" . $date_array_new[$i] . "'";
					if ($stmt->prepare ( $sql_query1 )) {
						// Execute query
						$stmt->execute ();
						// store result
						$stmt->store_result ();
						$stmt->bind_result (
								$data ['id'],
								$data ['project_id'],
								$data ['keywords'],
								$data ['updated-date'],
								$data ['search-engine'],
								$data ['current-rank'],
								$data ['previous-rank'],
								$data ['url'],
								$data ['status'] );
						// get total records
						$total_records = $stmt->num_rows;
						while ( $stmt->fetch () ) {
							$j= $j+1;
							$objPHPExcel->getActiveSheet ()->setCellValue('A' . strval ( $j), $data['keywords']);
							$objPHPExcel->getActiveSheet ()->getStyle ( 'A' . strval ( $j) )->applyFromArray ( $style_font_border );
							$objPHPExcel->getActiveSheet ()->getStyle ( 'B2' )->applyFromArray ( $style_font_first_column );
							if($colIndex == $i+2){
								$objPHPExcel->getActiveSheet ()->setCellValue($column . strval ( $j), $data['current-rank']);
								$objPHPExcel->getActiveSheet ()->getStyle ( $column . strval ( $j) )->applyFromArray ( $style_font_border );
							}
						}
					}
				}
			}
		
			$objPHPExcel->getActiveSheet()->getStyle ( $column . $row)->applyFromArray ( $style_font_heading );
			$objPHPExcel->getActiveSheet ()->getColumnDimension($column)->setWidth(15);
		}
	}
	//install column
	//$objPHPExcel->getActiveSheet()->insertNewColumnBefore('A', 1);
	
	$objPHPExcel->getActiveSheet()->setTitle("www.google.com.my");
	$objPHPExcel->createSheet(1);
	$objPHPExcel->setActiveSheetIndex(1);
	$objPHPExcel->getActiveSheet()->setTitle("www.google.com");
	
	
	//GOOGLE.COM
	$data = array ();
	$search_engine = "www.google.com";
	
	$sql_query = "SELECT * from `data` where project_id=" . $_GET ['project_id'] .  " and `search-engine`='". $search_engine."'";
	$stmt = $connect->stmt_init ();
	// Add some data
	$objPHPExcel->getActiveSheet ()->getColumnDimension('A')->setWidth(25);
	$objPHPExcel->getActiveSheet()->setCellValue('A1', $data_project_name);
	
	$objPHPExcel->getActiveSheet ()->getStyle ( 'A2' )->applyFromArray ( $style_font_heading );
	$objPHPExcel->getActiveSheet ()->setCellValue("A2","Keywords");
	$objPHPExcel->getActiveSheet ()->getStyle ( 'A2' )->applyFromArray ( $style_font_heading );
	
	$data_date = array();
	$total_get_date = 0;
	$query_get_date = "SELECT `updated-date` from `data` where project_id = ". $_GET['project_id'] ." and `search-engine`='www.google.com' GROUP BY `updated-date` DESC";
	$stmt_get_date = $connect->stmt_init ();
	if ($stmt_get_date->prepare ( $query_get_date )) {
		// Execute query
		$stmt_get_date->execute ();
		// store result
		$stmt_get_date->store_result ();
		$stmt_get_date->bind_result ( $data_date ['updated-date'] );
		// get total records
		$total_get_date = $stmt_get_date->num_rows;
	}
	
	$highestColumm = $objPHPExcel->getActiveSheet()->getHighestColumn(); // e.g. "EL"
	$highestRow =1;
	
	$date_array_new = array();
	while ( $stmt_get_date->fetch () ) {
		array_push($date_array_new, $data_date['updated-date']);
	}
	
	$columnLetter = PHPExcel_Cell::stringFromColumnIndex($total_get_date+1);
	//$colIndex = PHPExcel_Cell::columnIndexFromString("C");
	
	$dataset = array();
	for ($row = 2; $row <= 2; $row++) {
		for ($column = 'B'; $column != $columnLetter; $column++) {
			$cell_date = $objPHPExcel->getActiveSheet()->getCell($column . $row);
			$colIndex = PHPExcel_Cell::columnIndexFromString($column);
			for($i=0; $i < $total_get_date ; $i++){
				if($colIndex == $i+2){
					$cell_date->setValue($date_array_new[$i]);
					$j=2;
					$sql_query1 = $sql_query . " and `updated-date` = '" . $date_array_new[$i] . "'";
					if ($stmt->prepare ( $sql_query1 )) {
						// Execute query
						$stmt->execute ();
						// store result
						$stmt->store_result ();
						$stmt->bind_result (
								$data ['id'],
								$data ['project_id'],
								$data ['keywords'],
								$data ['updated-date'],
								$data ['search-engine'],
								$data ['current-rank'],
								$data ['previous-rank'],
								$data ['url'],
								$data ['status'] );
						// get total records
						$total_records = $stmt->num_rows;
						while ( $stmt->fetch () ) {
							$j= $j+1;
							$objPHPExcel->getActiveSheet ()->setCellValue('A' . strval ( $j), $data['keywords']);
							$objPHPExcel->getActiveSheet ()->getStyle ( 'A' . strval ( $j) )->applyFromArray ( $style_font_border );
							$objPHPExcel->getActiveSheet ()->getStyle ( 'B2' )->applyFromArray ( $style_font_first_column );
							if($colIndex == $i+2){
								$objPHPExcel->getActiveSheet ()->setCellValue($column . strval ( $j), $data['current-rank']);
								$objPHPExcel->getActiveSheet ()->getStyle ( $column . strval ( $j) )->applyFromArray ( $style_font_border );
							}
						}
					}
				}
			}
	
			$objPHPExcel->getActiveSheet()->getStyle ( $column . $row)->applyFromArray ( $style_font_heading );
			$objPHPExcel->getActiveSheet ()->getColumnDimension($column)->setWidth(15);
		}
	}
	
	$error1 = "";
	$objPHPExcel->setActiveSheetIndex(0);
	$objWriter = new PHPExcel_Writer_Excel2007 ( $objPHPExcel );
	try {
		$objWriter->save ( 'templates/'.$file_name );
		// insert new data to menu table
		$sql_query_install = "INSERT INTO `history_export` (`file_name`, `descriptions`, `create_date`, `project_id`, `status`) VALUES (
					'".$file_name."',"
					." 'DATO-SEO', "
					." '".date("Y-m-d")."', "
					. $_GET['project_id'].", 1)";
		
		if ($is_file_exists) {
		}else {
			$stmt_install = $connect->stmt_init();
			if($stmt_install->prepare($sql_query_install)) {
				$stmt_install->execute();
				// store result
				$result = $stmt_install->store_result();
				$stmt_install->close();
				if($result){
					$error1 = " <h4><div class='alert alert-success'>* Export success.
														<i class='fa fa-check fa-lg'></i>
														</div>
												  </h4>";
				}else{
					$error1 = " <span class='label label-danger'>Failed to export</span>";
				}
			}
		}
		
		
	} catch (Exception $e) {
		echo 'ERROR: ', $e->getMessage();
		die();
	}
	// create array variable to store data from database
	$data_export = array ();
	$sql_query_export = "select * from `history_export` where `project_id`=". $_GET ['project_id'] ;
	$stmt_export = $connect->stmt_init ();
	if ($stmt_export->prepare ( $sql_query_export )) {
		// Execute query
		$stmt_export->execute ();
		// store result
		$stmt_export->store_result ();
		$stmt_export->bind_result ( $data_export ['id'], $data_export ['file_name'], $data_export ['descriptions'], $data_export ['create_date'], $data_export ['project_id'], $data_export ['status'] );
		// get total records
		$total_records_export = $stmt_export->num_rows;
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
	?>
	<?php 
			echo $error1;
		?>
	<div class="col-md-12">
		<h1>
			History file export project <strong style="color: green;"><?php echo $data_project_name; ?> </strong>
		</h1>
		<hr />
	</div>
	<!-- search form -->
	<form class="list_header" method="get">
		<div class="col-md-12">
			<label>Search by Date :</label>
		</div>

		<div class="col-md-3">
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
			<input type="submit" class="btn btn-primary" name="btnSearch"
				value="Search" />
		</div>
	</form>
	<!-- end of search form -->

	<br />
	<div class="col-md-12">
	<?php if ($total_records_export == 0) { ?>
		DATA NOT FOUND
	<?php } else {
		$row_number = $from + 1;
		?>
		<table class="table table-hover">
			<tr>
				<th>ID</th>
				<th>File Name</th>
				<th>Create Date</th>
				<th>Actions</th>
			</tr>
	<?php
		while ( $stmt_export->fetch () ) {
			?>
			<tr>
				<td><?php echo $data_export['id']; ?></td>
				<td><?php echo $data_export['file_name'];?></td>
				<td><?php echo $data_export['create_date'];?></td>
				<td><a href="templates/<?php echo $data_export['file_name']; ?>" title="Download"> Download</a> </td>
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
		$function->doPages ( $offset, 'export-project.php', '', $total_records_export, "project_id=" . $_GET ['project_id'] . "&export=1" );
	?>
	</h4>
	</div>
	<div class="separator"></div>
</div>

<?php
include_once ('close_database.php');
?>
					
				