<?php
require_once "config.php";
function TurnIFromMToNInL($i, $m, $n, $l)
{
    if ($i == "''")
        return "'未知'";
    try {
        $pdo = new PDO(DBCONNSTRING, DBUSER, DBPASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "select * from $l where $m = $i";
        $result = $pdo->query($sql);
        while ($row = $result->fetch()) {
            return $row[$n];
        }
        $pdo = null;
        return null;
    } catch (PDOException $e) {
        die($e->getMessage());
    }
}

function ifMHasNInL($m, $n, $l)
{
    try {
        $pdo = new PDO(DBCONNSTRING, DBUSER, DBPASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "select * from $l where $m = $n";
        $result = $pdo->query($sql);
        while ($row = $result->fetch())
            return true;
        $pdo = null;
        return false;
    } catch (PDOException $e) {
        die($e->getMessage());
    }
}

function getCountInI($i)
{
    try {
        $pdo = new PDO(DBCONNSTRING, DBUSER, DBPASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "select count(*) from $i";
        $res = $pdo->query($sql);
        $row = $res->fetch();
        $i = $row[0];
        $pdo = null;
        return $i;
    } catch
    (PDOException $e) {
        die($e->getMessage());
    }
}

function random($j)
{
    $i = array();
    for ($k = 0; $k < $j; $k++)
        if (in_array($l = mt_rand(1, 81), $i) || $l == 79)
            $k--;
        else
            $i[] = $l;
    return $i;
}

function favored($i)
{
    try {
        $pdo = new PDO(DBCONNSTRING, DBUSER, DBPASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "select count(ImageID) from travelimagefavor where ImageID = $i";
        $result = $pdo->query($sql);
        $rows = $result->fetch();
        $rowCount = $rows[0];
        $pdo = null;
        return $rowCount;
    } catch (PDOException $e) {
        die($e->getMessage());
    }
}

function mostFavored()
{
    $like = array();
    for ($i = 1; $i <= 81; $i++)
        $like["$i"] = favored($i);
    arsort($like);
    return array_keys(array_slice($like, 0, 6, true));
}

function showPictures($i)
{
    try {
        $like = $i;
        $pdo = new PDO(DBCONNSTRING, DBUSER, DBPASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "select * from travelimage where ImageID = $like[0] or  ImageID = $like[1] or  ImageID = $like[2] or  ImageID = $like[3] or  ImageID = $like[4] or  ImageID = $like[5]";
        $result = $pdo->query($sql);
        while ($row = $result->fetch()) {
            echo "<div class='picture'>";
            echo "<img src=\"img/square-medium/" . $row["PATH"] . "\" alt onclick=\"document.getElementById('detail" . $row["ImageID"] . "').click()\">";
            echo "<a id='detail" . $row["ImageID"] . "' class=\"title\" href=\"src/details.php?id=" . $row["ImageID"] . "\">" . $row["Title"] . "</a>";
            echo "<p class=\"description\" >" . cut($row["Description"]) . "</p>";
            echo "</div>";
        }
        $pdo = null;
    } catch (PDOException $e) {
        die($e->getMessage());
    }
}

function image_center_crop($source, $width, $height, $target)
{
    if (!file_exists($source)) return false;
    /* 根据类型载入图像 */
    switch (exif_imagetype($source)) {
        case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $image = imagecreatefrompng($source);
            break;
        case IMAGETYPE_GIF:
            $image = imagecreatefromgif($source);
            break;
    }
    if (!isset($image)) return false;
    /* 获取图像尺寸信息 */
    $target_w = $width;
    $target_h = $height;
    $source_w = imagesx($image);
    $source_h = imagesy($image);
    /* 计算裁剪宽度和高度 */
    $judge = (($source_w / $source_h) > ($target_w / $target_h));
    $resize_w = $judge ? ($source_w * $target_h) / $source_h : $target_w;
    $resize_h = !$judge ? ($source_h * $target_w) / $source_w : $target_h;
    $start_x = $judge ? ($resize_w - $target_w) / 2 : 0;
    $start_y = !$judge ? ($resize_h - $target_h) / 2 : 0;
    /* 绘制居中缩放图像 */
    $resize_img = imagecreatetruecolor($resize_w, $resize_h);
    imagecopyresampled($resize_img, $image, 0, 0, 0, 0, $resize_w, $resize_h, $source_w, $source_h);
    $target_img = imagecreatetruecolor($target_w, $target_h);
    imagecopy($target_img, $resize_img, 0, 0, $start_x, $start_y, $resize_w, $resize_h);
    /* 将图片保存至文件 */
    if (!file_exists(dirname($target))) mkdir(dirname($target), 0777, true);
    switch (exif_imagetype($source)) {
        case IMAGETYPE_JPEG:
            imagejpeg($target_img, $target);
            break;
        case IMAGETYPE_PNG:
            imagepng($target_img, $target);
            break;
        case IMAGETYPE_GIF:
            imagegif($target_img, $target);
            break;
    }
    return boolval(file_exists($target));
}

function cut($i)
{
    if (strlen($i) > 80)
        return substr($i, 0, 80) . "..";
    else return $i;
}

function cnxk()
{
    echo "<p class='title'>猜你想看</p>";
    try {
        $pdo = new PDO(DBCONNSTRING, DBUSER, DBPASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $i = 0;
        $sql = "select * from travelimage";
        $result = $pdo->query($sql);
        while ($row = $result->fetch()) {
            if ($i >= 5) break;
            $i++;
            echo "<div id='picture" . $i . "' class='picture'>";
            echo "<div id='img" . $i . "' class='img'>";
            echo "<img src='../img/square-medium/" . $row["PATH"] . "' alt onclick='document.getElementById(\"title" . $i . "\").click()'>";
            echo "</div>";
            echo "<div id='tc" . $i . "' class='tc'>";
            echo "<a id='title" . $i . "' class='title' href='details.php?id=" . $i . "'>" . $row["Title"] . "</a>";
            echo "<p id='content" . $i . "' class='content'>" . $row["Description"] . "</p>";
            echo "</div>";
            echo "</div>";
            echo "<hr>";
        }
        $pdo = null;
    } catch (PDOException $e) {
        die($e->getMessage());
    }
}