<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>會員管理</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }
    h1 {
        background-color: #334144;
        color: white;
        padding: 10px;
        margin: 0;
        text-align: center;
    }
    table {
        border-collapse: collapse;
        width: 80%;
        margin: 20px auto;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }
    th, td {
        padding: 10px;
        text-align: center;
    }
    th {
        background-color: #334144;
        color: white;
    }
    tr:nth-child(even) {
        background-color: #f2f2f2;
    }
    a {
        text-decoration: none;
        color: #3498db;
    }
    a:hover {
        text-decoration: underline;
    }
    h1 a {
        text-decoration: none;
        color: white;
    }
    h1 a:hover {
        text-decoration: none;
        color: white;
    }
    .back-button {
        position: absolute;
        top: 40px;
        left: 10px;
        transform: scale(0.5); /* 調整縮小比例 */
    }
</style>
</head>
<body>
<div class="back-button">
    <a href="admin_page.php"><img src="img/back_icon.png" alt="返回首頁"></a>
</div>
<h1><a href="admin_page.php">會員信息</a></h1>
<?php
session_start();
$host = "localhost";
$username = "root";
$password = "";
$database = "shop web";
$db_link = mysqli_connect($host, $username, $password, $database);

if (!$db_link) {
    die("資料庫連接失敗: " . mysqli_connect_error());
}

if (!isset($_SESSION['name'])) {
    echo('<script>alert("請先登入帳號");</script>');
    header("Location: index.php");
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

    $yourAccount = $_SESSION['name'];

    if (isset($_GET['action'])) {
        $action = $_GET['action'];
        $account = $_GET['account'];

        $levelQuery = "SELECT level, status FROM vip WHERE account = '$account'";
        $levelResult = mysqli_query($db_link, $levelQuery);
        $userData = mysqli_fetch_assoc($levelResult);

        $userLevel = $userData['level'];
        $userStatus = $userData['status'];

        if ($action === 'disable' && $userLevel != '2') {
            if ($userStatus == 'on') {
                $updateQuery = "UPDATE vip SET status = 'off' WHERE account = '$account'";
                mysqli_query($db_link, $updateQuery);
            } else {
                echo('<script>alert("該用戶已經被禁用");</script>');
            }
        } elseif ($action === 'enable') {
            if ($userStatus == 'off') {
                $updateQuery = "UPDATE vip SET status = 'on' WHERE account = '$account'";
                mysqli_query($db_link, $updateQuery);
            } else {
                echo('<script>alert("該用戶已經啟用");</script>');
            }
        } elseif ($action === 'upgrade' && $userStatus == 'on') {
            // 只有在帳戶啟用的情況下才能升級
            $updateQuery = "UPDATE vip SET level = '2' WHERE account = '$account'";
            mysqli_query($db_link, $updateQuery);
        } elseif ($action === 'downgrade' && $userStatus == 'on') {
            // 只有在帳戶啟用的情況下才能降級
            $updateQuery = "UPDATE vip SET level = '1' WHERE account = '$account'";
            mysqli_query($db_link, $updateQuery);
        } else {
            echo('<script>alert("管理員無法禁用管理員");</script>');
        }
    }

    $query = "SELECT * FROM vip WHERE name <> '$yourAccount'";
    $result = mysqli_query($db_link, $query);

    echo "<table border='1'>";
    echo "<tr><th>帳號</th><th>姓名</th><th>性別</th><th>生日</th><th>地址</th><th>電話</th><th>等級</th><th>狀態</th><th>操作</th></tr>";

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['account'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['gender'] . "</td>";
        echo "<td>" . $row['birthday'] . "</td>";
        echo "<td>" . $row['address'] . "</td>";
        echo "<td>" . $row['phone'] . "</td>";
        echo "<td>" . ($row['level'] == '1' ? '使用者' : '管理員') . "</td>";
        echo "<td>" . $row['status'] . "</td>";

        echo "<td>";
        if ($row['status'] == 'on') {
            echo "<a href='vip_management.php?account=" . $row['account'] . "&action=disable'>禁用</a>";
        } else {
            echo "<a href='vip_management.php?account=" . $row['account'] . "&action=enable'>恢復</a>";
        }

        if ($row['level'] == '1' && $row['status'] == 'on') {
            echo " | <a href='vip_management.php?account=" . $row['account'] . "&action=upgrade'>升級</a>";
        } elseif ($row['level'] == '2' && $row['status'] == 'on') {
            echo " | <a href='vip_management.php?account=" . $row['account'] . "&action=downgrade'>降級</a>";
        }
        echo "</td>";

        echo "</tr>";
    }

    echo "</table>";
}
?>

<?php
if (isset($_GET['account'])) {
    $account = $_GET['account'];
    $query = "SELECT * FROM vip WHERE account = '$account'";
    $result = mysqli_query($db_link, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        if ($row['status'] == 'off') {
            
            echo('<script>window.location.href = "vip_management.php";</script>');
        }
    }
}
?>
</body>
</html>
