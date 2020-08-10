<?php

require_once 'Classes/PHPExcel.php';
$file = 'data.xlsx';

$objReader = PHPExcel_IOFactory::load($file);

//Lấy ra số trang sử dụng phương thức getSheetCount();
// Lấy Ra tên trang sử dụng getSheetNames();

//Chọn trang cần truy xuất
$sheet = $objReader->setActiveSheetIndex(0);

//Lấy ra số dòng cuối cùng
$Totalrow = $sheet->getHighestRow();
//Lấy ra tên cột cuối cùng
$LastColumn = $sheet->getHighestColumn();

//Chuyển đổi tên cột đó về vị trí thứ, VD: C là 3,D là 4
$TotalCol = PHPExcel_Cell::columnIndexFromString($LastColumn);

//Tạo mảng chứa dữ liệu
$data = [];

//Tiến hành lặp qua từng ô dữ liệu
//----Lặp dòng, Vì dòng đầu là tiêu đề cột nên chúng ta sẽ lặp giá trị từ dòng 2
for ($i = 2; $i <= $Totalrow; $i++) {
	//----Lặp cột
	for ($j = 0; $j < $TotalCol; $j++) {
		// Tiến hành lấy giá trị của từng ô đổ vào mảng
		$data[$i - 2][$j] = $sheet->getCellByColumnAndRow($j, $i)->getValue();;
	}
}
//Hiển thị mảng dữ liệu
// echo '<pre>';
// var_dump($data);

// tim khong phan biet chu hoa
// tim kiem chuoi con
// tim kiem khong dau
function compare2Str($str1, $str2)
{
	//...
	return true;
}

function searchByParames($name, $birthday, $no, $id, $year, $array)
{
	$result = $array;
	if ($id != null) {
		$result = array_filter($array, function ($var) use ($id) {
			return ($var[0] == $id);
		});
	}
	if ($name != null) {
		$result = array_filter($array, function ($var) use ($name) {
			return ($var[1] == $name);
		});
	}
	if ($no != null) {
		$result = array_filter($array, function ($var) use ($no) {
			return ($var[3] == $no);
		});
	}
	if ($birthday != null) {
		$result = array_filter($array, function ($var) use ($birthday) {
			return ($var[4] == $birthday);
		});
	}
	if ($year != null) {
		$result = array_filter($array, function ($var) use ($year) {
			return ($var[6] == $year);
		});
	}
	return $result;
}

// $result = searchForId('Ka És', $data);
echo '<pre>';
var_dump($result);
