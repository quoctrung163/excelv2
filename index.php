<?php
if ($_GET["txtHoTen"] || $_GET["txtNgaySinh"] || $_GET["txtSoHieuVanBang"] || $_GET["txtMaSoSinhVien"] || $_GET["txtNamTotNghiep"]) {


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

  function vn_to_str($str)
  {
    $unicode = array(
      'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
      'd' => 'đ',
      'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
      'i' => 'í|ì|ỉ|ĩ|ị',
      'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
      'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
      'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
      'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
      'D' => 'Đ',
      'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
      'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
      'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
      'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
      'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
    );

    foreach ($unicode as $nonUnicode => $uni) {
      $str = preg_replace("/($uni)/i", $nonUnicode, $str);
    }
    $str = str_replace(' ', '_', $str);
    return $str;
  }

  function compare2Str($strOriginal, $strChild)
  {

    // xoa dau va doi thanh chu thuong
    $handlestrOriginal = vn_to_str(strtolower($strOriginal));
    $handlestrChild = vn_to_str(strtolower($strChild));
    // so sanh khong phan biet chu hoa thuong
    $result = strpos($handlestrOriginal, $handlestrChild);

    if (gettype($result) === 'integer' && $result >= 0) {
      return true;
    }
    // return strpos($handlestrOriginal, $handlestrChild) !== '';
  }

  function searchByParams($name, $birthday, $no, $id, $year, $array)
  {
    $result = $array;
    if ($id != null) {
      $result = array_filter($result, function ($var) use ($id) {
        return compare2Str($var[0], $id);
      });
    }
    if ($name != null) {
      $result = array_filter($result, function ($var) use ($name) {
        return compare2Str($var[1], $name);
      });
    }
    if ($no != null) {
      $result = array_filter($result, function ($var) use ($no) {
        return compare2Str($var[3], $no);
      });
    }
    if ($birthday != null) {
      $result = array_filter($result, function ($var) use ($birthday) {
        if (gettype($var[4]) == 'integer') {
          // $strChildConvertDateTime = PHPExcel_Style_NumberFormat::toFormattedString($var[4], PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);
          return compare2Str($var[4], $birthday);
        }
        // return compare2Str($var[4], $birthday);
      });
    }
    if ($year != null) {
      $result = array_filter($result, function ($var) use ($year) {
        return compare2Str($var[6], $year);
      });
    }
    return $result;
  }

  // $result = searchForId('Ka És', $data);
  // echo strpos('phan quoc trung', 'thang');
  //var_dump($result);

  function getHeader()
  {
    echo '<!DOCTYPE html>';
    echo '<html>';
    echo '<head>';
    echo '<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css">';
    echo '</head>';
    echo '<body>';
  }

  function getFooter()
  {
    echo '<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>';
    echo '<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js"></script>';
    echo '</body>';
    echo '</html>';
  }

  function printTable($array)
  {
    getHeader();
    echo '<table class="table">';
    echo "<tr>";
    echo "<th>Mã sv</th><th>Tên sinh viên</th><th>Số vào sổ</th><th>Số hiệu bằng</th><th>Ngày sinh</th><th>Xếp loại</th><th>Năm TN</th>";
    echo "</tr>";
    foreach ($array as $item) {
      echo '<tr>';
      echo '<td>' . $item[0] . '</td>';
      echo '<td>' . $item[1] . '</td>';
      echo '<td>' . $item[2] . '</td>';
      echo '<td>' . $item[3] . '</td>';
      echo '<td>' . PHPExcel_Style_NumberFormat::toFormattedString($item[4], PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY) . '</td>';
      echo '<td>' . $item[5] . '</td>';
      echo '<td>' . $item[6] . '</td>';
      echo '</tr>';
    }
    echo '</table>';
    getFooter();
  }

  $result = searchByParams($_GET["txtHoTen"], $_GET["txtNgaySinh"], $_GET["txtSoHieuVanBang"], $_GET["txtMaSoSinhVien"], $_GET["txtNamTotNghiep"], $data);
  echo printTable($result);
  exit();
}
?>


<?php getHeader(); ?>
<div class="row">
  <form action="<?php $_PHP_SELF ?>" method="get">
    <div class="col-md-4">
      <div style="margin-bottom: 7px">
        Họ tên:
        <input type="text" class="form-control" id="txtHoTen" name="txtHoTen" placeholder="Nhập họ tên để tìm .....">
      </div>
      <div style="margin-bottom: 7px">
        Ngày sinh:
        <input type="text" class="form-control" name="txtNgaySinh" placeholder="Nhập ngày sinh để tìm .....">
      </div>
      <div style="margin-bottom: 7px">
        Số hiệu bằng:
        <input type="text" class="form-control" name="txtSoHieuVanBang" placeholder="Nhập số hiệu văn bằng để tìm .....">
      </div>
    </div>
    <div class="col-md-5">
      <div style="margin-bottom: 7px">
        Mã số sinh viên:
        <input type="text" class="form-control" name="txtMaSoSinhVien" placeholder="Nhập mã sinh viên để tìm .....">
      </div>
      <div style="margin-bottom: 7px">
        Năm tốt nghiệp:
        <input type="text" class="form-control" name="txtNamTotNghiep" placeholder="Nhập năm tốt nghiệp để tìm .....">
      </div>
      <br>
      <input type="submit" class="btn btn-success" value="Tra cứu" name="btnTraCuu">

    </div>
  </form>
</div>
<?php getFooter(); ?>