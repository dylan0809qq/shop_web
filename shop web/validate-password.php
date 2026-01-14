<?php
if (isset($_POST['password'], $_POST['checkPassword'])) {
    $password = $_POST['password'];
    $checkPassword = $_POST['checkPassword'];

    // 初始化回應數據
    $response = ['valid' => false];

    // 檢查密碼長度和是否匹配
    if (strlen($password) >= 8 && strlen($password) <= 20 && $password === $checkPassword) {
        $response['valid'] = true;
    }

    
    echo json_encode($response);
} else {
    // 沒有接收到期望的 POST 數據
    echo json_encode(['valid' => false]);
}
?>
