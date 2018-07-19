<?php

//////////////////// DDC  ////////////////

$row = 1;
if ($origin == 'web') {
    $excel = $objPHPExcel->createSheet(4);
    $excel = $objPHPExcel->setActiveSheetIndex(4);
} else {
    $excel = $objPHPExcel->setActiveSheetIndex(0);
}
$sheet = $objPHPExcel->getActiveSheet();


$excel->setCellValue('A' . $row, 'Department ID');
$excel->setCellValue('B' . $row, 'Department Name');
$row++;

$str = "
SELECT nr, name_formal FROM `care_department` WHERE nr IN(55,40,81,7,69,68,62)
";

$data = $db->query($str);

while ($rows = $data->fetch_assoc()) {
    $excel->setCellValue('A' . $row, $rows['nr']);
    $excel->setCellValue('B' . $row, $rows['name_formal']);
    $row++;
}

//Worksheet properties

$sheet->setTitle('DEPARTMENTS');
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
    $objWriter->save($save_path . $hos_name . ' DEPARTMENTS - ' . $date_to . '.xlsx');
}
?>