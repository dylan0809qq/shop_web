<?php
session_start();

// 在購物車頁面加載時更新商品價格
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_SESSION['name'])) {
        $account = $_SESSION['name'];
        $host = "localhost";
        $username = "root";
        $password = "";
        $database = "shop web";
        $db_link = mysqli_connect($host, $username, $password, $database);

        $query = "SELECT goods_id, price FROM goods";
        $result = mysqli_query($db_link, $query);

        $_SESSION['price'] = []; // 清空價格陣列
        $_SESSION['quantity'] = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $_SESSION['price'][$row['goods_id']] = (float)$row['price'];
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "shop web";
    $db_link = mysqli_connect($host, $username, $password, $database);

    $account = $_SESSION['name'];
    $insufficientInventoryProducts = []; // 用於儲存庫存不足的商品
    $priceMismatchProducts = []; // 用於儲存價格不符的商品
    $canCheckout = true; // 是否可以結帳的標誌

    $userStatusQuery = "SELECT status FROM vip WHERE name = '$account'";
    $userStatusResult = mysqli_query($db_link, $userStatusQuery);
    $userStatus = mysqli_fetch_assoc($userStatusResult)['status'];
    if ($userStatus === 'off') {
        echo '<script>alert("您已被列為黑名單，無法下單");</script>';
        echo '<script>window.location.href = "index.php";</script>';
        exit; // 停止脚本执行
    }
    if (isset($_POST['update_quantity'])) {
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'quantity_') === 0) {
                $product_id_to_update = substr($key, strlen('quantity_'));
                $new_quantity = intval($value);

                // 獲取商品庫存
                $inventory_query = "SELECT quantity FROM goods WHERE goods_id = $product_id_to_update";
                $inventory_result = mysqli_query($db_link, $inventory_query);

                if ($inventory_result) {
                    $product_inventory = mysqli_fetch_assoc($inventory_result)['quantity'];

                    // 檢查庫存是否足夠
                    if ($new_quantity > $product_inventory) {
                        $productNameQuery = "SELECT gdname FROM goods WHERE goods_id = $product_id_to_update";
                        $productNameResult = mysqli_query($db_link, $productNameQuery);
                        $productName = mysqli_fetch_assoc($productNameResult)['gdname'];

                        // 存储剩余库存信息到数组
                        $insufficientInventoryProducts[] = [
                            'product_id' => $product_id_to_update,
                            'product_name' => $productName,
                            'available_quantity' => $product_inventory, // 添加当前库存信息
                        ];

                        $canCheckout = false; // 不允许结账
                    } else { 
                        $update_query = "UPDATE cart SET goods_num = $new_quantity WHERE name = '$account' AND goods_id = $product_id_to_update";
                        mysqli_query($db_link, $update_query);
                        // 更新 session 中的商品数量
                        $_SESSION['quantity'][$product_id_to_update] = $new_quantity;
                    }
                } else {
                    // 獲取庫存失敗
                    echo '<script>alert("獲取庫存失敗");</script>';
                }
            }
        }
    }

    if (isset($_POST['remove_item'])) {
        // 從購物車中刪除商品
        $product_id_to_remove = $_POST['remove_item'];
        $delete_query = "DELETE FROM cart WHERE name = '$account' AND goods_id = $product_id_to_remove";
        if (mysqli_query($db_link, $delete_query)) {
            echo '<script>alert("已成功將該商品從購物車中移除");</script>';
        } else {
            echo '<script>alert("刪除商品失敗");</script>';
        }
    }

    if (isset($_POST['checkout'])) {
        $checkAddressQuery = "SELECT address FROM vip WHERE name = '$account'";
        $addressResult = mysqli_query($db_link, $checkAddressQuery);
        $userAddress = mysqli_fetch_assoc($addressResult)['address'];

        if (empty($userAddress)) {
            echo '<script>alert("請先在會員資料填寫地址");</script>';
        } else {
            $cartItemCountQuery = "SELECT COUNT(*) as itemCount FROM cart WHERE name = '$account'";
            $cartItemCountResult = mysqli_query($db_link, $cartItemCountQuery);
            $cartItemCount = mysqli_fetch_assoc($cartItemCountResult)['itemCount'];

            if ($cartItemCount === 0) {
                echo '<script>alert("您的購物車是空的，請先添加商品至購物車");</script>';
            } else {
                $cartQuery = "SELECT cart.goods_id, goods.gdname, goods.price, cart.goods_num, goods.photo, goods.updown, goods.quantity as available_quantity
                              FROM cart
                              JOIN goods ON cart.goods_id = goods.goods_id
                              WHERE cart.name = '$account'";
                $cartResult = mysqli_query($db_link, $cartQuery);

                while ($row = mysqli_fetch_assoc($cartResult)) {
                    $productStatus = $row['updown'];
                    $availableQuantity = $row['available_quantity'];
                    $cartQuantity = $row['goods_num'];
                    $productName = $row['gdname']; // 獲取商品名稱
                    $productID = $row['goods_id'];

                    // 檢查商品是否已经下架
                    if ($productStatus === '下架') {
                        echo '<script>alert("商品 ' . $productName . ' 已經下架，無法購買。");</script>';
                        $canCheckout = false; // 商品已经下架，不允许結帳
                        break; // 結束檢查
                    }

                    // 檢查商品庫存是否足夠
                    if ($cartQuantity > $availableQuantity) {
                        $insufficientInventoryProducts[] = [
                            'product_id' => $productID,
                            'product_name' => $productName,
                            'available_quantity' => $availableQuantity, // 添加当前库存信息
                        ];
                        $canCheckout = false; // 不允许結帳
                    }

                    // 獲取商品價格
                    $cartPriceQuery = "SELECT price FROM goods WHERE goods_id = $productID";
                    $cartPriceResult = mysqli_query($db_link, $cartPriceQuery);
                    $cartPrice = (float)mysqli_fetch_assoc($cartPriceResult)['price'];

                    // 檢查商品價格是否一致
                    if ($_SESSION['price'][$productID] !== $cartPrice) {
                        $priceMismatchProducts[] = [
                            'product_id' => $productID,
                            'product_name' => $productName,
                            'cart_price' => $cartPrice,
                        ];
                        $canCheckout = false; // 商品價格不一致，不允許結帳
                    }
                }

                $userLevelQuery = "SELECT level FROM vip WHERE name = '$account'";
                $userLevelResult = mysqli_query($db_link, $userLevelQuery);

                if ($userLevelResult) {
                    $userLevel = mysqli_fetch_assoc($userLevelResult)['level'];

                    if ($userLevel == 2) {
                        // 如果用户是管理员，清空购物车
                        $clearCartQuery = "DELETE FROM cart WHERE name = '$account'";
                        mysqli_query($db_link, $clearCartQuery);

                        echo('<script>alert("您的等級為管理者無法購物，已為您清空購物車");</script>');
                        echo('<script>window.location.href = "index.php";</script>');
                        exit; // 為了確保程式不會繼續執行
                    }
                }

                if ($canCheckout) {
                    // 創建訂單
                    $status = '未出貨'; //默認狀態為未出貨

                    $insertOrderQuery = "INSERT INTO orders (name, orderdate, status) VALUES ('$account', NOW(), '$status')";
                    $result = mysqli_query($db_link, $insertOrderQuery);

                    if ($result) {
                        $orderID = mysqli_insert_id($db_link); // 獲取剛剛插入的訂單ID

                        // 獲取購物車內容並保存到order_items
                        $cartQuery = "SELECT * FROM cart WHERE name = '$account'";
                        $cartResult = mysqli_query($db_link, $cartQuery);

                        while ($cartRow = mysqli_fetch_assoc($cartResult)) {
                            $productID = $cartRow['goods_id'];
                            $quantity = $cartRow['goods_num'];
                             // 執行結帳操作
                             $productPrice = $_SESSION['price'][$productID]; // 获取商品價格
                            // 執行結帳操作
                            $productPriceQuery = "SELECT price FROM goods WHERE goods_id = $productID";
                            $productPriceResult = mysqli_query($db_link, $productPriceQuery);
                            $productPrice = (float)mysqli_fetch_assoc($productPriceResult)['price'];

                            $insertOrderItemQuery = "INSERT INTO order_items (order_id, goods_id, quantity, price) VALUES ($orderID, $productID, $quantity, $productPrice)";
                            mysqli_query($db_link, $insertOrderItemQuery);

                            // 減少庫存數量
                            $updateInventoryQuery = "UPDATE goods SET quantity = quantity - $quantity WHERE goods_id = $productID";
                            mysqli_query($db_link, $updateInventoryQuery);
							
							$totalAmount=$quantity*$productPrice;
							$updateTotalAmountQuery = "UPDATE orders SET total_amount = $totalAmount WHERE order_id = $orderID";
            				mysqli_query($db_link, $updateTotalAmountQuery);
                        }
                    }

                    // 如果結帳成功就清空購物車
                    $clearCartQuery = "DELETE FROM cart WHERE name = '$account'";
                    mysqli_query($db_link, $clearCartQuery);

                    echo '<script>alert("訂單已成功建立，感謝您的購買");</script>';
                } else {
                    // 商品價格不一致的提示
    if (!empty($priceMismatchProducts)) {
        echo '<script>alert("以下商品價格已變動，無法購買：\\n';
        foreach ($priceMismatchProducts as $product) {
            $productName = $product['product_name'];
            $cartPrice = $product['cart_price'];
            echo "$productName - 新價格: $cartPrice\\n";
        }
        echo '");</script>';
    }
                }
            }
        }
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>購物車</title>
    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .cart-item {
            display: table-row;
            border: 1px solid #ccc;
        }
        .cart-item img {
            max-width: 100px;
            max-height: 100px;
            margin-right: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            vertical-align: middle; /* 垂直置中對齊 */
            border: 1px solid #ddd; /* 添加邊框 */
        }
        .th-name, .th-price {
            text-align: center;
        }
    .container {
}
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php"><img src="img/back_icon.png" alt="返回首頁" style="width: 84px;"></a>    
        <h1>購物車內容</h1>
        
        <form action="" method="post">
            <table>
                <!-- 商品列表部分 -->
                 <thead>
                    <tr>
                        <th>圖片</th>
                        <th>名稱</th>
                        <th>價格</th>
                        <th>購買數量</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
					
                    $totalPrice = 0;
					$showCheckoutButton = false; // 初始化是否顯示結帳按鈕
                 
                    if (!isset($_SESSION['name'])) {
                        echo('<script>alert("請先登入帳號");</script>');
                        echo('<script>window.location.href = "index.php";</script>');
                    } else {
                        $host = "localhost";
                        $username = "root";
                        $password = "";
                        $database = "shop web";
                        $db_link = mysqli_connect($host, $username, $password, $database);
                 
                        $account = $_SESSION['name'];
						// 檢查使用者的 status 是否為 'off'
                        $userStatusQuery = "SELECT status FROM vip WHERE name = '$account'";
                        $userStatusResult = mysqli_query($db_link, $userStatusQuery);
                        $userStatus = mysqli_fetch_assoc($userStatusResult)['status'];
                        if ($userStatus === 'off') {
						// 如果 status 是 'off'，顯示無法購物的訊息
                         echo('<script>alert("您已被列為黑名單，無法下單");</script>');
						 echo('<script>window.location.href = "index.php";</script>');
						}
                        $query = "SELECT cart.goods_id, goods.gdname, goods.price, cart.goods_num, goods.photo FROM cart
                                  JOIN goods ON cart.goods_id = goods.goods_id
                                  WHERE cart.name = '$account'";
                        $result = mysqli_query($db_link, $query);
                 
                        while ($row = mysqli_fetch_assoc($result)) {
                            $itemPrice = $row['price'];
                            $itemTotalPrice = $itemPrice * $row['goods_num'];
                    
                            echo '<tr class="cart-item">';
                            echo '<td><img src="' . $row['photo'] . '" alt="商品圖片"></td>';
                            echo '<td>' . $row['gdname'] . '</td>';
                            echo '<td>$' . $row['price'] . '</td>';
                            echo '<td>';
                            echo '<input type="number" name="quantity_' . $row['goods_id'] . '" value="' . $row['goods_num'] . '" min="1">';
                            echo '</td>';
                            echo '<td>';
                            echo '<button type="submit" name="update_quantity" value="' . $row['goods_id'] . '">更新數量</button>';
                            echo '<button type="submit" name="remove_item" value="' . $row['goods_id'] . '">刪除</button>';
                            echo '</td>';
                            echo '</tr>';

                            $totalPrice += $itemTotalPrice;
							$showCheckoutButton = true; //讓結帳按鈕顯示
                        }
                    }
                    // 顯示總價
                    echo '<tr><td colspan="5">總價格：</td><td>$' . $totalPrice . '</td></tr>';
                    ?>
					<!-- 結帳部分 -->
            <?php
					$userLevelQuery = "SELECT level FROM vip WHERE name = '$account'";
$userLevelResult = mysqli_query($db_link, $userLevelQuery);

if ($userLevelResult) {
    $userLevel = mysqli_fetch_assoc($userLevelResult)['level'];
} else {
    // 处理无法获取用户等级的情况
    echo('<script>alert("無法獲取用戶等級資訊，請稍後再試");</script>');
    exit;
}
            if ($userLevel !== '2') {
    if ($showCheckoutButton) {
        echo '<button type="submit" name="checkout">結帳</button>';
    } else {
        echo '<tr><td colspan="6">您的購物車是空的，請先添加商品至購物車</td></tr>';
    }
} else {
     echo ('<script>alert("您的等級為管理者無法購物");</script>');
	echo('<script>window.location.href = "index.php";</script>');
}
	      ?>
                </tbody>
            </table>
        </form>
    </div>
</body>
</html>
