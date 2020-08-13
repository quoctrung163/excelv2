<?php

require_once('vendor/autoload.php');

$inputFileName = __DIR__ . '\data.xlsx';
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
$data = $spreadsheet->getActiveSheet()->toArray(null, false, false, false);


// Xoá dòng tiêu đề
unset($data[1]);
echo '<pre>';
var_dump($data);