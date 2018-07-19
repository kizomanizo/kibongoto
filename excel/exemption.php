<?php

//////////////////// PAYER  ////////////////

$row = 1;
//$excel = $objPHPExcel->createSheet(7);
if ($origin == 'web') {
    $excel = $objPHPExcel->createSheet(7);
    $excel = $objPHPExcel->setActiveSheetIndex(7);
} else {
    $excel = $objPHPExcel->setActiveSheetIndex(0);
}
$sheet = $objPHPExcel->getActiveSheet();


$excel->setCellValue('A' . $row, 'Exemption ID');
$excel->setCellValue('B' . $row, 'Exemption Name');
$row++;

$str = "
SELECT id, name FROM `care_tz_company` WHERE care_tz_company.id IN(14,16,86,17,82,15,3)
";

$data = $db->query($str);

while ($rows = $data->fetch_assoc()) {
    $id = $rows['id'];
    $cat = 8;
    if ($id == 16)
        $cat = 2;
    if ($id == 14 || $id == 86 || $id == 82)
        $cat = 3;
    if ($id == 17)
        $cat = 6;

    $excel->setCellValue('A' . $row, $rows['id']);
    $excel->setCellValue('B' . $row, $rows['name']);
    $excel->setCellValue('C' . $row, $cat);
    $row++;
}

//Worksheet properties

$sheet->setTitle('EXEMPTION');
$sheet->getStyle('A1:C' . $row)->getFont()->setName('Calibri');
$sheet->getStyle('A1:C' . $row)->applyFromArray(
        array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '909090')
                )
            )
        )
);

$sheet->getColumnDimension('A')->setWidth(30);
$sheet->getColumnDimension('B')->setWidth(40);
$sheet->getColumnDimension('C')->setWidth(10);

if ($origin == 'auto') {
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save($save_path . $hos_name . ' EXEMPTION - ' . $date_to . '.xlsx');
}
?>