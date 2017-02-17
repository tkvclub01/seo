<?php
/** Error reporting */
// error_reporting(0);
if ($_POST ['domain'] == '') {
	echo "domain is empty, must be input";
	die ();
}
include ("db/db_config.php");
include ("db/db_lib.php");
include 'lib/PHPExcel/IOFactory.php';
require ('lib/PHPExcel.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Keywords rank report page</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<center>
		<h1> "<?php echo $_POST['domain'];?>" report (<?php echo $_POST['reportdate']?>)</h1>
	</center>

	<table class="bordered">
		<tr>
			<th>Update date</th>
			<th>Keyword</th>
			<th>Search Engine</th>
			<th>Area</th>
			<th>Current Rank</th>
			<th>Previous Rank</th>
			<th>Change</th>
			<th>URL</th>
		</tr>

<?php

if ($_POST) {
	
	// Create Oj excel
	$objPHPExcel = new PHPExcel ();
	$objWriter = new PHPExcel_Writer_Excel2007 ( $objPHPExcel );
	$googleUrl = $_POST ['googleUrl'];
	
	// Clean the post data and make usable
	$domain = $_POST ['domain'];
	$keywords = $_POST ['keywords'];
	
	$keywords = trim ( $_POST ['keywords'] );
	$keywords = str_replace ( "\r\n", '|', $keywords );
	$list_keywords = explode ( '|', $keywords );
	
	// Remove begining http and trailing /
	$domain = trim ( $domain );
	$domain = substr ( $domain, 0, 7 ) == 'http://' ? substr ( $domain, 7 ) : $domain;
	$domain = substr ( $domain, - 1 ) == '/' ? substr_replace ( $domain, '', - 1 ) : $domain;
	
	// Create file excel
	$file_name = "rank_report_" . $domain . "_" . $_POST ['reportdate'] . ".xlsx";
	if (file_exists ( 'templates/' . $file_name )) {
		unlink ( 'templates/' . $file_name );
	}
	$objPHPExcel->setActiveSheetIndex ( 0 );
	$objPHPExcel->getActiveSheet ()->mergeCells ( 'A1:I1' );
	
	$style_text_center = array (
			'alignment' => array (
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER 
			) 
	);
	$style_font_title = array (
			'font' => array (
					'bold' => true,
					'color' => array (
							'rgb' => '666666' 
					),
					'size' => 24,
					'name' => 'Verdana' 
			) 
	);
	$style_font_heading = array (
			'font' => array (
					'bold' => true,
					'color' => array (
							'rgb' => '666666' 
					),
					'size' => 9,
					'name' => 'Verdana' 
			),
			'fill' => array (
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array (
							'rgb' => 'dce9f9' 
					) 
			) 
	);
	
	$style_color_red = array (
			'font' => array (
					'color' => array (
							'rgb' => 'ff0000' 
					) 
			) 
	);
	$style_color_green = array (
			'font' => array (
					'color' => array (
							'rgb' => '00ff0c' 
					) 
			) 
	);
	
	$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'A' )->setWidth ( 15 );
	$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'B' )->setWidth ( 40 );
	$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'C' )->setWidth ( 15 );
	$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'D' )->setWidth ( 10 );
	$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'E' )->setWidth ( 15 );
	$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'F' )->setWidth ( 15 );
	$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'G' )->setWidth ( 10 );
	$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'H' )->setWidth ( 10 );
	$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'I' )->setWidth ( 50 );
	
	$objPHPExcel->getActiveSheet ()->setCellValue ( 'A1', $_POST ['domain'] . ' report (' . $_POST ['reportdate'] . ')' );
	$objPHPExcel->getActiveSheet ()->getStyle ( 'A1' )->applyFromArray ( $style_text_center );
	$objPHPExcel->getActiveSheet ()->getStyle ( 'A1' )->applyFromArray ( $style_font_title );
	
	$objPHPExcel->getActiveSheet ()->setCellValue ( 'A2', $_POST ['googleUrl'] );
	$objPHPExcel->getActiveSheet ()->setCellValue ( 'A3', 'Update date' );
	$objPHPExcel->getActiveSheet ()->getStyle ( 'A3' )->applyFromArray ( $style_font_heading );
	$objPHPExcel->getActiveSheet ()->setCellValue ( 'B3', 'Keyword' );
	$objPHPExcel->getActiveSheet ()->getStyle ( 'B3' )->applyFromArray ( $style_font_heading );
	$objPHPExcel->getActiveSheet ()->setCellValue ( 'C3', 'Search Engine' );
	$objPHPExcel->getActiveSheet ()->getStyle ( 'C3' )->applyFromArray ( $style_font_heading );
	$objPHPExcel->getActiveSheet ()->setCellValue ( 'D3', 'Area' );
	$objPHPExcel->getActiveSheet ()->getStyle ( 'D3' )->applyFromArray ( $style_font_heading );
	$objPHPExcel->getActiveSheet ()->setCellValue ( 'E3', 'Current Rank' );
	$objPHPExcel->getActiveSheet ()->getStyle ( 'E3' )->applyFromArray ( $style_font_heading );
	$objPHPExcel->getActiveSheet ()->setCellValue ( 'F3', 'Previous Rank' );
	$objPHPExcel->getActiveSheet ()->getStyle ( 'F3' )->applyFromArray ( $style_font_heading );
	$objPHPExcel->getActiveSheet ()->mergeCells ( 'G3:H3' );
	$objPHPExcel->getActiveSheet ()->setCellValue ( 'G3', 'Change' );
	$objPHPExcel->getActiveSheet ()->getStyle ( 'G3' )->applyFromArray ( $style_font_heading );
	$objPHPExcel->getActiveSheet ()->setCellValue ( 'I3', 'URL' );
	$objPHPExcel->getActiveSheet ()->getStyle ( 'I3' )->applyFromArray ( $style_font_heading );
	
	for($k = 0; $k < count ( $list_keywords ); $k ++) {
		$i = 1;
		
		$keyword = trim ( $list_keywords [$k] );
		
		// Loop through the nodes to look for our domain
		
		$rank = '0';
		$path = '';
		$date = string_to_date ( $_POST ['reportdate'] );
		// This is the data you want to pass to Python
		
		$url_para = end ( explode ( '.', $_POST ['googleUrl'] ) );
		if ($url_para == 'com') {
			$url_para = "";
		} else {
			$url_para = "." . $url_para;
		}
		$data = $url_para . "@@" . $domain . "@@" . $keyword;
		ob_start ();
		$file_py_path = 'python search.py ';
		$result = passthru ( $file_py_path . trim ( $data ) );
		$result = ob_get_clean ();
		// rank@@url
		$result = explode ( '@@', trim ( $result ) );
		if (count ( $result ) > 1) {
			$rank = intval(trim(substr ( $result [0], - 2 )));
			$path = $result [1];
		}
		$pre_result = search_result ( $keyword, $domain, trim ( $_POST ['googleUrl'] ) );
		if (! empty ( $pre_result )) {
			$id = $pre_result ['id'];
			$curr_rank = $rank;
			$pre_rank = $pre_result ['rank'];
			update_result ( $id, trim ( $_POST ['googleUrl'] ), $keyword, $domain, $curr_rank, $pre_rank, $path, $date );
		} else {
			add_result ( $date, $keyword, $rank, $path, 'English', trim ( $_POST ['googleUrl'] ), $domain );
		}
	}
	
	
	if (isset ( $_POST ['report'] )) {
		var_dump($_POST ['reportdate']);
		if (isset ( $_POST ['reportdate'] )) {
			$row = get_content_by_date ( $_POST ['reportdate'], $_POST ['keywords'], $_POST ['domain'], trim ( $_POST ['googleUrl'] ) );
		} else {
			echo "Please back to index";
		}
		for($i = 0; $i < count ( $row ); $i ++) {
			// Write content to excel
			$objPHPExcel->getActiveSheet ()->setCellValue ( 'A' . strval ( $i + 4 ), $row [$i] ['date'] );
			$objPHPExcel->getActiveSheet ()->setCellValue ( 'B' . strval ( $i + 4 ), $row [$i] ['keyword'] );
			$objPHPExcel->getActiveSheet ()->setCellValue ( 'C' . strval ( $i + 4 ), $row [$i] ['engine'] );
			$objPHPExcel->getActiveSheet ()->setCellValue ( 'D' . strval ( $i + 4 ), 'English' );
			$objPHPExcel->getActiveSheet ()->setCellValue ( 'E' . strval ( $i + 4 ), $row [$i] ['rank'] );
			$objPHPExcel->getActiveSheet ()->setCellValue ( 'F' . strval ( $i + 4 ), $row [$i] ['pre_rank'] );
			?>
	<tr>
			<td><?php echo $row[$i]['date']; ?></td>
			<td><?php echo str_replace("+"," ",$row[$i]['keyword']);?></td>
			<td><?php echo $row[$i]['engine']; ?></td>
			<td><?php echo "English"; ?></td>
			<td><?php echo $row[$i]['rank']; ?></td>
			<td><?php echo $row[$i]['pre_rank'];?></td>
			<td><?php
			$cur_rank = $row [$i] ['rank'];
			$pre_rank = $row [$i] ['pre_rank'];
			$change = $cur_rank - $pre_rank;
			
			// create image
			$objDrawingDown = new PHPExcel_Worksheet_Drawing ();
			$objDrawingDown->setName ( 'Down image' );
			$objDrawingDown->setDescription ( 'Down image' );
			$objDrawingDown->setPath ( 'icon/down.png' );
			
			$objDrawingUp = new PHPExcel_Worksheet_Drawing ();
			$objDrawingUp->setName ( 'Up image' );
			$objDrawingUp->setDescription ( 'Up image' );
			$objDrawingUp->setPath ( 'icon/up.png' );
			
			if ($change > 0) {
				if ($pre_rank == 0) {
					echo "<font style='color: green;'>" . str_replace ( '-', '', ($pre_rank - $cur_rank) ) . "</font> <img src='icon/up.jpg'>";
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'G' . strval ( $i + 4 ), str_replace ( '-', '', ($pre_rank - $cur_rank) ) );
					$objPHPExcel->getActiveSheet ()->getStyle ( 'G' . strval ( $i + 4 ) )->applyFromArray ( $style_color_green );
					$objDrawingUp->setCoordinates ( 'H' . strval ( $i + 4 ) );
					$objDrawingUp->setWorksheet ( $objPHPExcel->getActiveSheet () );
				} else {
					echo "<font style='color: red;'>" . ($cur_rank - $pre_rank) . "</font> <img src='icon/down.jpg'>";
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'G' . strval ( $i + 4 ), ($cur_rank - $pre_rank) );
					$objPHPExcel->getActiveSheet ()->getStyle ( 'G' . strval ( $i + 4 ) )->applyFromArray ( $style_color_red );
					$objDrawingDown->setCoordinates ( 'H' . strval ( $i + 4 ) );
					$objDrawingDown->setWorksheet ( $objPHPExcel->getActiveSheet () );
				}
			} else if ($change < 0) {
				if ($cur_rank == 0) {
					echo "<font style='color: red;'>" . str_replace ( '-', '', ($cur_rank - $pre_rank) ) . "</font> <img src='icon/down.jpg'>";
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'G' . strval ( $i + 4 ), str_replace ( '-', '', ($pre_rank - $cur_rank) ) );
					$objPHPExcel->getActiveSheet ()->getStyle ( 'G' . strval ( $i + 4 ) )->applyFromArray ( $style_color_red );
					$objDrawingDown->setCoordinates ( 'H' . strval ( $i + 4 ) );
					$objDrawingDown->setWorksheet ( $objPHPExcel->getActiveSheet () );
				} else {
					echo "<font style='color: green;'>" . ($pre_rank - $cur_rank) . "</font> <img src='icon/up.jpg'>";
					$objPHPExcel->getActiveSheet ()->setCellValue ( 'G' . strval ( $i + 4 ), ($cur_rank - $pre_rank) );
					$objPHPExcel->getActiveSheet ()->getStyle ( 'G' . strval ( $i + 4 ) )->applyFromArray ( $style_color_green );
					$objDrawingUp->setCoordinates ( 'H' . strval ( $i + 4 ) );
					$objDrawingUp->setWorksheet ( $objPHPExcel->getActiveSheet () );
				}
			} else {
				echo "<font style='color: green;'> 0 </font>";
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'G' . strval ( $i + 4 ), '0' );
				$objPHPExcel->getActiveSheet ()->getStyle ( 'G' . strval ( $i + 4 ) )->applyFromArray ( $style_color_green );
			}
			?></td>
			<td><?php
			if (isset ( $row [$i] ['path'] )) {
				echo '<a href="http://' . $row [$i] ['path'] . '">' . $row [$i] ['path'] . '</a>';
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'I' . strval ( $i + 4 ), $row [$i] ['path'] );
			}
			
			?></td>
		</tr>
<?php
		}
	}
	$objPHPExcel->getActiveSheet ()->setTitle ( $_POST ['reportdate'] );
	$objWriter->save ( 'templates/' . $file_name );
}
?>
</table>
	Click Download file report:
	<a href="templates/<?php echo $file_name; ?>"
		title="<?php echo $file_name; ?>"><?php echo $file_name; ?></a>
</body>
</html>
