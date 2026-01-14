<!DOCTYPE html>
<html>
	
<head>
    <meta charset="utf-8">
    <title>宥來購購物網站</title>
	<script src="https://code.jquery.com/jquery-3.4.1.min.js">
	</script>
	
    <style>
		.header h1 a {
    text-decoration: none;
    color: white;
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
            left: 50%;
            transform: translate(-50%, -50%);
            width: 300px;
            padding: 20px;
            background-color: white;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .toggle-button {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        .toggle-button button {
            width: 48%;
        }
        .toggle-button .right-button {
    text-align: right;
    margin-left: 230px;
        }
		.sure{
    margin-right: 32px;
		}
		.Yes{
			border:#3f0 5px solid;
			color:#3F0;
		}
		.No{
			border:#F00 5px solid;
			color: #F00;
		}
    </style>
</head>
<body>
    <div class="header">
        <h1><a href="index.php">宥來購</a></h1>
		 <?php
    session_start();
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "shop web";
    $db_link = mysqli_connect($host, $username, $password, $database);

    $message = '';

    if (isset($_POST["login"])) {
        if (isset($_SESSION['name'])) {
            $jmessage = $_SESSION['name'] . "已經登入了 您無法登入";
            echo '<script>alert("' . $jmessage . '");
            window.location.href = "shop log and create.php";
            </script>';
        } else {
            $username = $_POST["username"];
            $password1 = $_POST["password"];

            $sql = "SELECT * FROM vip WHERE account = '$username' AND password='$password1'";
            $result = mysqli_query($db_link, $sql);

            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);

                if ($row['status'] == 'on') {
                    $_SESSION['name'] = $row['name'];
                    $userLevel = $row['level'];

                    if ($userLevel === '2') {
                        // 是管理者，執行管理者相關操作
                        // 跳轉到管理者頁面
                        header("Location: admin_page.php");
                    } else {
                        // 不是管理者，跳轉到一般使用者頁面
                        header("Location: index.php");
                    }
                    exit();
                } else {
                    $message = "您的帳號已被禁用";
                    echo '<script>alert("' . $message . '");</script>';
                }
            } else {
                $message = "登入失敗";
                echo '<script>alert("' . $message . '");</script>';
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
        $sql = "INSERT INTO vip (name, account, password, status, level) VALUES ('$name', '$username', '$password1', 'on', '1')";
        if (mysqli_query($db_link, $sql)) {
            echo "帳號註冊成功!";
        } else {
            echo "錯誤: " . mysqli_error($db_link);
        }
    }
}
        ?>
    </div>
    <div class="container">
        <?php
        if (!isset($_POST['register1'])) {?>
            <h1>帳號登入</h1>
            <form action="" method="post">
            帳號：<input id="abc" type="text" name="username"  required><br><br>
            密碼：<input type="password" name="password"  required><br><br>
            <input type="submit" name="login" value="登入">
            </form>
            <div class="toggle-button">
            <form method="post">
            還沒有帳號嗎?<input type="submit" name="register1" value="註冊帳號" class="right-button">
            </form>
		    </div>
       <?php } else {?>
             <h1>註冊新帳號</h1>
             <form method="POST" action="" onsubmit="return checkFiles()">
             名稱：<input id="name"type="text" name="name"  required><br><br>
             帳號：<input  id="account"type="text" name="username"pattern="^[a-zA-Z0-9]+$" required><br><br>
             密碼：<input id="password"type="password" name="password" pattern="^[a-zA-Z0-9]+$" required><br><br>
             確認密碼：<input id="checkpassword"type="password" name="check" class="sure" pattern="^[a-zA-Z0-9]+$" required><br><br>
             <div class="button-container">
             <button type="submit" name="register">註冊</button>
             </div>
             </form>
             <div class="toggle-button">
             <form method="post">
             <input type="submit" name="login1" value="返回登入" class="right-button">
             </form>
             </div>
      <?php  }
        ?>
    </div>
    <!-- 表單驗證的 JavaScript -->
	 <script>
        $(document).ready(function() {
            var count = 0;
            $('#abc').on("keyup", function() {
                var target_data = $(this).val();
                console.log(count, "target:", target_data);
                $.ajax({
                    url: 'JudgeProgram.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {data: target_data}
                }).done(function(data) {
                   if (data === "" || data === null) {
                   $('#abc').removeClass("No").removeClass("Yes");
                   return; 
                   }
                   if(data === true) {
                   $("#abc").removeClass("No").addClass("Yes");
                   } else {
                   $("#abc").removeClass("Yes").addClass("No");
                    }
                    console.log(count, "data:", data);
                }).fail(function(error) {
                    console.log(count, "error", error);
                });
            });
        });
		 
    </script>
	<script>
$(document).ready(function() {
	var count = 0;
    // 當用戶在帳號輸入字段中鍵入時觸發
    $('#account').on("keyup", function() {
        var target_data = $(this).val();
		console.log(count, "target:", target_data);
        // 發送AJAX請求到create-back.php
        $.ajax({
            url: 'create-back.php',
            type: 'POST',
            dataType: 'json',
            data: {account: target_data} // 發送用戶輸入的帳號
        }).done(function(data) {
            // 根據返回的數據調整輸入框的樣式
            if (data === "" || data === null) {
                $('#account').removeClass("No").removeClass("Yes");
                return; 
            }
            if(data === true) {
                $("#account").removeClass("No").addClass("Yes"); 
            } else {
                $("#account").removeClass("Yes").addClass("No"); 
            }
			console.log(count, "data:", data);
        }).fail(function(error) {
            console.log("error", error);
        });
    });
});
		</script>
	<script>
$(document).ready(function() {
	var count = 0;
    // 名稱驗證
    $('#name').on("keyup", function() {
        var target_data = $(this).val();
		console.log(count, "target:", target_data);
        // 發送AJAX請求到create-back.php
        $.ajax({
            url: 'validate.php',
            type: 'POST',
            dataType: 'json',
            data: {name: target_data} // 發送用戶輸入的帳號
        }).done(function(data) {
            // 根據返回的數據調整輸入框的樣式
            if (data === "" || data === null) {
                $('#name').removeClass("No").removeClass("Yes");
                return; 
            }
            if(data === true) {
                $("#name").removeClass("No").addClass("Yes"); 
            } else {
                $("#name").removeClass("Yes").addClass("No"); 
            }
			console.log(count, "data:", data);
        }).fail(function(error) {
            console.log("error", error);
        });
    });
});
		</script>
<script>
$(document).ready(function() {
    // 密碼和確認密碼驗證
    $('#password, #checkpassword').on("keyup", function() {
        var password = $('#password').val();
        var checkPassword = $('#checkpassword').val();
        $.ajax({
            url: 'validate-password.php',
            type: 'post',
            dataType: 'json',
            data: { password: password, checkPassword: checkPassword },
            success: function(response) {
                if(response.valid) {
                    $('#password, #checkpassword').addClass("Yes").removeClass("No");
                } else {
                    $('#password, #checkpassword').addClass("No").removeClass("Yes");
                }
            }
        });
    });
});
</script>
   <script>
    function checkFiles() {
        var name = document.getElementsByName("name")[0].value;
        var account = document.getElementsByName("username")[0].value;
        var password = document.getElementsByName("password")[0].value;
        var check = document.getElementsByName("check")[0].value;
        var chinesePattern = /[\u4E00-\u9FA5]/;

        if (chinesePattern.test(account) || chinesePattern.test(password) || chinesePattern.test(check)) {
            alert("不可輸入中文喔！");
            return false; // 禁止表單送出
        }

        if (name === "" || account === "" || password === "" || check === "") {
            alert("都要輸入喔");
            return false; // 禁止表單送出
        }

        if (name.length < 2 || name.length > 8) {
            alert("名稱長度必須在2到8個字之間");
            return false; // 禁止表單送出
        }

        if (account.length < 8 || account.length > 20) {
            alert("帳號長度必須在8到20個字之間");
            return false; // 禁止表單送出
        }

        if (password.length < 8 || password.length > 20) {
            alert("密碼長度必須在8到20個字之間");
            return false; // 禁止表單送出
        }

        if (account === password) {
            alert("帳號跟密碼不得相符");
            return false; // 禁止表單送出
        }

        if (password !== check) {
            alert("密碼跟確認密碼不相符");
            return false; // 禁止表單送出
        }
        return true; // 允許表單送出
    }   
</script>
	
</body>
</html>