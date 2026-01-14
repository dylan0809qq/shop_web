<?php
// 確保請求是通過 POST 方法發送的
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 檢查 POST 請求中是否包含 'search' 參數
    if (isset($_POST['search'])) {
        // 獲取搜索文字
        $searchText = $_POST['search'];
        $host = "localhost";
        $username = "root";
        $password = "";
        $database = "shop web"; // 資料庫名稱
        $db_link = mysqli_connect($host, $username, $password, $database);

        // 準備 SQL 查詢，使用 LIKE 操作符進行部分匹配
        // 同時檢查商品是否上架並有庫存
        $sql = "SELECT gdname FROM goods WHERE gdname LIKE '%$searchText%' AND updown = '上架' AND quantity > 0";

        // 執行查詢
        $result = mysqli_query($db_link, $sql);

        // 檢查並輸出結果
        if (mysqli_num_rows($result) > 0) {
            // 輸出每個匹配的商品名稱作為選項
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<option value="' . $row['gdname'] . '">';
            }
        }

        // 關閉連接
        mysqli_close($db_link);
    }
}
?>
