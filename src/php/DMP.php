<?php
require_once "config.php";
$id = $_POST['id'];
$uid = $_POST['uid'];

try {
    $i = 0;
    $pdo = new PDO(DBCONNSTRING, DBUSER, DBPASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql_ = "select * from travelimage where ImageID = $id";
    $res_ = $pdo->query($sql_);
    while ($row = $res_->fetch()) {
        $path = $row['PATH'];
        break;
    }
    $sql = "delete from travelimage where ImageID = $id";
    $res = $pdo->exec($sql);
    if (file_exists('../../img/medium/' . $path))
        unlink('../../img/medium/' . $path);
    if (file_exists('../../img/square-medium/' . $path))
        unlink('../../img/square-medium/' . $path);
    $pdo = null;
} catch
(PDOException $e) {
    die($e->getMessage());
}

echo "<script>location.assign('../myPhoto.php')</script>";