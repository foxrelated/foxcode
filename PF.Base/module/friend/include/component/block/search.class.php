<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Friend
 * @version 		$Id: search.class.php 4593 2012-08-13 09:32:05Z Raymond_Benc $
 */
class Friend_Component_Block_Search extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$iPage = $this->getParam('page', 0);
		$iPageSize = 36;		
		$bIsOnline = false;		
		$oDb = Phpfox_Database::instance();
		$aParams = array();
		$aConditions = array();
		$iListId = 0;
		
		$aConditions[] = 'AND friend.is_page = 0';
		
		if ($this->getParam('type') != 'mail')
		{
			$aConditions[] = 'AND friend.user_id = ' . Phpfox::getUserId();
		}
		
		if (($sFind = $this->getParam('find')))
		{
			$aConditions[] = 'AND (u.full_name LIKE \'%' . $oDb->escape($sFind) . '%\' OR (u.email LIKE \'%' . $oDb->escape($sFind) . '@%\' OR u.email = \'' . $oDb->escape($sFind) . '\'))';	
		}		
		
		$aLetters = array(
			_p('all'), '#', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
		);			
		
		if (($sLetter = $this->getParam('letter')) && in_array($sLetter, $aLetters) && strtolower($sLetter) != 'all')
		{
			if ($sLetter == '#')
			{
				$sSubCondition = '';
				for ($i = 0; $i <= 9; $i++)
				{
					$sSubCondition .= "OR u.full_name LIKE '" . Phpfox_Database::instance()->escape($i) . "%' ";
				}
				$sSubCondition = ltrim($sSubCondition, 'OR ');
				$aConditions[] = 'AND (' . $sSubCondition . ')';
			}
			else 
			{
				$aConditions[] = "AND u.full_name LIKE '" . Phpfox_Database::instance()->escape($sLetter) . "%'";
			}
			
			$aParams['letter'] = $sLetter;
		}		
		
		if ($sView = $this->getParam('view'))
		{
			switch ($sView)
			{
				case 'top':
					$aConditions[] = 'AND is_top_friend = 1';
					break;
				case 'online':
					$bIsOnline = true;
					break;
				case 'all':
					
					break;
				default:					
					if ((int) $sView > 0 && ($aList = Friend_Service_List_List::instance()->getList($sView, Phpfox::getUserId())) && isset($aList['list_id']))
					{
						$iListId = (int) $aList['list_id'];
					}
					break;
			}
		}
		
		if ($this->getParam('type') == 'mail')
		{
			$aConditions[] = 'AND u.user_id != ' . Phpfox::getUserId();
			list($iCnt, $aFriends) = User_Service_Browse::instance()->conditions($aConditions)
				->sort('u.full_name ASC')
				->page($iPage)
				->limit($iPageSize)			
				->get();
			if (Phpfox::getParam('mail.disallow_select_of_recipients'))
			{
				$oMail = Mail_Service_Mail::instance();
				foreach ($aFriends as $iKey => $aFriend)
				{
					if (!$oMail->canMessageUser($aFriend['user_id']))
					{
						$aFriends[$iKey]['canMessageUser'] = false;
					}
				}
			}
		}
		else 
		{		
			list($iCnt, $aFriends) = Friend_Service_Friend::instance()->get($aConditions, 'u.full_name ASC', $iPage, $iPageSize, true, true, $bIsOnline, null, false, $iListId);
		}
		
		(($sPlugin = Phpfox_Plugin::get('friend.component_block_search_get')) ? eval($sPlugin) : false);
		
		$aParams['input'] = $this->getParam('input');
		$aParams['friend_item_id'] = $this->getParam('friend_item_id');
		$aParams['friend_module_id'] = $this->getParam('friend_module_id');
		$aParams['type'] = $this->getParam('type');
		$bInForm = $this->getParam('in_form', false);
			
		Phpfox_Pager::instance()->set(array('ajax' => 'friend.searchAjax', 'page' => $iPage, 'size' => $iPageSize, 'count' => $iCnt, 'aParams' => $aParams));
		
		$sFriendModuleId = $this->getParam('friend_module_id', '');

		$this->template()->assign(array(
				'aFriends' => $aFriends,
				'aLetters' => $aLetters,
				'sView' => $sView,
				'sActualLetter' => $sLetter,
				'sPrivacyInputName' => $this->getParam('input'),
				'aLists' => Friend_Service_List_List::instance()->get(),
				'bSearch' => $this->getParam('search'),
				'bIsForShare' => $this->getParam('friend_share', false),
				'sFriendItemId' => (int) $this->getParam('friend_item_id', '0'),
				'sFriendModuleId' => $sFriendModuleId,
				'sFriendType' => $this->getParam('type'),
				'bInForm' => $bInForm
			)
		);
		
		(($sPlugin = Phpfox_Plugin::get('friend.component_block_search_process')) ? eval($sPlugin) : false);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('friend.component_block_search_clean')) ? eval($sPlugin) : false);
	}
}