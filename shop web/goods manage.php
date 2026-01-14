<?php
session_start();

// 建立與資料庫的連接
$host = "localhost";
$username = "root";
$password = "";
$database = "shop web";
$db_link = mysqli_connect($host, $username, $password, $database);

// 檢查是否已登入
if (!isset($_SESSION['name'])) {
    echo('<script>alert("您未登入帳號");</script>');
    echo('<script>window.location.href = "index.php";</script>');
    exit;
}

// 檢查是否為管理員
$is_admin = false;
$username = $_SESSION['name'];

// 檢查用戶權限
$userLevelQuery = "SELECT level FROM vip WHERE name = '$username'";
$userLevelResult = mysqli_query($db_link, $userLevelQuery);

if ($userLevelResult) {
    $userLevel = mysqli_fetch_assoc($userLevelResult)['level'];

    if ($userLevel !== '2') {
        echo('<script>alert("您不是管理員請離開");</script>');
        echo('<script>window.location.href = "index.php";</script>');
        exit;
    } else {
        $is_admin = true;
    }
} else {
    echo('<script>alert("無法獲取用戶權限資訊，請稍後再試");</script>');
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>商品列表</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        img {
            max-width: 100px;
            max-height: 100px;
        }
    </style>
</head>
<body>
    <div class="container">
		<div class="back-button">
		<a href="admin_page.php"><img src="img/back_icon.png" alt="返回首頁"></a>
        </div>
        <h1>商品列表</a></h1>
        <table>
            <tr>
                <th>圖片</th>
                <th>名稱</th>
                <th>內容</th>
                <th>價格</th>
                <th>庫存</th>
                <th>操作</th> 
            </tr>

            <?php
            $host = "localhost";
            $username = "root";
            $password = "";
            $database = "shop web"; // 資料庫名稱
            $db_link = mysqli_connect($host, $username, $password, $database);

            $query = "SELECT gdname, price, photo, content, quantity, goods_id FROM goods";
            $result = mysqli_query($db_link, $query);
            
            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <tr>
                    <td><img src="<?php echo $row['photo'] ?>"></td>
                    <td><?php echo $row['gdname'] ?></td>
                    <td><?php echo $row['content'] ?></td>
                    <td><?php echo $row['price'] ?></td>
                    <td><?php echo $row['quantity'] ?></td>
                    <td><a href="edit_product.php?product_id=<?php echo $row['goods_id'] ?>">修改</a></td>
                </tr>
            <?php } ?>

        </table>
    </div>
</body>
</html>
