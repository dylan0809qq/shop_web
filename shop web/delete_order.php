<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>無標題文件</title>
</head>

<body>
	<?php
session_start();

$host = "localhost";
$username = "root";
$password = "";
$database = "shop web";
$db_link = mysqli_connect($host, $username, $password, $database);

// 檢查是否已登入
if (!isset($_SESSION['name'])) {
    header("Location: shop log and create.php"); // 重新導向至登入頁面
    exit();
}

$account = $_SESSION['name'];

// 檢查是否提供訂單 ID
if (isset($_GET['order_id'])) {
    $orderID = $_GET['order_id'];

    // 檢查訂單是否屬於當前使用者
    $checkOrderQuery = "SELECT * FROM orders WHERE order_id = $orderID AND name = '$account'";
    $checkOrderResult = mysqli_query($db_link, $checkOrderQuery);

    if (mysqli_num_rows($checkOrderResult) > 0) {
        // 獲取訂單狀態
        $orderStatusQuery = "SELECT status FROM orders WHERE order_id = $orderID";
        $orderStatusResult = mysqli_query($db_link, $orderStatusQuery);
        $status = mysqli_fetch_assoc($orderStatusResult)['status'];

        if ($status === '已出貨') {
            // 如果訂單已出貨，顯示無法取消的訊息
            header("Location: order.php?cancel_error=已出貨");
            exit();
        } elseif ($status === '已取消') {
            // 如果訂單已取消，顯示已取消的訊息
            header("Location: order.php?cancel_error=已取消");
            exit();
        } else {
            // 訂單狀態不是已出貨或已取消，可以執行取消訂單的操作

            // 獲取已取消的訂單中的商品資料（示範中的程式碼僅供參考，您需要根據您的資料庫結構進行調整）
            $cancelledOrderItemsQuery = "SELECT * FROM order_items WHERE order_id = $orderID";
            $cancelledOrderItemsResult = mysqli_query($db_link, $cancelledOrderItemsQuery);

            // 循環處理已取消的訂單中的每個商品，將商品庫存還原
            while ($itemRow = mysqli_fetch_assoc($cancelledOrderItemsResult)) {
                $goodsID = $itemRow['goods_id'];
                $quantity = $itemRow['quantity'];

                // 根據商品ID和數量將商品庫存還原（示範中的程式碼僅供參考，您需要根據您的資料庫結構進行調整）
                $restoreInventoryQuery = "UPDATE goods SET quantity = quantity + $quantity WHERE goods_id = $goodsID";
                mysqli_query($db_link, $restoreInventoryQuery);
            }

            // 更新訂單狀態為"已取消"
            $updateOrderStatusQuery = "UPDATE orders SET status = '已取消' WHERE order_id = $orderID";
            mysqli_query($db_link, $updateOrderStatusQuery);

            // 設定成功取消的訊息，並將此訊息作為 URL 參數傳遞回訂單頁面
            header("Location: order.php?cancel_success=true");
            exit();
        }
    }
}

// 如果未提供有效的訂單 ID，或訂單不屬於當前使用者，則重新導向回訂單頁面，並附帶錯誤訊息
header("Location: order.php?cancel_error=true");
exit();
?>
</body>
</html>