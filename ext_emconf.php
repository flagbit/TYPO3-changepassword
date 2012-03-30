<?php

########################################################################
# Extension Manager/Repository config file for ext "changepassword".
#
# Auto generated 30-03-2012 14:38
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Change front end user password',
	'description' => 'Change the password of the front end user in the frontend. Easy template system to configure the layout. User have to enter his old password an twice the new one. Support for salted passwords.',
	'category' => 'fe',
	'shy' => 0,
	'version' => '0.6.0',
	'dependencies' => 'felogin',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'alpha',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Frederic Gaus',
	'author_email' => 'info@flagbit.de',
	'author_company' => 'Flagbit GmbH & Co. KG',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'felogin' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:15:{s:9:"ChangeLog";s:4:"cb1f";s:12:"ext_icon.gif";s:4:"2359";s:17:"ext_localconf.php";s:4:"84d5";s:14:"ext_tables.php";s:4:"ab7d";s:15:"flexform_ds.xml";s:4:"34c7";s:16:"locallang_db.xml";s:4:"c113";s:14:"doc/manual.sxw";s:4:"cd17";s:19:"doc/wizard_form.dat";s:4:"dc9f";s:20:"doc/wizard_form.html";s:4:"c1dd";s:35:"pi1/class.tx_changepassword_pi1.php";s:4:"f1f8";s:17:"pi1/locallang.xml";s:4:"f170";s:13:"res/style.css";s:4:"c053";s:17:"res/template.html";s:4:"ef53";s:35:"static/changepassword/constants.txt";s:4:"d41d";s:31:"static/changepassword/setup.txt";s:4:"d41d";}',
	'suggests' => array(
	),
);

?>