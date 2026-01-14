<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>宥來購購物網站 - 個人檔案</title>
    <style>
		.container p strong {
    width: 100px; /* 調整標籤的寬度 */
    padding-right: 10px; /* 加上右側間距 */
    white-space: nowrap; /* 不換行 */
}
		  .personal-info {
    float: right;
    width: 40%;
    padding: 20px;
    background-color: #f4f4f4;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    margin-left: 30px;
}

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
        }
        .container {
            position: absolute;
            top: 50%;
            left: 31%;
            transform: translate(-50%, -50%);
            width: 500px;
            padding: 20px;
            background-color: white;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }
        .container h1 {
            margin-top: 0;
        }
        .container p {
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }
        .container strong {
            width: 100px; /* 調整標籤的寬度 */
            padding-right: 10px; /* 加上右側間距 */
        }
        .container textarea, .container input[type="date"], .container input[type="password"], .container input[type="radio"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        .container input[type="radio"] {
            margin-right: 5px;
        }
        .container input[type="submit"] {
            background-color: #334144;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 3px;
            cursor: pointer;
        }
        .container input[type="submit"]:hover {
            background-color: #293537;
        }
        .container a {
            text-decoration: none;
            color: #334144;
		}
		 
		.logo img {
            width: 80px;
            height: auto; 
            margin-left: 10px;
        }
		.logo {
            display: flex;
            align-items: center;
        }
		.container form{
		text-align: left;
		}
		.container p input[type="submit"] {
    margin: 0 auto; /* 將按鈕水平置中 */
}
		  .back-button {
            float: left;
            margin-right: 20px;
            transform: scale(0.5); /* 调整图片大小 */
        }
		
    </style>
	 <script>
        function validateAddress() {
            var address = document.getElementsByName("address")[0].value;

            // 使用正則表達式檢查地址格式
            var addressPattern = /^([\u4e00-\u9fa5]+)[市縣]([\u4e00-\u9fa5]+)[區市]([\u4e00-\u9fa5]+)[路街巷道]([0-9]+)[號館]([1-9]+)[樓]$/u;

            if (!addressPattern.test(address)) {
                alert("地址格式要有[市縣],[區],[路街巷道],[號館],[樓],請檢查並重新輸入。");
                return false; // 阻止表單提交
            }
            return true; // 地址格式正確，允許表單提交
        }
    </script>
</head>
<body>
    <div class="header">
       <h1 class="logo">
		   宥來購 
    <a href="index.php">
        <img src="img/logo.png" alt="宥來購商標">
    </a>
</h1>
        <?php
        session_start();
        $host = "localhost";
        $username = "root";
        $password = "";
        $database = "shop web"; // 資料庫名稱
        $db_link = mysqli_connect($host, $username, $password, $database);

        $message = '';
            if (isset($_POST["login"])) {
				if (isset($_SESSION['name'])) {
        $jmessage = $_SESSION['name'] . "已經登入了 您無法登入";
        echo '<script>alert("' . $jmessage . '");
        window.location.href = "shop log and create.php";
        </script>';
    } 
		
   else {
        if (isset($_POST["login"])) {
            $username = $_POST["username"];
            $password1 = $_POST["password"];
			

             $sql = "SELECT * FROM vip WHERE account = '$username' AND password='$password1'";
                $result = mysqli_query($db_link, $sql);

                if (mysqli_num_rows($result) == 1) {
                    $row = mysqli_fetch_assoc($result);
                    $_SESSION['name'] = $row['name'];
                    $isAdmin = strpos($row['account'], '@'); // 檢查是否為管理者
                    if ($isAdmin !== false) {
                        // 是管理者，執行管理者相關操作
                        // 例如：跳轉到管理者頁面
                        header("Location: admin_page.php");
                    } else {
                        // 不是管理者，跳轉到一般使用者頁面
                        header("Location: index.php");
                    }
                    exit();
                } else {
                    $message = "登入失敗";
                    echo '<script>alert("' . $message . '");</script>';
                }
            }
    }
  }
	
			
			if (isset($_POST["register"])) {
                
             $name = $_POST["name"];
             $username = $_POST["username"];
             $password1 = $_POST["password"];

        // 檢查帳號是否已存在
        $query = "SELECT * FROM vip WHERE account='$username'";
        $result = mysqli_query($db_link, $query);
        if (mysqli_num_rows($result) > 0) {
            echo "該帳號已經存在，請使用其他帳號註冊。";
        } else {
            // 若帳號不存在，則插入新的帳號資料，傳遞已加密的密碼
            $sql = "INSERT INTO vip (name, account, password) VALUES ('$name', '$username', '$password1')";
            if (mysqli_query($db_link, $sql)) {
                echo "帳號註冊成功!";
            } else {
                echo "錯誤: " . mysqli_error($db_link);
            }
		}
			}
        ?>

    </div>
	 <div class="back-button">
            <a href="index.php"><img src="img/back_icon.png" alt="返回首頁"></a>
        </div>
     <div class="container">
		
         <?php
if (!isset($_SESSION['name'])) {
    // 未登入，顯示登入表單
    echo('<script>alert("請先登入帳號");</script>');
    echo('<script>window.location.href = "index.php";</script>');
} else {
    // 已登入，顯示個人資料
    $username = $_SESSION['name'];

    $sql = "SELECT * FROM vip WHERE name = '$username'";
    $result = mysqli_query($db_link, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // 檢查用戶狀態是否為 "off"
        $userStatus = $row["status"];

        if ($userStatus === 'off') {
            // 用戶狀態為 'off'，顯示相應的訊息
			echo('<script>alert("您已被列為黑名單，無法查看會員資料");</script>');
           echo('<script>window.location.href = "index.php";</script>');
        } else {
            
        }
    } else {
        echo "找不到使用者資訊。";
    }
}
?>

<h1>個人檔案</h1>

<form method="post" action="" onsubmit="return validatePasswordMatch() || validateAddress()">
	 <p>
        <strong>帳號：</strong><?php echo $row["account"]; ?>
    </p>
   <p>
    <strong>密碼：</strong>
    <input type="password" name="password" pattern="^[a-zA-Z0-9]+$" required value="<?php echo $row["password"]; ?>">
</p>
<p>
    <strong>確認密碼：</strong>
    <input type="password" name="check" class="sure" pattern="^[a-zA-Z0-9]+$" required cols="60">
</p>
     <p>
    <strong>生日：</strong>
    <input type="date" name="birthday" max="<?php echo date('Y-m-d', strtotime('-12 years')); ?>">
</p>

    <p>
        <strong>地址：</strong>
        <textarea name="address" rows="4" cols="50" pattern="^([\u4e00-\u9fa5]*)市([\u4e00-\u9fa5]*)區([\u4e00-\u9fa5]*)路([0-9]*)號([1-9]*)樓$" required><?php echo $row["address"]; ?></textarea>
    </p>

    <p>
        <strong>電話：</strong>
        <textarea name="phone" rows="1" cols="50" oninput="validatePhoneInput(this)" required><?php echo $row["phone"]; ?></textarea>
    </p>
    
    <p><input type="submit" name="save" value="儲存"></p>
</form>
		 <script>
function validatePasswordMatch() {
    var password = document.getElementsByName("password")[0].value;
    var confirmPassword = document.getElementsByName("check")[0].value;

    if (password !== confirmPassword) {
        document.getElementsByName("check")[0].setCustomValidity("確認密碼必須和密碼相符");
        return false; // 密码不匹配，阻止表单提交
    } else {
        document.getElementsByName("check")[0].setCustomValidity("");
        return true; // 密码匹配，允许表单提交
    }
}
</script>
	<?php
	if (isset($_POST["save"])) {
    // 檢查必填欄位是否都有填寫
    $requiredFields = array("password", "birthday", "address", "phone");
    $missingFields = array();

    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $missingFields[] = $field;
        }
    }

    if (!empty($missingFields)) {
        echo "請完成以下欄位： " . implode(", ", $missingFields);
    } else {
        // 驗證通過，執行更新操作
        $birthday = $_POST["birthday"];
        $address = $_POST["address"];
        $phone = $_POST["phone"];
		$password1 =$_POST["password"];

        // 更新資料表中的對應欄位
        $updateSql = "UPDATE vip SET password='$password1' , birthday='$birthday', address='$address', phone='$phone' WHERE name='$username'";
        if (mysqli_query($db_link, $updateSql)) {
            echo('<script>alert("資料已成功更新");</script>');
			echo('<script>window.location.href = "vip.php";</script>');
        } else {
            echo "更新資料時發生錯誤: " . mysqli_error($db_link);
        }
    }
	}
		?>
		 <script>
    function validatePhoneInput(input) {
        var phonePattern = /^(09\d{8})$/;
        var phoneValue = input.value.trim();

        if (!phonePattern.test(phoneValue)) {
            input.setCustomValidity("請輸入有效的手機號碼 (開頭為09，總共10位數字)");
        } else {
            input.setCustomValidity("");
        }
    }
</script>
    </div>
	   <!-- 顯示个人资料 -->
    <div class="personal-info">
        <h2>個人資料</h2>
        <p><strong>帳號：</strong><?php echo $row["account"]; ?></p>
        <p>
        <strong>密碼：</strong>
        <span><?php echo str_repeat("*", strlen($row["password"])); ?></span>
        </p>
        <p><strong>生日：</strong><?php echo $row["birthday"]; ?></p>
        <p><strong>地址：</strong><?php echo $row["address"]; ?></p>
        <p><strong>電話：</strong><?php echo $row["phone"]; ?></p>
    </div>
</body>
</html>