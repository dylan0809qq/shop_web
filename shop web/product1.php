<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>宥來購購物網站 - 產品詳情</title>
    <style>
		.nav-links {
            font-size: 32px; /* 調整連結的字體大小 */
        }
		 .welcome {
            color: #FB0307; 
			font-size: 40px;}
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #334144;
            color: white;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
            font-weight: bold;
        }
        .logo {
            display: flex;
            align-items: center;
        }
        .logo img {
            width: 80px;
            height: auto;
            margin-left: 10px;
        }
        .product-details {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 30px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 20px;
        }
        .product-details img {
            max-width: 300px;
            max-height: 300px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-right: 20px;
        }
        .product-info {
            flex: 1;
        }
        .product-info h2 {
            color: #FB0307;
            margin-bottom: 10px;
        }
        .product-info p {
            font-size: 18px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="logo">宥來購 <a href="index.php"><img src="img/logo.png"></a></h1>
        <div class="header-links nav-links">
            <a href="order.php">訂單</a>
            <a href="vip.php">會員</a>
            <a href="cart.php">購物車</a>
            <?php
            session_start();
            // 資料庫連接設定
            $host = "localhost";
            $username = "root";
            $password = "";
            $database = "shop web"; // 資料庫名稱
            $db_link = mysqli_connect($host, $username, $password, $database);
            // 登出功能
            if (isset($_SESSION['name'])) {
            // 顯示用戶名稱和登出連結
            echo '<span class="welcome">歡迎，' . $_SESSION['name'] . '！</span>';
            echo '<a href="?logout=1" class="logout">登出</a>';
            
           

            // 檢查管理员用户登入狀態，如果登入則顯示返回管理者頁面的連接
            if ($_SESSION['name'] === '管理員') {
                echo '<a href="admin_page.php" class="admin-link">返回管理者頁面</a>';
            }
            } else {
               // 未登入狀態，顯示登入連結
            echo '<a href="shop log and create.php">登入/註冊</a>';
            }
            ?>

        </div>
        </div>
    </div>
   <div class="product-details">
        <?php
// 數據庫連接設置
$host = "localhost";
$username = "root";
$password = "";
$database = "shop web";
$db_link = mysqli_connect($host, $username, $password, $database);
if(empty($_GET['product_id'])){
	echo('<script>alert("商品不存在");</script>');
    echo('<script>window.location.href = "index.php";</script>');
	exit;
}
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    $query = "SELECT gdname, price, content, quantity, date, photo FROM goods WHERE goods_id = $product_id";
    $result = mysqli_query($db_link, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        ?>
        <img src="<?php echo $row['photo']; ?>" alt="產品圖片">
        <div class="product-info">
            <form action="" method="post">
                <label>商品名稱：<?php echo $row['gdname']; ?></label><br>
                <label>商品介紹：<?php echo $row['content']; ?></label><br>
                <label>價格：$<?php echo $row['price']; ?></label><br>
                <label>庫存：<?php echo $row['quantity']; ?></label><br>
                <label>上架日期：<?php echo $row['date']; ?></label><br>
                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                <input type="hidden" name="gdname" value="<?php echo $row['gdname']; ?>">
                <input type="hidden" name="price" value="<?php echo $row['price']; ?>">
                <label>購買數量：</label>
                <input type="number" name="item_quantity" value="1" min="1">
                  <?php	
                if (isset($_SESSION['name'])) {
                    $user_name = $_SESSION['name'];
                    $userLevelQuery = "SELECT level FROM vip WHERE name = '$user_name'";
                    $userLevelResult = mysqli_query($db_link, $userLevelQuery);
                    $userLevel = mysqli_fetch_assoc($userLevelResult)['level'];

                    if ($userLevel !== '2') {
                        // 檢查使用者的 status 是否為 'off'
                        $userStatusQuery = "SELECT status FROM vip WHERE name = '$user_name'";
                        $userStatusResult = mysqli_query($db_link, $userStatusQuery);
                        $userStatus = mysqli_fetch_assoc($userStatusResult)['status'];

                        if ($userStatus !== 'off') {
                            echo '<button type="submit" name="add_to_cart">加入購物車</button>';
                        } else {
                            // 如果 status 是 'off'，顯示無法購物的訊息
                            echo('<script>alert("您已被列為黑名單，無法購物");</script>');
                            echo('<script>window.location.href = "index.php";</script>');
                        }
                    }
                }
                ?>
            </form>
	   </div>
	    <?php
    } else {
        // 商品ID不存在，顯示提示信息並重定向到index.php
        echo('<script>alert("該商品不存在");</script>');
        echo('<script>window.location.href = "index.php";</script>');
    }
}
?>
            <?php
            if (isset($_POST['add_to_cart'])) {
                if (!isset($_SESSION['name'])) {
                    echo('<script>alert("請先登入帳號");</script>');
                } else {
                    $product_id = $_POST['product_id'];
                    $product_quantity = $_POST['item_quantity'];

                    //數據庫連接設置
                    $host = "localhost";
                    $username = "root";
                    $password = "";
                    $database = "shop web";
                    $db_link = mysqli_connect($host, $username, $password, $database);

                    // 獲取當前登入用户的帳戶名
                    $account = $_SESSION['name'];

                    // 查询用戶帳戶對應的 VIP 表的 name
                    $vip_query = "SELECT name FROM vip WHERE name = '$account'";
                    $vip_result = mysqli_query($db_link, $vip_query);

                    if ($vip_row = mysqli_fetch_assoc($vip_result)) {
                        $vip_account = $vip_row['name'];

                        // 查詢商品的價格名稱庫存
                        $product_query = "SELECT gdname, price, quantity FROM goods WHERE goods_id = $product_id";
                        $product_result = mysqli_query($db_link, $product_query);
                        $product_row = mysqli_fetch_assoc($product_result);
                        $product_name = $product_row['gdname'];
                        $product_price = $product_row['price'];
                        $product_quantity_available = $product_row['quantity'];

                        // 检查加入購物車的數量是否大於庫存
                        if ($product_quantity > $product_quantity_available) {
                            echo('<script>alert("已達購買上限");</script>');
                            return;
                        }

                        // 检查商品是否已經在購物車中
                        $check_query = "SELECT goods_id FROM cart WHERE name = '$vip_account' AND goods_id = $product_id";
                        $check_result = mysqli_query($db_link, $check_query);

                        if (mysqli_num_rows($check_result) == 0) {
                            // 商品還未在購物車中，插入新紀錄
                            $insert_query = "INSERT INTO cart (name, goods_id, goods_name, goods_price, goods_num) VALUES ('$vip_account', $product_id, '$product_name', $product_price, $product_quantity)";
                            $result = mysqli_query($db_link, $insert_query);

                            if ($result) {
                                // 獲取該用户已经添加到購物車的商品
                                $select_query = "SELECT goods_id FROM cart WHERE name = '$vip_account'";
                                $result = mysqli_query($db_link, $select_query);

                                $cart_items = array();
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $cart_items[] = $row['goods_id'];
                                }

                                $_SESSION['cart_items'][$vip_account] = $cart_items;
                                $_SESSION['item_quantities'][$vip_account][$product_id] = $product_quantity;

                                echo('<script>alert("已成功加入購物車");</script>');
                            } else {
                                echo('<script>alert("資料庫儲存失敗");</script>');
                            }
                        } else {
                            // 商品已经在購物車中，更新購買數量
                            $update_query = "UPDATE cart SET goods_num = goods_num + $product_quantity WHERE name = '$vip_account' AND goods_id = $product_id";
                            $result = mysqli_query($db_link, $update_query);

                            if ($result) {
                                $_SESSION['item_quantities'][$vip_account][$product_id] += $product_quantity;
                                echo('<script>alert("已更新購物車中的商品數量");</script>');
                            } else {
                                echo('<script>alert("更新購物車商品數量失敗");</script>');
                            }
                        }
                    } else {
                        echo('<script>alert("VIP 用戶不存在");</script>');
                    }
                }
            }
            ?>
  
    </div>
</body>
</html>