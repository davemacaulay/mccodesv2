/**
 * MCCodes Version 2.0.5b
 * Copyright (C) 2005-2012 Dabomstew
 * All rights reserved.
 *
 * Redistribution of this code in any form is prohibited, except in
 * the specific cases set out in the MCCodes Customer License.
 *
 * This code license may be used to run one (1) game.
 * A game is defined as the set of users and other game database data,
 * so you are permitted to create alternative clients for your game.
 *
 * If you did not obtain this code from MCCodes.com, you are in all likelihood
 * using it illegally. Please contact MCCodes to discuss licensing options
 * in this case.
 *
 * File: js/login.js
 * Signature: 45166f2fb1d14bc62137b8dc74f14cf1
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */
const getCookieVal = (offset) => {
    let endstr = document.cookie.indexOf(";", offset);
    if (endstr === -1) {
        endstr = document.cookie.length;
    }
    return decodeURIComponent(document.cookie.substring(offset, endstr));
}

const GetCookie = (name) => {
    const arg = name + "=";
    const alen = arg.length;
    const clen = document.cookie.length;
    let i = 0;
    while (i < clen) {
        const j = i + alen;
        if (document.cookie.substring(i, j) === arg) {
            return getCookieVal(j);
        }
        i = document.cookie.indexOf(" ", i) + 1;
        if (i === 0) {
            break;
        }
    }
    return null;
}

const SetCookie = (name, value, expires, path = null, domain = null, secure = null) => {
    document.cookie = name + "=" + encodeURIComponent(value) + ((expires) ? "; expires=" + expires.toDateString() : "") + ((path) ? "; path=" + path : "") + ((domain) ? "; domain=" + domain : "") + ((secure) ? "; secure" : "");
}

const DeleteCookie = (name, path, domain) => {
    if (GetCookie(name)) {
        document.cookie = name + "=" + ((path) ? "; path=" + path : "") + ((domain) ? "; domain=" + domain : "") + "; expires=Thu, 01-Jan-70 00:00:01 GMT";
    }
}

let usr;
let pw;
let sv;

const getme = () => {
    usr = document.login.username;
    pw = document.login.password;
    sv = document.login.save;

    if (GetCookie('username') != null) {
        usr.value = GetCookie('username');
        pw.value = GetCookie('password');
    }
    if (GetCookie('save') === 'true') {
        sv[0].checked = true;
    } else {
        sv[1].checked = true;
    }

}

const saveme = () => {
    if (usr.value.length > 0 && pw.value.length > 0) {
        if (sv[0].checked) {
            let expdate = new Date();
            expdate.setTime(expdate.getTime() + 31536000000);
            SetCookie('username', usr.value, expdate);
            SetCookie('password', pw.value, expdate);
            SetCookie('save', 'true', expdate);
        }
        if (sv[1].checked) {
            DeleteCookie('username');
            DeleteCookie('password');
            DeleteCookie('save');
        }
    } else {
        alert('You must enter a username/password.');
        return false;
    }
}
