<?php
include "functions.php";

function upload()
{
    $country = '"' . $_POST['country'] . '"';
    $city = '"' . $_POST['city'] . '"';
    if (!ifMHasNInL('AsciiName', $city, 'geocities')) {
        echo '<script>alert("该城市不存在")</script>';
        echo '<script>window.history.back()</script>';
        return;
    }
    if (!ifMHasNInL('CountryName', $country, 'geocountries')) {
        echo '<script>alert("该国家不存在")</script>';
        echo '<script>window.history.back()</script>';
        return;
    }
    if (TurnIFromMToNInL($city, 'AsciiName', 'CountryCodeISO', 'geocities') != TurnIFromMToNInL($country, 'CountryName', 'ISO', 'geocountries')) {
        echo '<script>alert("该国家不存在该城市")</script>';
        echo '<script>window.history.back()</script>';
        return;
    }
    try {
        $pdo = new PDO(DBCONNSTRING, DBUSER, DBPASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $cc = TurnIFromMToNInL($city, 'AsciiName', 'GeoNameID', 'geocities');
        $cci = '"' . TurnIFromMToNInL($country, 'CountryName', 'ISO', 'geocountries') . '"';
        $de = '"' . $_POST['description'] . '"';
        $title = '"' . $_POST['title'] . '"';
        $id = getCountInI('travelimage') + 2;
        $uid = TurnIFromMToNInL('"' . $_COOKIE["name"] . '"', 'UserName', 'UID', 'traveluser');
        $name_ =date('mdHis') . substr($_FILES['img']['name'], strpos($_FILES['img']['name'], '.'));
        $name = '"' . date('mdHis') . substr($_FILES['img']['name'], strpos($_FILES['img']['name'], '.')) . '"';
        $tmp = $_FILES['img']['tmp_name'];
        $filepath = '../../img/medium/';
        if (empty($de))
            $sql = "insert into travelimage (ImageID, Title, CityCode, CountryCodeISO, UID, PATH) values ($id, $title, $cc, $cci, $uid, $name)";
        else
            $sql = "insert into travelimage (ImageID, Title, Description, CityCode, CountryCodeISO, UID, PATH) values ($id, $title, $de, $cc, $cci, $uid, $name)";
        $pdo->exec($sql);
        move_uploaded_file($tmp, $filepath . $name_);
        image_center_crop($filepath . $name_, 150, 150, '../../img/square-medium/' . $name_);
        echo "<script>alert('上传成功')</script>";
        echo "<script>location.assign('../myPhoto.php');</script>";
        $pdo = null;
    } catch
    (PDOException $e) {
        die($e->getMessage());
    }
}

upload();