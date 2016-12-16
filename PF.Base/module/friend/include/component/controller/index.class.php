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
 * @version 		$Id: index.class.php 3441 2011-11-02 15:53:59Z Miguel_Espinoza $
 */
class Friend_Component_Controller_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::isUser(true);

		if (($iDeleteList = $this->request()->getInt('dlist')) && Friend_Service_List_Process::instance()->delete($iDeleteList))
		{
			$this->url()->send('friend', true, _p('list_successfully_deleted'));
		}

		$sView = $this->request()->get('view');
		$iPage = $this->request()->getInt('page');
		$this->template()->setTitle(_p('friends'))->setBreadCrumb(_p('friends'), $this->url()->makeUrl('friend'));

		$aSort = array();
		if ($sView == 'list')
		{
			$aSort['custom'] = array(
				'fld.ordering', _p('custom_order')
			);
		}

		$aSort['latest'] = array('friend.time_stamp', _p('newest_friends'));
		$aSort['first-name'] = array('u.full_name', _p('by_first_name'), 'ASC');

		$aParams = array(
			'type' => 'friend',
			'field' => 'friend.friend_id',
			'search_tool' => array(
				'table_alias' => 'friend',
				'search' => array(
					'action' =>  $this->url()->makeUrl('friend', array('view' => $this->request()->get('view'))),
					'default_value' => _p('search_friends_dot_dot_dot'),
					'name' => 'search',
					'field' => 'u.full_name'
				),
				'sort' => $aSort,
				'show' => array(10, 15, 20)
			)
		);

		$this->search()->set($aParams);

		$iPageSize = $this->search()->getDisplay();

		$bIsOnline = false;
		$iListId = 0;
		$aSend = null;
		$aList = array();
		switch ($sView)
		{
			case 'list':
				if (($iListId = $this->request()->getInt('id')) && ($aList = Friend_Service_List_List::instance()->getList($iListId, Phpfox::getUserId())) && isset($aList['list_id']))
				{
					$this->search()->setCondition('AND fld.list_id = ' . (int) $aList['list_id']);
					$aSend = array('list' => $iListId);
				}
				else
				{
					return Phpfox_Error::display(_p('invalid_friend_list'));
				}
				break;
			default:
				$this->search()->setCondition('AND friend.is_page = 0 AND friend.user_id = ' . Phpfox::getUserId());
				break;
		}

		if (($aVals = $this->request()->getArray('val')) && isset($aVals['id']) && is_array($aVals['id']))
		{
			$oServiceFriendProcess = Friend_Service_Process::instance();
			foreach ($aVals['id'] as $iId)
			{
				$oServiceFriendProcess->delete($iId);
			}

			$this->url()->send('friend', $aSend, _p('successfully_deleted'));
		}

		list($iCnt, $aRows) = Friend_Service_Friend::instance()->get($this->search()->getConditions(), $this->search()->getSort(), $this->search()->getPage(), $iPageSize, true, true, $bIsOnline, null, true);

		Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iPageSize, 'count' => $iCnt, 'ajax' => 'friend.viewMoreFriends'));

		Friend_Service_Friend::instance()->buildMenu();

		$this->template()->setHeader('jquery/ui.js', 'static_script');
		$this->template()->setHeader('cache', array(
				'friend.js' => 'module_friend',
			)
		)
			->assign(array(
					'aFriends' => $aRows,
					'aList' => $aList,
					'iList' => $iListId,
					'sView' => $sView,
					'iTotalFriendRequests' => Friend_Service_Request_Request::instance()->getUnseenTotal()
				)
			);
        return null;
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('friend.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}