<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 * 
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

global $db, $ir, $userid, $h;
require_once('globals.php');
echo '<h3>Bank</h3>';
$bank_cost = 50000;
$bank_maxfee = 3000;
$bank_feepercent = 15;
if ($ir['bankmoney'] > -1)
{
    if (!isset($_GET['action']))
    {
        $_GET['action'] = '';
    }
    switch ($_GET['action'])
    {
    case 'deposit':
        deposit();
        break;

    case 'withdraw':
        withdraw();
        break;

    default:
        index();
        break;
    }

} elseif (isset($_GET['buy'])) {
    if ($ir['money'] >= $bank_cost) {
        echo 'Congratulations, you bought a bank account for '
            . money_formatter($bank_cost)
            . "!<br />
<a href='bank.php'>Start using my account</a>";
        $db->query(
            "UPDATE `users` SET `money` = `money` - {$bank_cost}, `bankmoney` = 0 WHERE `userid` = $userid");
    } else {
        echo "You do not have enough money to open an account.
<a href='explore.php'>Back to town...</a>";
    }
} else {
    echo 'Open a bank account today, just ' . money_formatter($bank_cost)
        . "!<br />
<a href='bank.php?buy'>&gt; Yes, sign me up!</a>";
}

/**
 * @return void
 */
function index(): void
{
    global $ir, $bank_maxfee, $bank_feepercent;
    echo "\n<b>You currently have" . money_formatter($ir['bankmoney'])
            . " in the bank.</b><br />
At the end of each day, your bank balance will go up by 2%.<br />
<table width='75%' cellspacing=1 class='table'> <tr> <td width='50%'><b>Deposit Money</b><br />
It will cost you {$bank_feepercent}% of the money you deposit, rounded up. The maximum fee is "
            . money_formatter($bank_maxfee)
            . ".<form action='bank.php?action=deposit' method='post'>
Amount: <input type='text' name='deposit' value='{$ir['money']}' /><br />
<input type='submit' value='Deposit' /></form></td> <td>
<b>Withdraw Money</b><br />
There is no fee on withdrawals.<form action='bank.php?action=withdraw' method='post'>
Amount: <input type='text' name='withdraw' value='{$ir['bankmoney']}' /><br />
<input type='submit' value='Withdraw' /></form></td> </tr> </table>";
}

/**
 * @return void
 */
function deposit(): void
{
    global $db, $ir, $userid, $bank_maxfee, $bank_feepercent;
    $_POST['deposit'] = abs((int) $_POST['deposit']);
    if ($_POST['deposit'] > $ir['money'])
    {
        echo 'You do not have enough money to deposit this amount.';
    }
    else
    {
        $fee = ceil($_POST['deposit'] * $bank_feepercent / 100);
        if ($fee > $bank_maxfee)
        {
            $fee = $bank_maxfee;
        }
        $gain = $_POST['deposit'] - $fee;
        $ir['bankmoney'] += $gain;
        $db->query(
                "UPDATE `users` SET `bankmoney` = `bankmoney` + $gain,
                        `money` = `money` - {$_POST['deposit']} WHERE `userid` = $userid");
        echo 'You hand over ' . money_formatter($_POST['deposit'])
                . ' to be deposited, <br />
after the fee is taken (' . money_formatter($fee) . ', '
                . money_formatter($gain)
                . ' is added to your account. <br />
<b>You now have ' . money_formatter($ir['bankmoney'])
                . " in the bank.</b><br />
<a href='bank.php'>&gt; Back</a>";
    }
}

/**
 * @return void
 */
function withdraw(): void
{
    global $db, $ir, $userid;
    $_POST['withdraw'] = abs((int) $_POST['withdraw']);
    if ($_POST['withdraw'] > $ir['bankmoney'])
    {
        echo 'You do not have enough banked money to withdraw this amount.';
    }
    else
    {

        $gain = $_POST['withdraw'];
        $ir['bankmoney'] -= $gain;
        $db->query(
                "UPDATE `users` SET `bankmoney` = `bankmoney` - $gain,
                        `money` = `money` + $gain WHERE `userid` = $userid");
        echo 'You ask to withdraw ' . money_formatter($gain)
                . ', <br />
the banking lady grudgingly hands it over. <br />
<b>You now have ' . money_formatter($ir['bankmoney'])
                . " in the bank.</b><br />
<a href='bank.php'>&gt; Back</a>";
    }
}
$h->endpage();
