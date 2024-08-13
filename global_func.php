<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 *
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */

/**
 * Return the difference between the current time and a given time, formatted in appropriate units so the number is not too big or small.
 * @param string|int $time_stamp The timestamp to find the difference to.
 * @return string The difference formatted in units so that the numerical component is not less than 1 or absurdly large.
 */
function datetime_parse(string|int $time_stamp): string
{
    $time_difference = ($_SERVER['REQUEST_TIME'] - (int)$time_stamp);
    $unit =
            ['second', 'minute', 'hour', 'day', 'week', 'month', 'year'];
    $lengths = [60, 60, 24, 7, 4.35, 12];
    for ($i = 0; $time_difference >= $lengths[$i]; $i++)
    {
        $time_difference = $time_difference / $lengths[$i];
    }
    $time_difference = round($time_difference);
    return $time_difference . ' ' . $unit[$i]
            . (($time_difference > 1 OR $time_difference < 1) ? 's'
                    : '') . ' ago';
}

/**
 * Format money in the way humans expect to read it.
 * @param int|float|string $muny The amount of money to display
 * @param string $symb The money unit symbol to use, e.g. $
 * @return string
 */
function money_formatter(int|float|string $muny, string $symb = '$'): string
{
    return $symb . number_format((float)$muny);
}

/**
 * Constructs a drop-down listbox of all the item types in the game to let the user select one.
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the item type which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first item type alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function itemtype_dropdown(string $ddname = 'item_type', int $selected = -1): string
{
    global $db;
    $ret = "<select name='$ddname' type='dropdown'>";
    $q =
            $db->query(
                'SELECT `itmtypeid`, `itmtypename`
    				 FROM `itemtypes`
    				 ORDER BY `itmtypename` ASC');
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['itmtypeid']}'";
        if ($selected == $r['itmtypeid'] || $first == 0)
        {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['itmtypename']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the items in the game to let the user select one.
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the item which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first item alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function item_dropdown(string $ddname = 'item', int $selected = -1): string
{
    global $db;
    $ret = "<select name='$ddname' type='dropdown'>";
    $q =
            $db->query(
                'SELECT `itmid`, `itmname`
    				 FROM `items`
    				 ORDER BY `itmname` ASC');
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['itmid']}'";
        if ($selected == $r['itmid'] || $first == 0)
        {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['itmname']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the items in the game to let the user select one, including a "None" option.
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the item which should be selected by default.<br />
 * Not specifying this or setting it to a number less than 1 makes "None" selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function item2_dropdown(string $ddname = 'item', int $selected = -1): string
{
    global $db;
    $ret = "<select name='$ddname' type='dropdown'>";
    $q =
            $db->query(
                'SELECT `itmid`, `itmname`
    				 FROM `items`
    				 ORDER BY `itmname` ASC');
    if ($selected < 1)
    {
        $ret .= "<option value='0' selected='selected'>-- None --</option>";
    }
    else
    {
        $ret .= "<option value='0'>-- None --</option>";
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['itmid']}'";
        if ($selected == $r['itmid'])
        {
            $ret .= " selected='selected'";
        }
        $ret .= ">{$r['itmname']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the locations in the game to let the user select one.
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the location which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first item alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function location_dropdown(string $ddname = 'location', int $selected = -1): string
{
    global $db;
    $ret = "<select name='$ddname' type='dropdown'>";
    $q =
            $db->query(
                'SELECT `cityid`, `cityname`
    				 FROM `cities`
    				 ORDER BY `cityname` ASC');
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['cityid']}'";
        if ($selected == $r['cityid'] || $first == 0)
        {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['cityname']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the shops in the game to let the user select one.
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the shop which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first shop alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function shop_dropdown(string $ddname = 'shop', int $selected = -1): string
{
    global $db;
    $ret = "<select name='$ddname' type='dropdown'>";
    $q =
            $db->query(
                'SELECT `shopID`, `shopNAME`
    				 FROM `shops`
    				 ORDER BY `shopNAME` ASC');
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['shopID']}'";
        if ($selected == $r['shopID'] || $first == 0)
        {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['shopNAME']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the registered users in the game to let the user select one.
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the user who should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first user alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function user_dropdown(string $ddname = 'user', int $selected = -1): string
{
    global $db;
    $ret = "<select name='$ddname' type='dropdown'>";
    $q =
            $db->query(
                'SELECT `userid`, `username`
    				 FROM `users`
    				 ORDER BY `username` ASC');
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['userid']}'";
        if ($selected == $r['userid'] || $first == 0)
        {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['username']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the challenge bot NPC users in the game to let the user select one.
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the bot who should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first bot alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function challengebot_dropdown(string $ddname = 'bot', int $selected = -1): string
{
    global $db;
    $ret = "<select name='$ddname' type='dropdown'>";
    $q =
            $db->query(
                'SELECT `u`.`userid`, `u`.`username`
                     FROM `challengebots` AS `cb`
                     INNER JOIN `users` AS `u`
                     ON `cb`.`cb_npcid` = `u`.`userid`
                     ORDER BY `u`.`username` ASC');
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['userid']}'";
        if ($selected == $r['userid'] || $first == 0)
        {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['username']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the users in federal jail in the game to let the user select one.
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the user who should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first user alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function fed_user_dropdown(string $ddname = 'user', int $selected = -1): string
{
    global $db;
    $ret = "<select name='$ddname' type='dropdown'>";
    $q =
            $db->query(
                'SELECT `userid`, `username`
                     FROM `users`
                     WHERE `fedjail` = 1
                     ORDER BY `username` ASC');
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['userid']}'";
        if ($selected == $r['userid'] || $first == 0)
        {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['username']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the mail banned users in the game to let the user select one.
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the user who should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first user alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function mailb_user_dropdown(string $ddname = 'user', int $selected = -1): string
{
    global $db;
    $ret = "<select name='$ddname' type='dropdown'>";
    $q =
            $db->query(
                'SELECT `userid`, `username`
                     FROM `users`
                     WHERE `mailban` > 0
                     ORDER BY `username` ASC');
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['userid']}'";
        if ($selected == $r['userid'] || $first == 0)
        {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['username']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the forum banned users in the game to let the user select one.
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the user who should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first user alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function forumb_user_dropdown(string $ddname = 'user', int $selected = -1): string
{
    global $db;
    $ret = "<select name='$ddname' type='dropdown'>";
    $q =
            $db->query(
                'SELECT `userid`, `username`
                     FROM `users`
                     WHERE `forumban` > 0
                     ORDER BY `username` ASC');
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['userid']}'";
        if ($selected == $r['userid'] || $first == 0)
        {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['username']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the jobs in the game to let the user select one.
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the job which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first job alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function job_dropdown(string $ddname = 'job', int $selected = -1): string
{
    global $db;
    $ret = "<select name='$ddname' type='dropdown'>";
    $q =
            $db->query(
                'SELECT `jID`, `jNAME`
    				 FROM `jobs`
    				 ORDER BY `jNAME` ASC');
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['jID']}'";
        if ($selected == $r['jID'] || $first == 0)
        {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['jNAME']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the job ranks in the game to let the user select one.
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the job rank which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first job's first job rank alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function jobrank_dropdown(string $ddname = 'jobrank', int $selected = -1): string
{
    global $db;
    $ret = "<select name='$ddname' type='dropdown'>";
    $q =
            $db->query(
                'SELECT `jrID`, `jNAME`, `jrNAME`
                     FROM `jobranks` AS `jr`
                     INNER JOIN `jobs` AS `j`
                     ON `jr`.`jrJOB` = `j`.`jID`
                     ORDER BY `j`.`jNAME` ASC, `jr`.`jrNAME` ASC');
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['jrID']}'";
        if ($selected == $r['jrID'] || $first == 0)
        {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['jNAME']} - {$r['jrNAME']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the houses in the game to let the user select one.
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the house which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first house alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function house_dropdown(string $ddname = 'house', int $selected = -1): string
{
    global $db;
    $ret = "<select name='$ddname' type='dropdown'>";
    $q =
            $db->query(
                'SELECT `hID`, `hNAME`
    				 FROM houses
    				 ORDER BY `hNAME` ASC');
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['hID']}'";
        if ($selected == $r['hID'] || $first == 0)
        {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['hNAME']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the houses in the game to let the user select one.<br />
 * However, the values in the list box return the house's maximum will value instead of its ID.
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the house which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first house alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function house2_dropdown(string $ddname = 'house', int $selected = -1): string
{
    global $db;
    $ret = "<select name='$ddname' type='dropdown'>";
    $q =
            $db->query(
                'SELECT `hWILL`, `hNAME`
    				 FROM houses
    				 ORDER BY `hNAME` ASC');
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['hWILL']}'";
        if ($selected == $r['hWILL'] || $first == 0)
        {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['hNAME']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the courses in the game to let the user select one.
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the course which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first course alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function course_dropdown(string $ddname = 'course', int $selected = -1): string
{
    global $db;
    $ret = "<select name='$ddname' type='dropdown'>";
    $q =
            $db->query(
                'SELECT `crID`, `crNAME`
    				 FROM `courses`
    				 ORDER BY `crNAME` ASC');
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['crID']}'";
        if ($selected == $r['crID'] || $first == 0)
        {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['crNAME']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the crimes in the game to let the user select one.
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the crime which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first crime alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function crime_dropdown(string $ddname = 'crime', int $selected = -1): string
{
    global $db;
    $ret = "<select name='$ddname' type='dropdown'>";
    $q =
            $db->query(
                'SELECT `crimeID`, `crimeNAME`
    				 FROM `crimes`
    				 ORDER BY `crimeNAME` ASC');
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['crimeID']}'";
        if ($selected == $r['crimeID'] || $first == 0)
        {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['crimeNAME']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the crime groups in the game to let the user select one.
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the crime group which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first crime group alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function crimegroup_dropdown(string $ddname = 'crimegroup',
                             int    $selected = -1): string
{
    global $db;
    $ret = "<select name='$ddname' type='dropdown'>";
    $q =
            $db->query(
                'SELECT `cgID`, `cgNAME`
    				 FROM `crimegroups`
    				 ORDER BY `cgNAME` ASC');
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['cgID']}'";
        if ($selected == $r['cgID'] || $first == 0)
        {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['cgNAME']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Sends a user an event, given their ID and the text.
 * @param int $userid The user ID to be sent the event
 * @param string $text The event's text. This should be fully sanitized for HTML, but not pre-escaped for database insertion.
 * @return int 1
 */
function event_add(int $userid, string $text): int
{
    global $db;
    $text = $db->escape($text);
    $db->query(
            "INSERT INTO `events`
             VALUES(NULL, $userid, " . time() . ", 0, '$text')");
    $db->query(
            "UPDATE `users`
             SET `new_events` = `new_events` + 1
             WHERE `userid` = {$userid}");
    return 1;
}

/**
 * Internal function: used to see if a user is due to level up, and if so, perform that levelup.
 */
function check_level(): void
{
    global $db, $ir, $userid;
    $ir['exp_needed'] =
            (int) (($ir['level'] + 1) * ($ir['level'] + 1)
                    * ($ir['level'] + 1) * 2.2);
    if ($ir['exp'] >= $ir['exp_needed'])
    {
        $expu = $ir['exp'] - $ir['exp_needed'];
        $ir['level'] += 1;
        $ir['exp'] = $expu;
        $ir['energy'] += 2;
        $ir['brave'] += 2;
        $ir['maxenergy'] += 2;
        $ir['maxbrave'] += 2;
        $ir['hp'] += 50;
        $ir['maxhp'] += 50;
        $ir['exp_needed'] =
                (int) (($ir['level'] + 1) * ($ir['level'] + 1)
                        * ($ir['level'] + 1) * 2.2);
        $db->query(
                "UPDATE `users`
                 SET `level` = `level` + 1, exp = {$expu},
                 `energy` = `energy` + 2, `brave` = `brave` + 2,
                 `maxenergy` = `maxenergy` + 2, `maxbrave` = `maxbrave` + 2,
                 `hp` = `hp` + 50, `maxhp` = `maxhp` + 50
                 WHERE `userid` = {$userid}");
    }
}

/**
 * Get the "rank" a user has for a particular stat - if the return is n, then the user has the nth-highest value for that stat.
 * @param int|float $stat The value of the current user's stat.
 * @param string $mykey The stat to be ranked in. Must be a valid column name in the userstats table
 * @return int The user's rank in the stat
 */
function get_rank(int|float $stat, string $mykey): int
{
    global $db, $userid;
    $q =
            $db->query(
                    "SELECT count(`u`.`userid`)
                    FROM `userstats` AS `us`
                    LEFT JOIN `users` AS `u`
                    ON `us`.`userid` = `u`.`userid`
                    WHERE {$mykey} > {$stat}
                    AND `us`.`userid` != {$userid} AND `u`.`user_level` != 0");
    $result = $db->fetch_single($q) + 1;
    $db->free_result($q);
    return $result;
}

/**
 * Give a particular user a particular quantity of some item.
 * @param int $user The user ID who is to be given the item
 * @param int $itemid The item ID which is to be given
 * @param int $qty The item quantity to be given
 * @param int $notid [optional] If specified and greater than zero, prevents the item given's<br />
 * database entry combining with inventory id $notid.
 */
function item_add(int $user, int $itemid, int $qty, int $notid = 0): void
{
    global $db;
    if ($notid > 0)
    {
        $q =
                $db->query(
                        "SELECT `inv_id`
                         FROM `inventory`
                         WHERE `inv_userid` = {$user}
                         AND `inv_itemid` = {$itemid}
                         AND `inv_id` != {$notid}
                         LIMIT 1");
    }
    else
    {
        $q =
                $db->query(
                        "SELECT `inv_id`
                         FROM `inventory`
                         WHERE `inv_userid` = {$user}
                         AND `inv_itemid` = {$itemid}
                         LIMIT 1");
    }
    if ($db->num_rows($q) > 0)
    {
        $r = $db->fetch_row($q);
        $db->query(
                "UPDATE `inventory`
                SET `inv_qty` = `inv_qty` + {$qty}
                WHERE `inv_id` = {$r['inv_id']}");
    }
    else
    {
        $db->query(
                "INSERT INTO `inventory`
                 (`inv_itemid`, `inv_userid`, `inv_qty`)
                 VALUES ({$itemid}, {$user}, {$qty})");
    }
    $db->free_result($q);
}

/**
 * Take away from a particular user a particular quantity of some item.<br />
 * If they don't have enough of that item to be taken, takes away any that they do have.
 * @param int $user The user ID who is to lose the item
 * @param int $itemid The item ID which is to be taken
 * @param int $qty The item quantity to be taken
 */
function item_remove(int $user, int $itemid, int $qty): void
{
    global $db;
    $q =
            $db->query(
                    "SELECT `inv_id`, `inv_qty`
                     FROM `inventory`
                     WHERE `inv_userid` = {$user}
                     AND `inv_itemid` = {$itemid}
                     LIMIT 1");
    if ($db->num_rows($q) > 0)
    {
        $r = $db->fetch_row($q);
        if ($r['inv_qty'] > $qty)
        {
            $db->query(
                    "UPDATE `inventory`
                     SET `inv_qty` = `inv_qty` - {$qty}
                     WHERE `inv_id` = {$r['inv_id']}");
        }
        else
        {
            $db->query(
                    "DELETE FROM `inventory`
            		 WHERE `inv_id` = {$r['inv_id']}");
        }
    }
    $db->free_result($q);
}

/**
 * Constructs a drop-down listbox of all the forums in the game to let the user select one.
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the forum which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first forum alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function forum_dropdown(string $ddname = 'forum', int $selected = -1): string
{
    global $db;
    $ret = "<select name='$ddname' type='dropdown'>";
    $q =
            $db->query(
                'SELECT `ff_id`, `ff_name`
    				 FROM `forum_forums`
    				 ORDER BY `ff_name` ASC');
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['ff_id']}'";
        if ($selected == $r['ff_id'] || $first == 0)
        {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['ff_name']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the forums in the game, except gang forums, to let the user select one.<br />
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the forum which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first forum alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function forum2_dropdown(string $ddname = 'forum', int $selected = -1): string
{
    global $db;
    $ret = "<select name='$ddname' type='dropdown'>";
    $q =
            $db->query(
                    "SELECT `ff_id`, `ff_name`
                     FROM `forum_forums`
                     WHERE `ff_auth` != 'gang'
                     ORDER BY `ff_name` ASC");
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['ff_id']}'";
        if ($selected == $r['ff_id'] || $first == 0)
        {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['ff_name']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Records an action by a member of staff in the central staff log.
 * @param string $text The log's text. This should be fully sanitized for HTML, but not pre-escaped for database insertion.
 */
function stafflog_add(string $text): void
{
    global $db, $ir;
    $IP = $db->escape($_SERVER['REMOTE_ADDR']);
    $text = $db->escape($text);
    $db->query(
            "INSERT INTO `stafflog`
             VALUES(NULL, {$ir['userid']}, " . time() . ", '$text', '$IP')");
}

/**
 * Request that an anti-CSRF verification code be issued for a particular form in the game.
 * @param string $formid A unique string used to identify this form to match up its submission with the right token.
 * @return string The code issued to be added to the form.
 */
function request_csrf_code(string $formid): string
{
    // Generate the token
    $token = md5((string)mt_rand());
    // Insert/Update it
    $issue_time = time();
    $_SESSION["csrf_{$formid}"] =
            ['token' => $token, 'issued' => $issue_time];
    return $token;
}

/**
 * Request that an anti-CSRF verification code be issued for a particular form in the game, and return the HTML to be placed in the form.
 * @param string $formid A unique string used to identify this form to match up its submission with the right token.
 * @return string The HTML for the code issued to be added to the form.
 */
function request_csrf_html(string $formid): string
{
    return "<input type='hidden' name='verf' value='"
            . request_csrf_code($formid) . "' />";
}

/**
 * Check the CSRF code we received against the one that was registered for the form - return false if the request shouldn't be processed...
 * @param string $formid A unique string used to identify this form to match up its submission with the right token.
 * @param string $code The code the user's form input returned.
 * @return bool Whether the user provided a valid code or not
 */
function verify_csrf_code(string $formid, string $code): bool
{
    // Lookup the token entry
    // Is there a token in existence?
    if (!isset($_SESSION["csrf_{$formid}"])
            || !is_array($_SESSION["csrf_{$formid}"]))
    {
        // Obviously verification fails
        return false;
    }
    else
    {
        // From here on out we always want to remove the token when we're done - so don't return immediately
        $verified = false;
        $token = $_SESSION["csrf_{$formid}"];
        // Expiry time on a form?
        $expiry = 900; // hacky lol
        if ($token['issued'] + $expiry > time())
        {
            // It's ok, check the contents
            $verified = ($token['token'] === $code);
        } // don't need an else case - verified = false
        // Remove the token before finishing
        unset($_SESSION["csrf_{$formid}"]);
        return $verified;
    }
}

/**
 * Given a password input given by the user and their actual details,
 * determine whether the password entered was correct.
 *
 * Note that password-salt systems don't require the extra md5() on the $input.
 * This is only here to ensure backwards compatibility - that is,
 * a v2 game can be upgraded to use the password salt system without having
 * previously used it, without resetting every user's password.
 *
 * @param string $input The input password given by the user.
 * 						Should be without slashes.
 * @param string $salt 	The user's unique pass salt
 * @param string $pass	The user's encrypted password
 *
 * @return bool    true for equal, false for not (login failed etc)
 *
 */
function verify_user_password(string $input, string $salt, string $pass): bool
{
    return ($pass === encode_password($input, $salt));
}

/**
 * Given a password and a salt, encode them to the form which is stored in
 * the game's database.
 *
 * @param string $password 		The password to be encoded
 * @param string $salt			The user's unique pass salt
 * @param bool $already_md5	Whether the specified password is already
 * 								a md5 hash. This would be true for legacy
 * 								v2 passwords.
 *
 * @return string	The resulting encoded password.
 */
function encode_password(string $password, string $salt, bool $already_md5 = false): string
{
    if (!$already_md5)
    {
        $password = md5($password);
    }
    return md5($salt . $password);
}

/**
 * Generate a salt to use to secure a user's password
 * from rainbow table attacks.
 *
 * @return string	The generated salt, 8 alphanumeric characters
 */
function generate_pass_salt(): string
{
    return substr(md5((string)microtime(true)), 0, 8);
}

/**
 *
 * @return string The URL of the game.
 */
function determine_game_urlbase(): string
{
    $domain = $_SERVER['HTTP_HOST'];
    $turi = $_SERVER['REQUEST_URI'];
    $turiq = '';
    for ($t = strlen($turi) - 1; $t >= 0; $t--)
    {
        if ($turi[$t] != '/')
        {
            $turiq = $turi[$t] . $turiq;
        }
        else
        {
            break;
        }
    }
    $turiq = '/' . $turiq;
    if ($turiq == '/')
    {
        $domain .= substr($turi, 0, -1);
    }
    else
    {
        $domain .= str_replace($turiq, '', $turi);
    }
    return $domain;
}

/**
 * Check to see if this request was made via XMLHttpRequest.
 * Uses variables supported by most JS frameworks.
 *
 * @return bool Whether the request was made via AJAX or not.
 **/

function is_ajax(): bool
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && is_string($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])
                    === 'xmlhttprequest';
}

/**
 * Get the file size in bytes of a remote file, if we can.
 *
 * @param string $url	The url to the file
 *
 * @return int			The file's size in bytes, or 0 if we could
 * 						not determine its size.
 */

function get_filesize_remote(string $url): int
{
    // Retrieve headers
    if (strlen($url) < 8)
    {
        return 0; // no file
    }
    $is_ssl = false;
    /** @noinspection HttpUrlsUsage */
    if (str_starts_with($url, 'http://'))
    {
        $port = 80;
    }
    elseif (str_starts_with($url, 'https://') && extension_loaded('openssl'))
    {
        $port = 443;
        $is_ssl = true;
    }
    else
    {
        return 0; // bad protocol
    }
    // Break up url
    $url_parts = explode('/', $url);
    $host = $url_parts[2];
    unset($url_parts[2]);
    unset($url_parts[1]);
    unset($url_parts[0]);
    $path = '/' . implode('/', $url_parts);
    if (str_contains($host, ':'))
    {
        $host_parts = explode(':', $host);
        if (count($host_parts) == 2 && ctype_digit($host_parts[1]))
        {
            $port = (int) $host_parts[1];
            $host = $host_parts[0];
        }
        else
        {
            return 0; // malformed host
        }
    }
    $request =
            "HEAD {$path} HTTP/1.1\r\n" . "Host: {$host}\r\n"
                    . "Connection: Close\r\n\r\n";
    $fh = fsockopen(($is_ssl ? 'ssl://' : '') . $host, $port);
    if ($fh === false)
    {
        return 0;
    }
    fwrite($fh, $request);
    $headers = [];
    $total_loaded = 0;
    while (!feof($fh) && $line = fgets($fh, 1024))
    {
        if ($line == "\r\n")
        {
            break;
        }
        if (str_contains($line, ':'))
        {
            [$key, $val] = explode(':', $line, 2);
            $headers[strtolower($key)] = trim($val);
        }
        else
        {
            $headers[] = strtolower($line);
        }
        $total_loaded += strlen($line);
        if ($total_loaded > 50000)
        {
            // Stop loading garbage!
            break;
        }
    }
    fclose($fh);
    if (!isset($headers['content-length']))
    {
        return 0;
    }
    return (int) $headers['content-length'];
}

/**
 * Typecasting the player data array - tasty - to set values to have the expected data types.
 * Ideally, this engine should be updated to use db abstraction from column types.
 * Note: The match() array is not an exhaustive list, simply the types found in the default bundled dbdata.sql.
 * Note: At the time of writing, there are no overlapping column types. Consider that a growing project will likely hit this "gotcha!".
 * @param array $ir
 * @return void
 */
function set_userdata_data_types(array &$ir): void
{
    global $db, $_CONFIG;
    $types_query = $db->query('SELECT COLUMN_NAME AS colName, DATA_TYPE AS dataType FROM information_schema.COLUMNS WHERE COLUMNS.TABLE_SCHEMA = \''.$_CONFIG['database'].'\' AND COLUMNS.TABLE_NAME IN (\'users\', \'userstats\', \'houses\', \'jobs\', \'jobranks\')');
    $data = [];
    while ($row = $db->fetch_row($types_query)) {
        $data[$row['colName']] = match ($row['dataType']) {
            'tinyint', 'bool' => 'bool',
            'smallint', 'int', 'bigint' => 'int',
            'varchar', 'tinytext', 'smalltext', 'text', 'longtext', 'enum' => 'string',
            'float', 'decimal' => 'float',
            default => null,
        };
    }
    foreach ($ir as $column => $value) {
        if (array_key_exists($column, $data) && $data[$column] !== null) {
            settype($ir[$column], $data[$column]);
        }
    }
}

function get_site_settings(): array
{
    global $db;
    $set = [];
    $settq = $db->query('SELECT * FROM `settings`');
    while ($r = $db->fetch_row($settq)) {
        $set[$r['conf_name']] = $r['conf_value'];
        settype($set[$r['conf_name']], $r['data_type']);
    }
    return $set;
}

function userBox(int|string $target_id): string
{
    return $target_id;
}

/**
 * @param string|array $permissions
 * @param int|null $target_id
 * @return bool
 */
function check_access(string|array $permissions, ?int $target_id = null): bool
{
    global $db, $userid;
    // We want an array
    if (is_string($permissions)) {
        $permissions = [$permissions];
    }
    // We're quite permissive with formats allowed in $permissions, turn them back into "true" permission format
    $permissions = array_map(function ($permission) {
        return strtolower(str_replace([' ', '-'], '_', $permission));
    }, $permissions);
    // If target_id isn't provided, use the current user
    $target_id ??= (int)$userid;
    // Get the target's roles
    $get_user_roles = $db->query(
        'SELECT staff_role FROM users_roles WHERE userid = '.$target_id,
    );
    $target_roles = [];
    while ($role = $db->fetch_row($get_user_roles)) {
        $target_roles[] = $role['staff_role'];
    }
    // They don't have any
    if (!$target_roles) {
        return false;
    }
    // Get the corresponding role data
    $get_staff_roles = $db->query(
        'SELECT * FROM staff_roles WHERE id IN ('.implode(',', $target_roles).')',
    );
    $role_permissions = [];
    while ($row = $db->fetch_row($get_staff_roles)) {
        foreach ($row as $key => $value) {
            // id and name aren't permissions
            if (in_array($key, ['id', 'name'])) {
                continue;
            }
            // If the target has the `administrator` permission, grant all accesses
            if ($row['administrator']) {
                $value = true;
            }
            // If we've not already added it, and it's true, add it
            if (!array_key_exists($key, $role_permissions) && $value) {
                $role_permissions[] = $key;
            }
        }
    }
    // Check the given permissions against the roles' combined permissions
    $matches = array_intersect($role_permissions, $permissions);
    // No matches
    if (empty($matches)) {
        return false;
    }
    // No need to exit. Access granted!
    return true;
}

/**
 * @return bool
 */
function is_staff(): bool
{
    global $db, $userid;
    $preliminary = $db->query(
        'SELECT COUNT(*) FROM users_roles WHERE staff_role > 0 AND userid = '.$userid,
    );
    return $db->fetch_single($preliminary) > 0;
}

function get_online_staff(?int $online_cutoff = null): array
{
    global $db;
    $online_cutoff ??= time() - 900;
    $q = $db->query(
        'SELECT u.userid, u.username, u.laston
        FROM users AS u
        INNER JOIN users_roles AS ur ON ur.userid = u.userid
        WHERE ur.staff_role > 0 AND u.laston > ' .$online_cutoff. '
        GROUP BY u.userid
        ORDER BY userid'
    );
    $rows = [];
    while ($r = $db->fetch_row($q)) {
        $rows[] = $r;
    }
    $db->free_result($q);
    return $rows;
}
