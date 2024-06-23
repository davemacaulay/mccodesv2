-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `a_text` text NOT NULL,
  `a_time` int(11) NOT NULL default '0'
) ENGINE=MyISAM ;

--
-- Dumping data for table `announcements`
--


-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `appID` int(11) NOT NULL auto_increment,
  `appUSER` int(11) NOT NULL default '0',
  `appGANG` int(11) NOT NULL default '0',
  `appTEXT` text NOT NULL,
  PRIMARY KEY  (`appID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `applications`
--


-- --------------------------------------------------------

--
-- Table structure for table `attacklogs`
--

CREATE TABLE `attacklogs` (
  `log_id` int(11) NOT NULL auto_increment,
  `attacker` int(11) NOT NULL default '0',
  `attacked` int(11) NOT NULL default '0',
  `result` enum('won','lost') NOT NULL default 'won',
  `time` int(11) NOT NULL default '0',
  `stole` int(11) NOT NULL default '0',
  `attacklog` longtext NOT NULL,
  PRIMARY KEY  (`log_id`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `attacklogs`
--


-- --------------------------------------------------------

--
-- Table structure for table `bankxferlogs`
--

CREATE TABLE `bankxferlogs` (
  `cxID` int(11) NOT NULL auto_increment,
  `cxFROM` int(11) NOT NULL default '0',
  `cxTO` int(11) NOT NULL default '0',
  `cxAMOUNT` int(11) NOT NULL default '0',
  `cxTIME` int(11) NOT NULL default '0',
  `cxFROMIP` varchar(15) NOT NULL default '127.0.0.1',
  `cxTOIP` varchar(15) NOT NULL default '127.0.0.1',
  `cxBANK` enum('bank','cyber') NOT NULL default 'bank',
  PRIMARY KEY  (`cxID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `bankxferlogs`
--


-- --------------------------------------------------------

--
-- Table structure for table `blacklist`
--

CREATE TABLE `blacklist` (
  `bl_ID` int(11) NOT NULL auto_increment,
  `bl_ADDER` int(11) NOT NULL default '0',
  `bl_ADDED` int(11) NOT NULL default '0',
  `bl_COMMENT` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`bl_ID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `blacklist`
--


-- --------------------------------------------------------

--
-- Table structure for table `cashxferlogs`
--

CREATE TABLE `cashxferlogs` (
  `cxID` int(11) NOT NULL auto_increment,
  `cxFROM` int(11) NOT NULL default '0',
  `cxTO` int(11) NOT NULL default '0',
  `cxAMOUNT` int(11) NOT NULL default '0',
  `cxTIME` int(11) NOT NULL default '0',
  `cxFROMIP` varchar(15) NOT NULL default '127.0.0.1',
  `cxTOIP` varchar(15) NOT NULL default '127.0.0.1',
  PRIMARY KEY  (`cxID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `cashxferlogs`
--


-- --------------------------------------------------------

--
-- Table structure for table `challengebots`
--

CREATE TABLE `challengebots` (
  `cb_npcid` int(11) NOT NULL default '0',
  `cb_money` int(11) NOT NULL default '0'
) ENGINE=MyISAM ;

--
-- Dumping data for table `challengebots`
--


-- --------------------------------------------------------

--
-- Table structure for table `challengesbeaten`
--

CREATE TABLE `challengesbeaten` (
  `userid` int(11) NOT NULL default '0',
  `npcid` int(11) NOT NULL default '0'
) ENGINE=MyISAM ;

--
-- Dumping data for table `challengesbeaten`
--


-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `cityid` int(11) NOT NULL auto_increment,
  `cityname` varchar(255) NOT NULL default '',
  `citydesc` longtext NOT NULL,
  `cityminlevel` int(11) NOT NULL default '0',
  PRIMARY KEY  (`cityid`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`cityid`, `cityname`, `citydesc`, `cityminlevel`) VALUES
(1, 'Default City', 'A standard city added to start you off', 1);

-- --------------------------------------------------------

--
-- Table structure for table `contactlist`
--

CREATE TABLE `contactlist` (
  `cl_ID` int(11) NOT NULL auto_increment,
  `cl_ADDER` int(11) NOT NULL default '0',
  `cl_ADDED` int(11) NOT NULL default '0',
  PRIMARY KEY  (`cl_ID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `contactlist`
--


-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `crID` int(11) NOT NULL auto_increment,
  `crNAME` varchar(255) NOT NULL default '',
  `crDESC` text NOT NULL,
  `crCOST` int(11) NOT NULL default '0',
  `crENERGY` int(11) NOT NULL default '0',
  `crDAYS` int(11) NOT NULL default '0',
  `crSTR` int(11) NOT NULL default '0',
  `crGUARD` int(11) NOT NULL default '0',
  `crLABOUR` int(11) NOT NULL default '0',
  `crAGIL` int(11) NOT NULL default '0',
  `crIQ` int(11) NOT NULL default '0',
  PRIMARY KEY  (`crID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `courses`
--


-- --------------------------------------------------------

--
-- Table structure for table `coursesdone`
--

CREATE TABLE `coursesdone` (
  `userid` int(11) NOT NULL default '0',
  `courseid` int(11) NOT NULL default '0'
) ENGINE=MyISAM ;

--
-- Dumping data for table `coursesdone`
--


-- --------------------------------------------------------

--
-- Table structure for table `crimegroups`
--

CREATE TABLE `crimegroups` (
  `cgID` int(11) NOT NULL auto_increment,
  `cgNAME` varchar(255) NOT NULL default '',
  `cgORDER` int(11) NOT NULL default '0',
  PRIMARY KEY  (`cgID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `crimegroups`
--


-- --------------------------------------------------------

--
-- Table structure for table `crimes`
--

CREATE TABLE `crimes` (
  `crimeID` int(11) NOT NULL auto_increment,
  `crimeNAME` varchar(255) NOT NULL default '',
  `crimeBRAVE` int(11) NOT NULL default '0',
  `crimePERCFORM` text NOT NULL,
  `crimeSUCCESSMUNY` int(11) NOT NULL default '0',
  `crimeSUCCESSCRYS` int(11) NOT NULL default '0',
  `crimeSUCCESSITEM` int(11) NOT NULL default '0',
  `crimeGROUP` int(11) NOT NULL default '0',
  `crimeITEXT` text NOT NULL,
  `crimeSTEXT` text NOT NULL,
  `crimeFTEXT` text NOT NULL,
  `crimeJTEXT` text NOT NULL,
  `crimeJAILTIME` int(10) NOT NULL default '0',
  `crimeJREASON` varchar(255) NOT NULL default '',
  `crimeXP` int(11) NOT NULL default '0',
  PRIMARY KEY  (`crimeID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `crimes`
--


-- --------------------------------------------------------

--
-- Table structure for table `crystalmarket`
--

CREATE TABLE `crystalmarket` (
  `cmID` int(11) NOT NULL auto_increment,
  `cmQTY` int(11) NOT NULL default '0',
  `cmADDER` int(11) NOT NULL default '0',
  `cmPRICE` int(11) NOT NULL default '0',
  PRIMARY KEY  (`cmID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `crystalmarket`
--


-- --------------------------------------------------------

--
-- Table structure for table `crystalxferlogs`
--

CREATE TABLE `crystalxferlogs` (
  `cxID` int(11) NOT NULL auto_increment,
  `cxFROM` int(11) NOT NULL default '0',
  `cxTO` int(11) NOT NULL default '0',
  `cxAMOUNT` int(11) NOT NULL default '0',
  `cxTIME` int(11) NOT NULL default '0',
  `cxFROMIP` varchar(15) NOT NULL default '127.0.0.1',
  `cxTOIP` varchar(15) NOT NULL default '127.0.0.1',
  PRIMARY KEY  (`cxID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `crystalxferlogs`
--


-- --------------------------------------------------------

--
-- Table structure for table `dps_accepted`
--

CREATE TABLE `dps_accepted` (
  `dpID` int(11) NOT NULL auto_increment,
  `dpBUYER` int(11) NOT NULL default '0',
  `dpFOR` int(11) NOT NULL default '0',
  `dpTYPE` varchar(255) NOT NULL default '',
  `dpTIME` int(11) NOT NULL default '0',
  `dpTXN` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`dpID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `dps_accepted`
--


-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `evID` int(11) NOT NULL auto_increment,
  `evUSER` int(11) NOT NULL default '0',
  `evTIME` int(11) NOT NULL default '0',
  `evREAD` int(11) NOT NULL default '0',
  `evTEXT` text NOT NULL,
  PRIMARY KEY  (`evID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `events`
--


-- --------------------------------------------------------

--
-- Table structure for table `fedjail`
--

CREATE TABLE `fedjail` (
  `fed_id` int(11) NOT NULL auto_increment,
  `fed_userid` int(11) NOT NULL default '0',
  `fed_days` int(11) NOT NULL default '0',
  `fed_jailedby` int(11) NOT NULL default '0',
  `fed_reason` text NOT NULL,
  PRIMARY KEY  (`fed_id`),
  UNIQUE (`fed_userid`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `fedjail`
--


-- --------------------------------------------------------

--
-- Table structure for table `forum_forums`
--

CREATE TABLE IF NOT EXISTS `forum_forums` (
  `ff_id` int(11) NOT NULL auto_increment,
  `ff_name` varchar(255) NOT NULL default '',
  `ff_desc` varchar(255) NOT NULL default '',
  `ff_posts` int(11) NOT NULL default '0',
  `ff_topics` int(11) NOT NULL default '0',
  `ff_lp_time` int(11) NOT NULL default '0',
  `ff_lp_poster_id` int(11) NOT NULL default '0',
  `ff_lp_poster_name` text NOT NULL,
  `ff_lp_t_id` int(11) NOT NULL default '0',
  `ff_lp_t_name` varchar(255) NOT NULL default '',
  `ff_auth` enum('public','gang','staff') NOT NULL default 'public',
  `ff_owner` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ff_id`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `forum_forums`
--


-- --------------------------------------------------------

--
-- Table structure for table `forum_posts`
--

CREATE TABLE IF NOT EXISTS `forum_posts` (
  `fp_id` int(11) NOT NULL auto_increment,
  `fp_topic_id` int(11) NOT NULL default '0',
  `fp_forum_id` int(11) NOT NULL default '0',
  `fp_poster_id` int(11) NOT NULL default '0',
  `fp_poster_name` text NOT NULL,
  `fp_time` int(11) NOT NULL default '0',
  `fp_subject` varchar(255) NOT NULL default '',
  `fp_text` text NOT NULL,
  `fp_editor_id` int(11) NOT NULL default '0',
  `fp_editor_name` text NOT NULL,
  `fp_editor_time` int(11) NOT NULL default '0',
  `fp_edit_count` int(11) NOT NULL default '0',
  PRIMARY KEY  (`fp_id`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `forum_posts`
--


-- --------------------------------------------------------

--
-- Table structure for table `forum_topics`
--

CREATE TABLE IF NOT EXISTS `forum_topics` (
  `ft_id` int(11) NOT NULL auto_increment,
  `ft_forum_id` int(11) NOT NULL default '0',
  `ft_name` varchar(150) NOT NULL default '',
  `ft_desc` varchar(255) NOT NULL default '',
  `ft_posts` int(11) NOT NULL default '0',
  `ft_owner_id` int(11) NOT NULL default '0',
  `ft_owner_name` text NOT NULL,
  `ft_start_time` int(11) NOT NULL default '0',
  `ft_last_id` int(11) NOT NULL default '0',
  `ft_last_name` text NOT NULL,
  `ft_last_time` int(11) NOT NULL default '0',
  `ft_pinned` tinyint(4) NOT NULL default '0',
  `ft_locked` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`ft_id`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `forum_topics`
--


-- --------------------------------------------------------

--
-- Table structure for table `friendslist`
--

CREATE TABLE `friendslist` (
  `fl_ID` int(11) NOT NULL auto_increment,
  `fl_ADDER` int(11) NOT NULL default '0',
  `fl_ADDED` int(11) NOT NULL default '0',
  `fl_COMMENT` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`fl_ID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `friendslist`
--


-- --------------------------------------------------------

--
-- Table structure for table `gangevents`
--

CREATE TABLE `gangevents` (
  `gevID` int(11) NOT NULL auto_increment,
  `gevGANG` int(11) NOT NULL default '0',
  `gevTIME` int(11) NOT NULL default '0',
  `gevTEXT` text NOT NULL,
  PRIMARY KEY  (`gevID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `gangevents`
--


-- --------------------------------------------------------

--
-- Table structure for table `gangs`
--

CREATE TABLE `gangs` (
  `gangID` int(11) NOT NULL auto_increment,
  `gangNAME` varchar(255) NOT NULL default '',
  `gangDESC` text NOT NULL,
  `gangPREF` varchar(12) NOT NULL default '',
  `gangSUFF` varchar(12) NOT NULL default '',
  `gangMONEY` int(11) NOT NULL default '0',
  `gangCRYSTALS` int(11) NOT NULL default '0',
  `gangRESPECT` int(11) NOT NULL default '0',
  `gangPRESIDENT` int(11) NOT NULL default '0',
  `gangVICEPRES` int(11) NOT NULL default '0',
  `gangCAPACITY` int(11) NOT NULL default '0',
  `gangCRIME` int(11) NOT NULL default '0',
  `gangCHOURS` int(11) NOT NULL default '0',
  `gangAMENT` longtext NOT NULL,
  PRIMARY KEY  (`gangID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `gangs`
--


-- --------------------------------------------------------

--
-- Table structure for table `gangwars`
--

CREATE TABLE `gangwars` (
  `warID` int(11) NOT NULL auto_increment,
  `warDECLARER` int(11) NOT NULL default '0',
  `warDECLARED` int(11) NOT NULL default '0',
  `warTIME` int(11) NOT NULL default '0',
  PRIMARY KEY  (`warID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `gangwars`
--


-- --------------------------------------------------------

--
-- Table structure for table `houses`
--

CREATE TABLE `houses` (
  `hID` int(11) NOT NULL auto_increment,
  `hNAME` varchar(255) NOT NULL default '',
  `hPRICE` int(11) NOT NULL default '0',
  `hWILL` int(11) NOT NULL default '0',
  PRIMARY KEY  (`hID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `houses`
--

INSERT INTO `houses` (`hID`, `hNAME`, `hPRICE`, `hWILL`) VALUES
(1, 'Default House', 0, 100);

-- --------------------------------------------------------

--
-- Table structure for table `imarketaddlogs`
--

CREATE TABLE `imarketaddlogs` (
  `imaID` int(11) NOT NULL auto_increment,
  `imaITEM` int(11) NOT NULL default '0',
  `imaPRICE` int(11) NOT NULL default '0',
  `imaINVID` int(11) NOT NULL default '0',
  `imaADDER` int(11) NOT NULL default '0',
  `imaTIME` int(11) NOT NULL default '0',
  `imaCONTENT` text NOT NULL,
  PRIMARY KEY  (`imaID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `imarketaddlogs`
--


-- --------------------------------------------------------

--
-- Table structure for table `imbuylogs`
--

CREATE TABLE `imbuylogs` (
  `imbID` int(11) NOT NULL auto_increment,
  `imbITEM` int(11) NOT NULL default '0',
  `imbADDER` int(11) NOT NULL default '0',
  `imbBUYER` int(11) NOT NULL default '0',
  `imbPRICE` int(11) NOT NULL default '0',
  `imbIMID` int(11) NOT NULL default '0',
  `imbINVID` int(11) NOT NULL default '0',
  `imbTIME` int(11) NOT NULL default '0',
  `imbCONTENT` text NOT NULL,
  PRIMARY KEY  (`imbID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `imbuylogs`
--


-- --------------------------------------------------------

--
-- Table structure for table `imremovelogs`
--

CREATE TABLE `imremovelogs` (
  `imrID` int(11) NOT NULL auto_increment,
  `imrITEM` int(11) NOT NULL default '0',
  `imrADDER` int(11) NOT NULL default '0',
  `imrREMOVER` int(11) NOT NULL default '0',
  `imrIMID` int(11) NOT NULL default '0',
  `imrINVID` int(11) NOT NULL default '0',
  `imrTIME` int(11) NOT NULL default '0',
  `imrCONTENT` text NOT NULL,
  PRIMARY KEY  (`imrID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `imremovelogs`
--


-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `inv_id` int(11) NOT NULL auto_increment,
  `inv_itemid` int(11) NOT NULL default '0',
  `inv_userid` int(11) NOT NULL default '0',
  `inv_qty` int(11) NOT NULL default '0',
  PRIMARY KEY  (`inv_id`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `inventory`
--


-- --------------------------------------------------------

--
-- Table structure for table `itembuylogs`
--

CREATE TABLE `itembuylogs` (
  `ibID` int(11) NOT NULL auto_increment,
  `ibUSER` int(11) NOT NULL default '0',
  `ibITEM` int(11) NOT NULL default '0',
  `ibTOTALPRICE` int(11) NOT NULL default '0',
  `ibQTY` int(11) NOT NULL default '0',
  `ibTIME` int(11) NOT NULL default '0',
  `ibCONTENT` text NOT NULL,
  PRIMARY KEY  (`ibID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `itembuylogs`
--


-- --------------------------------------------------------

--
-- Table structure for table `itemmarket`
--

CREATE TABLE `itemmarket` (
  `imID` int(11) NOT NULL auto_increment,
  `imITEM` int(11) NOT NULL default '0',
  `imADDER` int(11) NOT NULL default '0',
  `imPRICE` int(11) NOT NULL default '0',
  `imCURRENCY` enum('money','crystals') NOT NULL default 'money',
  `imQTY` int(11) NOT NULL default '0',
  PRIMARY KEY  (`imID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `itemmarket`
--


-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `itmid` int(11) NOT NULL auto_increment,
  `itmtype` int(11) NOT NULL default '0',
  `itmname` varchar(255) NOT NULL default '',
  `itmdesc` text NOT NULL,
  `itmbuyprice` int(11) NOT NULL default '0',
  `itmsellprice` int(11) NOT NULL default '0',
  `itmbuyable` int(11) NOT NULL default '0',
  `effect1_on` tinyint(4) NOT NULL default '0',
  `effect1` text NOT NULL,
  `effect2_on` tinyint(4) NOT NULL default '0',
  `effect2` text NOT NULL,
  `effect3_on` tinyint(4) NOT NULL default '0',
  `effect3` text NOT NULL,
  `weapon` int(11) NOT NULL default '0',
  `armor` int(11) NOT NULL default '0',
  PRIMARY KEY  (`itmid`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `items`
--


-- --------------------------------------------------------

--
-- Table structure for table `itemselllogs`
--

CREATE TABLE `itemselllogs` (
  `isID` int(11) NOT NULL auto_increment,
  `isUSER` int(11) NOT NULL default '0',
  `isITEM` int(11) NOT NULL default '0',
  `isTOTALPRICE` int(11) NOT NULL default '0',
  `isQTY` int(11) NOT NULL default '0',
  `isTIME` int(11) NOT NULL default '0',
  `isCONTENT` text NOT NULL,
  PRIMARY KEY  (`isID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `itemselllogs`
--


-- --------------------------------------------------------

--
-- Table structure for table `itemtypes`
--

CREATE TABLE `itemtypes` (
  `itmtypeid` int(11) NOT NULL auto_increment,
  `itmtypename` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`itmtypeid`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `itemtypes`
--


-- --------------------------------------------------------

--
-- Table structure for table `itemxferlogs`
--

CREATE TABLE `itemxferlogs` (
  `ixID` int(11) NOT NULL auto_increment,
  `ixFROM` int(11) NOT NULL default '0',
  `ixTO` int(11) NOT NULL default '0',
  `ixITEM` int(11) NOT NULL default '0',
  `ixQTY` int(11) NOT NULL default '0',
  `ixTIME` int(11) NOT NULL default '0',
  `ixFROMIP` varchar(255) NOT NULL default '',
  `ixTOIP` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`ixID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `itemxferlogs`
--


-- --------------------------------------------------------

--
-- Table structure for table `jaillogs`
--

CREATE TABLE `jaillogs` (
  `jaID` int(11) NOT NULL auto_increment,
  `jaJAILER` int(11) NOT NULL default '0',
  `jaJAILED` int(11) NOT NULL default '0',
  `jaDAYS` int(11) NOT NULL default '0',
  `jaREASON` longtext NOT NULL,
  `jaTIME` int(11) NOT NULL default '0',
  PRIMARY KEY  (`jaID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `jaillogs`
--


-- --------------------------------------------------------

--
-- Table structure for table `jobranks`
--

CREATE TABLE `jobranks` (
  `jrID` int(11) NOT NULL auto_increment,
  `jrNAME` varchar(255) NOT NULL default '',
  `jrJOB` int(11) NOT NULL default '0',
  `jrPAY` int(11) NOT NULL default '0',
  `jrIQG` int(11) NOT NULL default '0',
  `jrLABOURG` int(11) NOT NULL default '0',
  `jrSTRG` int(11) NOT NULL default '0',
  `jrIQN` int(11) NOT NULL default '0',
  `jrLABOURN` int(11) NOT NULL default '0',
  `jrSTRN` int(11) NOT NULL default '0',
  PRIMARY KEY  (`jrID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `jobranks`
--


-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `jID` int(11) NOT NULL auto_increment,
  `jNAME` varchar(255) NOT NULL default '',
  `jFIRST` int(11) NOT NULL default '0',
  `jDESC` varchar(255) NOT NULL default '',
  `jOWNER` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`jID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `jobs`
--


-- --------------------------------------------------------

--
-- Table structure for table `mail`
--

CREATE TABLE `mail` (
  `mail_id` int(11) NOT NULL auto_increment,
  `mail_read` int(11) NOT NULL default '0',
  `mail_from` int(11) NOT NULL default '0',
  `mail_to` int(11) NOT NULL default '0',
  `mail_time` int(11) NOT NULL default '0',
  `mail_subject` varchar(255) NOT NULL default '',
  `mail_text` text NOT NULL,
  PRIMARY KEY  (`mail_id`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `mail`
--


-- --------------------------------------------------------

--
-- Table structure for table `oclogs`
--

CREATE TABLE `oclogs` (
  `oclID` int(11) NOT NULL auto_increment,
  `oclOC` int(11) NOT NULL default '0',
  `oclGANG` int(11) NOT NULL default '0',
  `oclLOG` text NOT NULL,
  `oclRESULT` enum('success','failure') NOT NULL default 'success',
  `oclMONEY` int(11) NOT NULL default '0',
  `ocCRIMEN` varchar(255) NOT NULL default '',
  `ocTIME` int(11) NOT NULL default '0',
  PRIMARY KEY  (`oclID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `oclogs`
--


-- --------------------------------------------------------

--
-- Table structure for table `orgcrimes`
--

CREATE TABLE `orgcrimes` (
  `ocID` int(11) NOT NULL auto_increment,
  `ocNAME` varchar(255) NOT NULL default '',
  `ocUSERS` int(11) NOT NULL default '0',
  `ocSTARTTEXT` text NOT NULL,
  `ocSUCCTEXT` text NOT NULL,
  `ocFAILTEXT` text NOT NULL,
  `ocMINMONEY` int(11) NOT NULL default '0',
  `ocMAXMONEY` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ocID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `orgcrimes`
--


-- --------------------------------------------------------

--
-- Table structure for table `papercontent`
--

CREATE TABLE `papercontent` (
  `content` longtext NOT NULL
) ENGINE=MyISAM ;

INSERT INTO `papercontent` VALUES('Here you can put game news, or prehaps an update log.');


-- --------------------------------------------------------

--
-- Table structure for table `polls`
--

CREATE TABLE `polls` (
  `id` int(11) NOT NULL auto_increment,
  `active` enum('0','1') NOT NULL default '0',
  `question` varchar(255) NOT NULL default '',
  `choice1` varchar(255) NOT NULL default '',
  `choice2` varchar(255) NOT NULL default '',
  `choice3` varchar(255) NOT NULL default '',
  `choice4` varchar(255) NOT NULL default '',
  `choice5` varchar(255) NOT NULL default '',
  `choice6` varchar(255) NOT NULL default '',
  `choice7` varchar(255) NOT NULL default '',
  `choice8` varchar(255) NOT NULL default '',
  `choice9` varchar(255) NOT NULL default '',
  `choice10` varchar(255) NOT NULL default '',
  `voted1` int(11) NOT NULL default '0',
  `voted2` int(11) NOT NULL default '0',
  `voted3` int(11) NOT NULL default '0',
  `voted4` int(11) NOT NULL default '0',
  `voted5` int(11) NOT NULL default '0',
  `voted6` int(11) NOT NULL default '0',
  `voted7` int(11) NOT NULL default '0',
  `voted8` int(11) NOT NULL default '0',
  `voted9` int(11) NOT NULL default '0',
  `voted10` int(11) NOT NULL default '0',
  `votes` int(11) NOT NULL default '0',
  `winner` int(11) NOT NULL default '0',
  `hidden` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `polls`
--


-- --------------------------------------------------------

--
-- Table structure for table `preports`
--

CREATE TABLE `preports` (
  `prID` int(11) NOT NULL auto_increment,
  `prREPORTER` int(11) NOT NULL default '0',
  `prREPORTED` int(11) NOT NULL default '0',
  `prTEXT` longtext NOT NULL,
  PRIMARY KEY  (`prID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `preports`
--


-- --------------------------------------------------------

--
-- Table structure for table `referals`
--

CREATE TABLE `referals` (
  `refID` int(11) NOT NULL auto_increment,
  `refREFER` int(11) NOT NULL default '0',
  `refREFED` int(11) NOT NULL default '0',
  `refTIME` int(11) NOT NULL default '0',
  `refREFERIP` varchar(15) NOT NULL default '127.0.0.1',
  `refREFEDIP` varchar(15) NOT NULL default '127.0.0.1',
  PRIMARY KEY  (`refID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `referals`
--


-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `conf_id` int(11) NOT NULL auto_increment,
  `conf_name` varchar(255) NOT NULL default '',
  `conf_value` text NOT NULL,
  `data_type` varchar(16) NOT NULL default 'string',
  PRIMARY KEY  (`conf_id`)
) ENGINE=MyISAM ;

INSERT INTO `settings` VALUES (NULL, 'validate_period', '15', 'string');
INSERT INTO `settings` VALUES (NULL, 'validate_on', '0', 'bool');
INSERT INTO `settings` VALUES (NULL, 'regcap_on', '0', 'bool');
INSERT INTO `settings` VALUES (NULL, 'hospital_count', '0', 'int');
INSERT INTO `settings` VALUES (NULL, 'jail_count', '0', 'int');
INSERT INTO `settings` VALUES (NULL, 'sendcrys_on', '1', 'bool');
INSERT INTO `settings` VALUES (NULL, 'sendbank_on', '1', 'bool');
INSERT INTO `settings` VALUES (NULL, 'ct_refillprice', '12', 'int');
INSERT INTO `settings` VALUES (NULL, 'ct_iqpercrys', '5', 'int');
INSERT INTO `settings` VALUES (NULL, 'ct_moneypercrys', '200', 'int');
INSERT INTO `settings` VALUES (NULL, 'staff_pad', 'Here you can store notes for all staff to see.', 'string');
INSERT INTO `settings` VALUES (NULL, 'willp_item', '0', 'int');
INSERT INTO `settings` VALUES (NULL, 'jquery_location', 'js/jquery-1.7.1.min.js', 'string');
INSERT INTO `settings` VALUES (NULL, 'use_timestamps_over_crons', '1', 'bool');


-- --------------------------------------------------------

--
-- Table structure for table `shopitems`
--

CREATE TABLE `shopitems` (
  `sitemID` int(11) NOT NULL auto_increment,
  `sitemSHOP` int(11) NOT NULL default '0',
  `sitemITEMID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`sitemID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `shopitems`
--


-- --------------------------------------------------------

--
-- Table structure for table `shops`
--

CREATE TABLE `shops` (
  `shopID` int(11) NOT NULL auto_increment,
  `shopLOCATION` int(11) NOT NULL default '0',
  `shopNAME` varchar(255) NOT NULL default '',
  `shopDESCRIPTION` text NOT NULL,
  PRIMARY KEY  (`shopID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `shops`
--


-- --------------------------------------------------------

--
-- Table structure for table `stafflog`
--

CREATE TABLE `stafflog` (
  `id` int(11) NOT NULL auto_increment,
  `user` int(11) NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  `action` varchar(255) NOT NULL default '',
  `ip` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `stafflog`
--


-- --------------------------------------------------------

--
-- Table structure for table `staffnotelogs`
--

CREATE TABLE `staffnotelogs` (
  `snID` int(11) NOT NULL auto_increment,
  `snCHANGER` int(11) NOT NULL default '0',
  `snCHANGED` int(11) NOT NULL default '0',
  `snTIME` int(11) NOT NULL default '0',
  `snOLD` longtext NOT NULL,
  `snNEW` longtext NOT NULL,
  PRIMARY KEY  (`snID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `staffnotelogs`
--


-- --------------------------------------------------------

--
-- Table structure for table `surrenders`
--

CREATE TABLE `surrenders` (
  `surID` int(11) NOT NULL auto_increment,
  `surWAR` int(11) NOT NULL default '0',
  `surWHO` int(11) NOT NULL default '0',
  `surTO` int(11) NOT NULL default '0',
  `surMSG` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`surID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `surrenders`
--


-- --------------------------------------------------------

--
-- Table structure for table `unjaillogs`
--

CREATE TABLE `unjaillogs` (
  `ujaID` int(11) NOT NULL auto_increment,
  `ujaJAILER` int(11) NOT NULL default '0',
  `ujaJAILED` int(11) NOT NULL default '0',
  `ujaTIME` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ujaID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `unjaillogs`
--


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `userid` int(11) NOT NULL auto_increment,
  `username` varchar(255) NOT NULL default '',
  `userpass` varchar(255) NOT NULL default '',
  `level` int(11) NOT NULL default '0',
  `exp` decimal(11,4) NOT NULL default '0.0000',
  `money` int(11) NOT NULL default '0',
  `crystals` int(11) NOT NULL default '0',
  `laston` int(11) NOT NULL default '0',
  `lastip` varchar(255) NOT NULL default '',
  `job` int(11) NOT NULL default '0',
  `energy` int(11) NOT NULL default '0',
  `will` int(11) NOT NULL default '0',
  `maxwill` int(11) NOT NULL default '0',
  `brave` int(11) NOT NULL default '0',
  `maxbrave` int(11) NOT NULL default '0',
  `maxenergy` int(11) NOT NULL default '0',
  `hp` int(11) NOT NULL default '0',
  `maxhp` int(11) NOT NULL default '0',
  `lastrest_life` int(11) NOT NULL default '0',
  `lastrest_other` int(11) NOT NULL default '0',
  `location` int(11) NOT NULL default '0',
  `hospital` int(11) NOT NULL default '0',
  `jail` int(11) NOT NULL default '0',
  `jail_reason` varchar(255) NOT NULL default '',
  `fedjail` int(11) NOT NULL default '0',
  `user_level` int(11) NOT NULL default '1',
  `gender` enum('Male','Female') NOT NULL default 'Male',
  `daysold` int(11) NOT NULL default '0',
  `signedup` int(11) NOT NULL default '0',
  `gang` int(11) NOT NULL default '0',
  `daysingang` int(11) NOT NULL default '0',
  `course` int(11) NOT NULL default '0',
  `cdays` int(11) NOT NULL default '0',
  `jobrank` int(11) NOT NULL default '0',
  `donatordays` int(11) NOT NULL default '0',
  `email` varchar(255) NOT NULL default '',
  `login_name` varchar(255) NOT NULL default '',
  `display_pic` text NOT NULL,
  `duties` varchar(255) NOT NULL default 'N/A',
  `bankmoney` int(11) NOT NULL default '0',
  `cybermoney` int(11) NOT NULL default '-1',
  `staffnotes` longtext NOT NULL,
  `mailban` int(11) NOT NULL default '0',
  `mb_reason` varchar(255) NOT NULL default '',
  `hospreason` varchar(255) NOT NULL default '',
  `lastip_login` varchar(255) NOT NULL default '127.0.0.1',
  `lastip_signup` varchar(255) NOT NULL default '127.0.0.1',
  `last_login` int(11) NOT NULL default '0',
  `voted` text NOT NULL,
  `crimexp` int(11) NOT NULL default '0',
  `attacking` int(11) NOT NULL default '0',
  `verified` int(11) NOT NULL default '0',
  `forumban` int(11) NOT NULL default '0',
  `fb_reason` varchar(255) NOT NULL default '',
  `posts` int(11) NOT NULL default '0',
  `forums_avatar` varchar(255) NOT NULL default '',
  `forums_signature` varchar(250) NOT NULL default '',
  `new_events` int(11) NOT NULL default '0',
  `new_mail` int(11) NOT NULL default '0',
  `friend_count` int(11) NOT NULL default '0',
  `enemy_count` int(11) NOT NULL default '0',
  `new_announcements` int(11) NOT NULL default '0',
  `boxes_opened` int(11) NOT NULL default '0',
  `user_notepad` text NOT NULL,
  `equip_primary` int(11) NOT NULL default '0',
  `equip_secondary` int(11) NOT NULL default '0',
  `equip_armor` int(11) NOT NULL default '0',
  `force_logout` tinyint(4) NOT NULL default '0',
  `pass_salt` varchar(8) NOT NULL default '',
  PRIMARY KEY  (`userid`)
) ENGINE=MyISAM ;
--
-- Dumping data for table `users`
--


-- --------------------------------------------------------

--
-- Table structure for table `userstats`
--

CREATE TABLE `userstats` (
  `userid` int(11) NOT NULL default '0',
  `strength` float NOT NULL default '0',
  `agility` float NOT NULL default '0',
  `guard` float NOT NULL default '0',
  `labour` float NOT NULL default '0',
  `IQ` float NOT NULL default '0',
  PRIMARY KEY  (`userid`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `userstats`
--


-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `userid` int(11) NOT NULL default '0',
  `list` varchar(255) NOT NULL default ''
) ENGINE=MyISAM ;

--
-- Dumping data for table `votes`
--


-- --------------------------------------------------------

--
-- Table structure for table `willps_accepted`
--

CREATE TABLE `willps_accepted` (
  `dpID` int(11) NOT NULL auto_increment,
  `dpBUYER` int(11) NOT NULL default '0',
  `dpFOR` int(11) NOT NULL default '0',
  `dpAMNT` varchar(255) NOT NULL default '',
  `dpTIME` int(11) NOT NULL default '0',
  `dpTXN` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`dpID`)
) ENGINE=MyISAM ;

--
-- Dumping data for table `willps_accepted`
--


-- --------------------------------------------------------

--
-- Table structure for table `logs_cron_fails`
--

CREATE TABLE logs_cron_fails
(
    id            INT         NOT NULL PRIMARY KEY AUTO_INCREMENT,
    cron          VARCHAR(32) NOT NULL DEFAULT '',
    method        VARCHAR(32) NOT NULL DEFAULT '',
    message       TEXT        NULL,
    time_started  TIMESTAMP   NULL,
    time_finished TIMESTAMP   NULL,
    time_logged   TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    handled       BOOL        NOT NULL DEFAULT FALSE,
    INDEX (cron)
);

--
-- Dumping data for table `logs_cron_fails`
--

-- --------------------------------------------------------

--
-- Table structure for table `logs_cron_runtimes`
--
CREATE TABLE logs_cron_runtimes
(
    id            INT         NOT NULL PRIMARY KEY AUTO_INCREMENT,
    cron          VARCHAR(32) NOT NULL DEFAULT '',
    time_started  TIMESTAMP   NULL,
    time_finished TIMESTAMP   NULL,
    time_logged   TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_cnt   INT         NOT NULL DEFAULT 0,
    INDEX(cron)
);

--
-- Dumping data for table `logs_cron_runtimes`
--

-- --------------------------------------------------------

--
-- Table structure for table `cron_times`
--

CREATE TABLE cron_times (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(32) NOT NULL UNIQUE,
    last_run TIMESTAMP NULL
);

--
-- Dumping data for table `cron_times`
--

INSERT INTO cron_times (name, last_run) VALUES ('minute-1', CONCAT(CONCAT(DATE_FORMAT(NOW(), '%Y-%m-%d'), ' '), SEC_TO_TIME((TIME_TO_SEC(NOW()) DIV 60) * 60)));
INSERT INTO cron_times (name, last_run) VALUES ('minute-5', CONCAT(CONCAT(DATE_FORMAT(NOW(), '%Y-%m-%d'), ' '), SEC_TO_TIME((TIME_TO_SEC(NOW()) DIV 300) * 300)));
INSERT INTO cron_times (name, last_run) VALUES ('hour-1', CONCAT(CONCAT(DATE_FORMAT(NOW(), '%Y-%m-%d'), ' '), SEC_TO_TIME((TIME_TO_SEC(NOW()) DIV 3600) * 3600)));
INSERT INTO cron_times (name, last_run) VALUES ('day-1', CONCAT(CONCAT(DATE_FORMAT(NOW(), '%Y-%m-%d'), ' '), SEC_TO_TIME((TIME_TO_SEC(NOW()) DIV 86400) * 86400)));

-- --------------------------------------------------------

--
-- Table structure for table `staff_roles`
--

CREATE TABLE `staff_roles`
(
    `id`                    INT          NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `name`                  VARCHAR(255) NOT NULL DEFAULT '',
    `administrator`         BOOL         NOT NULL DEFAULT FALSE,
    `credit_all_users`      BOOL         NOT NULL DEFAULT FALSE,
    `credit_item`           BOOL         NOT NULL DEFAULT FALSE,
    `credit_user`           BOOL         NOT NULL DEFAULT FALSE,
    `edit_newspaper`        BOOL         NOT NULL DEFAULT FALSE,
    `manage_challenge_bots` BOOL         NOT NULL DEFAULT FALSE,
    `manage_cities`         BOOL         NOT NULL DEFAULT FALSE,
    `manage_courses`        BOOL         NOT NULL DEFAULT FALSE,
    `manage_crimes`         BOOL         NOT NULL DEFAULT FALSE,
    `manage_donator_packs`  BOOL         NOT NULL DEFAULT FALSE,
    `manage_forums`         BOOL         NOT NULL DEFAULT FALSE,
    `manage_gangs`          BOOL         NOT NULL DEFAULT FALSE,
    `manage_houses`         BOOL         NOT NULL DEFAULT FALSE,
    `manage_items`          BOOL         NOT NULL DEFAULT FALSE,
    `manage_jobs`           BOOL         NOT NULL DEFAULT FALSE,
    `manage_player_reports` BOOL         NOT NULL DEFAULT FALSE,
    `manage_polls`          BOOL         NOT NULL DEFAULT FALSE,
    `manage_punishments`    BOOL         NOT NULL DEFAULT FALSE,
    `manage_roles`          BOOL         NOT NULL DEFAULT FALSE,
    `manage_shops`          BOOL         NOT NULL DEFAULT FALSE,
    `manage_staff`          BOOL         NOT NULL DEFAULT FALSE,
    `manage_users`          BOOL         NOT NULL DEFAULT FALSE,
    `mass_mail`             BOOL         NOT NULL DEFAULT FALSE,
    `use_staff_forums`      BOOL         NOT NULL DEFAULT FALSE,
    `view_logs`             BOOL         NOT NULL DEFAULT FALSE,
    `view_user_inventory`   BOOL         NOT NULL DEFAULT FALSE
);

--
-- Dumping data for table `willps_accepted`
--

INSERT INTO `staff_roles` (`name`, `administrator`) VALUES ('Administrator', true);
INSERT INTO `staff_roles` (`name`, `view_user_inventory`, `credit_user`, `manage_player_reports`, `credit_item`, `view_logs`, `manage_gangs`, `manage_punishments`, `use_staff_forums`) VALUES ('Secretary', true, true, true, true, true, true, true, true);
INSERT INTO `staff_roles` (`name`, `view_logs`, `manage_punishments`, `use_staff_forums`) VALUES ('Assistant', true, true, true);

-- --------------------------------------------------------

--
-- Table structure for table `users_roles`
--

CREATE TABLE users_roles
(
    id         INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    userid     INT NOT NULL REFERENCES users (userid),
    staff_role INT NOT NULL REFERENCES staff_roles (id)
);

-- --------------------------------------------------------

--
-- Dumping data for table `users_roles`
--

INSERT INTO users_roles (userid, staff_role) VALUES (1, 1);
