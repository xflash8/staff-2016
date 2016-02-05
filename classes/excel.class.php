<?php
include get_template_directory().'/classes/PHPExcel.php';
include get_template_directory().'/classes/PHPExcel/Writer/Excel2007.php';

class Excel{
	var $filename = 'file.xlsx';
	var $data;
	function __construct(LIST_TABLE $table_obj){
		$this->table_obj = $table_obj;
		$this->data = $table_obj->query();
		$this->cols = $table_obj->get_columns_xl();
	}
	function setFilename($filename){
		$this->filename = $filename.'.XLSX';
	}
	
	function setCols($cols){
		$this->cols = $cols;
	}
	function cellItem($obj, $index, $value){
		
	}
	
	// Save Excel 2007 file
	function display(){		
		global $current_user;
		$c = range('A', 'Z');
		
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		
		// set column size to auto
		foreach($c as $columnID) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
				->setAutoSize(true);
		}
		
		
		// Set properties
		$objPHPExcel->getProperties()->setCreator($current_user->display_name);
		$objPHPExcel->getProperties()->setLastModifiedBy($current_user->display_name);
		$objPHPExcel->getProperties()->setTitle($this->title);
		$objPHPExcel->getProperties()->setSubject($this->subject);
		$objPHPExcel->getProperties()->setDescription($this->description);		
		// Add some data
		$objPHPExcel->setActiveSheetIndex(0);		
		
		// table header
		$x = 0;
		foreach($this->cols as $v){
			$objPHPExcel->getActiveSheet()->SetCellValue($c[$x].'1', $v)->getStyle($c[$x].'1')->getFont()->setBold(true);; // A1, B1, etc
			$x++;
		}
		
		// table row
		foreach($this->data as $i => $item){
			$j = $i +2; // A2, B2, etc
			$x = 0;
			$index = $i + 1;
			$item->index = 0;
			
			foreach($this->cols as $column_name => $v){
				if($column_name == 'index'){
					$item->index = 10;
				}
				
				#$val = $item->$column_name;
				
				$method = 'column_'.$column_name;
				if(method_exists($this->table_obj, $method)){
					$val = $this->table_obj->$method($item, $column_name);
				}else{
					$val = $this->table_obj->column_default($item, $column_name);
				}
				
				
				
				$objPHPExcel->getActiveSheet()->SetCellValue($c[$x].$j, $val, $format);
				
				$x++;
			}
		}
		
		$objPHPExcel->getActiveSheet()->setTitle('Sheet 1');
		
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="'.$this->filename.'"');
		$objWriter->save('php://output');
		exit;
	}
}