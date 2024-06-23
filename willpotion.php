<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 * 
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */
global $userid, $h, $set, $domain;
require_once('globals.php');
print
    <<<EOF
<h3>Will Potions</h3>

Buy will potions today! They restore 100% will.<br />
<b>Buy One:</b> (\$1)<br />
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick" />
<input type="hidden" name="business" value="{$set['paypal']}" />
<input type="hidden" name="item_name" value="{$domain}|WP|1|{$userid}" />
<input type="hidden" name="amount" value="1.00" />
<input type="hidden" name="no_shipping" value="1" />
<input type="hidden" name="return"
	value="https://{$domain}/willpdone.php?action=done" />
<input type="hidden" name="cancel_return"
	value="https://{$domain}/willpdone.php?action=cancel" />
<input type="hidden" name="notify_url"
	value="https://{$domain}/ipn_wp.php" />
<input type="hidden" name="cn" value="Your Player ID" />
<input type="hidden" name="currency_code" value="USD" />
<input type="hidden" name="tax" value="0" />
<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but21.gif"
	border="0" name="submit"
	alt="Make payments with PayPal - it's fast, free and secure!" />
</form>
<b>Buy Five:</b> (\$4.50)<br />
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick" />
<input type="hidden" name="business" value="{$set['paypal']}" />
<input type="hidden" name="item_name" value="{$domain}|WP|5|{$userid}" />
<input type="hidden" name="amount" value="4.50" />
<input type="hidden" name="no_shipping" value="1" />
<input type="hidden" name="return"
	value="https://{$domain}/willpdone.php?action=done" />
<input type="hidden" name="cancel_return"
	value="https://{$domain}/willpdone.php?action=cancel" />
<input type="hidden" name="notify_url"
	value="https://{$domain}/ipn_wp.php" />
<input type="hidden" name="cn" value="Your Player ID" />
<input type="hidden" name="currency_code" value="USD" />
<input type="hidden" name="tax" value="0" />
<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but21.gif"
	border="0" name="submit"
	alt="Make payments with PayPal - it's fast, free and secure!" />
</form>
EOF;
$h->endpage();
