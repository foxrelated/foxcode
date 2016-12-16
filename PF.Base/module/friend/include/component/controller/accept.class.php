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
 * @package 		Phpfox_Component
 * @version 		$Id: controller.class.php 103 2009-01-27 11:32:36Z Raymond_Benc $
 */
class Friend_Component_Controller_Accept extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::isUser(true);
		
		$aCheckParams = array(
			'url' => $this->url()->makeUrl('friend'),
			'start' => 3,
			'reqs' => array(
					'2' => array('accept', 'pending')
				)
			);
				
		if (Phpfox::getParam('core.force_404_check') && !Core_Service_Redirect_Redirect::instance()->check404($aCheckParams))
		{
			return Phpfox_Module::instance()->setController('error.404');
		}
		
		$iPage = $this->request()->getInt('page');
		$iLimit = Phpfox::getParam('friend.total_requests_display');
		$iRequestId = $this->request()->getInt('id');	
		
		list($iCnt, $aFriends) = Friend_Service_Request_Request::instance()->get($iPage, $iLimit, $iRequestId);
		
		Phpfox_Pager::instance()->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $iCnt));
		
		Friend_Service_Friend::instance()->buildMenu();
		
		$this->setParam('global_moderation', array(
				'name' => 'friend',
				'ajax' => 'friend.moderation',
				'menu' => array(
					array(
						'phrase' => _p('accept'),
						'action' => 'accept'
					),
					array(
						'phrase' => _p('deny'),
						'action' => 'deny'
					)					
				)
			)
		);		
		
		$this->template()->setTitle(_p('friend_requests'))
			->setBreadCrumb(_p('friends'), $this->url()->makeUrl('friend'))
			->assign(array(
				'aFriends' => $aFriends,
				'iRequestId' => $iRequestId,
				'bIsFriendController' => true
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
		(($sPlugin = Phpfox_Plugin::get('friend.component_controller_accept_clean')) ? eval($sPlugin) : false);
	}
}