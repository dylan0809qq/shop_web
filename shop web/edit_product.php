<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>編輯商品</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h1 {
            margin-top: 0;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        textarea {
            resize: vertical;
        }
        input[type="submit"] {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 3px;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
		.button-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
    </style>
</head>
<body>
	
    <div class="container">
        <?php
session_start();

$host = "localhost";
$username = "root";
$password = "";
$database = "shop web"; // 資料庫名稱
$db_link = mysqli_connect($host, $username, $password, $database);

// 檢查是否已登入
if (!isset($_SESSION['name'])) {
    header("Location: shop log and create.php"); // 重新導向至登入頁面
    exit();
}
		if (isset($_SESSION['name'])) {
				$user_name = $_SESSION['name'];
                $userLevelQuery = "SELECT level FROM vip WHERE name = '$user_name'";
                $userLevelResult = mysqli_query($db_link, $userLevelQuery);
                $userLevel = mysqli_fetch_assoc($userLevelResult)['level'];
				if ($userLevel !== '2') {
					echo('<script>alert("您不是管理員請離開");</script>');
					echo('<script>window.location.href = "index.php";</script>');
				}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $gdname = $_POST['gdname'];
    $price = $_POST['price'];
    $content = $_POST['content'];
    $quantity = $_POST['quantity'];

    // 更新商品資訊
    $update_query = "UPDATE goods SET gdname = '$gdname', price = '$price', content = '$content', quantity = '$quantity' WHERE goods_id = '$product_id'";
    $update_result = mysqli_query($db_link, $update_query);

    if ($update_result) {
        echo ('<script>alert("商品更新成功");</script>');
    } else {
        echo ('<script>alert("商品更新時發生錯誤");</script>') . mysqli_error($db_link);
    }

    // 判斷是否要下架商品
    if (isset($_POST['change_status'])) {
        $product_id = $_POST['product_id'];
        $status_query = "SELECT updown FROM goods WHERE goods_id = '$product_id'";
        $status_result = mysqli_query($db_link, $status_query);

        if ($status_result && mysqli_num_rows($status_result) > 0) {
            $status_row = mysqli_fetch_assoc($status_result);
            $current_status = $status_row['updown'];

            // 切換上架/下架狀態
            $new_status = ($current_status == '上架') ? '下架' : '上架';
            $update_status_query = "UPDATE goods SET updown = '$new_status' WHERE goods_id = '$product_id'";
            mysqli_query($db_link, $update_status_query);

            echo ('<script>alert("商品狀態已更改為 ' . $new_status . '");</script>');
        } else {
            echo ('<script>alert("無法獲取商品狀態");</script>');
        }
    }
}
		}
// 獲取商品資訊
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    $query = "SELECT gdname, price, photo, content, quantity, updown FROM goods WHERE goods_id = '$product_id'";
    $result = mysqli_query($db_link, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
?>
               <div class="container">
                <h1>編輯商品 - <?php echo $row['gdname']; ?></h1>
                <form action="" method="post">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    名稱: <input type="text" name="gdname" value="<?php echo $row['gdname']; ?>"><br>
                    價格: <input type="text" name="price" value="<?php echo $row['price']; ?>"><br>
                    內容: <textarea name="content" maxlength="30"><?php echo $row['content']; ?></textarea>
                    庫存: <input type="text" name="quantity" value="<?php echo $row['quantity']; ?>"><br>
                    狀態: <?php echo $row['updown']; ?><br>
                    <input type="submit" name="update_product" value="更新商品">
                    <input type="submit" name="change_status" value="<?php echo ($row['updown'] == '上架') ? '下架' : '上架'; ?>">
                    <a href="admin_page.php"><input type="button" value="返回"></a>
                </form>
            </div>
                
		<?php
            } else {
               echo('<script>alert("找不到該商品");</script>');
			   echo('<script>window.location.href = "goods manage.php";</script>');
            }
        } else {
            echo('<script>alert("未提供商品編號");</script>');
        }
        ?>
    </div>
</body>
</html>