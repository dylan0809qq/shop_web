<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "shop web"; 
$db_link = mysqli_connect($host, $username, $password, $database);




if (isset($_POST['account'])) {
    $account = $_POST['account'];

    // 檢查用戶名長度及是否包含中文字符
    if (strlen($account) < 8 || strlen($account) > 20 || preg_match('/[\x{4e00}-\x{9fa5}]/u', $account)) {
        echo json_encode(false); 
        exit;
    }

    // 準備 SQL 查詢
    $query = $db_link->prepare("SELECT * FROM vip WHERE account = ?");
    $query->bind_param("s", $account);

    // 执行查询
    $query->execute();
    $result = $query->get_result();

    // 检查是否有结果
    if ($result->num_rows > 0) {
        echo json_encode(false); // 帳號存在
    } else {
        echo json_encode(true); // 帳號不存在
    }
} else {
    echo json_encode(""); // 没有收到數據
}

?>
