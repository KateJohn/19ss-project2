<?php
include "php/functions.php";

function detail($str)
{
    try {
        $i = getID();
        $pdo = new PDO(DBCONNSTRING, DBUSER, DBPASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "select * from travelimage where ImageID = $i";
        $result = $pdo->query($sql);
        while ($row = $result->fetch())
            return $row[$str];
        $pdo = null;
    } catch (PDOException $e) {
        die($e->getMessage());
    }
}

function getID()
{
    return isset($_GET['id']) && $_GET['id'] > 0 ? $_GET['id'] : 1;
}

?>
<!DOCTYPE html>
<html lang="ch">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/reset.css">
    <link rel="stylesheet" type="text/css" href="css/header.css">
    <link rel="stylesheet" type="text/css" href="css/body.css">
    <link rel="stylesheet" type="text/css" href="css/details.css">
    <title>样例</title>
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
        <span>个人中心</span>
        <div class="dropdownContent">
            <a href="upload.php" id="AUpload"><img src="../img/system/upload.png" width="20" alt/> 上传</a><br>
            <a href="myPhoto.php" id="AMP"><img src="../img/system/mine.png" width="20" alt/> 我的照⽚</a><br>
            <a href="myLike.php" id="AML"><img src="../img/system/like.png" width="20" alt/> 我的收藏</a><br>
            <a href="login.html" id="ALogin" onclick="deleteCookie('name', '/');deleteCookie('password', '/')">
                <img src="../img/system/login.png" width="20" alt/> 登出
            </a><br>
        </div>
    </div>
</div>
<div id="body">
    <div id="details">
        <p id="detailsTitle" class="title" onclick="alert(1)">样例</p>
        <hr>
        <div id="pictureTitle">
            <?php
            echo "<p id=\"mainTitle\">" . detail("Title") . "</p>";
            echo "<p id=\"smallTitle\">by " . TurnIFromMToNInL(detail("UID"), "UID", "UserName", "traveluser") . "</p>";
            ?>
        </div>
        <div id="mid">
            <div id="detailsPicture">
                <?php
                echo "<img id=\"picture\" src=\"../img/medium/" . detail("PATH") . "\" alt>";
                ?>
            </div>
            <div id="detailsDescription">
                <div id="numberLike">
                    <p id="NLTitle" class="title">喜欢数</p>
                    <hr>
                    <?php
                    echo "<p id=\"NLNumber\">" . favored(getID()) . "</p>";
                    ?>
                </div>
                <div id="someDetails">
                    <p id="SDTitle" class="title">图片细节</p>
                    <hr>
                    <p id="SDContent">内容： 未知</p>
                    <?php
                    echo "<script>alert(" . detail("CountryCodeISO") . ")</script>";
                    echo "<p id=\"SDCountry\">国家： " . TurnIFromMToNInL("'" . detail("CountryCodeISO") . "'", "ISO", "CountryName", "geocountries") . "</p>";
                    echo "<p id=\"SDCountry\">城市： " . TurnIFromMToNInL("'" . detail("CityCode") . "'", "GeoNameID", "AsciiName", "geocities") . "</p>";
                    ?>
                </div>
                <?php
                try {
                    $name = empty($_COOKIE['name']) ? "''" : "'" . $_COOKIE['name'] . "'";
                    $uid = TurnIFromMToNInL($name, 'UserName', 'UID', 'traveluser');
                    $id = getID();
                    $pdo = new PDO(DBCONNSTRING, DBUSER, DBPASS);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $sql = "select count(*) from travelimagefavor where UID = $uid and ImageID = $id";
                    $res = $pdo->query($sql);
                    $row = $res->fetch();
                    $i = $row[0];
                    if ($i == 0) {
                        echo "<form id='FLike' action='php/like.php' method='post'>";
                        echo "<label>";
                        echo "<input class='invisible' name='uid' value='" . $uid . "'>";
                        echo "<input class='invisible' name='id' value='" . getID() . "'>";
                        echo "</label>";
                        echo "<button id='like' type='submit'>收藏</button>";
                        echo "</form>";
                    } else
                        echo "<button id='alreadyLiked' disabled>已收藏</button>";
                    $pdo = null;
                } catch
                (PDOException $e) {
                    die($e->getMessage());
                }
                ?>
            </div>
        </div>
        <div id="detailsIntroduction">
            <?php
            echo "<p id=\"DIWords\">" . detail("Description") . "</p>";
            ?>
        </div>
    </div>
</div>
<div id="foot">
    <p>© 2020－2021 index.html, no rights reserved 复旦大学</p>
</div>
</body>
</html>