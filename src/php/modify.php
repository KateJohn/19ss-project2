<?php
include "functions.php";

function modify()
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
        $id = $_POST['id'];
        if (!empty($_FILES['img']['tmp_name'])) {
            $sql_ = "select * from travelimage where ImageID = $id";
            $result = $pdo->query($sql_);
            while ($row = $result->fetch()) {
                $path = $row['PATH'];
                break;
            }
            $tmp = $_FILES['img']['tmp_name'];
            $filepath = '../../img/medium/';
            $name_ = date('mdHis') . substr($_FILES['img']['name'], strpos($_FILES['img']['name'], '.'));
            $name = '"' . date('mdHis') . substr($_FILES['img']['name'], strpos($_FILES['img']['name'], '.')) . '"';
            if (empty($de))
                $sql = "update travelimage set Title = $title, CityCode = $cc, CountryCodeISO = $cci, PATH = $name where ImageID = $id";
            else
                $sql = "update travelimage set Description = $de, Title = $title, CityCode = $cc, CountryCodeISO = $cci, PATH = $name where ImageID = $id";
            $pdo->exec($sql);
            if (file_exists('../../img/medium/' . $path))
                unlink('../../img/medium/' . $path);
            if (file_exists('../../img/square-medium/' . $path))
                unlink('../../img/square-medium/' . $path);
            move_uploaded_file($tmp, $filepath . $name_);
            image_center_crop($filepath . $name_, 150, 150, '../../img/square-medium/' . $name_);
        } else {
            if (empty($de))
                $sql = "update travelimage set Title = $title, CityCode = $cc, CountryCodeISO = $cci where ImageID = $id";
            else
                $sql = "update travelimage set Description = $de, Title = $title, CityCode = $cc, CountryCodeISO = $cci where ImageID = $id";
            $pdo->exec($sql);
        }
        echo "<script>alert('修改成功')</script>";
        echo "<script>location.assign('../myPhoto.php');</script>";
        $pdo = null;
    } catch
    (PDOException $e) {
        die($e->getMessage());
    }
}

modify();