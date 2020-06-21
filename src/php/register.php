<?php
require_once 'config.php';
include "functions.php";
$name = $_POST['email'];
$password = $_POST['password'];

function register($email, $password)
{
    try {
        $pdo = new PDO(DBCONNSTRING, DBUSER, DBPASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "select * from traveluser where UserName = '$email'";
        $res = $pdo->query($sql);
        while ($row = $res->fetch()) {
            echo "<script>alert('该邮箱已被注册')</script>";
            echo "<script>location.assign('../register.html');</script>";
            $pdo = null;
            return;
        }
        $uid = getCountInI('traveluser') + 1;
        $date = date("Y-m-d H:i:s");
        $sql_ = "insert into traveluser (UID, UserName, Pass, State, DateJoined, DateLastModified) values ($uid,  '$email', '$password', 1, '$date', '$date')";
        $pdo->exec($sql_);
        echo "<script>alert('注册成功')</script>";
        echo "<script>location.assign('../login.html');</script>";
        $pdo = null;
    } catch
    (PDOException $e) {
        die($e->getMessage());
    }
}

register($name, $password);