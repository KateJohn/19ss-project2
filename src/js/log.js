function getCookieValue(name_) {
    var name = escape(name_);
    var allcookies = document.cookie;
    name += "=";
    var pos = allcookies.indexOf(name);
    if (pos != -1) {
        var start = pos + name.length;
        var end = allcookies.indexOf(";", start);
        if (end == -1) end = allcookies.length;
        var value = allcookies.substring(start, end);
        return (value);
    } else {
        return "";
    }
}

function deleteCookie(name_, path) {
    var name = escape(name_);
    var expires = new Date(0);
    path = path == "" ? "" : ";path=" + path;
    document.cookie = name + "=" + ";expires=" + expires.toUTCString() + path;
}

document.onreadystatechange = function () {
    if (window.location.href.indexOf("login.html") !== -1) {
        document.getElementById("SUIName").value = getCookieValue("name");
        document.getElementById("SUIPassword").value = getCookieValue("password");
    }
    if (getCookieValue("name") === "")
        if (window.location.href.indexOf("index") !== -1)
            document.getElementsByClassName("dropdown")[0].innerHTML = "<a href='src/login.html' id='ALogin'/>登录</a>";
        else
            document.getElementsByClassName("dropdown")[0].innerHTML = "<a href='login.html' id='ALogin'/>登录</a>";
    if (getCookieValue("name") === "")
        if (window.location.href.indexOf("detail") !== -1)
            document.getElementById("like").onclick = function () {
                alert("请先登录");
            };
};