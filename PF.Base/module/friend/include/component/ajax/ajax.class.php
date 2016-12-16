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
 * @version 		$Id: ajax.class.php 7314 2014-05-09 13:41:44Z Fern $
 */
class Friend_Component_Ajax_Ajax extends Phpfox_Ajax
{
	public function getOnlineFriends()
	{
		Phpfox::getBlock('friend.mini');
		
		$this->call('$(\'#js_block_border_friend_mini\').find(\'.content:first\').html(\'' . $this->getContent() . '\');');
		if(!Phpfox::getParam('core.site_wide_ajax_browsing'))
		{
			$this->call('$Core.loadInit();');
		}
	}
	
	public function request()
	{
		Phpfox::isUser(true);	
		Phpfox::getUserParam('friend.can_add_friends', true);		
		
		$this->setTitle(_p('add_to_friends'));
		
		Phpfox::getBlock('friend.request', array('user_id' => $this->get('user_id')));
    $this->call('<script>$Behavior.globalInit();</script>');
		echo $this->template()->getHeader();
	}
	
	public function processRequest()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('friend.can_add_friends', true);
		
		if (Friend_Service_Friend::instance()->isFriend($this->get('user_id'), Phpfox::getUserId()))
		{
            Friend_Service_Request_Process::instance()->delete($this->get('request_id'), $this->get('user_id'));
			$this->call(' $("#js_new_friend_request_' . $this->get('request_id') . '").remove();');
			return false;
		}
		
		$aVal = $this->get('val');
		if ($this->get('type') == 'yes')
		{
			if (Friend_Service_Process::instance()->add(Phpfox::getUserId(), $this->get('user_id'), (isset($aVal['list_id']) ? (int) $aVal['list_id'] : 0)))
			{
				$this->hide('#drop_down_' . $this->get('request_id'));
				$sMess = _p('The request has been accepted successfully!');
			}
		}
		else 
		{
			if (Friend_Service_Process::instance()->deny(Phpfox::getUserId(), $this->get('user_id')))
			{
				$this->hide('#drop_down_' . $this->get('request_id'));
				$sMess = _p('The request has been denied successfully!');
			}			
		}
		
		if ($this->get('inline'))
		{
			$aUser = User_Service_User::instance()->getUser($this->get('user_id'));
			$this->call('$(\'.js_friend_request_' . $this->get('request_id') . '\').find(\'.js_drop_data_add\').hide();');
			if ($this->get('type') == 'yes')
			{	
				$this->addClass('.js_friend_request_' . $this->get('request_id'), 'row_moderate');
				
				$this->call('$(\'.js_friend_request_' . $this->get('request_id') . '\').find(\'.extra_info_middot\').show();');				
			}
		}
		else 
		{
			$this->call("tb_remove();");
		}
		$this->remove('.js_profile_online_friend_request');
		$this->remove('.add_as_friend_button');
		$this->remove('.pending-friend-request');

		$this->call($this->alert($sMess, _p('Notice'), 300, 150, true, true));
        return null;
	}
	
	public function addRequest()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('friend.can_add_friends', true);
		
		$aVals = $this->get('val');		
		$aUser = User_Service_User::instance()->getUser($aVals['user_id'], 'u.user_id, u.user_name, u.user_image, u.server_id');
		
		if (Phpfox::getUserId() === $aUser['user_id'])
		{
			return false;
		}
		elseif (Friend_Service_Request_Request::instance()->isRequested(Phpfox::getUserId(), $aUser['user_id']))
		{	
			Phpfox_Error::set(_p('you_were_already_requested_to_be_friends'));
		}
		elseif (Friend_Service_Request_Request::instance()->isRequested($aUser['user_id'], Phpfox::getUserId()))
		{
			Phpfox_Error::set(_p('you_already_requested_to_be_friends'));
		}
		elseif (Friend_Service_Friend::instance()->isFriend($aUser['user_id'], Phpfox::getUserId()))
		{	
			Phpfox_Error::set(_p('you_are_already_friends_with_this_user'));
		}
		else if (User_Service_Block_Block::instance()->isBlocked($aUser['user_id'], Phpfox::getUserId()))
		{
			$this->call('tb_remove();');
			return Phpfox_Error::set(_p('unable_to_send_a_friend_request_to_this_user_at_this_moment'));
		}
		if (Phpfox_Error::isPassed() != true)
		{
			$this->call('tb_remove();');
			return false;
		}
		if (Friend_Service_Request_Process::instance()->add(Phpfox::getUserId(), $aVals['user_id']))
		{
			if (isset($aVals['invite']))
			{
				$this->call('tb_remove();')->html('#js_invite_user_' . $aVals['user_id'], '' . _p('friend_request_successfully_sent') . '');	
			}			
			else 
			{
				$this->call('tb_remove(); $("#core_js_messages").html(""); $("#core_js_messages").message("' . _p('friend_request_successfully_sent') . '", "valid").slideDown("slow").fadeOut(5000);');
				$this->remove('#js_add_friend_on_profile');
			}

			$this->call('$(\'#js_parent_user_' . $aVals['user_id'] . '\').find(\'.user_browse_add_friend:first\').hide();');
			$this->call('$(\'#js_user_tool_tip_cache_profile-' . $aVals['user_id'] . '\').closest(\'.js_user_tool_tip_holder:first\').remove();');
			
			if (isset($aVals['suggestion']))
			{				
				$this->loadSuggestion(false);
			}
			
			if (isset($aVals['page_suggestion']))
			{
				$this->hide('#js_suggestion_parent_' . $aVals['user_id']);
			}
		}
        $this->remove('.add_as_friend_button');
        return null;
	}
	
	public function addList()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('friend.can_add_folders', true);
		
		$sName = $this->get('name');

		if (Phpfox::getLib('parse.format')->isEmpty($sName))
		{
			$this->html('#js_friend_list_add_error', _p('provide_a_name_for_your_list'), '.show()');
			$this->call('$Core.processForm(\'#js_friend_list_add_submit\', true);');
		}
		elseif (Friend_Service_List_List::instance()->reachedLimit()) // Did they reach their limit?
		{
			$this->html('#js_friend_list_add_error', _p('you_have_reached_your_limit'), '.show()');
			$this->call('$Core.processForm(\'#js_friend_list_add_submit\', true);');
		}			
		elseif (Friend_Service_List_List::instance()->isFolder($sName))
		{
			$this->html('#js_friend_list_add_error', _p('folder_already_use'), '.show()');
			$this->call('$Core.processForm(\'#js_friend_list_add_submit\', true);');
		}
		else 
		{
			if ($iId = Friend_Service_List_Process::instance()->add($sName))
			{
				if ($this->get('custom'))
				{
					$this->hide('#js_create_custom_friend_list')->show('#js_add_friends_to_list')->val('#js_custom_friend_list_id', $iId);
				}
				else 
				{
					$this->call('js_box_remove($(\'#js_friend_list_add_error\'));');
					$this->alert(_p('list_successfully_created'), _p('create_new_list'), 400, 150, true);
					$this->call('$Core.reloadPage();');
				}
				$this->call('$Core.loadInit();');
			}
		}
	}

	public function editListName()
	{
		Phpfox::isUser(true);

		$sName = $this->get('name');
		$iListId = $this->get('id');

		if (Phpfox::getLib('parse.format')->isEmpty($sName))
		{
			$this->html('#js_friend_list_edit_name_error', _p('provide_a_name_for_your_list'), '.show()');
			$this->call('$Core.processForm(\'#js_friend_list_edit_name_submit\', true);');
		}
		elseif (Friend_Service_List_List::instance()->isFolder($sName, $iListId))
		{
			$this->html('#js_friend_list_edit_name_error', _p('folder_already_use'), '.show()');
			$this->call('$Core.processForm(\'#js_friend_list_edit_name_submit\', true);');
		}
		else
		{
			if (Friend_Service_List_Process::instance()->update($iListId, $sName))
			{
				$this->call('js_box_remove($(\'#js_friend_list_edit_name_error\'));');
				$this->alert(_p('list_successfully_edited'), _p('edit_list_name'), 400, 150, true);
				$this->call('$Core.reloadPage();');
			}
		}
	}

	public function addNewList()
	{
		$this->setTitle(_p('create_new_list'));
		
		Phpfox::getBlock('friend.list.add');
	}

	public function editName()
	{
		$this->setTitle(_p('edit_list_name'));

		Phpfox::getBlock('friend.list.edit-name');
	}
	
	public function buildCache()
	{
		$this->call('$Cache.friends = ' . json_encode(Friend_Service_Friend::instance()->getFromCache($this->get('allow_custom'))) . ';');
		$this->call('$Core.loadInit();');
	}
	
	public function getLiveSearch()
	{
		// This function is called from friend.static.search.js::getFriends in response to a key up event when is_mail is passed as true in building the template
		// parent_id we have to find the class "js_temp_friend_search_form" from its parents
		// search_for 
		$aUsers = Friend_Service_Friend::instance()->getFromCache(false,$this->get('search_for'));
		
		if (empty($aUsers))
		{
			return false;
		}
		// The next block is copied and modified from friend.static.search.js::getFriends
		$sHtml = '';
		$iFound = 0;
		$sStoreUser = '';
		foreach ($aUsers as $aUser)
		{
			$iFound++;
			if (substr($aUser['user_image'], 0, 5) == 'http:') {
				$aUser['user_image'] = '<img src="' . $aUser['user_image'] . '">';
			}
			$sHtml .= '<li><div rel="' . $aUser['user_id'] . '" class="js_friend_search_link ' . (($iFound == 1) ? 'js_temp_friend_search_form_holder_focus' : '') . '" href="#" onclick="return $Core.searchFriendsInput.processClick(this, \'' . $aUser['user_id'] . '\');"><span class="image">' . $aUser['user_image'] . '</span><span class="user">' . $aUser['full_name'] . '</span></div></li>';
			$sStoreUser .= '$Core.searchFriendsInput.storeUser('.$aUser['user_id'].', JSON.parse('. json_encode(json_encode($aUser)) .'));';
			
			if ($iFound > $this->get('total_search'))
			{
				break;
			}
		}
		$sHtml = '<div class="js_temp_friend_search_form_holder"><ul>' . $sHtml . '</ul></div>';
		$this->call($sStoreUser);
		$this->call('$("#'.$this->get('parent_id') . '").parent().find(".js_temp_friend_search_form").html(\''. str_replace("'", "\\'",$sHtml) .'\').show();');
	}
	
	public function delete()
	{
		$bDeleted = $this->get('id') ? Friend_Service_Process::instance()->delete($this->get('id')) : Friend_Service_Process::instance()->delete($this->get('friend_user_id'), false);
		
		if ($bDeleted)
		{
			if ($this->get('reload'))
			{				
				$this->call('window.location.href=window.location.href');
				return;
			}
			$this->call('$("#js_friend_' . $this->get('id') . '").remove();');
			$this->alert(_p('friend_successfully_removed'), _p('remove_friend'), 300, 150, true);
		}
	}
	
	public function search()
	{
		Phpfox::getBlock('friend.search', array('input' => $this->get('input'), 'friend_module_id' => $this->get('friend_module_id'), 'friend_item_id' => $this->get('friend_item_id'), 'type' => $this->get('type')));
		if ($this->get('type') == 'mail')
		{
			$this->call('<script type="text/javascript">$(\'#TB_ajaxWindowTitle\').html(\'' . _p('search_for_members', array('phpfox_squote' => true)) . '\');</script>');
		}
		else 
		{			
			$this->call('<script type="text/javascript">$(\'#TB_ajaxWindowTitle\').html(\'' . _p('search_for_your_friends', array('phpfox_squote' => true)) . '\');</script>');
		}
	}
	
	public function searchAjax()
	{		
		Phpfox::getBlock('friend.search', array('search' => true, 'friend_module_id' => $this->get('friend_module_id'), 'friend_item_id' => $this->get('friend_item_id'), 'page' => $this->get('page'), 'find' => $this->get('find'), 'letter' => $this->get('letter'), 'input' => $this->get('input'), 'view' => $this->get('view'), 'type' => $this->get('type')));
		
		$this->call('$(\'#js_friend_search_content\').html(\'' . $this->getContent() . '\'); updateFriendsList();$Behavior.globalInit();');
	}
	
	public function searchDropDown()
	{
		Phpfox::isUser(true);
		$oDb = Phpfox_Database::instance();
		$sFind = $this->get('search');
		if (empty($sFind))
		{
			$iCnt = 0;
		}
		else 
		{
			list($iCnt, $aFriends) = Friend_Service_Friend::instance()->get('friend.is_page = 0 AND friend.user_id = ' . Phpfox::getUserId() . ' AND (u.full_name LIKE \'%' . Phpfox::getLib('parse.input')->convert($oDb->escape($sFind)) . '%\' OR (u.email LIKE \'%' . $oDb->escape($sFind) . '@%\' OR u.email = \'' . $oDb->escape($sFind) . '\'))', 'friend.time_stamp DESC', 0, 10, true, true);
		}
		
		if ($iCnt && isset($aFriends))
		{
			$sHtml = '';
			foreach ($aFriends as $aFriend)
			{
				$sHtml .= '<li><a href="#" onclick="$(\'#' . $this->get('div_id') . '\').parent().hide(); $(\'#' . $this->get('input_id') . '\').val(\'' . $aFriend['user_id'] . '\'); $(\'#' . $this->get('text_id') . '\').val(\'' . addslashes(str_replace("O&#039;", "'", $aFriend['full_name'])) . '\'); return false;">' . Phpfox::getLib('parse.output')->shorten(Phpfox::getLib('parse.output')->clean($aFriend['full_name']), 40, '...') . '</a></li>';
			}
			$this->html('#' . $this->get('div_id'), '<ul>' . $sHtml . '</ul>');
			$this->call('$(\'#' . $this->get('div_id') . '\').parent().show();');
		}
		else 
		{
			$this->html('#' . $this->get('div_id'), '');
			$this->call('$(\'#' . $this->get('div_id') . '\').parent().hide();');
		}
	}
	
	public function loadSuggestion($bLoadTemplate = true)
	{		
		Phpfox::getBlock('friend.suggestion', 'reload=true');
		
		if ($bLoadTemplate === true)
		{
			Phpfox_Template::instance()->getTemplate('friend.block.suggestion');
		}
		
		$this->slideUp('#js_friend_suggestion_loader')->html('#js_friend_suggestion', $this->getContent(false))->slideDown('#js_friend_suggestion');	
		$this->call('$Core.loadInit();');	
	}
	
	public function removeSuggestion()
	{
		Phpfox::isUser(true);
		if (Friend_Service_Suggestion::instance()->remove($this->get('user_id')))
		{
			if ($this->get('load'))
			{
				$this->loadSuggestion(false);	
			}			
		}
	}
	
	public function addFriendsToList()
	{
		Phpfox::isUser(true);
		if (Friend_Service_List_Process::instance()->addFriendsToList((int) $this->get('list_id'), (array) $this->get('friends')))
		{
			Phpfox::getBlock('privacy.friend', array('bNoCustomDiv' => true, 'list_id' => (int) $this->get('list_id')));					
			
			$this->html('#js_custom_friend_list', $this->getContent(false));				
		}
	}
	
	public function manageList()
	{
		Phpfox::isUser(true);
		
		if ($this->get('type') == 'add')
		{
            Friend_Service_List_Process::instance()->addFriendsTolist($this->get('list_id'), $this->get('friend_id'));
		}
		else
		{
            Friend_Service_List_Process::instance()->removeFriendsFromlist($this->get('list_id'), $this->get('friend_id'));
		}
	}
	
	public function setProfileList()
	{
		Phpfox::isUser(true);
		
		if ($this->get('type') == 'add')
		{
			if (Friend_Service_List_Process::instance()->addListToProfile($this->get('list_id')))
			{
				$this->call('$(\'.friend_list_display_profile\').parent().hide();');
				$this->call('$(\'.friend_list_remove_profile\').parent().show();');
				$this->alert(_p('successfully_added_this_list_to_your_profile'), _p('profile_friend_lists'), 300, 150, true);								
			}
		}
		else
		{
			if (Friend_Service_List_Process::instance()->removeListFromProfile($this->get('list_id')))
			{
				$this->call('$(\'.friend_list_display_profile\').parent().show();');
				$this->call('$(\'.friend_list_remove_profile\').parent().hide();');
			}
		}
	}
	
	public function updateListOrder()
	{
		Phpfox::isUser(true);

		if (Friend_Service_List_Process::instance()->updateListOrder($this->get('list_id'), $this->get('friend_id')))
		{
			$this->alert(_p('order_successfully_saved'), _p('list_order'), 400, 150, true);
			$this->call('$Core.processForm(\'#js_friend_list_order_form\', true);');
		}
	}
	
	public function viewMoreFriends()
	{
		Phpfox::getComponent('friend.index', array(), 'controller');
		$this->remove('.js_pager_view_more_link');
		$this->append('#js_view_more_friends', $this->getContent(false));
		$this->call('$Core.loadInit();');		
	}
	
	public function getMutualFriends()
	{
		Phpfox::isUser(true);
		if ((int) $this->get('page') == 0)
		{
			$this->setTitle(_p('mutual_friends'));
		}
		Phpfox::getBlock('friend.mutual-browse');	
		
		if ((int) $this->get('page') > 0)
		{
			$this->remove('#js_friend_mutual_browse_append_pager');
			$this->append('#js_friend_mutual_browse_append', $this->getContent(false));
		}
    //reload user profile, https://github.com/moxi9/phpfox/issues/546
    $this->call('<script>$Behavior.globalInit();</script>');
	}
	
	public function moderation()
	{
		Phpfox::isUser(true);
				
		switch ($this->get('action'))
		{
			case 'accept':
				foreach ((array) $this->get('item_moderate') as $iId)
				{
					if (($aRequest = Friend_Service_Request_Request::instance()->getRequest($iId)) === false)
					{
						continue;
					}
                    
                    Friend_Service_Process::instance()->add(Phpfox::getUserId(), $aRequest['friend_user_id']);
					
					$this->remove('.js_friend_request_' . $iId);					
				}				
				$this->updateCount();
				break;
			case 'deny':
				foreach ((array) $this->get('item_moderate') as $iId)
				{
					if (($aRequest = Friend_Service_Request_Request::instance()->getRequest($iId)) === false)
					{
						continue;
					}
                    
                    Friend_Service_Process::instance()->deny(Phpfox::getUserId(), $aRequest['friend_user_id']);
					
					$this->remove('.js_friend_request_' . $iId);
				}				
				break;
		}
		
		$this->hide('.moderation_process');
	}

    public function removePendingRequest(){
        $iId = $this->get('id');
        if (Friend_Service_Request_Process::instance()->delete($iId, Phpfox::getUserId()))
        {
            $this->call('$Core.reloadPage();');
        }
    }

    public function denyRequest(){
        if (Friend_Service_Process::instance()->deny(Phpfox::getUserId(), $this->get('user_id'))){
            $this->call('$Core.reloadPage();');
        }
    }
}