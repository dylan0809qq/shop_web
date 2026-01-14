<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>宥來購購物網站</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
		
		 .welcome {
    font-size: 24px;
  }
        .product-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            margin-top: 30px;
        }
       .product {
    border: 1px solid #ddd;
    padding: 10px;
    margin: 10px;
    width: calc(20.33% - 30px); /* 使用 calc 計算寬度减去外邊距寬度 */
    box-sizing: border-box; /* 讓 padding 不會增加元素的實際寬度 */
}
         .product img {
        max-width: 100%;
        height: auto;
        width: 600px; 
        height: 500px; 
        cursor: pointer;
    }
        .cart {
            margin-top: 30px;
            background-color: #f9f9f9;
            padding: 10px;
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
        .header a.logout {
            color: blue; 
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
        .welcome {
            color: #FB0307; 
			font-size: 40px;}
		.nav-links {
            font-size: 32px; /* 調整連結的字體大小 */
        }
		.row {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin-top: 10px; /*調整兼具*/
			padding: 20px;
        }

		.search-form input[type="text"] {
         flex-grow: 1;
         font-size: 1rem;
         padding: 10px; 
        }

    </style>
</head>
<body>
    <div class="header">
		
        <h1 class="logo">宥來購 <img src="img/logo.png"></h1>
		<div>
		<form action="search_backend.php" method="POST" class="search-form">
            <input id="search-input" type="text" name="search" placeholder="搜尋商品..." list="search-suggestions">
            <datalist id="search-suggestions"></datalist>
        </form>
			</div>
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
                // 顯示用戶名稱和登出連結
                echo '<span class="welcome">歡迎，' . $_SESSION['name'] . '！</span>';
                echo '<a href="?logout=1" class="logout">登出</a>'; // 登出連結
                // 檢查管理員用戶登入狀態，如果是等級2就要顯示返回管理者葉面連結
                if ($userLevel === '2') {
                    echo '<a href="admin_page.php" class="admin-link">返回管理者頁面</a>';
                }
            } else {
                // 未登入狀態，顯示登入連結
                echo '<a href="shop log and create.php">登入/註冊</a>';
            }
            ?>

        </div>
    </div>
 <div class="product-list">
    <table>
        <tr>
            <?php
            // 從資料庫讀取商品資訊並顯示
            $query = "SELECT gdname, price, photo, goods_id, updown, quantity FROM goods"; // 添加 quantity 欄位到查詢
            $result = mysqli_query($db_link, $query);

            $count = 0; // 初始化計數變數

            while ($row = mysqli_fetch_assoc($result)) {
                // 檢查庫存數量，如果庫存為0，則更新商品狀態為下架
                if ($row['quantity'] == 0 && $row['updown'] !== '下架') {
                    $updateStatusQuery = "UPDATE goods SET updown = '下架' WHERE goods_id = " . $row['goods_id'];
                    mysqli_query($db_link, $updateStatusQuery);
                }

                // 只顯示未下架的商品
                if ($row['updown'] === '上架') {
                    if ($count % 3 == 0 && $count != 0) {
                        echo '</tr><tr>';
                    }

                    echo '<td class="product">';
                    // 將下面這行連結加入到產品圖片上
                    echo '<a href="product1.php?product_id=' . $row['goods_id'] . '"><img src="' . $row['photo'] . '" alt="' . $row['gdname'] . '"></a>';
                    echo '<h3>' . $row['gdname'] . '</h3>';
                    echo '<p>價格：$' . $row['price'] . '</p>';
                    echo '</td>';

                    $count++;
                }
            }

            // 填充任何空的單元格，如果最後一行不足三個商品
            $remainingCells = 3 - ($count % 3);
            if ($remainingCells < 3) {
                for ($i = 0; $i < $remainingCells; $i++) {
                    echo '<td class="empty-product"></td>';
                }
                echo '</tr>';
            }
            ?>
        </tr>
    </table>
</div>
</div>
</body>
<script>
$(document).ready(function() {
    $('#search-input').on('keyup', function() {
        var searchText = $(this).val();
        $.ajax({
            url: 'search_backend.php',
            type: 'POST',
            data: {search: searchText},
            success: function(data) {
                $('.product-list').html(data); // 將搜索結果顯示在 '.product-list' 元素中
            }
        });
    });
});
</script>
<script>
	$(document).ready(function() {
    $('#search-input').on('input', function() {
        var searchText = $(this).val();
        if (searchText.length > 0) {
            $.ajax({
                url: 'search_suggestions.php', 
                type: 'POST',
                data: {search: searchText},
                success: function(data) {
                    $('#search-suggestions').html(data); // 直接插入數據到 datalist
                }
            });
        } else {
            $('#search-suggestions').empty(); // 清空 datalist
        }
    });
});

</script>
</html>