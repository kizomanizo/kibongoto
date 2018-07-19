<?php

//////////////////// DDC  ////////////////

$row = 1;
if ($origin == 'web') {
    $excel = $objPHPExcel->createSheet(1);
    $excel = $objPHPExcel->setActiveSheetIndex(1);
} else {
    $excel = $objPHPExcel->setActiveSheetIndex(0);
}
$sheet = $objPHPExcel->getActiveSheet();

$excel->setCellValue('A' . $row, 'Message Type');
$excel->setCellValue('B' . $row, 'Org Name');
$excel->setCellValue('C' . $row, 'Local Org ID');
$excel->setCellValue('D' . $row, 'Ward ID');
$excel->setCellValue('E' . $row, 'Ward Name');
$excel->setCellValue('F' . $row, 'Pat ID');
$excel->setCellValue('G' . $row, 'Gender');
$excel->setCellValue('H' . $row, 'Disease Code');
$excel->setCellValue('I' . $row, 'DOB');
$excel->setCellValue('J' . $row, 'Date Death Occured');
$row++;

$str = "
SELECT DISTINCT care_encounter.pid, care_encounter.encounter_nr, sex, ICD_10_code, ICD_10_description, date_birth, death_date
FROM care_encounter
    INNER JOIN 
        care_person ON care_person.pid = care_encounter.pid
    LEFT JOIN care_tz_diagnosis ON care_tz_diagnosis.encounter_nr = care_encounter.encounter_nr
WHERE death_date >= '$date_from' AND `death_date` <= '$date_to'
";

$data = $db->query($str);

while ($rows = $data->fetch_assoc()) {
    $sex = 'Female';
    if ($rows['sex'] == 'm')
        $sex = 'Male';
    $excel->setCellValue('A' . $row, 'DDC');
    $excel->setCellValue('B' . $row, $hos_name);
    $excel->setCellValue('C' . $row, $hos_number);
    $excel->setCellValue('D' . $row, '');
    $excel->setCellValue('E' . $row, '');
    $excel->setCellValue('F' . $row, $rows['pid']);
    $excel->setCellValue('G' . $row, $sex);
    $excel->setCellValue('H' . $row, $rows['ICD_10_code']);
    $excel->setCellValue('I' . $row, $rows['date_birth']);
    $excel->setCellValue('J' . $row, $rows['death_date']);
    $row++;
}

// Worksheet properties
$sheet->setTitle('UC1B - DDC');
$sheet->getStyle('A1:L' . $row)->getFont()->setName('Calibri');
$sheet->getStyle('A1:L' . $row)->applyFromArray(
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
$sheet->getColumnDimension('E')->setWidth(30);
$sheet->getColumnDimension('F')->setWidth(20);
$sheet->getColumnDimension('G')->setWidth(10);
$sheet->getColumnDimension('H')->setWidth(10);
$sheet->getColumnDimension('I')->setWidth(20);
$sheet->getColumnDimension('J')->setWidth(20);

if ($origin == 'auto') {
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save($save_path.'DDC_' .date('YmdHis').'.xlsx');
}
?>