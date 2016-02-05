<?php
//xls

if($_GET['action'] == 'xlssasas'){
	global $wpdb;
	$q = "
		select users.*, meta2.meta_value hp, meta3.meta_value NIY from $wpdb->users users
		left join $wpdb->usermeta meta
		on meta.user_id = users.ID and meta.meta_key = 'pegawai'
		left join $wpdb->usermeta meta2
		on meta2.user_id = users.ID and meta2.meta_key = '_hp'
		left join $wpdb->usermeta meta3
		on meta3.user_id = users.ID and meta3.meta_key = 'niy'
		where meta.meta_value is not null
		order by users.display_name ASC
		
	";

	$x = new Excel($wpdb->get_results($q));
	$x->setCols(array(
		'NIY'=>'NIY',
		'display_name'=>'Nama Lengkap',
		'hp'=>'hp',
		'user_email'=> 'Email'
	));
	$x->setFilename('Daftar Pegawai STIBA Makassar');
	$x->display();
}
function ar_to_xls($ar){
	include get_template_directory().'/classes/PHPExcel.php';
	
	/** PHPExcel_Writer_Excel2007 */
	include get_template_directory().'/classes/PHPExcel/Writer/Excel2007.php';

	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	
	// Set properties
	$objPHPExcel->getProperties()->setCreator("Maarten Balliauw");
	$objPHPExcel->getProperties()->setLastModifiedBy("Maarten Balliauw");
	$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
	$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
	$objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");

	// determine table header from first row
	$c = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P');
	$c = range('A', 'Z');
	
	foreach($c as $columnID) {
		$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
			->setAutoSize(true);
	}
	
	$col = array(
		'NIY'=>'NIY',
		'display_name'=>'Nama Lengkap',
		'hp'=>'hp',
		'user_email'=> 'Email'
	);
	
	
	
	// Add some data
	$objPHPExcel->setActiveSheetIndex(0);
	
	// header
	$x = 0;
	foreach($col as $v){
		$objPHPExcel->getActiveSheet()->SetCellValue($c[$x].'1', $v); // A1, B1, etc
		$x++;
	}
	
	// content
	foreach($ar as $i => $d){
		$j = $i +2; // A2, B2, etc
		$x = 0;
		foreach($col as $k => $v){
			$objPHPExcel->getActiveSheet()->SetCellValue($c[$x].$j, $d->$k);
			$x++;
		}
	}
	
	$objPHPExcel->getActiveSheet()->setTitle('Simple');

	
}

