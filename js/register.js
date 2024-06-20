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
 * File: js/register.js
 * Signature: 4bea7180c37e405b9005226355cea1a1
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 */

/**
 * Functions for Register Page (mostly just simple AJAX calls)
 */

const doCheck = (opts) => {
    const fd = new FormData();
    fd.set(opts.key, encodeURIComponent(opts.value));
    fetch(opts.location, {
        method: "post",
        body: fd
    }).then(r => r.json()).then(response => {
        document.getElementById(opts.responseElem).innerHTML = response;
    }).catch(err => console.error(err));
}
const CheckPasswords = (password) => {
    doCheck({
        location: "check.php",
        key: "password",
        value: password,
        responseElem: "passwordresult"
    });
}

const CheckUsername = (name) => {
    doCheck({
        location: "checkun.php",
        key: "username",
        value: name,
        responseElem: "usernameresult"
    });
}

function CheckEmail(email) {
    doCheck({
        location: "checkem.php",
        key: "email",
        value: email,
        responseElem: "emailresult"
    });
}

const PasswordMatch = () => {
    const pwt1 = document.getElementById("pw1").value;
    const pwt2 = document.getElementById("pw2").value;
    const resultElem = document.getElementById("cpasswordresult");
    resultElem.innerHTML = (pwt1.length > 0 && pwt1 === pwt2) ? `<span style="color: #008800;">OK</span>` : `<span style="color: #FF0000;">Not Matching</span>`;
}
