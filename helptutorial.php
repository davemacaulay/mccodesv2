<?php
declare(strict_types=1);
/**
 * MCCodes v2 by Dabomstew & ColdBlooded
 * 
 * Repository: https://github.com/davemacaulay/mccodesv2
 * License: MIT License
 */
global $h, $set;
require_once('globals.php');
print
        <<<EOF
<h1>{$set['game_name']} Tutorial</h1>
<br />
<br />
<p>Welcome to the {$set['game_name']} Tutorial, we hope that this guide will help you to better
understand the game.<p>
<br />
<p>In {$set['game_name']}, you are free to choose your own path. You can protect the weak, or
exploit their weakness. Spend your money to help your friends, or horde it, they can take
care of themselves. Buy a gang and become the most respected group of players in the land.
Declare war on an enemy, or an innocent bystander, the choice is yours.</p>

<br />
<h3>Guide</h3>
<a href="#general">General</a>
<br />
<a href="#explore">Explore</a>
<br />
<a href="#stat">Training</a>
<br />
<a href="#fight">Attacking</a>
<br />
<a href="#preferences">Preferences</a>

<br />
<a href="#gangs">Gangs</a>

<a name="general"><h4>General</h4></a>
<br />
<p><u>Personal Info and Status Bars</u></p>
<p>In the top right corner of the screen is your personal information. This shows your current
name, amount of cash, level, and number of crystals. To the right of your personal info is your
status bars. These show your current energy, will, brave, experience, and health.
1)Energy is used for training and attacking.Refills 8% every 5 minutes, or 17% every 5 minutes for donators,
2)Will determines the effectiveness of your training,
3)Brave is used to do crimes, different crimes take more brave to do, these crimes are harder to succeed at
so be careful not to try them to soon.
4)Experience shows how close you are to leveling up.
5)Health shows how much health you have remaining. You lose this if you're hit in a fight.
<br />
<p><u>Stats:</u></p>
<p>There are 5 types of stats used on {$set['game_name']}: Strength, Agility, Guard, Labor, and IQ.
1)Strength determines how much damage you do in battle,
2)Agiligty is used to determine your hit rate in battle,
3)Guard reduces the amount of damage done to you when you are hit,
4)Labor and IQ are used to what jobs you are able to do.</p>
<br />

<p><u>Sidebar</u></p>
<p>The sidebar shows much of the things you are able to do in MC.</p>
<ol>
<li>The Home link will bring you to your homepage.</li>
<li>Items will bring you to your item page.</li>
<li>Explore brings up a list of places that you can go on MC.</li>
<li>Events displays the number of new events, and when clicked tells you what they are.</li>
<li>Mailbox will display any new messages you have received.</li>
<li>Gym is where you go to train your fighting stats.</li>

<li>Crimes will let you select which crime you want to do.</li>
<li>Your Job brings you to the Job screen.</li>
<li>Local School will let you take education classes.</li>
<li>MonoPaper displays recent updates to the game.</li>
<li>Forums will bring you to the official Mono Country Forums.</li>
<li>Search allows you to find other players by their name or their ID.</li>
<li>Preferences will bring you the the Preferences page.</li>
<li>Player Report is used to report players that have broken the rules of the game.</li>
<li>My Profile shows you your profile.</li>

</ol>
<br />
<a name="explore"><h4>Exploring</h4></a>
<ol>
<li>Shops: Here you can buy everything from med supplies, to weapons to make your enemy need meds.</li>
<li>Item Market: You can go and see what people are selling here.</li>
<li>Crystal Market: Come here to buy or sell crystals.</li>
<li>Travel Agency: This will bring you to new towns with different equipment, keep in mind you can only fight someone in your town.</li>
<li>Estate Agent:Go here to buy yourself a new house.</li>
<li>City Bank: Here you can deposit your money. You must first open an account for 50K, and pay a fee for depositing.</li>

<li>Gangs: See a list of all the gangs in {$set['game_name']}.</li>
<li>Gang Wars: A list of all current wars between gangs.</li>
<li>Federal Jail: Where all the suspected cheaters on the game go. If you're in here without cheating, ???</li>
<li>Slot Machines: Go here to make your fortune, or lose your shirt.</li>
<li>User List: Shows a list of all the players on the game.</li>
<li>{$set['game_name']} Staff: A list of all the staff on {$set['game_name']}.</li>
<li>Hall of Fame: Shows the top players in various fields.</li>
<li>Country Stats: A list of various statistics about the game.</li>
<li>Users Online: Shows which players have acted last.</li>

<li>Crystal temple: Trade your crystals for various things.</li>
</ol>
<br />
<a name="stat"><h4>Training</h4></a>
<br />
<p><u>Gym</u></p>
<p>To use the gym, type in the number of times you want to train, select the stat to train and click ok. The next screen will tell
you how much of that stat you gained, and what your total in that stat is.</p>
<br />
<p><u>Crimes</u></p>
<p>Go to the crime screen and select the crime you want to do. Remember that trying a crime that is to hard may land you in jail,
and lose the experience you've worked so hard to get.</p>
<br />

<p><u>School</u></p>
<p>School offers courses that will raise your stats over a certain period of time</p>
<br />
<p><u>Your Job</u></p>
<p>A job will provide you with money at 5:00PM every day, as well as raising your job stats everyday. Some jobs have requirements before
you can do them, so make sure to keep an eye out for that.</p>
<br />
<p><u>Attacking</u></p>
<p>Attacking will gain you experience when you win, but you lose experience if you lose. The amount of experience depends on the comparative
strength of your enemy, if they are much weaker, you won't get much experience</p>
<br />
<a name="fight"><h4>Attacking</h4>

<br />
<p>Attacking is a good way to get experience, and exert your superiority over those weaker than you. In order to attack you need 50% energy,
and should have a weapon. When you win a fight you will get a percentage of experience depending on how much stronger you are compared to the
person you are attacking. Make sure that you really want to fight the person, because once you start you can't stop until one of you loses.
When you start a fight, you will have the option of using any weapon that you currently have in your items page.<br />
<a name="gangs"><h4>Gangs</h4>
<br />
<p>Gangs are a group of players that band together to work for a common purpose, granted this may be robbing a bank, or taking down the losers
in a rival gang. Gangs cost \$500K to create, and once you buy it, you are the president of your gang. Your faction will initially be able to hold
5 members, but will be able to upgrade to more as time goes on. The President will be able to assign a Vice-President to the gang. Gangs are able to
do Organised Crimes for money and respect.The president can also select to go to war with another gang. One should be careful about doing this though,
as it may come back to haunt you.</p>
<a name="preferences"><h4>Preferences</h4></a>
<br />
<p><u>Sex Change</u></p>
<p>This will allow you to change from male to female and back for free, try finding that deal in the real world!</p>
<br />
<p><u>Password Change</u></p>

<p>The place to change your password, you should do this often to avoid having someone use your account if they crack your password</p>
<p><u>Name Change</u></p>
<p>Go here to change your name, remember that your ID stays the same, so you can't use this to avoid consequences of your actions</p>
<br />
<p><u>Change Display Pic</u></p>
<p>Here you can change the display picture in your profile, it will automatically refit the picture to 150x150. Don't post anything offensive
or you may be federal jailed.</p>
<br />
EOF;
$h->endpage();
