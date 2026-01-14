<?php
session_start();

$host = "localhost";
$username = "root";
$password = "";
$database = "shop web";
$db_link = mysqli_connect($host, $username, $password, $database);

// 檢查是否已登入
if (!isset($_SESSION['name'])) {
    echo('<script>alert("請先登入帳號");</script>');
    echo('<script>window.location.href = "index.php";</script>');
} else {
    $account = $_SESSION['name'];

    // 檢查使用者的 status 是否為 'off'
    $userStatusQuery = "SELECT status FROM vip WHERE name = '$account'";
    $userStatusResult = mysqli_query($db_link, $userStatusQuery);
    $userStatus = mysqli_fetch_assoc($userStatusResult)['status'];
    
    if ($userStatus === 'off') {
        // 如果 status 是 'off'，顯示無法購物的訊息
        echo('<script>alert("您已被列為黑名單，無法查看訂單");</script>');
        echo('<script>window.location.href = "index.php";</script>');
    }

    // 從 orders 資料表中檢索訂單資料
    $orderQuery = "SELECT * FROM orders WHERE name = '$account' ORDER BY orderdate DESC";
    $orderResult = mysqli_query($db_link, $orderQuery);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>訂單資訊</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
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
        h2 {
            margin-top: 20px;
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
        p {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
   <div class="container">
         <a href="index.php"><img src="img/back_icon.png" alt="返回首頁" style="width: 84px;"></a>
        <h1>訂單資訊</h1>
        <?php
        while ($orderRow = mysqli_fetch_assoc($orderResult)) {
            $orderID = $orderRow['order_id'];
            $orderDate = $orderRow['orderdate'];
            $orderStatus = $orderRow['status']; // 取得訂單狀態
            
            // 從 order_items 資料表中檢索訂單項目資料
            $orderItemsQuery = "SELECT * FROM order_items WHERE order_id = $orderID";
            $orderItemsResult = mysqli_query($db_link, $orderItemsQuery);
            
			// 获取用户等级
$userLevelQuery = "SELECT level FROM vip WHERE name = '$account'";
$userLevelResult = mysqli_query($db_link, $userLevelQuery);

if ($userLevelResult) {
    $userLevel = mysqli_fetch_assoc($userLevelResult)['level'];
} else {
    // 处理无法获取用户等级的情况
    echo('<script>alert("無法獲取用戶等級資訊，請稍後再試");</script>');
    exit;
}
            echo '<h2>訂單編號：' . $orderID . '</h2>';
            echo '<p>訂單日期：' . $orderDate . '</p>';
             echo '<p>訂單狀態：' . $orderStatus . '</p>'; // 顯示訂單狀態
			// 增加條件檢查，只有在訂單狀態不是"已出貨"或"已取消"時才顯示刪除連結
                if ($orderStatus !== '已出貨' && $orderStatus !== '已取消' && $userLevel !== '2') {
                    echo '<td><a href="delete_order.php?order_id=' . $orderID . '">取消訂單</a></td>'; 
                } else {
                    echo '<td></td>'; // 不顯示取消連結
                }
            // 從 vip 資料表中取得地址
            $addressQuery = "SELECT address FROM vip WHERE name = '$account'";
            $addressResult = mysqli_query($db_link, $addressQuery);
            $userAddress = mysqli_fetch_assoc($addressResult)['address'];
            // 顯示訂單項目資料
            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<th>商品名稱</th>';
            echo '<th>數量</th>';
            echo '<th>單價</th>';
            echo '<th>小計</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            $totalAmount = 0; // 初始化總金額	

            while ($itemRow = mysqli_fetch_assoc($orderItemsResult)) {
                $goodsID = $itemRow['goods_id'];

                // 查詢商品名稱
                $goodsNameQuery = "SELECT gdname FROM goods WHERE goods_id = $goodsID";
                $goodsNameResult = mysqli_query($db_link, $goodsNameQuery);
                $goodsName = mysqli_fetch_assoc($goodsNameResult)['gdname'];

                // 計算小計
                $subtotal = $itemRow['quantity'] * $itemRow['price'];
                $totalAmount += $subtotal; // 累加總金額
				
				

                echo '<tr>';
                echo '<td>' . $goodsName . '</td>';
                echo '<td>' . $itemRow['quantity'] . '</td>';
                echo '<td>' . $itemRow['price'] . '</td>';
                echo '<td>' . $subtotal . '</td>';
				
                echo '</tr>';
            }

            echo '<tr>';
            echo '<td colspan="3"><strong>總價格：</strong></td>';
            echo '<td><strong>' . $totalAmount . '</strong></td>';
            echo '</tr>';
			
            echo '<tr>';
            echo '<td colspan="4"><strong>送貨地址：</strong>' . $userAddress . '</td>';
            echo '</tr>';
			
            echo '</tbody>';
            echo '</table>';

            // 將總金額更新到orders資料表中
        }
        ?>
	   <?php
if (isset($_GET['cancel_success']) && $_GET['cancel_success'] == true) {
    echo '<script>alert("訂單已成功取消，庫存已成功返還");</script>';
} elseif (isset($_GET['cancel_error']) && $_GET['cancel_error'] == true) {
    echo '<script>alert("訂單已被管理者更新，無法取消");</script>';
}
?>
    </div>
</body>
</html>