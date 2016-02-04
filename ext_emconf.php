<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "changepassword".
 *
 * Auto generated 04-02-2016 15:35
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
  'title' => 'Change front end user password',
  'description' => 'Change the password of the front end user in the frontend. Easy template system to configure the layout. User have to enter his old password an twice the new one. Support for salted passwords.',
  'category' => 'fe',
  'version' => '0.7.3',
  'state' => 'alpha',
  'uploadfolder' => false,
  'createDirs' => '',
  'clearcacheonload' => false,
  'author' => 'Frederic Gaus',
  'author_email' => 'info@flagbit.de',
  'author_company' => 'Flagbit GmbH & Co. KG',
  'constraints' => 
  array (
    'depends' => 
    array (
      'php' => '5.3.0-5.5.99',
      'typo3' => '4.5.0-6.2.99',
      'felogin' => '',
    ),
    'conflicts' => 
    array (
    ),
    'suggests' => 
    array (
    ),
  ),
);

