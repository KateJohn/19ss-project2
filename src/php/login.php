<?php
require_once('config.php');

$name = $_POST['name'];
$password = $_POST['password'];

function check_param($value = null)
{
    $str = 'select|insert|and|or|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile';
    if (strpos($str, $value)) {
        exit('参数非法！');
    }
    return true;
}

function login($name, $password)
{
    if (check_param($name) == true && check_param($password) == true) {
        try {
            $pdo = new PDO(DBCONNSTRING, DBUSER, DBPASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "select * from traveluser where UserName = '$name' and Pass = '$password';";
            $res = $pdo->query($sql);
            while ($row = $res->fetch()) {
                $date = date("Y-m-d H:i:s");
                $sql_ = "update traveluser set DateLastModified = '$date' where UserName = '$name'";
                $pdo->exec($sql_);
                setcookie("name", $name, time() + 3600000, '/');
                setcookie("password", $password, time() + 3600000, '/');
                echo "<script>alert('登录成功')</script>";
                echo "<script>location.assign('../../index.html');</script>";
                $pdo = null;
                return;
            }
            echo "<script>alert('用户名或密码错误');location.assign('../login.html');</script>";
            $pdo = null;
        } catch
        (PDOException $e) {
            die($e->getMessage());
        }
    }
}

login($name, $password);
