<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "shop web";
$db_link = mysqli_connect($host, $username, $password, $database);


// 檢查 POST 請求是否包含 'name'
if (isset($_POST['name'])) {
    $name = $_POST['name'];

    
    // 檢查名稱長度是否在 2 到 8 個字符之間
    if (mb_strlen($name, 'UTF-8') < 2 || mb_strlen($name, 'UTF-8') > 8) {
        echo json_encode(false); // 名稱長度不符合要求
        exit;
    }

    // 準備 SQL 查詢
    $query = $db_link->prepare("SELECT * FROM vip WHERE name = ?");
    $query->bind_param("s", $name);

    // 執行查詢
    $query->execute();
    $result = $query->get_result();

    // 檢查是否有結果
    if ($result->num_rows > 0) {
        echo json_encode(false); // 名稱已存在
    } else {
        echo json_encode(true); // 名稱不存在
    }
} else {
    echo json_encode(''); // 沒有收到數據
}

?>
