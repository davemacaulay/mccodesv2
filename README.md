
# MCCodes 2.0

MCCodes v2 is now Open Source! You may use this under the terms of the MIT `LICENSE`. This is provided without warranty or support, you can open PRs and issues for others to fix if you desire.

Introduction
------------
New to MCCodes? Or simply wanting reference on a simple task? That's what this beginner's manual is for. This manual contains general information to help you run your game.

Contents
--------   
* [1\. Installation](#1p0)
  * [1.1 Basic Installation](#1p1)
  * [1.2 Cronjobs](#1p2)
  * [1.3 PayPal Configuration](#1p3)
* [2\. Basic Usage & Tips](#2p0)
  * [2.1 Setting up Will Potions](#2p1)
  * [2.2 Tips for running a game](#2p2)

<a id="1p0"></a>1\. Installation
----------------   
### <a id="1p1"></a> 1.1 Basic Installation

So you have downloaded MCCodes 2.0, and are ready to set up. First, check these things:

* You have hosting which is at the root level of a domain or subdomain (aka not in a subdirectory)
* You have FTP (or some other file uploader) and MySQL access
* You have a MySQL database (preferably empty to avoid table name clashes) and a MySQL user with permissions to use that database (the specific permissions are up to you, although you'll need at least SELECT, INSERT, UPDATE, DELETE, CREATE  TABLE, DROP TABLE and TRUNCATE).

Got all the above? Good. Upload all the files and directories in the upload folder of the MCC2 .zip archive to your  
webserver. Normally, you will be uploading to either the public\_html folder, or the root level folder. A good way to check this is to upload one file then try to access it through your domain and see if it works. If it doesn't, you've got the path wrong. Once done, run install.php. First, your server diagnostics will be checked. If one of these tests fails, you will need to correct it before moving on. Next, you need to fill in your database info, and a few basic  
settings. Make sure the PayPal email you specify is the main email of the PayPal account, and that the account is at least Premier. Otherwise, the basic settings are up to you. After you submit this form, the installer should hopefully run, and insert all the tables and entries into SQL you need. If not, go back and check your config. Now the basic installation is complete, and you're ready to move on to setting up the Cron Jobs.

* * *   
### <a id="1p2"></a> 1.2 Cronjobs

The Cronjobs are the thing most people are confused on, or they simply do not work for them. However, if you follow the below instructions correctly, your crons should work fine, unless your server is configured differently to a normal cPanel server. If you are not using cPanel, you will need Shell access to your server (through SSH or SFTP).

**If you are using cPanel, follow these instructions:**
* Login to cPanel (obviously)
* Look for Cron Jobs on the first page that comes up. If you cannot find them, you probably need to upgrade your hosting plan to enable them, or they may be located elsewhere. Either way, once you have successfully found Cron Jobs, click it.
* Click Advanced (Unix Style)
* Look at the first line of cron jobs given to you by the installer. Now visualize it split into six sections, split by  
  spaces (" "). But, do not split the word php and the path by the space, or else you will end up with seven sections,  and this is wrong. Now, begin typing in the sections into the row of empty boxes available, beginning with the Minute box for the first section, then the Hour box for the second section, then the Day box for the third section, then the Month box for the fourth section, then the Weekday box for the fifth section, and finally the Command box should contain the text "php /path/to/yourgame/blablabla" left over at the end of the line.
* But, you ask, how can I enter the rest of the crons? There are no more blank boxes, are there? Well, no, not yet. We need to make more appear. To do this, click Commit Changes. It should now say **Cron Updated!**. Click Go Back.  Depending on your server config, it may go automatically back to the Crons page, complete with new blank boxes, or you may have to click **Advanced (Unix Style)** again. Either way, make your way back to the Cron input page.
* Repeat the above two steps for the three remaining cron lines.
* Something to note: If you leave an address to send cron output to in the **Please enter an email address where the cron output will be sent:** field, that address will get bombed from the constantly running MCC2 crons (you'd be  getting about 73 emails per hour). If you erase this and click Commit Changes, it will no longer send any email to any address with the output, which is usually desirable, unless you are trying to debug your crons.
* The crons should work fine now, if not check your setup. They should look somewhat like this:

  **Minute**
  **Hour**
  **Day**
  **Month**
  **Weekday**
  **Command**

  Except, obviously the path and code will be different. But this is a good guide to see if you have the basics right.

**If you do not have cPanel but do have Shell access, follow these instructions:**

* Copy the 4 cron lines given to you into a file. Save this file as crons.txt.
* Login to your shell access account.
* Run the command: **crontab -l**. This will give you a list of crons currently on your account. You will need to copy these into crons.txt, making sure you have a new line for each cron.
* Upload crons.txt onto your server, making sure you know the server path to it (if you do not, you can ask your host).
* Go back to your shell access account and run this command: **crontab /path/to/crons.txt**, replacing  
  /path/to/crons.txt with the server path of crons.txt, which you should have established last step.
* If everything goes to plan, you should now have working crons.

* * *   
### <a id="1p3"></a> 1.3 PayPal Configuration.

MCC2 uses several PayPal devices to ensure that donations are credited securely and accurately. However, some of these devices must be enabled in your PayPal account before MCC2 can use them. If these steps are not followed, your donation system will not work properly. Follow these steps to get it up and working:

* Login to your PayPal account.
* Up at the top menu, click Profile (it's next to Resolution Center)
* First, you will want to click Website Payment Preferences.
* If "Auto Return" is set to Off, set it to On. If **Return URL:** is blank, enter in *
  *https://yourgame.com/donatordone.php**, replacing `yourgame.com` with your game's real domain or subdomain.
* If "Payment Data Transfer" is set to "Off", set it to "On".
* If "Block Non-encrypted Website Payment" is set to "On", set it to "Off".
* Scroll down and click Save (the rest of the settings do not affect the DP system).
* You should be redirected back to Profile, with the message **You have successfully saved your preferences.** Next up to click is "Instant Payment Notification Preferences". Click it.
* If Instant Payment Notification (IPN) is listed as "On" on this page, you can ignore the next step. If not, click  
  Edit.
* Click the checkbox next to "Instant Payment Notification integrates PayPal payment notification and authentication with your website's back-end operations...". Also, enter in **https://yourgame.com/ipn_donator.php** into the Notification URL box, replacing `yourgame.com` with your game's real domain or subdomain. Click Save.
* Your donation system should now be up and running. Test it. If it doesn't work (give you the money and credit the account), recheck your PayPal config to make sure you have not missed anything.

**NB:** Will Potion crediting will still not work until you carry out the instructions in 2.1.

* * *   
<a id="2p0"></a>2\. Basic Usage & Tips
----------------------   
### <a id="2p1"></a> 2.1 Setting up Will Potions

So you've installed MCC2, and are ready to use it. Log in to your account (you specified the details during  
installation) and take a look around. On the left side should be a menu, containing the major functions of MCC2. First off, you will want to set up your Will Potion crediting. Click the **Staff Panel** link to be taken to the MCC2 Staff Panel. First, you need an item type to put the Will Potion item into. Scroll down and click **\> Add Item Type**. Put in a name (suggestions: Special, Donation Item), then click the Add Item Type button to add the type. Now, scroll down to **> Create New Item** and click it. The Item Name can be anything you like, although it is advisable to make it something will-related, such as Will Potion or Will Bottle. Again, you can choose the Description. The Item Type you made before should be automatically selected, since it should be the only item type so far. Make the item unbuyable, unless you want to sell it in shops. If you do wish to sell it in shops, you will want to set a buy and sell price, otherwise just get these to 0. Now for the effect. Turn Effect 1 On with the appropriate radio button. For the Stat, choose Will. Direction should be left on Increase. For Amount, put in 100, and for the drop-down box next to the amount, open it and choose Percent. Summary: This item will increase Will by 100%, as you specified. You're done configuring the Will Potion item - scroll down and click "Add Item To Game" to add it. Now to link it to your Will Potion payment system. Click **Basic Settings** on the left-hand menu. Now go down to the **Will Potion Item** setting. If your Will Potion is not already selected for some reason, select it in the drop-down box. Now press Update Settings to save your Will Potion item as the item to credit when someone buys a Will Potion pack. Congratulations, your first item is complete.

* * *   
### <a id="2p2"></a> 2.2 Tips for running a game

* **Attempt to customize your game as much as possible.** You have tools to customize a lot of things in the game, from the crimes to the houses. Don't just copy other games, try to go for unique storylines and names in order to get people hooked.
* **Don't just go for the vanilla look.** Editing the skin is highly advisable - you should, at least, add a new logo  
  for your game. People won't be interested in your game if they have seen the layout 100x before. The layout included in MCC2 is just to start you off while you invent your own unique style.
* **Features == Good.** Your game can never have enough (good) features. MCC2 may come with a lot feature-wise, but eventually, you should be adding more. Can't code what you want? There are lots of mod sellers out there who have done the coding for you, and charge a small fee to add their features to your game.
* **Don't put down other games publicly.** Putting out public announcements about other games doing something bad to you may actually advertise them to players of your game, and they may join up and like that game better than yours, thus losing you money.
* **Avoid the players influencing you on everything you do.** While the public should have their say, it is YOUR game, not theirs. Something a player tells you to add may end up negatively affecting your game for a long time. Remember, there are millions of potential players out there, don't make huge sacrifices just to keep a few players playing your game.
* **Advertising is Advisable.** As well as advertising your game on various ad chains, you should also consider  
  displaying ads on your own site. They are an easy source of money, and simple to maintain. As long as the ads do not dominate your game, players should be fine with them.
* **Someone else has probably had the same problem as you.** It is recommended you join [MakeWebGames](http://makewebgames.com), which is has an active community of MC Codes users. From there, you will  
  be able to get support as well as download free user mods. Be careful though - 1.0 mods do not work on 2.0. If you don't wish to join MWG, you can always talk to other game owners you know about any problems you have.
