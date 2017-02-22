<?php
include_once('connect_database.php');
include_once('functions.php');

include_once 'lib/PHPExcel.php';
include_once 'lib/PHPExcel/IOFactory.php';
ini_set("display_errors", 1);
ini_set("track_errors", 1);
ini_set("html_errors", 1);
error_reporting(E_ALL);
?>
<div id="content" class="container col-md-12">
	<?php 
		$projects_data = array();
		
		if(isset($_POST["btnImport"]) && !empty( $_FILES['file_name']['name'])){
			
			$file_name = $_FILES['file_name']['name'];
			$file_name_error = $_FILES['file_name']['error'];
			$file_name_type = $_FILES['file_name']['type'];
		
			// get file file extension
			error_reporting(E_ERROR | E_PARSE);
			$extension = end(explode(".", $_FILES["file_name"]["name"]));
			$allowedExts = array("xlsx");
			if($file_name_error > 0){
				$error['file_name'] = " <span class='label label-danger'>File Not Uploaded!!</span>";
			}else if(!(($file_name_type == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet")) &&
					!(in_array($extension, $allowedExts))){
						$error['file_name'] = " <span class='label label-danger'>File type must XlSX!</span>";
			}
				
			$path = 'upload/'.$file_name;
			$upload = move_uploaded_file($_FILES['file_name']['tmp_name'], $path);
			error_reporting(E_ERROR | E_PARSE);
			
			copy($file_name, $path);
			
			$objPHPExcel = PHPExcel_IOFactory::load($path);
			foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
				$worksheetTitle     = $worksheet->getTitle();
				$highestRow         = $worksheet->getHighestRow(); // e.g. 10
				$highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
				$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
				$nrColumns = ord($highestColumn) - 64;
			}
			for ($row = 4; $row <= $highestRow; ++ $row) {
				$val=array();
				for ($col = 0; $col < $highestColumnIndex; ++ $col) {
					$cell = $worksheet->getCellByColumnAndRow($col, $row);
					$val[] = $cell->getValue();
				}
				
				$total_result = 0;
				//check data overide
				$sql_check_data = "select * from `data` where `project_id` = " . $_POST['project_id'] . " and `keywords`='".$val[1]."' and `search-engine` = '".$val[2]."' and `updated-date` = '" . $val[0] . "'";
				
				$stmt_check = $connect->stmt_init();
				if($stmt_check->prepare($sql_check_data)) {
					$stmt_check->execute();
					$stmt_check->store_result();
					$total_result = $stmt_check->num_rows;
				}
				
				$updated_date = PHPExcel_Shared_Date::ExcelToPHP($val[0]); 
				$updated_date = date('Y-m-d',$updated_date);
				
				$search_engine_file = $val[2];
				if($search_engine_file == 'Google'){
					$search_engine_file = 'www.google.com';
				}
				
				if($total_result > 0){
					
					$Connection="UPDATE `data` set `current-rank` = '".$val[4]."', `previous-rank` = '" .$val[5]. "', `url` = '" . $val[8] . "'
									where `project_id` = " . $_POST['project_id'] . " and `keywords`='".$val[1]."' and `search-engine` = '".$search_engine_file."' and `updated-date` = '" . $updated_date . "'";
					$stmt = $connect->stmt_init();
					if($stmt->prepare($Connection)) {
						$stmt->execute();
						$update_result = $stmt->store_result();
					}
				}else{
					$Connection="INSERT INTO `data` (`project_id`, `keywords`, `updated-date`, `search-engine`, `current-rank`, `previous-rank`, `url`, `status`)
					VALUES ('".$_POST['project_id'] . "','" . $val[1] . "','" . $updated_date. "','" . $search_engine_file. "','" . $val[4]. "','" . $val[5]. "','" . $val[8]. "','0')";
					$stmt = $connect->stmt_init();
					if($stmt->prepare($Connection)) {
						$stmt->execute();
						$insert_result = $stmt->store_result();
					}
				}
				
			}
			
			if($insert_result || $update_result){
				$error['update_projects'] = " <h4><div class='alert alert-success'>
														* Import Data success.
														<a href='projects.php'>
														<i class='fa fa-check fa-lg'></i>
														</a></div>
												  </h4>";
			}else{
				$error['update_projects'] = " <span class='label label-danger'>Failed to Import Data.</span>";
			}
			
		}
		
		//$objPHPExcel = PHPExcel_IOFactory::load($path);
		// create array variable to store previous data
		$data = array();
		
		$sql_query = "SELECT * FROM projects";
		
		$stmt = $connect->stmt_init();
		if($stmt->prepare($sql_query)) {	
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result(
					$data['id'], 
					$data['name'],
					$data['create_at'],
					$data['create_by'],
					$data['status']
					);
			$total_records = $stmt->num_rows;
		}

		if(isset($_POST['btnCancel'])){
			header("location: projects.php");
		}
		
	?>
	<div class="col-md-12">
		<h1>Import Project</h1>
		<?php echo isset($error['update_projects']) ? $error['update_projects'] : '';?>
		<hr />
	</div>
	
	<div class="col-md-5">
		<form method="post"
			enctype="multipart/form-data">
			<label>Project Name :</label><?php echo isset($error['project_id']) ? $error['project_id'] : '';?>
			<select class="form-control" name="project_id">
			<?php while ($stmt->fetch()){ ?>
				<option value="<?php echo $data["id"]; ?>"><?php echo $data["name"]; ?></option>
			<?php  }  ?>
				
			</select>
			<br/>
			<label>File input:</label><?php echo isset($error['file_name']) ? $error['file_name'] : '';?>
			(Download file EX: <a href="rank_report_www.vdato.com_2017-02-10.xlsx" title="rank_report_www.vdato.com_2017-02-10.xlsx">rank_report_www.vdato.com_2017-02-10.xlsx</a>
			<input type="file" name="file_name" class="form-control"/>
			<br/><br/>
			<input type="submit" class="btn-primary btn" value="Import" name="btnImport"/>
			<input type="submit" class="btn-danger btn" value="Cancel" name="btnCancel"/>
		</form>
	</div>

	<div class="separator"> </div>
</div>
	
<?php include_once('close_database.php'); ?>