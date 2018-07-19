<?php

//////////////////// PAYER  ////////////////

$row = 1;
//$excel = $objPHPExcel->createSheet(6); 
if ($origin == 'web') {
    $excel = $objPHPExcel->createSheet(6);
    $excel = $objPHPExcel->setActiveSheetIndex(6);
} else {
    $excel = $objPHPExcel->setActiveSheetIndex(0);
}
$sheet = $objPHPExcel->getActiveSheet();


$excel->setCellValue('A' . $row, 'Payer ID');
$excel->setCellValue('B' . $row, 'Payer Name');
$row++;

$str = "
SELECT id, name FROM `care_tz_company` WHERE id IN(0,14,16,85,83,25,84,86,12,31,17,15,82,3)
";

$data = $db->query($str);

while ($rows = $data->fetch_assoc()) {
    $excel->setCellValue('A' . $row, $rows['id']);
    $excel->setCellValue('B' . $row, $rows['name']);
    $row++;
}

//Worksheet properties

$sheet->setTitle('PAYER');
$sheet->getStyle('A1:B' . $row)->getFont()->setName('Calibri');
$sheet->getStyle('A1:B' . $row)->applyFromArray(
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

if ($origin == 'auto') {
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save($save_path . $hos_name . ' PAYER - ' . $date_to . '.xlsx');
}
?>