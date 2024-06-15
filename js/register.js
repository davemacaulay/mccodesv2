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

function CheckPasswords(password)
{
    $.ajax({
        type : "POST",
        url : "check.php",
        data : "password=" + escape(password),
        success : function(resps)
        {
            $("#passwordresult").html(resps);
        }
    });
}

function CheckUsername(name)
{
    $.ajax({
        type : "POST",
        url : "checkun.php",
        data : "username=" + escape(name),
        success : function(resps)
        {
            $("#usernameresult").html(resps);
        }
    });
}

function CheckEmail(email)
{
    $.ajax({
        type : "POST",
        url : "checkem.php",
        data : "email=" + escape(email),
        success : function(resps)
        {
            $("#emailresult").html(resps);
        }
    });
}

function PasswordMatch()
{
    pwt1 = $("#pw1").val();
    pwt2 = $("#pw2").val();
    if (pwt1 == pwt2)
    {
        $("#cpasswordresult").html("<font color='green'>OK</font>");
    }
    else
    {
        $("#cpasswordresult").html("<font color='red'>Not Matching</font>");
    }
}