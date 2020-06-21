<?php
require_once "config.php";
include "functions.php";
$uid = $_POST['uid'];
$id = $_POST['id'];

if (empty($id))
{
    echo "<script>alert('请先登录')</script>";
    echo "<script>location.assign('../detail.php')</script>";
}

function getFID()
{
    try {
        $pdo = new PDO(DBCONNSTRING, DBUSER, DBPASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "select count(*) from travelimagefavor";
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

function like($uid, $id)
{
    try {
        $pdo = new PDO(DBCONNSTRING, DBUSER, DBPASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $fid = getFID() + 1;
        $sql_ = "insert into travelimagefavor (FavorID, UID, ImageID) values ($fid, $uid, $id)";
        $pdo->exec($sql_);
        echo "<script>location.assign('../details.php?id=" . $id . "');</script>";
        $pdo = null;
    } catch
    (PDOException $e) {
        die($e->getMessage());
    }
}

like($uid, $id);