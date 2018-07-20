<?php
// Get the message type case from the URL
$case = htmlspecialchars($_GET["case"]);
$case = strtolower($case);

// Define the root folder of the app
$root = 'C:/xampp/htdocs/care2xdata/';
// $root = '/var/www/html/care2xdata';
$root2 = '';
require_once($root . 'config.php');
require_once($root2 . 'Classes/PHPExcel.php');
ini_set('default_timezone', 'Africa/Dar_es_Salaam');
PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
$row = 1;

// Pull files from last week in case they were skipped
$date_from = date('Y-m-d H:i:s', strtotime("2 wednesdays ago"));;
$date_to = date('Y-m-d H:i:s', strtotime("1 tuesday ago  23:59:59"));;

// Pull files for this week Wednesday to this Tuesday
// $date_from = date('Y-m-d H:i:s', strtotime("last wednesday"));;
// $date_to = date('Y-m-d H:i:s', strtotime("last tuesday 23:59:59"));;


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
$objPHPExcel->getProperties()
    ->setCreator("KIDH")
    ->setLastModifiedBy("KIDH")
    ->setTitle("KIDH - Reports")
    ->setSubject("KIDH - Reports")
    ->setDescription("KIDH - Reports")
    ->setKeywords("KIDH - Reports")
    ->setCategory("KIDH - Reports");

// Check the script origin (auto vs manual)
$origin='auto';

// Add use-case data
include ($case.'.php');

exit;