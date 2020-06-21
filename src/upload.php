<?php
require_once('php/config.php');
include "php/functions.php";
$name = empty($_COOKIE["name"]) ? '' : $_COOKIE["name"];
$id = empty($_POST['id']) ? '' : $_POST['id'];
if (!empty($_POST['id']))
    try {
        $pdo = new PDO(DBCONNSTRING, DBUSER, DBPASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "select * from travelimage where ImageID = $id";
        $result = $pdo->query($sql);
        while ($row = $result->fetch()) {
            $path = $row['PATH'];
            $de = $row['Description'];
            $title = $row['Title'];
            $city = TurnIFromMToNInL("'" . $row['CityCode'] . "'", "GeoNameID", "AsciiName", "geocities");
            $country = TurnIFromMToNInL("'" . $row['CountryCodeISO'] . "'", "ISO", "CountryName", "geocountries");
        }
        $pdo = null;
    } catch (PDOException $e) {
        die($e->getMessage());
    }
?>
<!DOCTYPE html>
<html lang="ch">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/reset.css">
    <link rel="stylesheet" type="text/css" href="css/header.css">
    <link rel="stylesheet" type="text/css" href="css/body.css">
    <link rel="stylesheet" type="text/css" href="css/upload.css">
    <title>上传</title>
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
            <a href="upload.php" id="AUpload" class="select"><img src="../img/system/upload.png" width="20" alt/> 上传</a><br>
            <a href="myPhoto.php" id="AMP"><img src="../img/system/mine.png" width="20" alt/> 我的照⽚</a><br>
            <a href="myLike.php" id="AML"><img src="../img/system/like.png" width="20" alt/> 我的收藏</a><br>
            <a href="login.html" id="ALogin" onclick="deleteCookie('name', '/');deleteCookie('password', '/')">
                <img src="../img/system/login.png" width="20" alt/> 登出
            </a><br>
        </div>
    </div>
</div>
<div id="body">
    <div id="upload">
        <?php
        if (empty($_POST['id'])) {
            echo '<form action="php/upload.php" id="uploadForm" method="post" enctype="multipart/form-data">';
            echo '<p id="uploadTitle" class="title">上传</p>';
        } else {
            echo '<form action="php/modify.php" id="uploadForm" method="post" enctype="multipart/form-data">';
            echo '<p id="uploadTitle" class="title">上传</p>';
            echo '<input id="id" type="number" value="' . $id . '" name="id">';
        }
        ?>
        <hr>
        <div id="uploadBody">
            <?php
            if (empty($_POST['id'])) {
                echo '<input name="img" id="input" type="file" accept="image/jepg,image/png" required><br>';
                echo '<img id="img" src="../img/system/notUploaded.png" alt="Image preview area..." title="preview-img"><br>';
            } else {
                echo '<input name="img" id="input" type="file" accept="image/jepg,image/png"><br>';
                echo '<img id="img" src="../img/medium/' . $path . '" alt="Image preview area..." title="preview-img"><br>';
            }
            ?>
            <input id="UPButton" type="button" onclick="document.getElementById('input').click()" value="浏览">
            <script>
                var fileInput = document.getElementById('input'),
                    previewImg = document.getElementById('img');
                fileInput.addEventListener('change', function () {
                    var file = this.files[0];
                    var reader = new FileReader();
                    reader.addEventListener("load", function () {
                        previewImg.src = reader.result;
                    }, false);
                    reader.readAsDataURL(file);
                }, false);
            </script>
        </div>
        <hr>
        <div id="uploadDescription">
            <p>图片标题</p>
            <label>
                <?php
                if (empty($_POST['id']))
                    echo '<input id="UPTitle" type="text" required name="title" class="UP">';
                else
                    echo '<input id="UPTitle" type="text" required name="title" class="UP" value="' . $title . '">';
                ?>
            </label>
            <p>图片描述</p>
            <label>
                <?php
                if (empty($_POST['id']))
                    echo '<textarea name="description" rows="" cols="" id="UPDescription" contenteditable="true" class="UP"></textarea>';
                else
                    echo '<textarea name="description" rows="" cols="" id="UPDescription" contenteditable="true" class="UP">' . $de . '</textarea>';
                ?>
            </label>
            <p>拍摄国家</p>
            <label>
                <?php
                if (empty($_POST['id']))
                    echo '<input id="UPCountry" type="text" required name="country" class="UP">';
                else
                    echo '<input id="UPCountry" type="text" required name="country" class="UP" value="' . $country . '">';
                ?>
            </label>
            <p>拍摄城市</p>
            <label>
                <?php
                if (empty($_POST['id']))
                    echo '<input id="UPCity" type="text" required name="city" class="UP">';
                else
                    echo '<input id="UPCity" type="text" required name="city" class="UP" value="' . $city . '">';
                ?>
            </label>
            <p>主题:</p>
            <label id="select">
                <select name="type" id="type">
                    <option id="scenery" value="scenery">自然</option>
                    <option id="city" value="city">城市</option>
                    <option id="animal" value="animal">动物</option>
                    <option id="building" value="building">建筑</option>
                    <option id="people" value="people">人物</option>
                    <option id="wonder" value="wonder">奇观</option>
                    <option id="other" value="other">其他</option>
                </select>
            </label>
        </div>
        <div id="submit">
            <?php
            if (empty($_POST['id']))
                echo '<button id="submitButton" type="submit" onclick="if (document.getElementById(\'input\').files.length == 0) alert(\'图片未上传\')">提交</button>';
            else
                echo '<button id="submitButton" type="submit">修改</button>';
            ?>
        </div>
        <?php
        echo '</form>'
        ?>
    </div>
</div>
<div id="foot">
    <p>© 2020－2021 index.html, no rights reserved 复旦大学</p>
</div>
</body>
</html>
