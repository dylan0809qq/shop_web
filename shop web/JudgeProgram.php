<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "shop web";
$db_link = mysqli_connect($host, $username, $password, $database);

// 檢查 POST 請求是否包含 'data'
if (isset($_POST['data'])) {
    $account = $_POST['data'];

    // 如果接收到的數據是空的，則返回 null 或空字符串
    if (trim($account) == '') {
        echo json_encode('');
        exit;
    }

    // 準備 SQL 查詢
    $query = $db_link->prepare("SELECT * FROM vip WHERE account = ?");
    $query->bind_param("s", $account);

    // 執行查詢
    $query->execute();
    $result = $query->get_result();

    // 檢查是否有結果
    if ($result->num_rows > 0) {
        echo json_encode(true); // 帳號存在
    } else {
        echo json_encode(false); // 帳號不存在
    }
} else {
    echo json_encode(''); // 沒有收到數據
}

?>
