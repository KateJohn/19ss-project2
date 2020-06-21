<?php
require_once "config.php";
$id = $_POST['id'];
$uid = $_POST['uid'];

try {
    $i = 0;
    $pdo = new PDO(DBCONNSTRING, DBUSER, DBPASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "delete from travelimagefavor where ImageID = $id and UID = $uid";
    $res = $pdo->exec($sql);
    $pdo = null;
} catch
(PDOException $e) {
    die($e->getMessage());
}

echo "<script>location.assign('../myLike.php')</script>";