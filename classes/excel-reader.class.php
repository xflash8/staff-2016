<?php

/**
 * Description of excel-reader
 *
 * @author Gapt3k
 */
class excel_reader {
  var $filename;
  function __construct($filename) {
    $this->filename = $filename;
      
    include_once get_template_directory().'/classes/PHPExcel.php';
    include_once get_template_directory().'/classes/excel/PHPExcel/IOFactory.php';
  }
  function read(){
    //  Read your Excel workbook
    try {
      $inputFileType = PHPExcel_IOFactory::identify($this->filename);
      $objReader = PHPExcel_IOFactory::createReader($this->filename);
      $objPHPExcel = $objReader->load($this->filename);
    } catch(Exception $e) {
      die('Error loading file "'.pathinfo($this->filename,PATHINFO_BASENAME).'": '.$e->getMessage());
    }    
    
    //  Get worksheet dimensions
    $sheet = $objPHPExcel->getSheet(0);
    $highestRow = $sheet->getHighestRow(); 
    $highestColumn = $sheet->getHighestColumn();
   
    
    //  Loop through each row of the worksheet in turn
    for ($row = 1; $row <= $highestRow; $row++){ 
      //  Read a row of data into an array
      $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                                      NULL,
                                      TRUE,
                                      FALSE);
      
      print_r($rowData);
    }
  }    
}
