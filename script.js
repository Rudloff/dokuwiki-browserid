/*jslint browser: true */
var initLogin = function () {
    "use strict";
    var login, connect, loginBtn, script;
    script = document.createElement("script");
    script.setAttribute("src", "https://browserid.org/include.js");
    document.head.appendChild(script);
    login = function (assertion) {
        if (assertion) {
            var form = document.createElement('form'), postvar = document.createElement('input');
            form.setAttribute('action', "doku.php?do=login");
            form.setAttribute('method', 'post');
            postvar.setAttribute('type', 'hidden');
            postvar.setAttribute('name', "assertion");
            postvar.setAttribute('value', assertion);
            form.appendChild(postvar);
            document.body.appendChild(form);
            form.submit();
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
