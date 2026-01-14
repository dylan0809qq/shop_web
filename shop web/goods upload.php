<?php
session_start();

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>商品上架</title>
<style>
	.back-button{
    transform: scale(0.5); /* 調整縮小比例 */
	}
	a {
    text-decoration: none;
}
	 h1 {
    text-align: center;
    margin-bottom: 10px;
    text-decoration: none;
    margin-left: 860px;
    padding-right: 20px;
		 position: absolute;
		 
    }
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f2f2f2;
    }
    .header {
        background-color: #334144;
        color: white;
        padding: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .container {
        max-width: 600px;
        margin: 20px auto;
        padding: 20px;
        background-color: white;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        border-radius: 5px;
    }
     h3, h4 {
        text-align: center;
        margin-bottom: 10px;
    }
    form {
        text-align: center;
    }
    .file-input {
        font-size: 18px;
    }
    input[type="text"], textarea {
        width: 100%;
        padding: 10px;
        margin: 5px 0;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }
    input[type="number"] {
        width: 100%;
        padding: 10px;
        margin: 5px 0;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }
    input[type="submit"] {
        background-color: #334144;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
    }
    input[type="submit"]:hover {
        background-color: #29353a;
    }
</style>
	<script type="text/javascript">
    // 使用 JavaScript 防止恶意刷新和表单重复提交
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    function validateForm() {
		
        // 檢查商品名稱是否填寫
        var productName = document.forms["form1"]["product_name"].value;
        if (productName == "") {
            alert("商品名稱必須填寫");
            return false;
        }

        // 檢查商品介紹是否填寫
        var description = document.forms["form1"]["description"].value;
        if (description == "") {
            alert("商品介紹必須填寫");
            return false;
        }

        // 檢查商品價格是否填寫
        var price = document.forms["form1"]["price"].value;
        if (price == "") {
            alert("商品價格必須填寫");
            return false;
        }

        // 檢查商品庫存是否填寫
        var stock = document.forms["form1"]["stock"].value;
        if (stock == "") {
            alert("商品庫存必須填寫");
            return false;
        }

        // 檢查是否上傳了圖片
        var fileInput = document.forms["form1"]["file1"];
        if (fileInput.files.length === 0) {
            alert("請上傳圖片");
            return false;
        }
		
		 var fileInput = document.forms["form1"]["file1"];
         var allowedExtensions = /(\.png|\.jpg|\.jpeg|\.bmp)$/i;  // 允許的副檔名
         var fileName = fileInput.value;

         if (!allowedExtensions.exec(fileName)) {
         alert("只能上傳 png、jpg、jpeg、bmp 格式的圖片");
         fileInput.value = ''; // 清空檔案欄位
         return false;
        }

        return true;
    }
</script>
</head>
<body>

<div class="header">
	<div class="back-button">
		<a href="admin_page.php"><img src="img/back_icon.png" alt="返回首頁"></a>
        </div>
    <h1><a href="admin_page.php" style="color: white;">商品上架</a></h1>
    <span></span>
</div>
<div class="container">
    <h3>上傳商品圖片、名稱、介紹、價格和庫存</h3>
   <form id="form1" name="form1" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
        <div class="file-input">
            上傳圖片： <input name='file1' type='file' accept='image/*' class="file-input"/><br><br>
            商品名稱： <input name="product_name" type="text"/><br><br>
            商品介紹： <textarea name="description" rows="4" cols="50"></textarea><br><br>
            商品價格： <input name="price" type="number" step="0.01" min="0"/><br><br>
            商品庫存： <input name="stock" type="number" min="0"/><br><br>
			商品上架日期： <input name="date" type="date" value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>" readonly/><br><br>
        </div>
        <p>
            <input type="submit" value="上架商品" name="go"/>
        </p>
    </form>
</div>
		<?php

// 設定資料庫連線
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shop web";

// 建立連線
$conn = mysqli_connect($servername, $username, $password, $dbname);

// 檢查連線是否成功
if (!$conn) {
    die("資料庫連線失敗: " . mysqli_connect_error());
}
if(!isset($_SESSION['name'])) {
			echo('<script>alert("您還未登入");</script>');
			echo('<script>window.location.href = "index.php";</script>');
		}
// 檢查用戶權限
if (isset($_SESSION['name'])) {
    $user_name = $_SESSION['name'];

    $userLevelQuery = "SELECT level FROM vip WHERE name = '$user_name'";
    $userLevelResult = mysqli_query($conn, $userLevelQuery);

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
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 從表單中獲取數據
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']); // 轉換為浮點數
    $stock = intval($_POST['stock']); // 轉換為整數
    $current_date = mysqli_real_escape_string($conn, $_POST['date']);

      // 處理上傳圖片
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["file1"]["name"]);
    
    if ($_FILES["file1"]["size"] <= 64 * 1024) {
        if (move_uploaded_file($_FILES["file1"]["tmp_name"], $target_file)) {
            $goodsphoto = mysqli_real_escape_string($conn, $target_file);
            
            // 根據庫存狀態設置商品上架狀態
            $updown = ($stock > 0) ? '上架' : '下架';
            
            $sql = "INSERT INTO goods (gdname, content, price, quantity, date, photo, updown) VALUES ('$product_name', '$description', $price, $stock, '$current_date', '$goodsphoto', '$updown')";
            if (mysqli_query($conn, $sql)) {
                echo('<script>alert("商品上架成功");</script>');
            } else {
                echo "錯誤: " . mysqli_error($conn);
            }
        } else {
            echo('<script>alert("上傳圖片失敗");</script>');
        }
    } else {
        echo('<script>alert("圖片大小超過 64KB，請上傳小於 64KB 的圖片。");</script>');
    }
}
?>

</body>
</html>