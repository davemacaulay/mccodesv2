<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 * 
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

global $db, $userid, $h, $domain, $set;
require_once('globals.php');
$fiveK = money_formatter(5000);
$fiftK = money_formatter(15000);
$thirtfvK = money_formatter(35000);
print
    <<<EOF
<h3>Donations</h3>
<b>[<a href='willpotion.php'>Buy Will Potions</a>]</b><br />
If you become a donator to {$set['game_name']}, you will receive
 (each time you donate):<br />
<b>1st Offer:</b><ul>
<li>{$fiveK} game money</li>
<li>50 crystals</li>
<li>50 IQ, the hardest stat to get in the game!</li>
<li>30 days Donator Status: Red name + cross next to your name</li>
<li><b>NEW!</b> Friend and Black Lists</li>
<li><b>NEW!</b> 17% Energy every 5 mins instead of 8%</li></ul><br />
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick" />
<input type="hidden" name="business" value="{$set['paypal']}" />
<input type="hidden" name="item_name" value="{$domain}|DP|1|{$userid}" />
<input type="hidden" name="amount" value="3.00" />
<input type="hidden" name="no_shipping" value="1" />
<input type="hidden" name="return"
	value="https://{$domain}/donatordone.php?action=done" />
<input type="hidden" name="cancel_return"
	value="https://{$domain}/donatordone.php?action=cancel" />
<input type="hidden" name="notify_url"
	value="https://{$domain}/ipn_donator.php" />
<input type="hidden" name="cn" value="Your Player ID" />
<input type="hidden" name="currency_code" value="USD" />
<input type="hidden" name="tax" value="0" />
<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but21.gif"
	border="0" name="submit"
	alt="Make payments with PayPal - it's fast, free and secure!" />
</form>
<b>2nd Offer:</b><ul>
<li>100 crystals</li>
<li>30 days Donator Status: Red name + cross next to your name</li>
<li><b>NEW!</b> Friend and Black Lists</li>
<li><b>NEW!</b> 17% Energy every 5 mins instead of 8%</li></ul><br />
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick" />
<input type="hidden" name="business" value="{$set['paypal']}" />
<input type="hidden" name="item_name" value="{$domain}|DP|2|{$userid}" />
<input type="hidden" name="amount" value="3.00" />
<input type="hidden" name="no_shipping" value="1" />
<input type="hidden" name="return"
	value="https://{$domain}/donatordone.php?action=done" />
<input type="hidden" name="cancel_return"
	value="https://{$domain}/donatordone.php?action=cancel" />
<input type="hidden" name="notify_url"
	value="https://{$domain}/ipn_donator.php" />
<input type="hidden" name="cn" value="Your Player ID" />
<input type="hidden" name="currency_code" value="USD" />
<input type="hidden" name="tax" value="0" />
<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but21.gif"
	border="0" name="submit"
	alt="Make payments with PayPal - it's fast, free and secure!" />
</form>
<b>3rd Offer:</b><ul>
<li>120 IQ, the hardest stat to get in the game!</li>
<li>30 days Donator Status: Red name + cross next to your name</li>
<li><b>NEW!</b> Friend and Black Lists</li>
<li><b>NEW!</b> 17% Energy every 5 mins instead of 8%</li></ul><br />
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick" />
<input type="hidden" name="business" value="{$set['paypal']}" />
<input type="hidden" name="item_name" value="{$domain}|DP|3|{$userid}" />
<input type="hidden" name="amount" value="3.00" />
<input type="hidden" name="no_shipping" value="1" />
<input type="hidden" name="return"
	value="https://{$domain}/donatordone.php?action=done" />
<input type="hidden" name="cancel_return"
	value="https://{$domain}/donatordone.php?action=cancel" />
<input type="hidden" name="notify_url"
	value="https://{$domain}/ipn_donator.php" />
<input type="hidden" name="cn" value="Your Player ID" />
<input type="hidden" name="currency_code" value="USD" />
<input type="hidden" name="tax" value="0" />
<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but21.gif"
	border="0" name="submit"
	alt="Make payments with PayPal - it's fast, free and secure!" />
</form>
<b>4th Offer ($5.00 pack):</b><ul>
<li>{$fiftK} game money</li>
<li>75 crystals</li>
<li>80 IQ, the hardest stat to get in the game!</li>
<li>55 days Donator Status: Red name + cross next to your name</li>
<li><b>NEW!</b> Friend and Black Lists</li>
<li><b>NEW!</b> 17% Energy every 5 mins instead of 8%</li></ul><br />
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick" />
<input type="hidden" name="business" value="{$set['paypal']}" />
<input type="hidden" name="item_name" value="{$domain}|DP|4|{$userid}" />
<input type="hidden" name="amount" value="5.00" />
<input type="hidden" name="no_shipping" value="1" />
<input type="hidden" name="return"
	value="https://{$domain}/donatordone.php?action=done" />
<input type="hidden" name="cancel_return"
	value="https://{$domain}/donatordone.php?action=cancel" />
<input type="hidden" name="notify_url"
	value="https://{$domain}/ipn_donator.php" />
<input type="hidden" name="cn" value="Your Player ID" />
<input type="hidden" name="currency_code" value="USD" />
<input type="hidden" name="tax" value="0" />
<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but21.gif"
	border="0" name="submit"
	alt="Make payments with PayPal - it's fast, free and secure!" />
</form>
<b>5th Offer ($10.00 pack):</b><ul>
<li>{$thirtfvK} game money</li>
<li>160 crystals</li>
<li>180 IQ, the hardest stat to get in the game!</li>
<li>115 days Donator Status: Red name + cross next to your name</li>
<li><b>NEW!</b> Friend and Black Lists</li>
<li><b>NEW!</b> 17% Energy every 5 mins instead of 8%</li></ul><br />
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick" />
<input type="hidden" name="business" value="{$set['paypal']}" />
<input type="hidden" name="item_name" value="{$domain}|DP|5|{$userid}" />
<input type="hidden" name="amount" value="10.00" />
<input type="hidden" name="no_shipping" value="1" />
<input type="hidden" name="return"
	value="https://{$domain}/donatordone.php?action=done" />
<input type="hidden" name="cancel_return"
	value="https://{$domain}/donatordone.php?action=cancel" />
<input type="hidden" name="notify_url"
	value="https://{$domain}/ipn_donator.php" />
<input type="hidden" name="cn" value="Your Player ID" />
<input type="hidden" name="currency_code" value="USD" />
<input type="hidden" name="tax" value="0" />
<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but21.gif"
	border="0" name="submit"
	alt="Make payments with PayPal - it's fast, free and secure!" />
</form>
EOF;
$h->endpage();
