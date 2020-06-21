<!DOCTYPE html>
<?php
include "php/functions.php";
$name = empty($_COOKIE["name"]) ? '' : $_COOKIE["name"];
$page = isset($_GET['page']) && $_GET['page'] > 1 ? $_GET['page'] : 1;
?>
<html lang="ch">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/reset.css">
    <link rel="stylesheet" type="text/css" href="css/header.css">
    <link rel="stylesheet" type="text/css" href="css/body.css">
    <link rel="stylesheet" type="text/css" href="css/myPL.css">
    <title>我的照片</title>
</head>
<body>
<script src="js/log.js"></script>
<div id="header">
    <div id="logo">
        <img src="../img/system/logo.jpg" alt width="35" height="35">
    </div>
    <div id="navItems">
        <a href="../index.html" id="AIndex">首页</a>
        <a href="browser.php" id="ABrowse">浏览</a>
        <a href="search.php" id="ASearch">搜索</a>
    </div>
    <div class="dropdown">
        <span class="select">个人中心</span>
        <div class="dropdownContent">
            <a href="upload.php" id="AUpload"><img src="../img/system/upload.png" width="20" alt/> 上传</a><br>
            <a href="myPhoto.php" id="AMP" class="select"><img src="../img/system/mine.png" width="20" alt/> 我的照⽚</a><br>
            <a href="myLike.php" id="AML"><img src="../img/system/like.png" width="20" alt/> 我的收藏</a><br>
            <a href="login.html" id="ALogin" onclick="deleteCookie('name', '/');deleteCookie('password', '/')">
                <img src="../img/system/login.png" width="20" alt/> 登出
            </a><br>
        </div>
    </div>
</div>
<div id="body">
    <div id="myPhoto">
        <p id="MPTitle" class="title">我的照片</p>
        <hr>
        <div id="pictures">
            <?php
            if ($name == '') {
                echo "<a href='login.html' class='tc'>请先登录</a><br><br><br><hr>";
                cnxk();
            } else
                try {
                    $uid = TurnIFromMToNInL("'" . $name . "'", 'UserName', 'UID', 'traveluser');
                    $pdo = new PDO(DBCONNSTRING, DBUSER, DBPASS);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $sql = "select * from travelimage where UID = $uid";
                    $result = $pdo->query($sql);
                    $k = 0;
                    $i = 0;
                    $sql_ = "select count(UID) from travelimage where UID = $uid";
                    $result_ = $pdo->query($sql_);
                    $rows_ = $result_->fetch();
                    $rowCount = $rows_[0];
                    $pages = ceil($rowCount / 5);
                    while ($row = $result->fetch()) {
                        if ($k < $page * 5 - 5) {
                            $k++;
                            continue;
                        }
                        $i++;
                        if ($i > 5) break;
                        echo "<div id='picture" . $i . "' class='picture'>";
                        echo "<div id='img" . $i . "' class='img'>";
                        echo "<img src='../img/square-medium/" . $row['PATH'] . "' alt onclick='document.getElementById(\"title" . $i . "\").click()'>";
                        echo "</div>";
                        echo "<div id='tc" . $i . "' class='tc'>";
                        echo "<a id='title" . $i . "' class='title' href='details.php?id=" . $row['ImageID'] . "'>" . TurnIFromMToNInL($row['ImageID'], 'ImageID', 'Title', 'travelimage') . "</a>";
                        echo "<p id='content" . $i . "' class='content'>" . $row['Description'] . "</p>";
                        echo "<form class='buttons' id='DButtons" . $i . "' method='post' action='php/DMP.php'>";
                        echo "<input id='input" . $i . "' name='id' class='input' value='" . $row['ImageID'] . "'>";
                        echo "<input id='input" . $i . "' name='uid' class='input' value='" . $uid . "'>";
                        echo "<br><button class='delete' id='DE" . $i . "'>删除</button>";
                        echo "</form>";
                        echo "<form class='buttons' id='MButtons" . $i . "' method='post' action='upload.php'>";
                        echo "<input id='input" . $i . "' name='id' class='input' value='" . $row['ImageID'] . "'>";
                        echo "<br><button class='modify' id='MY" . $i . "'>修改</button>";
                        echo "</form>";
                        echo "</div>";
                        echo "</div>";
                        echo "<hr>";
                    }
                    if ($i == 0) {
                        echo "<p class='tc'>你还没有上传照片呢</p><br><br><br><hr>";
                        cnxk();
                    } else {
                        echo "<div id='pages'>";
                        $pre = ($page - 1 < 1) ? 1 : $page - 1;
                        $nex = ($page + 1 > $pages) ? $pages : $page + 1;
                        echo '<a href="?page=' . $pre . '"><</a>';
                        for ($j = 1; $j <= $pages; $j++)
                            if ($j == $page)
                                echo '<a href="?page=' . $j . '" class="selected">' . $j . '</a>';
                            else
                                echo '<a href="?page=' . $j . '">' . $j . '</a>';
                        echo '<a href="?page=' . $nex . '">></a>';
                        echo "</div>";
                    }
                    $pdo = null;
                } catch (PDOException $e) {
                    die($e->getMessage());
                }
            ?>
        </div>
    </div>
</div>
<div id="foot">
    <p>© 2020－2021 index.html, no rights reserved 复旦大学</p>
</div>
</body>
</html>
