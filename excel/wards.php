<?php

//////////////////// DDC  ////////////////

$row = 1;
//$excel = $objPHPExcel->createSheet(5);
if ($origin == 'web') {
    $excel = $objPHPExcel->createSheet(5);
    $excel = $objPHPExcel->setActiveSheetIndex(5);
} else {
    $excel = $objPHPExcel->setActiveSheetIndex(0);
}
$sheet = $objPHPExcel->getActiveSheet();


$excel->setCellValue('A' . $row, 'Ward ID');
$excel->setCellValue('B' . $row, 'Ward Name');
$excel->setCellValue('C' . $row, 'Department ID');
$excel->setCellValue('D' . $row, 'No of Beds');
$row++;

$str = "
SELECT nr, name, description, dept_nr FROM `care_ward` WHERE nr > 8 AND nr != 10
";

$data = $db->query($str);

while ($rows = $data->fetch_assoc()) {

    $ward_id = $rows['nr'];
    $bed = 0;
    $rm = "SELECT nr_of_beds FROM care_room WHERE ward_nr = '$ward_id' AND info = ''";
    $dt = $db->query($rm);
    while ($beds = $dt->fetch_assoc()) {
        $bed += $beds['nr_of_beds'];
    }

    $excel->setCellValue('A' . $row, $rows['nr']);
    $excel->setCellValue('B' . $row, $rows['description']);
    $excel->setCellValue('C' . $row, $rows['dept_nr']);
    $excel->setCellValue('D' . $row, $bed);
    $row++;
}

//Worksheet properties

$sheet->setTitle('WARDS');
$sheet->getStyle('A1:D' . $row)->getFont()->setName('Calibri');
$sheet->getStyle('A1:D' . $row)->applyFromArray(
        array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '909090')
                )
            )
        )
);

$sheet->getColumnDimension('A')->setWidth(20);
$sheet->getColumnDimension('B')->setWidth(40);
$sheet->getColumnDimension('C')->setWidth(20);
$sheet->getColumnDimension('D')->setWidth(20);

if ($origin == 'auto') {
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save($save_path . $hos_name . ' WARDS - ' . $date_to . '.xlsx');
}
?>