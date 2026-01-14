<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>管理者頁面</title>
    <style>
        body {
            text-align: center;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .header {
            background-color: #334144;
            color: white;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
			height: 110px;
        }
                .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: row; /* 将垂直排列改为水平排列 */
        }
        .button-row {
            display: flex;
            justify-content: center;
            margin-top: 20px; /* 添加上方间距 */
        }
                .button-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px;
            margin: 0 10px;
            width: calc(16.33% - 20px);
        }
        .button {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px 50px;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.2s;
            margin: 10px;
            text-decoration: none;
        }
        .button img {
            width: 100px;
            height: 100px;
        }
        .button span {
            font-size: 20px;
        }
        .button:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
        .logout-link {
            color: white;
            text-decoration: none;
            font-weight: bold;
			font-size: 36px;
        }
		.welcome{
			font-size: 36px;
		}
    </style>
</head>
<body>
    <div class="header">
        <?php
            session_start();
            // 資料庫連接設定
            $host = "localhost";
            $username = "root";
            $password = "";
            $database = "shop web"; // 資料庫名稱
            $db_link = mysqli_connect($host, $username, $password, $database);

            if (isset($_GET['logout'])) {
                session_destroy(); // 清除 Session
                header("Location: shop log and create.php"); // 重新導向到登入頁面
                exit;
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
                // 顯示用戶名稱和登出連結
                echo '<span class="welcome">歡迎，' . $_SESSION['name'] . '！</span>';
                echo '<a href="?logout=1" class="logout-link">登出</a>';
            } else {
                // 未登入狀態，顯示登入連結
                echo '<a href="shop log and create.php">登入/註冊</a>';
            }
		if(!isset($_SESSION['name'])) {
			echo('<script>alert("您還未登入");</script>');
			echo('<script>window.location.href = "index.php";</script>');
			
			
		}
        ?>
    </div>
<div class="container">
        <div class="button-container">
            <a href="goods upload.php" class="button">
                <img src="img/上架.png" alt="商品上架">
                <span style="font-size: 32px;">商品上架</span>
            </a>
        </div>
  <div class="button-container">
            <a href="order_admin.php" class="button">
                <img src="img/訂單.png" alt="管理訂單">
                <span style="font-size: 32px;">管理訂單</span>
            </a>
        </div>
  <div class="button-container">
            <a href="index.php" class="button">
                <img src="img/瀏覽.png" alt="瀏覽網頁">
                <span style="font-size: 32px;">瀏覽網頁</span>
            </a>
        </div>
  <div class="button-container">
            <a href="goods manage.php" class="button">
                <img src="img/商品管理.png" alt="商品管理">
                <span style="font-size: 32px;">商品管理</span>
            </a>
        </div>
  <div class="button-container">
            <a href="vip_management.php" class="button">
                <img src="img/vip.png" alt="管理會員">
                <span style="font-size: 32px;">會員管理</span>
            </a>
        </div>
</div>
</body>
</html>