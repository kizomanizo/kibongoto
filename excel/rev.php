<?php

//////////////////// REV  ////////////////

$row = 1;
//$excel = $objPHPExcel->createSheet(3);
if ($origin == 'web') {
    $excel = $objPHPExcel->createSheet(3);
    $excel = $objPHPExcel->setActiveSheetIndex(3);
} else {
    $excel = $objPHPExcel->setActiveSheetIndex(0);
}
$sheet = $objPHPExcel->getActiveSheet();


$excel->setCellValue('A' . $row, 'Message Type');
$excel->setCellValue('B' . $row, 'System Trans ID');
$excel->setCellValue('C' . $row, 'Org Name');
$excel->setCellValue('D' . $row, 'Local Org ID');
$excel->setCellValue('E' . $row, 'Transaction Date');
$excel->setCellValue('F' . $row, 'Pat ID');
$excel->setCellValue('G' . $row, 'Gender');
$excel->setCellValue('H' . $row, 'DOB');
$excel->setCellValue('I' . $row, 'Med Svc Code');
$excel->setCellValue('J' . $row, 'Payer ID');
$excel->setCellValue('K' . $row, 'Exemption ID');
$excel->setCellValue('L' . $row, 'Billed Amount');
$excel->setCellValue('M' . $row, 'Waived Amount');
$row++;

$str = "
    SELECT DISTINCT care_encounter.pid, care_encounter.current_dept_nr, care_tz_billing_archive.nr, care_tz_company.id, care_tz_billing_archive_elem.insurance_id, sex, date_birth, encounter_date, price, description, care_tz_drugsandservices.item_number, encounter_date
        FROM care_tz_billing_archive

        INNER JOIN care_tz_billing_archive_elem ON care_tz_billing_archive.nr = care_tz_billing_archive_elem.nr
        INNER JOIN care_encounter ON care_encounter.encounter_nr = care_tz_billing_archive.encounter_nr 
        INNER JOIN care_person ON care_person.pid = care_encounter.pid
        INNER JOIN care_tz_drugsandservices ON care_tz_drugsandservices.item_id = care_tz_billing_archive_elem.item_number
        INNER JOIN care_tz_company ON care_tz_billing_archive_elem.insurance_id = care_tz_company.id
    WHERE `encounter_date` >= '$date_from' AND `encounter_date` <= '$date_to'
    ORDER BY `encounter_date` DESC
";

$data = $db->query($str);
$sex = '';
$ins = '';
$bill = '';
$waived = 0;
$exemption = '';
while ($rows = $data->fetch_assoc()) {

    $sex = 'Female';
    if ($rows['sex'] == 'm')
        $sex = 'Male';
    $price = $rows['price'];
    $bill = $price;
    $waived = 0;
    $exemption = '';
    $ins = $rows['insurance_id'];

    if ($ins == '14' || $ins == '86' || $ins == '82') {
        $exemption = '3';
        $waived = $bill;
    }

    if ($ins == '16') {
        $exemption = '2';
        $waived = $bill;
    }

    if ($ins == '17') {
        $exemption = '6';
        $waived = $bill;
    }

    $excel->setCellValue('A' . $row, 'REV');
    $excel->setCellValue('B' . $row, $rows['nr']);
    $excel->setCellValue('C' . $row, $hos_name);
    $excel->setCellValue('D' . $row, $hos_number);
    $excel->setCellValue('E' . $row, $rows['encounter_date']);
    $excel->setCellValue('F' . $row, $rows['pid']);
    $excel->setCellValue('G' . $row, $sex);
    $excel->setCellValue('H' . $row, $rows['date_birth']);
    $excel->setCellValue('I' . $row, $rows['item_number']);
    $excel->setCellValue('J' . $row, $ins);
    $excel->setCellValue('K' . $row, $exemption);
    $excel->setCellValue('L' . $row, $bill);
    $excel->setCellValue('M' . $row, $waived);
    $row++;
}

//Worksheet properties

$sheet->setTitle('UC1D - REV');
$sheet->getStyle('A1:R' . $row)->getFont()->setName('Calibri');
$sheet->getStyle('A1:R' . $row)->applyFromArray(
        array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '909090')
                )
            )
        )
);

$excel->setCellValue('A' . $row, 'REV');
$excel->setCellValue('B' . $row, $rows['nr']);
$excel->setCellValue('C' . $row, $hos_name);
$excel->setCellValue('D' . $row, $hos_number);
$excel->setCellValue('E' . $row, $rows['encounter_date']);
$excel->setCellValue('F' . $row, $rows['pid']);
$excel->setCellValue('G' . $row, $sex);
$excel->setCellValue('H' . $row, $rows['date_birth']);
$excel->setCellValue('I' . $row, $rows['item_number']);
$excel->setCellValue('J' . $row, $ins);
$excel->setCellValue('K' . $row, $exemption);
$excel->setCellValue('L' . $row, $bill);
$excel->setCellValue('M' . $row, $waived);

$sheet->getColumnDimension('A')->setWidth(10);
$sheet->getColumnDimension('B')->setWidth(20);
$sheet->getColumnDimension('C')->setWidth(20);
$sheet->getColumnDimension('D')->setWidth(20);
$sheet->getColumnDimension('E')->setWidth(20);
$sheet->getColumnDimension('F')->setWidth(20);
$sheet->getColumnDimension('G')->setWidth(10);
$sheet->getColumnDimension('H')->setWidth(20);
$sheet->getColumnDimension('I')->setWidth(20);
$sheet->getColumnDimension('J')->setWidth(10);
$sheet->getColumnDimension('K')->setWidth(10);
$sheet->getColumnDimension('L')->setWidth(20);
$sheet->getColumnDimension('M')->setWidth(20);

if ($origin == 'auto') {
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save($save_path.'REV_'.date('YmdHis').'.xlsx');
}
?>