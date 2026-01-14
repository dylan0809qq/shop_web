<?php
// 建立與資料庫的連接
$host = "localhost";
$username = "root";
$password = "";
$database = "shop web"; // 資料庫名稱
$db_link = mysqli_connect($host, $username, $password, $database);

if (!$db_link) {
    die("資料庫連接失敗: " . mysqli_connect_error());
}
if (!isset($_SESSION['name'])) {
    echo('<script>alert("請先登入帳號");</script>');
    header("Location: index.php");
    exit();
}

if (isset($_GET['account'])) {
    $account = $_GET['account'];

    // 根據帳號查詢相應的用戶記錄
    $query = "SELECT * FROM vip WHERE account = '$account'";
    $result = mysqli_query($db_link, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        if ($row['status'] == 'off') {
            echo '<script>alert("您的帳號已被禁用，無法升級。");</script>';
            echo('<script>window.location.href = "vip_management.php";</script>');
            exit; // 這裡新增了一行，確保在禁用狀態下不會繼續執行後面的程式碼
        } else {
            // 更改狀態
            $newStatus = ($row['status'] == 'on') ? 'off' : 'on';

            // 更新狀態
            $updateQuery = "UPDATE vip SET status = '$newStatus' WHERE account = '$account'";
            $updateResult = mysqli_query($db_link, $updateQuery);

            if ($updateResult) {
                echo '<script>alert("操作成功");</script>';
            } else {
                echo '<script>alert("操作失敗");</script>';
            }
        }
    }
}
// 重新導向回會員管理頁面
header("Location: vip_management.php");
exit;
?>