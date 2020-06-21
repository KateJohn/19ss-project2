<!DOCTYPE html>
<html lang="ch">
<?php
include 'php/functions.php';
$st = empty($_GET['ST']) ? '' : $_GET['ST'];
$page = isset($_GET['page']) && $_GET['page'] > 1 ? $_GET['page'] : 1;
$arrP = array();
$arrID = array();
$url = '';
try {
    $pdo = new PDO(DBCONNSTRING, DBUSER, DBPASS);
} catch (PDOException $e) {
    die($e->getMessage());
}
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
if (!empty($_GET['ST'])) {
    $pat = '"%' . $st . '%"';
    $sql = "select * from travelimage where Title like $pat";
    $result = $pdo->query($sql);
    $url .= '&ST=' . $st;
    while ($row = $result->fetch()) {
        $arrP[] = $row['PATH'];
        $arrID[] = $row['ImageID'];
    }
}
$city = empty($_GET['city']) ? '' : $_GET['city'];
$country = empty($_GET['country']) ? '' : $_GET['country'];
if (!empty($country) && $country != 'any' && (empty($city) || $city == 'any')) {
    $country_ = '"' . $country . '"';
    $countryISO = '"' . TurnIFromMToNInL($country_, 'CountryName', 'ISO', 'geocountries') . '"';
    $sql = "select * from travelimage where CountryCodeISO = $countryISO";
    $result = $pdo->query($sql);
    $url .= '&country=' . $country;
    while ($row = $result->fetch()) {
        $arrP[] = $row['PATH'];
        $arrID[] = $row['ImageID'];
    }
}
if (!empty($city) && $city != 'any') {
    $city_ = '"' . $city . '"';
    $sql_ = "select * from geocities where AsciiName = $city_";
    $result_ = $pdo->query($sql_);
    if (!empty($country) && $country != 'any')
        $url .= '&country=' . $country;
    $url .= '&city=' . $city;
    while ($row_ = $result_->fetch()) {
        $cityCode = $row_['GeoNameID'];
        if ($cityCode == '') continue;
        $sql = "select * from travelimage where CityCode = $cityCode";
        $result = $pdo->query($sql);
        while ($row = $result->fetch()) {
            $arrP[] = $row['PATH'];
            $arrID[] = $row['ImageID'];
        }
    }
}
if ($url == '')
    for ($i = 1; $i <= 81; $i++) {
        if ($i == 79) continue;
        $arrID[] = $i;
        $arrP[] = TurnIFromMToNInL($i, 'ImageID', 'PATH', 'travelimage');
    }
$count = count($arrID);
$cc = array();
try {
    $sql = "select distinct CityCode from travelimage";
    $result = $pdo->query($sql);
    while ($row = $result->fetch())
        if (!empty($row['CityCode'])) {
            $ctISO = TurnIFromMToNInL("'" . $row['CityCode'] . "'", "GeoNameID", "CountryCodeISO", "geocities");
            $ctName = TurnIFromMToNInL("'" . $ctISO . "'", "ISO", "CountryName", "geocountries");
            $cc[TurnIFromMToNInL("'" . $row['CityCode'] . "'", "GeoNameID", "AsciiName", "geocities")] = $ctName;
        }
} catch (PDOException $e) {
    die($e->getMessage());
}
$cityCount = count($cc);
ksort($cc);
?>
<script type="text/javascript">
    let city = [];
    let country = [];
    <?php
    $i = 0;
    foreach ($cc as $key => $value) {
        echo "city[$i] = '$key';\n";
        echo "country[$i] = '$value';\n";
        $i++;
    }
    ?>
    function change(i) {
        let html = '<option id="noneCity" value="any" selected>全部</option>';
        if (i === 'any')
            for (let j = 0; j < city.length; j++)
                html += '<option value="' + city[j] + '">' + city[j] + '</option>';
        else
            for (let k = 0; k < city.length; k++)
                if (country[k] === i)
                    html += '<option value="' + city[k] + '">' + city[k] + '</option>';
        document.getElementById('city').innerHTML = html;
    }
</script>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/reset.css">
    <link rel="stylesheet" type="text/css" href="css/header.css">
    <link rel="stylesheet" type="text/css" href="css/body.css">
    <link rel="stylesheet" type="text/css" href="css/browser.css">
    <title>浏览</title>
</head>
<body>
<script src="js/log.js"></script>
<div id="header">
    <div id="logo">
        <img src="../img/system/logo.jpg" alt width="35" height="35">
    </div>
    <div id="navItems">
        <a href="../index.html" id="AIndex">首页</a>
        <a href="browser.php" id="ABrowse" class="select">浏览</a>
        <a href="search.php" id="ASearch">搜索</a>
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
    <div id="leftBody">
        <form id="search" method="get" action="browser.php">
            <p>搜索标题</p>
            <label>
                <input id="searchTitle" required name="ST" type="text">
            </label>
            <button id="SB" type="submit">搜索</button>
        </form>
        <div id="hotThemes">
            <div class="firstType">热⻔主题快速浏览</div>
            <hr>
            <a onclick="document.getElementById('sceneryTheme').selected='selected'">自然</a>
            <hr>
            <a onclick="document.getElementById('cityTheme').selected='selected'">城市</a>
            <hr>
            <a onclick="document.getElementById('buildingTheme').selected='selected'">建筑</a>
            <hr>
            <a onclick="document.getElementById('animalTheme').selected='selected'">动物</a>
        </div>
        <div id="hotCountries">
            <div class="firstType">热⻔国家快速浏览</div>
            <?php
            $sql = "select CountryCodeISO, count( * ) as count from travelimage group by CountryCodeISO order by count desc limit 5";
            $result = $pdo->query($sql);
            while ($rows = $result->fetch()) {
                $cou = TurnIFromMToNInL('"' . $rows['CountryCodeISO'] . '"', 'ISO', 'CountryName', 'geocountries');
                echo '<hr>';
                echo '<a href="browser.php?country=' . $cou . '">' . $cou . '</a>';
            }
            ?>
        </div>
        <div id="hotCities">
            <div class="firstType">热⻔城市快速浏览</div>
            <?php
            $sql = "select CityCode, count( * ) as count from travelimage group by CityCode order by count desc limit 6";
            $result = $pdo->query($sql);
            while ($rows = $result->fetch()) {
                if (empty($rows['CityCode'])) continue;
                $cit = TurnIFromMToNInL($rows['CityCode'], 'GeoNameID', 'AsciiName', 'geocities');
                echo '<hr>';
                echo '<a href="browser.php?city=' . $cit . '">' . $cit . '</a>';
            }
            ?>
        </div>
    </div>
    <div id="rightBody">
        <div id="rightTitle">
            <p>筛选</p>
        </div>
        <div id="selectors">
            <form action="browser.php" method="get">
                <p class="form">主题</p>
                <label class="form">
                    <select id="theme" class="theme" name="theme">
                        <option id="noneTheme" value="any" selected>全部</option>
                        <option id="sceneryTheme" value="scenery">自然</option>
                        <option id="cityTheme" value="city">城市</option>
                        <option id="buildingTheme" value="building">建筑</option>
                        <option id="animalTheme" value="animal">动物</option>
                        <option id="peopleTheme" value="people">人物</option>
                        <option id="wonderTheme" value="wonder">奇观</option>
                        <option id="otherTheme" value="other">其他</option>
                    </select>
                </label>
                <p class="form">国家</p>
                <label class="form">
                    <select id="country" class="country" onchange="change(this.value)" name="country">
                        <option id="noneCountry" value="any" selected>全部</option>
                        <?php
                        $countryL = array();
                        $sql = "select distinct CountryCodeISO from travelimage";
                        $result = $pdo->query($sql);
                        while ($row = $result->fetch())
                            $countryL[] = TurnIFromMToNInL('"' . $row['CountryCodeISO'] . '"', "ISO", "CountryName", "geocountries");
                        sort($countryL);
                        foreach ($countryL as $item)
                            echo '<option value="' . $item . '">' . $item . '</option>';
                        ?>
                    </select>
                </label>
                <p class="form">城市</p>
                <label class="form">
                    <select id="city" class="city" name="city">
                        <option id="noneCity" value="any" selected>全部</option>
                        <?php
                        foreach ($cc as $key => $item)
                            echo '<option value="' . $key . '">' . $key . '</option>';
                        ?>
                    </select>
                </label>
                <button type="submit">搜索</button>
            </form>
        </div>
        <hr>
        <div id="result">
            <?php
            if ($url == '')
                echo '结果<br><br>';
            else if ($count != 0) {
                $from = $page * 16 - 15;
                $to = min($page * 16, $count);
                echo substr($url, 1) . '的' . $count . '个结果(' . $from . '~' . $to . ')<br><br>';
            } else {
                echo '找不到' . $st . '的搜索结果';
                echo '<hr>';
                echo '更多';
                for ($i = 1; $i <= 81; $i++) {
                    if ($i == 79) continue;
                    $arrID[] = $i;
                    $arrP[] = TurnIFromMToNInL($i, 'ImageID', 'PATH', 'travelimage');
                }
                $count = count($arrID);
            }
            ?>
            <div id="pictures">
                <?php
                for ($i = $page * 16 - 16; $i < $page * 16 && $i < count($arrP); $i++) {
                    echo '<div class="picture">';
                    echo '<img src="../img/square-medium/' . $arrP[$i] . '" alt="" onclick="location.assign(\'details.php?id=' . $arrID[$i] . '\')">';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
        <hr>
        <div id="pages">
            <?php
            $pages = ceil($count / 16);
            $pre = ($page - 1 < 1) ? 1 : $page - 1;
            $nex = ($page + 1 > $pages) ? $pages : $page + 1;
            echo '<a href="?page=' . $pre . $url . '"><</a>';
            for ($i = 1; $i <= $pages; $i++)
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
<div id="foot">
    <p>© 2020－2021 index.html, no rights reserved 复旦大学</p>
</div>
</body>
</html>