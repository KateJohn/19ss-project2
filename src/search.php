<!DOCTYPE html>
<html lang="ch">
<?php
include 'php/functions.php';
$ts = empty($_GET['TS']) ? '' : $_GET['TS'];
$ds = empty($_GET['DS']) ? '' : $_GET['DS'];
$page = isset($_GET['page']) && $_GET['page'] > 1 ? $_GET['page'] : 1;
$arrP = array();
$arrID = array();
$arrD = array();
$arrT = array();
$url = '';
$pdo = new PDO(DBCONNSTRING, DBUSER, DBPASS);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
if (!empty($_GET['TS'])) {
    $pat = '"%' . $ts . '%"';
    $sql = "select * from travelimage where Title like $pat";
    $result = $pdo->query($sql);
    $url .= '&TS=' . $ts;
    while ($row = $result->fetch()) {
        $arrP[] = $row['PATH'];
        $arrID[] = $row['ImageID'];
        $arrD[] = $row['Description'];
        $arrT[] = $row['Title'];
    }
    $pdo = null;
}
if (!empty($_GET['DS'])) {
    $pat = '"%' . $ds . '%"';
    $sql = "select * from travelimage where Description like $pat";
    $result = $pdo->query($sql);
    $url .= '&DS=' . $ds;
    while ($row = $result->fetch()) {
        $arrP[] = $row['PATH'];
        $arrID[] = $row['ImageID'];
        $arrD[] = $row['Description'];
        $arrT[] = $row['Title'];
    }
    $pdo = null;
}
if ($url == '')
    for ($i = 1; $i <= 25; $i++) {
        $arrID[] = $i;
        $arrP[] = TurnIFromMToNInL($i, 'ImageID', 'PATH', 'travelimage');
        $arrD[] = TurnIFromMToNInL($i, 'ImageID', 'Description', 'travelimage');
        $arrT[] = TurnIFromMToNInL($i, 'ImageID', 'Title', 'travelimage');
    }
$count = count($arrID);
?>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/reset.css">
    <link rel="stylesheet" type="text/css" href="css/header.css">
    <link rel="stylesheet" type="text/css" href="css/body.css">
    <link rel="stylesheet" type="text/css" href="css/search.css">
    <title>搜索</title>
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
        <a href="search.php" id="ASearch" class="select">搜索</a>
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
    <div id="search">
        <form id="searchContent" method="get" action="search.php">
            <p id="searchTitle" class="title">搜索</p>
            <hr>
            <div id="formContent">
                <label>
                    <input id="methodTitle" checked name="method" type="radio" value="title"
                           onclick="document.getElementById('titleSearch').removeAttribute('disabled');document.getElementById('descriptionSearch').setAttribute('disabled','');document.getElementById('descriptionSearch').value=''">搜索标题
                </label>
                <br>
                <label>
                    <input name="TS" type="text" id="titleSearch" class="searching">
                </label>
                <br>
                <label>
                    <input id="methodDescription" name="method" type="radio" value="description"
                           onclick="document.getElementById('titleSearch').setAttribute('disabled','');document.getElementById('descriptionSearch').removeAttribute('disabled');document.getElementById('titleSearch').value=''">搜索内容
                </label>
                <br>
                <label>
                    <input name="DS" type="text" disabled id="descriptionSearch" class="searching">
                </label>
                <br>
                <button type="submit">搜索</button>
            </div>
        </form>
    </div>
    <div id="result">
        <?php
        if ($url == '')
            echo '<p id="resultTitle" class="title">搜索结果</p><hr>';
        else if ($count != 0) {
            $from = $page * 5 - 4;
            $to = min($page * 5, $count);
            echo '<p id="resultTitle" class="title">' . substr($url, 1) . '的' . $count . '个搜索结果(' . $from . '~' . $to . ')</p><hr>';
        } else {
            echo '<p id="resultTitle" class="title">无搜索结果</p><hr>';
            echo '<p id="resultTitle" class="title">更多</p>';
            for ($i = 1; $i <= 25; $i++) {
                $arrID[] = $i;
                $arrP[] = TurnIFromMToNInL($i, 'ImageID', 'PATH', 'travelimage');
                $arrD[] = TurnIFromMToNInL($i, 'ImageID', 'Description', 'travelimage');
                $arrT[] = TurnIFromMToNInL($i, 'ImageID', 'Title', 'travelimage');
            }
            $count = count($arrID);
        }
        ?>
        <div id="pictures">
            <?php
            for ($i = $page * 5 - 5; $i < $page * 5 & $i < count($arrP); $i++) {
                $j = $i - ($page * 5 - 5);
                echo '<div id="picture' . $j . '" class="picture">';
                echo '<div id="img' . $j . '" class="img">';
                echo '<img src="../img/square-medium/' . $arrP[$i] . '" alt="" onclick="document.getElementById(\'title' . $j . '\').click()">';
                echo '</div>';
                echo '<div id="tc' . $j . '" class="tc">';
                echo '<a id="title' . $j . '" class="title" href="details.php?id=' . $arrID[$i] . '">' . $arrT[$i] . '</a>';
                echo '<p id="content' . $j . '" class="content">' . $arrD[$i] . '</p>';
                echo '</div></div><hr>';
            }
            ?>
            <div id="pages">
                <?php
                $pages = ceil($count / 5);
                $pre = ($page - 1 < 1) ? 1 : $page - 1;
                $nex = ($page + 1 > $pages) ? $pages : $page + 1;
                echo '<a href="?page=' . $pre . $url . '"><</a>';
                for ($i = 1; $i <= $pages && $i <= 5; $i++)
                    if ($i == $page)
                        echo '<a href="?page=' . $i . $url . '" class="selected">' . $i . '</a>';
                    else
                        echo '<a href="?page=' . $i . $url . '">' . $i . '</a>';
                echo '<a href="?page=' . $nex . $url . '">></a>';
                $pdo = null;
                ?>
            </div>
        </div>
    </div>
</div>
<div id="foot">
    <p>© 2020－2021 index.html, no rights reserved 复旦大学</p>
</div>
</body>
</html>