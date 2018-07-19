<?php

//////////////////// BEDOCC  ////////////////

$row = 1;
//$excel = $objPHPExcel->createSheet(2);
if ($origin == 'web') {
    $excel = $objPHPExcel->createSheet(2);
    $excel = $objPHPExcel->setActiveSheetIndex(2);
} else {
    $excel = $objPHPExcel->setActiveSheetIndex(0);
}
$sheet = $objPHPExcel->getActiveSheet();
$current_date = date('Y-m-d');


$excel->setCellValue('A' . $row, 'Message Type');
$excel->setCellValue('B' . $row, 'Org Name');
$excel->setCellValue('C' . $row, 'Local Org ID');
$excel->setCellValue('D' . $row, 'Pat ID');
$excel->setCellValue('E' . $row, 'Admission Date');
$excel->setCellValue('F' . $row, 'Discharge Date');
$excel->setCellValue('G' . $row, 'Ward ID');
$excel->setCellValue('H' . $row, 'Ward Name');
$row++;

$str = "
        SELECT DISTINCT care_encounter.pid, encounter_nr, encounter_date, discharge_date, current_ward_nr, ward_id, care_ward.description 
        FROM care_encounter 
        INNER JOIN care_ward ON current_ward_nr = care_ward.nr 
        WHERE 
            (`encounter_date` >= '$date_from' AND `encounter_date` <= '$date_to') 
        OR 
            (`discharge_date` >= '$date_from' AND `discharge_date` <= '$date_to') 
        ORDER BY encounter_nr ASC
    ";

$data = $db->query($str);

while ($rows = $data->fetch_assoc()) {
    $excel->setCellValue('A' . $row, 'BEDOCC');
    $excel->setCellValue('B' . $row, $hos_name);
    $excel->setCellValue('C' . $row, $hos_number);
    $excel->setCellValue('D' . $row, $rows['pid']);
    $excel->setCellValue('E' . $row, $rows['encounter_date']);
    $excel->setCellValue('F' . $row, $rows['discharge_date']);
    $excel->setCellValue('G' . $row, $rows['ward_id']);
    $excel->setCellValue('H' . $row, $rows['description']);
    $row++;
}

//Worksheet properties

$sheet->setTitle('UC1C - BEDOCC');
$sheet->getStyle('A1:H' . $row)->getFont()->setName('Calibri');
$sheet->getStyle('A1:H' . $row)->applyFromArray(
        array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '909090')
                )
            )
        )
);

$sheet->getColumnDimension('A')->setWidth(10);
$sheet->getColumnDimension('B')->setWidth(10);
$sheet->getColumnDimension('C')->setWidth(10);
$sheet->getColumnDimension('D')->setWidth(10);
$sheet->getColumnDimension('E')->setWidth(20);
$sheet->getColumnDimension('F')->setWidth(20);
$sheet->getColumnDimension('G')->setWidth(10);
$sheet->getColumnDimension('H')->setWidth(30);

if ($origin == 'auto') {
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save($save_path.'BEDOCC_'.date('YmdHis').'.xlsx');
}
?>