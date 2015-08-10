<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Muhammed Alat <alat@bizlogix.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

/**
 * Plugin 'Change Password' for the 'changepassword' extension.
 *
 * @author Muhammed Alat <alat@bizlogix.de>
 * @author Frederic Gaus <gaus@flagbit.de>
 * @package TYPO3
 * @subpackage tx_changepassword
 */
class tx_changepassword_pi1 extends \TYPO3\CMS\Frontend\Plugin\AbstractPlugin {
	public $prefixId = 'tx_changepassword_pi1'; // Same as class name
	public $scriptRelPath = 'pi1/class.tx_changepassword_pi1.php'; // Path to this script relative to the extension dir.
	public $extKey = 'changepassword'; // The extension key.


	private function init($conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_initPIflexForm(); // Init FlexForm configuration for plugin

		// Get the logged in user id or return false
		$this->config['userid'] = $GLOBALS['TSFE']->loginUser ? $GLOBALS['TSFE']->fe_user->user['uid'] : false;

		// Getting the pid list via the flexform
		$pidList = $this->cObj->data['pages'];
		$this->conf['pidList'] = $pidList ?
			$pidList :
			$GLOBALS['TSFE']->id;

		// Template code
		$this->config['templateFile'] = $this->cObj->fileResource($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'templateFile', 'sDEF') ? $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'templateFile', 'sDEF')  : t3lib_extMgm::siteRelPath($this->extKey) . 'res/template.html');
	}


	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	public function main($content, $conf) {
		$this->pi_USER_INT_obj = 1;
		$this->init($conf);
		if(!$this->config['userid']) {
			return 'User is not logged in.';
		}
		$content = '';

		// Get Part of the Template
		$template = $this->cObj->getSubpart($this->config['templateFile'], '###TEMPLATE###');
		$subTemplateErrOldPassword = $GLOBALS['TSFE']->cObj->getSubpart($template, '###SUBPART_ERR_OLDPASSWORD###');
		$subTemplateErrNewPassword = $GLOBALS['TSFE']->cObj->getSubpart($template, '###SUBPART_ERR_NEWPASSWORD###');
		$subTemplateErrRePassword = $GLOBALS['TSFE']->cObj->getSubpart($template, '###SUBPART_ERR_REPASSWORD###');
		$subpartArray = array(
			'###SUBPART_ERR_OLDPASSWORD###' => '',
			'###SUBPART_ERR_NEWPASSWORD###' => '',
			'###SUBPART_ERR_REPASSWORD###' => '',
		);

		$changePassword = false;
		if(isset($this->piVars['save'])) {
			$changePassword = true;
			if(! $this->isOldPasswordCorrect()) {
				$changePassword = false;
				$subMarkerArrayErrOldPassword = array();
				$subMarkerArrayErrOldPassword['###ERR_OLDPASSWORD###'] = $this->pi_getLL('wrongOldPassword');
				$subpartArray['###SUBPART_ERR_OLDPASSWORD###'] .= $this->cObj->substituteMarkerArrayCached($subTemplateErrOldPassword, $subMarkerArrayErrOldPassword, array(), array());
			} else if(! $this->isNewPasswordLongEnough()) {
				$changePassword = false;
				$subMarkerArrayErrNewPassword = array();
				$subMarkerArrayErrNewPassword['###ERR_NEWPASSWORD###'] = $this->pi_getLL('toshortpassword');
				$subpartArray['###SUBPART_ERR_NEWPASSWORD###'] .= $this->cObj->substituteMarkerArrayCached($subTemplateErrNewPassword, $subMarkerArrayErrNewPassword, array(), array());
			} else if(! $this->isNewPasswortEqual()) {
				$changePassword = false;
				$subMarkerArrayErrRePassword = array();
				$subMarkerArrayErrRePassword['###ERR_REPASSWORD###'] = $this->pi_getLL('twodifferentnewpasswords');
				$subpartArray['###SUBPART_ERR_REPASSWORD###'] .= $this->cObj->substituteMarkerArrayCached($subTemplateErrRePassword, $subMarkerArrayErrRePassword, array(), array());
			}
			if($changePassword) {
				$this->savePassword();
			}
		}

		$markerArray=array();
		if($changePassword) {
			$template = $this->cObj->getSubpart($this->config['templateFile'], '###TEMPLATE_SUCCESS###');
			$markerArray['###HEADER###'] = $this->pi_getLL('passwordsaved');
			$markerArray['###MESSAGE###'] = $this->pi_getLL('passwordsaved_message');
		} else {
			$markerArray['###FORM_ACTION###'] = $this->pi_getPageLink($GLOBALS['TSFE']->id);
			$markerArray['###LEGEND###'] =  $this->pi_getLL('legend');
			$markerArray['###OLDPASSWORD###'] =  $this->pi_getLL('oldpassword');
			$markerArray['###NEWPASSWORD###'] =  $this->pi_getLL('newpassword');
			$markerArray['###REPASSWORD###'] =  $this->pi_getLL('repassword');
			$markerArray['###SUBMIT###'] =  $this->pi_getLL('submit');
		}

		// build template
		$content .= $this->cObj->substituteMarkerArrayCached($template, $markerArray, $subpartArray,array());

		return $this->pi_wrapInBaseClass($content);
	}


	// Save the new password
	private function savePassword() {
		$updatePassword = array('password' => '', 'tstamp' => time());
		if (t3lib_extMgm::isLoaded('saltedpasswords') && tx_saltedpasswords_div::isUsageEnabled('FE')) {
			// EXT: saltedpassword
			$instanceSalted = tx_saltedpasswords_salts_factory::getSaltingInstance();
			$updatePassword['password'] = $instanceSalted->getHashedPassword($this->piVars['newpassword']);
		} else if (t3lib_extMgm::isLoaded('kb_md5fepw')) {
			// EXT: kb_md5fepw
			$updatePassword['password'] = md5($this->piVars['newpassword']);
		} else {
			// Plain Text
			$updatePassword['password'] = $this->piVars['newpassword'];
		}
		// Save new Password in the db
		$updateQueryStatus = $GLOBALS['TYPO3_DB']->exec_UPDATEquery (
			'fe_users',
			'uid = ' . $this->config['userid'] . ' AND pid IN (' . $this->conf['pidList'] . ')',
			$updatePassword
		);
		return $updateQueryStatus;
	}


	// Check the old password
	private function isOldPasswordCorrect() {
		// Check old password
		$password = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow (
			'password', // SELECT
			'fe_users', // FROM
			'uid = ' . $this->config['userid'] .' AND pid IN (' . $this->conf['pidList'] . ')' //WHERE
		);
		$password = current($password);
		if (t3lib_extMgm::isLoaded('saltedpasswords') && tx_saltedpasswords_div::isUsageEnabled('FE')) {
			$instanceSalted = tx_saltedpasswords_salts_factory::getSaltingInstance();
		}
		if ($instanceSalted && $instanceSalted->isValidSaltedPW($password)) {
			if (! $instanceSalted->checkPassword($this->piVars['oldpassword'], $password)) {
				return false;
			}
		} else if (t3lib_extMgm::isLoaded('kb_md5fepw')) {
			if(strcmp(md5($this->piVars['oldpassword']),$password)!=0) {
				return false;
			}
		} else {
			if(strcmp($this->piVars['oldpassword'],$password)!=0) {
				return false;
			}
		}
		return true;
	}


	// Check the length of the new password
	private function isNewPasswordLongEnough() {
		if( strlen($this->piVars['newpassword']) < 6 ) {
				return false;
		}
		return true;
	}


	// Check if the new password was enter two times and is equal
	private function isNewPasswortEqual() {
		if(strcmp($this->piVars['newpassword'],$this->piVars['repassword'])!=0) {
			return false;
		}
		return true;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/changepassword/pi1/class.tx_changepassword_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/changepassword/pi1/class.tx_changepassword_pi1.php']);
}

?>
