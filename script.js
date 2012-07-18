/*jslint browser: true */
var initLogin = function () {
    "use strict";
    var login, connect, loginBtn, script;
    script = document.createElement("script");
    script.setAttribute("src", "https://browserid.org/include.js");
    document.head.appendChild(script);
    login = function (assertion) {
        if (assertion) {
            document.location = "doku.php?do=login&assertion=" + assertion;
        }
    };
    connect = function (e) {
        e.preventDefault();
        navigator.id.get(login);
        return false;
    };
    loginBtn = document.getElementById("browserid");
    if (loginBtn.addEventListener) {
        loginBtn.addEventListener("click", connect, true);
    } else if (loginBtn.attachEvent) {
        loginBtn.attachEvent("onclick", connect);
    }
};
if (window.addEventListener) {
    window.addEventListener("load", initLogin, false);
} else if (window.attachEvent) {
    window.attachEvent("onload", initLogin);
}
