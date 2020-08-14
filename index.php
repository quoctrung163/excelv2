<?php
if (isset($_GET["txtHoTen"]) || isset($_GET["txtNgaySinh"]) || isset($_GET["txtSoHieuVanBang"]) || isset($_GET["txtMaSoSinhVien"]) || isset($_GET["txtNamTotNghiep"])) {

    require_once('vendor/autoload.php');

    $inputFileName = __DIR__ . '\data.xlsx';
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
    $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

    // Xoá dòng tiêu đề
    unset($data[1]);

    /**
     * Đổi chuỗi có dấu thành không dấu
     */
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

    /**
     * So sánh chuỗi ban đầu và chuỗi con
     * - So sánh có/không dấu
     * - So sánh không phân biệt hoa/thường
     * - Kiểm tra chuỗi con
     */
    function compare2Str($strOriginal, $strChild)
    {
        // xoa dau va doi thanh chu thuong
        $handlestrOriginal = vn_to_str(strtolower($strOriginal));
        $handlestrChild = vn_to_str(strtolower($strChild));
        // so sanh khong phan biet chu hoa thuong
        $result = strpos($handlestrOriginal, $handlestrChild);

        return (gettype($result) === 'integer' && $result >= 0);
    }

    /**
     * Tìm kiếm trong array với nhiều tiêu chí khác nhau
     */
    function searchByParams($name, $birthday, $no, $id, $year, $array)
    {
        if ($name == null && $birthday == null && $no == null && $id == null && $year == null) {
            echo "<script type='text/javascript'>alert('Không được để trống');</script>";
            return null;
        }
        $result = $array;
        if ($id != null) {
            $result = array_filter($result, function ($var) use ($id) {
                return compare2Str($var['A'], $id);
            });
        }
        if ($name != null) {
            $result = array_filter($result, function ($var) use ($name) {
                return compare2Str($var['B'], $name);
            });
        }
        if ($no != null) {
            $result = array_filter($result, function ($var) use ($no) {
                return compare2Str($var['D'], $no);
            });
        }
        if ($birthday != null) {
            $result = array_filter($result, function ($var) use ($birthday) {
                if (gettype($var['E']) == 'double') {
                    $strChildConvertDateTime = date("d/m/Y", strtotime($var['E']));
                    return compare2Str($strChildConvertDateTime, $birthday);
                }
                return compare2Str($var['E'], $birthday);
            });
        }
        if ($year != null) {
            $result = array_filter($result, function ($var) use ($year) {
                return compare2Str($var['G'], $year);
            });
        }
        return $result;
    }

    /**
     * In array thành bảng trong HTML
     */
    function printTable($array)
    {
        getHeader();
        echo '<div style="margin-top: 70px;">';
        echo '<table class="table">';
        echo "<tr>";
        echo "<th>Mã sv</th><th>Tên sinh viên</th><th>Số vào sổ</th><th>Số hiệu bằng</th><th>Ngày sinh</th><th>Xếp loại</th><th>Năm TN</th>";
        echo "</tr>";
        if ($array != null)
            foreach ($array as $item) {
                echo '<tr>';
                echo '<td>' . $item['A'] . '</td>';
                echo '<td>' . $item['B'] . '</td>';
                echo '<td>' . $item['C'] . '</td>';
                echo '<td>' . $item['D'] . '</td>';
                echo '<td>' . $item['E'] . '</td>';
                echo '<td>' . $item['F'] . '</td>';
                echo '<td>' . $item['G'] . '</td>';
                echo '</tr>';
            }
        echo '</table>';
        echo '</div>';
        getFooter();
    }

    $result = searchByParams($_GET["txtHoTen"], $_GET["txtNgaySinh"], $_GET["txtSoHieuVanBang"], $_GET["txtMaSoSinhVien"], $_GET["txtNamTotNghiep"], $data);
    echo printTable($result);
    exit();
}

/**
 * Xuất phần header của HTML
 */
function getHeader()
{
    echo '<!DOCTYPE html>';
    echo '<html>';
    echo '<head>';
    echo '<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css">';
    echo '<link rel="stylesheet" href="./style.css">';
    echo '</head>';
    echo '<body>';
    echo '<div class="tracuuvanbang">';
    echo '<div class="container-fluid">
          <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top border-bottom shadow-sm font-header" style="background-color: white !important;">
            <div class="container">
              <a class="navbar-brand" 
                href="http://localhost/webttcntt">
                <img src="http://localhost/webttcntt/wp-content/uploads/2020/07/bannerPage.png" alt="bannerpage" width="70%" height="65%"/>
              </a>
            </div>
          </nav>';
}

/**
 * Xuất phần Footer của HTML
 */
function getFooter()
{
    echo '</div>';
    echo '</div>';
    echo '<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>';
    echo '<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js"></script>';
    echo '</body>';
    echo '</html>';
}
?>

<!--HTMl FORM-->
<?php getHeader(); ?>
<div class="row justify-content-center align-item-center" style="margin-top: 100px;">
    <form style="display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;" action="<?php $_PHP_SELF ?>" method="get">
        <h1 style="text-align: center">Tra cứu văn bằng</h1>
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
        <div class="col-md-4">
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