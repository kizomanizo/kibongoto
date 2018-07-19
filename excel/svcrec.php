<?php

////////// SVCREC  ////////////////

$excel = $objPHPExcel->setActiveSheetIndex(0);
$sheet = $objPHPExcel->getActiveSheet();

$excel->setCellValue('A' . $row, 'Message Type');
$excel->setCellValue('B' . $row, 'Org Name');
$excel->setCellValue('C' . $row, 'Local Org ID');
$excel->setCellValue('D' . $row, 'Dept ID');
$excel->setCellValue('E' . $row, 'Dept Name');
$excel->setCellValue('F' . $row, 'Pat ID');
$excel->setCellValue('G' . $row, 'Gender');
$excel->setCellValue('H' . $row, 'DOB');
$excel->setCellValue('I' . $row, 'Med SVC Code');
$excel->setCellValue('J' . $row, 'ICD10 Code');
$excel->setCellValue('K' . $row, 'Service Date');
$row++;

$str = "
SELECT  
DISTINCT
care_encounter.current_dept_nr,
care_tz_billing_archive.nr,
sex,
date_birth,
encounter_date,
name_formal,
care_encounter.pid,
ICD_10_code,
care_tz_billing_archive_elem.description,
care_tz_drugsandservices.item_number,
encounter_date

FROM care_tz_billing_archive

INNER JOIN
care_tz_billing_archive_elem
ON care_tz_billing_archive.nr = care_tz_billing_archive_elem.nr

INNER JOIN 
care_encounter
ON care_encounter.encounter_nr = care_tz_billing_archive.encounter_nr 

INNER JOIN 
care_person
ON care_person.pid = care_encounter.pid

INNER JOIN
care_department
ON care_department.nr = care_encounter.current_dept_nr

INNER JOIN
care_tz_drugsandservices
ON care_tz_billing_archive_elem.item_number = care_tz_drugsandservices.item_id

LEFT JOIN care_tz_diagnosis
ON care_encounter.encounter_nr = care_tz_diagnosis.encounter_nr

WHERE (`encounter_date` >= '$date_from' AND `encounter_date` <= '$date_to')
AND care_encounter.current_dept_nr > 0
";

$data = $db->query($str);

while ($rows = $data->fetch_assoc()) {
    $sex = 'Female';
    if ($rows['sex'] == 'm')
        $sex = 'Male';
    $excel->setCellValue('A' . $row, 'SVCREC');
    $excel->setCellValue('B' . $row, $hos_name);
    $excel->setCellValue('C' . $row, $hos_number);
    $excel->setCellValue('D' . $row, $rows['current_dept_nr']);
    $excel->setCellValue('E' . $row, $rows['name_formal']);
    $excel->setCellValue('F' . $row, $rows['pid']);
    $excel->setCellValue('G' . $row, $sex);
    $excel->setCellValue('H' . $row, $rows['date_birth']);
    $excel->setCellValue('I' . $row, $rows['item_number']);
    $excel->setCellValue('J' . $row, $rows['ICD_10_code']);
    $excel->setCellValue('K' . $row, $rows['encounter_date']);
    $row++;
}

//Worksheet properties

$sheet->setTitle('UC1A - SVCREC');
$sheet->getStyle('A1:K' . $row)->getFont()->setName('Calibri');
$sheet->getStyle('A1:K' . $row)->applyFromArray(
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
$sheet->getColumnDimension('B')->setWidth(10);
$sheet->getColumnDimension('C')->setWidth(10);
$sheet->getColumnDimension('D')->setWidth(10);
$sheet->getColumnDimension('E')->setWidth(40);
$sheet->getColumnDimension('F')->setWidth(20);
$sheet->getColumnDimension('G')->setWidth(10);
$sheet->getColumnDimension('H')->setWidth(20);
$sheet->getColumnDimension('I')->setWidth(20);
$sheet->getColumnDimension('J')->setWidth(20);
$sheet->getColumnDimension('K')->setWidth(20);

if ($origin == 'auto') {
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);    
    
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save($save_path.'SVCREC_'.date('YmdHis') . '.xlsx');
}
?>