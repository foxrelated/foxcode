<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox_Ajax
 * @version 		$Id: ajax.class.php 7075 2014-01-28 16:04:34Z Fern $
 */
class Pages_Component_Ajax_Ajax extends Phpfox_Ajax
{
	public function removeLogo()
	{
		if (($aPage = Pages_Service_Process::instance()->removeLogo($this->get('page_id'))) !== false)
		{
			$this->call('window.location.href = \'' . $aPage['link'] . '\';');
		}
	}	
	
	public function deleteWidget()
	{
		if (Pages_Service_Process::instance()->deleteWidget($this->get('widget_id')))
		{
			$this->slideUp('#js_pages_widget_' . $this->get('widget_id'));
		}
	}
	
	public function widget()
	{
		$this->setTitle(_p('widgets'));
		Phpfox::getComponent('pages.widget', array(), 'controller');			
		
		(($sPlugin = Phpfox_Plugin::get('pages.component_ajax_widget')) ? eval($sPlugin) : false);
		
		echo '<script type="text/javascript">$Core.loadInit();</script>';
	}
	
	public function add()
	{
		Phpfox::isUser(true);
		if (($iId = Pages_Service_Process::instance()->add($this->get('val'))))
		{
			$aPage = Pages_Service_Pages::instance()->getPage($iId);
			$this->call('window.location.href = \'' . Phpfox_Url::instance()->makeUrl('pages.add', array('id' => $aPage['page_id'], 'new' => '1')) . '\';');
		}
		else
		{
			$sError = Phpfox_Error::get();
			$sError = implode('<br />', $sError);
			$this->alert($sError);
			$this->call('$Core.processForm(\'#js_pages_add_submit_button\', true);');
		}
	}
	
	public function addFeedComment()
	{
		Phpfox::isUser(true);
				
		$aVals = (array) $this->get('val');
        $iCustomPageId = isset($_REQUEST['custom_pages_post_as_page']) ? $_REQUEST['custom_pages_post_as_page'] : 0;
        if (($iCustomPageId && $iCustomPageId != $aVals['callback_item_id']) || !Pages_Service_Pages::instance()->hasPerm($aVals['callback_item_id'], 'pages.share_updates')){
            $this->alert(_p('You do not have permission to add comments'));
            $this->call('$Core.activityFeedProcess(false);');
            return;
        }

		if (!defined('PAGE_TIME_LINE'))
		{
		    // Check if this item is a page and is using time line
		    if (isset($aVals['callback_module']) && $aVals['callback_module'] == 'pages' && isset($aVals['callback_item_id']) && Pages_Service_Pages::instance()->timelineEnabled($aVals['callback_item_id']))
		    {
			    define('PAGE_TIME_LINE', true);
		    }
		}
		
		if (Phpfox::getLib('parse.format')->isEmpty($aVals['user_status']))
		{
			$this->alert(_p('add_some_text_to_share'));
			$this->call('$Core.activityFeedProcess(false);');
			return;			
		}
		
		$aPage = Pages_Service_Pages::instance()->getPage($aVals['callback_item_id']);

		if (!isset($aPage['page_id']))
		{
			$this->alert(_p('unable_to_find_the_page_you_are_trying_to_comment_on'));
			$this->call('$Core.activityFeedProcess(false);');
			return;
		}
		
		$sLink = Pages_Service_Pages::instance()->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']);
		$aCallback = array(
			'module' => 'pages',
			'table_prefix' => 'pages_',
			'link' => $sLink,
			'email_user_id' => $aPage['user_id'],
			'subject' => _p('full_name_wrote_a_comment_on_your_page_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aPage['title'])),
			'message' => _p('full_name_wrote_a_comment_link', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aPage['title'])),
			'notification' => ($this->get('custom_pages_post_as_page') ? null : 'pages_comment'),
			'feed_id' => 'pages_comment',
			'item_id' => $aPage['page_id'],
			'add_tag' => true
		);
		
		$aVals['parent_user_id'] = $aVals['callback_item_id'];
		
		if (isset($aVals['user_status']) && ($iId = Feed_Service_Process::instance()->callback($aCallback)->addComment($aVals)))
		{
			Phpfox_Database::instance()->updateCounter('pages', 'total_comment', 'page_id', $aPage['page_id']);
			
			Feed_Service_Feed::instance()->callback($aCallback)->processAjax($iId);
		}
		else 
		{
			$this->call('$Core.activityFeedProcess(false);');
		}		
	}	
	
	public function changeUrl()
	{
		Phpfox::isUser(true);
		
		if (($aPage = Pages_Service_Pages::instance()->getForEdit($this->get('id'))))
		{
			$aVals = $this->get('val');
			
			$sNewTitle = Phpfox::getLib('parse.input')->cleanTitle($aVals['vanity_url']);
			
			if (Phpfox::getLib('parse.input')->allowTitle($sNewTitle, _p('page_name_not_allowed_please_select_another_name')))
			{
				if (Pages_Service_Process::instance()->updateTitle($this->get('id'), $sNewTitle))
				{
					$this->alert(_p('successfully_updated_your_pages_url'), _p('url_updated'), 300, 150, true);
				}
			}		
		}
		
		$this->call('$Core.processForm(\'#js_pages_vanity_url_button\', true);');
	}
	
	public function signup()
	{
		Phpfox::isUser(true);
		if (Pages_Service_Process::instance()->register($this->get('page_id')))
		{
			$this->alert(_p('successfully_registered_for_this_page'));
		}
	}
	
	public function moderation()
	{
		Phpfox::isUser(true);
        $sAction = $this->get('action');

		if (Pages_Service_Process::instance()->moderation($this->get('item_moderate'), $this->get('action')))
		{
			foreach ((array) $this->get('item_moderate') as $iId)
			{
				$this->remove('#js_pages_user_entry_' . $iId);	
			}
			
			$this->updateCount();
			switch ($sAction) {
                case 'delete':
                    $sMessage = _p('successfully_deleted_user_s_dot');
                    break;
                case 'approve':
                    $sMessage = _p('successfully_approved_user_s_dot');
                    break;
                default:
                    $sMessage = _p('successfully_moderated_user_s');
                    break;
            }
			$this->alert($sMessage, _p('moderation'), 300, 150, true);
		}		
		
		$this->hide('.moderation_process');			
	}	
	
	public function logBackUser()
	{
		$this->error(false);
		Phpfox::isUser(true);
		$aUser = Pages_Service_Pages::instance()->getLastLogin();
		list ($bPass, $aReturn) = User_Service_Auth::instance()->login($aUser['email'], $this->get('password'), true, $sType = 'email');
		if ($bPass)			
		{
            Pages_Service_Process::instance()->clearLogin($aUser['user_id']);
			
			$this->call('window.location.href = \'' . Phpfox_Url::instance()->makeUrl('') . '\';');
		}
		else
		{
			$this->html('#js_error_pages_login_user', '<div class="error_message">' . implode('<br />', Phpfox_Error::get()) . '</div>');
		}		
	}
	
	public function login()
	{
		Phpfox::isUser(true);
		$this->setTitle(_p('login_as_a_page'));
		Phpfox::getBlock('pages.login');
	}
	
	public function loginSearch()
	{
        // Parameters to be sent to the block
        $aParams = array(
            'page' => $this->get('page'),
        );
		
		// Call the block and send the parameters
		Phpfox::getBlock('pages.login', $aParams);
		
		// Display the block into the TB box
        $this->call('$(\'.js_box_content\').html(\'' . $this->getContent() . '\');');
 
	}
	
	public function processLogin()
	{
		if (Pages_Service_Process::instance()->login($this->get('page_id')))
		{
			$this->call('window.location.href = \'' . Phpfox_Url::instance()->makeUrl('') . '\';');
		}
	}
	
	public function pageModeration()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('pages.can_moderate_pages', true);
		
		switch ($this->get('action'))
		{
			case 'approve':
				foreach ((array) $this->get('item_moderate') as $iId)
				{
                    Pages_Service_Process::instance()->approve($iId);
					$this->remove('#js_pages_' . $iId);					
				}								
				$sMessage = _p('pages_s_successfully_approved');
				break;			
			case 'delete':
				foreach ((array) $this->get('item_moderate') as $iId)
				{
                    Pages_Service_Process::instance()->delete($iId);
					$this->slideUp('#js_pages_' . $iId);
				}				
				$sMessage = _p('pages_s_successfully_deleted');
				break;
            default:
                $sMessage = '';
                break;
		}
		
		$this->updateCount();
		
		$this->alert($sMessage, _p('moderation'), 300, 150, true);
		$this->hide('.moderation_process');					
	}
	
	public function approve()
	{
		if (Pages_Service_Process::instance()->approve($this->get('page_id')))
		{
			$this->alert(_p('page_has_been_approved'), _p('page_approved'), 300, 100, true);
			$this->hide('#js_item_bar_approve_image');
			$this->hide('.js_moderation_off'); 
			$this->show('.js_moderation_on');
		}
	}	
	
	public function updateActivity()
	{
		if (Pages_Service_Process::instance()->updateActivity($this->get('id'), $this->get('active'), $this->get('sub')))
		{

		}
	}	
	
	public function categoryOrdering()
	{
		Phpfox::isAdmin(true);
		$aVals = $this->get('val');
        Core_Service_Process::instance()->updateOrdering(array(
				'table' => 'pages_type',
				'key' => 'type_id',
				'values' => $aVals['ordering']
			)
		);		
		
		Phpfox::getLib('cache')->remove('pages', 'substr');
	}	
	
	public function categorySubOrdering()
	{
		Phpfox::isAdmin(true);
		$aVals = $this->get('val');
        Core_Service_Process::instance()->updateOrdering(array(
				'table' => 'pages_category',
				'key' => 'category_id',
				'values' => $aVals['ordering']
			)
		);		
		
		Phpfox::getLib('cache')->remove('pages', 'substr');
	}	

	public function approveClaim()
	{
		Phpfox::isAdmin(true);
		if (Pages_Service_Process::instance()->approveClaim($this->get('claim_id')))
		{
			$this->hide('#claim_' . $this->get('claim_id'));
		}
		else
		{
			$this->alert(_p('An error occurred'));
		}
	}
	
	public function denyClaim()
	{
		Phpfox::isAdmin(true);
		if (Pages_Service_Process::instance()->denyClaim($this->get('claim_id')))
		{
			$this->hide('#claim_' . $this->get('claim_id'));
		}
		else
		{
			$this->alert(_p('An error occurred'));
		}
	}
	
	public function setCoverPhoto()
	{
		$iPageId = $this->get('page_id');
		$iPhotoId = $this->get('photo_id');
		
		if (Pages_Service_Process::instance()->setCoverPhoto($iPageId , $iPhotoId))
		{
			$this->call('window.location.href = "' . Phpfox::permalink('pages', $this->get('page_id'), '') . 'coverupdate_1";');
			
		}
	}

	public function repositionCoverPhoto()
	{
		if (Pages_Service_Process::instance()->updateCoverPosition($this->get('id'), $this->get('position')))
		{
			Phpfox::addMessage(_p('position_set_correctly'));
		}
	}
	
	public function updateCoverPosition()
	{
		if (Pages_Service_Process::instance()->updateCoverPosition($this->get('page_id'), $this->get('position')))
		{
			$this->call('window.location.href = "' . Phpfox::permalink('pages', $this->get('page_id'), '') . '";');
			Phpfox::addMessage(_p('position_set_correctly'));
		}
	}
	
	public function removeCoverPhoto()
	{
		if (Pages_Service_Process::instance()->removeCoverPhoto($this->get('page_id')))
		{
			$this->call('window.location.href=window.location.href;');
		}
	}

    public function cropme(){
        Phpfox::getBlock('pages.cropme');
        $this->call('<script>$Behavior.crop_pages_image_photo();</script>');
    }

    public function processCropme(){
        $aVals = $this->get('val');
        $aPage = Pages_Service_Pages::instance()->getForEdit($aVals['page_id']);
        if (!Pages_Service_Pages::instance()->isAdmin($aPage)){
            return false;
        }
        //Process crop image
        if (isset($aVals['crop-data']) && !empty($aVals['crop-data'])){
            $sTempPath = PHPFOX_DIR_CACHE . md5('pages_avatar' . $aVals['page_id']) . '.png';
            list($type, $data) = explode(';', $aVals['crop-data']);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);
            file_put_contents($sTempPath, $data);
            $oImage = Phpfox_Image::instance();
            $aSize = [
                '50' => '',
                '120' => '',
                '200' => 'square',
            ];
            foreach ($aSize as $iSize => $value){
                $oImage->createThumbnail(sprintf($sTempPath, ''), Phpfox::getParam('pages.dir_image') . sprintf($aPage['image_path'], '_' . $iSize), $iSize, $iSize, false);
                if ($value == 'square'){
                    $oImage->createThumbnail(sprintf($sTempPath, ''), Phpfox::getParam('pages.dir_image') . sprintf($aPage['image_path'], '_' . $iSize . '_square'), $iSize, $iSize, false);
                }
            }
            @unlink($sTempPath);
        }
        //End crop image
        $sImagePath = Phpfox::getLib('image.helper')->display([
            'server_id' => $aPage['image_server_id'],
            'path' => 'pages.url_image',
            'file' => $aPage['image_path'],
            'suffix' => '_120',
            'max_width' => '120',
            'max_height' => '120',
            'thickbox' => true,
            'time_stamp' => true
        ]);
        $sImagePath = str_replace(array("\n", "\t"), '', $sImagePath);
        $sImagePath = str_replace('\\', '\\\\', $sImagePath);
        $sImagePath = str_replace("'", "\\'", $sImagePath);
        $sImagePath = str_replace('"', '\"', $sImagePath);
        $this->call('$("#js_event_current_image").html("' . $sImagePath . '");');
        $this->call("tb_remove();");
    }
}