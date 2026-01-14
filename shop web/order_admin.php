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

$user_name = $_SESSION['name'];
$action = "";

// 获取用户权限等级
$userLevelQuery = "SELECT level FROM vip WHERE name = '$user_name'";
$userLevelResult = mysqli_query($db_link, $userLevelQuery);

if ($userLevelResult) {
    $userLevel = mysqli_fetch_assoc($userLevelResult)['level'];

    if ($userLevel !== '2') {
        echo('<script>alert("您不是管理員請離開");</script>');
        echo('<script>window.location.href = "index.php";</script>');
        exit; // 終止腳本執行
    }
} else {
    echo('<script>alert("無法獲取用戶權限資訊，請稍後再試");</script>');
}

// 從 orders 資料表中檢索所有訂單資料
$orderQuery = "SELECT * FROM orders ORDER BY orderdate DESC";
$orderResult = mysqli_query($db_link, $orderQuery);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $orderID = $_POST['order_id'];

    // 再次获取用户状态
    $checkUserStatusQuery = "SELECT status FROM vip WHERE name = '$user_name'";
    $checkUserStatusResult = mysqli_query($db_link, $checkUserStatusQuery);
    $userStatus = mysqli_fetch_assoc($checkUserStatusResult)['status'];

    // 查詢訂單的當前狀態
    $checkStatusQuery = "SELECT status FROM orders WHERE order_id = $orderID";
    $checkStatusResult = mysqli_query($db_link, $checkStatusQuery);
    $currentStatus = mysqli_fetch_assoc($checkStatusResult)['status'];

	 if ($currentStatus === '已出貨' || $currentStatus === '已取消') {
        echo '<script>alert("訂單已經是已出貨或已取消狀態。");</script>';
        echo('<script>window.location.href = "order_admin.php";</script>');
        exit;
    }
   if ($action === 'ship' && ($currentStatus === '未出貨' || $currentStatus === '未取消' )) {
    if ($userStatus === 'off') {
						// 如果 status 是 'off'，顯示無法購物的訊息
                         echo('<script>alert("使用者被列為黑名單，無法出貨");</script>');
						 echo('<script>window.location.href = "order_admin.php";</script>');
						}
	  
}
    // 出貨訂單
    $itemsQuery = "SELECT goods_id, quantity FROM order_items WHERE order_id = $orderID";
    $itemsResult = mysqli_query($db_link, $itemsQuery);

    $canShip = true; // 假設可以出貨

    while ($itemRow = mysqli_fetch_assoc($itemsResult)) {
        $goodsID = $itemRow['goods_id'];
        $quantity = $itemRow['quantity'];

        // 查詢商品當前庫存
        $currentStockQuery = "SELECT quantity FROM goods WHERE goods_id = $goodsID";
        $currentStockResult = mysqli_query($db_link, $currentStockQuery);
        $currentStock = mysqli_fetch_assoc($currentStockResult)['quantity'];

        if ($currentStock < $quantity) {
            $canShip = false; // 庫存不足就顯示false
            break; // 結束循環
        }
    }

    if ($canShip  && $action !== 'cancel' && $userStatus !== 'off') {
        // 庫存足夠，執行出貨操作
        $updateQuery = "UPDATE orders SET status = '已出貨' WHERE order_id = $orderID";
        mysqli_query($db_link, $updateQuery);

        // 重置 $itemsResult 指针到开始位置
        mysqli_data_seek($itemsResult, 0);

        while ($itemRow = mysqli_fetch_assoc($itemsResult)) {
            $goodsID = $itemRow['goods_id'];
            $quantity = $itemRow['quantity'];

            // 查詢商品當前庫存
            $currentStockQuery = "SELECT quantity FROM goods WHERE goods_id = $goodsID";
            $currentStockResult = mysqli_query($db_link, $currentStockQuery);
            $currentStock = mysqli_fetch_assoc($currentStockResult)['quantity'];

            $updatedStock = $currentStock - $quantity;
            $updateStockQuery = "UPDATE goods SET quantity = $updatedStock WHERE goods_id = $goodsID";
            mysqli_query($db_link, $updateStockQuery);
        }

        echo '<script>alert("訂單已成功出貨。");</script>';
		echo('<script>window.location.href = "order_admin.php";</script>');
		
    } elseif ($action === 'cancel' && ($currentStatus === '未出貨' || $currentStatus === '未取消')) {
    // 取消訂單
    $updateQuery = "UPDATE orders SET status = '已取消' WHERE order_id = $orderID";
    mysqli_query($db_link, $updateQuery);
    
    // 查詢訂單的購買項目
    $itemsQuery = "SELECT goods_id, quantity FROM order_items WHERE order_id = $orderID";
    $itemsResult = mysqli_query($db_link, $itemsQuery);

    while ($itemRow = mysqli_fetch_assoc($itemsResult)) {
        $goodsID = $itemRow['goods_id'];
        $quantity = $itemRow['quantity'];

        // 查詢商品當前庫存
        $currentStockQuery = "SELECT quantity FROM goods WHERE goods_id = $goodsID";
        $currentStockResult = mysqli_query($db_link, $currentStockQuery);
        $currentStock = mysqli_fetch_assoc($currentStockResult)['quantity'];

        // 更新商品庫存，將取消的商品數量加回去
        $updatedStock = $currentStock + $quantity;
        $updateStockQuery = "UPDATE goods SET quantity = $updatedStock WHERE goods_id = $goodsID";
        mysqli_query($db_link, $updateStockQuery);
    }

    // 重置 $itemsResult 指针到开始位置
    mysqli_data_seek($itemsResult, 0);

    echo '<script>alert("訂單已成功取消，並將商品庫存恢復。");</script>';
}
else {
        // 庫存不足
        echo '<script>alert("庫存不足，無法出貨。");</script>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>管理員訂單管理</title>
	 <style>
		
       <style>
		 
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed; /* 固定表格布局 */
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px; /* 調整最大寬度 */
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #ccc;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        td.text-right {
            text-align: right;
        }

        td.text-left {
            text-align: left;
        }

        form button {
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        form button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        form button.ship {
            background-color: #5cb85c;
            color: #fff;
        }

        form button.cancel {
            background-color: #d9534f;
            color: #fff;
        }

        td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            white-space: nowrap; /* 防止内容换行 */
        }
    </style>
</head>
<body>
    <div class="container">
		<a href="admin_page.php"><img src="img/back_icon.png" alt="返回首頁"></a>
        <h1>管理員訂單管理</h1>
        <table class="table">
    <thead>
        <tr>
            <th>訂單編號</th>
            <th>訂單日期</th>
            <th>訂單狀態</th>
            <th>使用者</th>
            <th>地址</th> 
            <th>購買項目</th>
			<th>購買數量</th>
            <th>價格</th>
            <th>總額</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
       <?php
while ($orderRow = mysqli_fetch_assoc($orderResult)) {
    $orderID = $orderRow['order_id'];
    $orderDate = $orderRow['orderdate'];
    $orderStatus = $orderRow['status'];
    $username = $orderRow['name']; // 獲取用户名

    // 查詢購買項目、價格和數量
    $itemsQuery = "SELECT goods_id, price, quantity FROM order_items WHERE order_id = $orderID";
    $itemsResult = mysqli_query($db_link, $itemsQuery);

    // 查詢訂單總金額
    $totalAmountQuery = "SELECT total_amount FROM orders WHERE order_id = $orderID";
    $totalAmountResult = mysqli_query($db_link, $totalAmountQuery);
    $totalAmount = mysqli_fetch_assoc($totalAmountResult)['total_amount'];

    // 查詢用戶的地址
    $userAddressQuery = "SELECT address FROM vip WHERE name = '$username'";
    $userAddressResult = mysqli_query($db_link, $userAddressQuery);
    $userAddressRow = mysqli_fetch_assoc($userAddressResult);
    $address = $userAddressRow['address']; // 獲取用户地址信息

    echo '<tr>';
    echo '<td>' . $orderID . '</td>';
    echo '<td>' . $orderDate . '</td>';
    echo '<td>' . $orderStatus . '</td>';
    echo '<td>' . $username . '</td>';
    echo '<td>' . $address . '</td>'; 
    echo '<td>';
    while ($itemRow = mysqli_fetch_assoc($itemsResult)) {
        $goodsID = $itemRow['goods_id'];

        $goodsNameQuery = "SELECT gdname FROM goods WHERE goods_id = $goodsID";
        $goodsNameResult = mysqli_query($db_link, $goodsNameQuery);
        $goodsName = mysqli_fetch_assoc($goodsNameResult)['gdname'];

        echo $goodsName . '<br>';
    }
    echo '</td>';
    echo '<td>';
    mysqli_data_seek($itemsResult, 0);
    while ($itemRow = mysqli_fetch_assoc($itemsResult)) {
        echo $itemRow['quantity'] . '<br>'; // 顯示購買數量
    }
    echo '</td>';
    echo '<td>';
    mysqli_data_seek($itemsResult, 0);
    while ($itemRow = mysqli_fetch_assoc($itemsResult)) {
        echo $itemRow['price'] . '<br>';
    }
    echo '</td>';
    echo '<td>' . $totalAmount . '</td>';
    echo '<td>';
    echo '<form method="post">';
    echo '<input type="hidden" name="order_id" value="' . $orderID . '">';
    echo '<button type="submit" name="action" value="ship" ' . ($orderStatus !== '未出貨' && $orderStatus !== '未取消' ? 'disabled' : '') . '>出貨</button>';
    echo '<button type="submit" name="action" value="cancel" ' . ($orderStatus !== '未出貨' && $orderStatus !== '未取消' ? 'disabled' : '') . '>取消</button>';
    echo '</form>';
    echo '</td>';
    echo '</tr>';
}
?>
    </tbody>
</table>
    </div>
	<script>
		 if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
	</script>
</body>
</html>