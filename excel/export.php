<?php

//ini_set('display_errors', 1);
//error_reporting(E_ALL);


$root = '../';
//$save_path = '/home/exportdata/';
$root2 = '';
require_once($root . 'config.php');
require_once($root2 . 'Classes/PHPExcel.php');

ini_set('default_timezone', 'Africa/Dar_es_Salaam');

PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
//error_reporting(E_ERROR | E_PARSE | E_NOTICE);
//$month = date('m');
//if (isset($_REQUEST['month']))
//    $month = $_REQUEST['month'];
//$year = date('Y');
//if (isset($_REQUEST['year']))
//    $year = $_REQUEST['year'];
//
//$date = $year . '-' . $month;

$row = 1;

if (isset($_REQUEST['date_from'])) {
    $date_from = $_REQUEST['date_from'];
}

if (isset($_REQUEST['date_to'])) {
    $date_to = $_REQUEST['date_to'];
}

//echo $date_from;

function nextColumn(&$column) {
    $left = substr($column, 0, 1);
    $right = $left;
    if (strlen($column) > 1)
        $right = substr($column, 1, 1);
    if ($right == 'Z') {
        if (strlen($column) > 1) {
            $left++;
            $right = 'A';
        } else {
            $left = 'A';
            $right = 'A';
        }
        $column = $left . $right;
    } else {
        $right++;
        if (strlen($column) > 1) {
            $column = $left . $right;
        } else {
            $column = $right;
        }
    }
}

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("KIDH")
        ->setLastModifiedBy("KIDH")
        ->setTitle("KIDH - Reports")
        ->setSubject("KIDH - Reports")
        ->setDescription("KIDH - Reports")
        ->setKeywords("KIDH - Reports")
        ->setCategory("KIDH - Reports");

$origin='web';

// Add some data

include('svcrec.php');

include('ddc.php');

include('bedocc.php');

include('rev.php');

include('depts.php');

include('wards.php');

include('payer.php');

include('exemption.php');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
// header('Content-Disposition: attachment;filename="' . $hos_name . ' REPORT - ' . $date_to . '.xlsx"');
header('Content-Disposition: attachment;filename="'.'REPORT_' .date('YmdHis').'.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

exit;
