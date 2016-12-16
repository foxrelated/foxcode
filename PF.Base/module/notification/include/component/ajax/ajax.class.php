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
 * @package 		Phpfox_Ajax
 * @version 		$Id: ajax.class.php 100 2009-01-26 15:15:26Z Raymond_Benc $
 */
class Notification_Component_Ajax_Ajax extends Phpfox_Ajax
{	
	public function update()
	{
		Phpfox::massCallback('getGlobalNotifications');
        
        if ($sPlugin = Phpfox_Plugin::get('notification.component_ajax_update_1')){eval($sPlugin);}
        
		$this->call('$Core.notification.setTitle();');
	}
	
	public function updateSeen()
	{
		Phpfox::isUser(true);
		$sIds = $this->get('id');
		if (!empty($sIds) && Phpfox::getLib('parse.format')->isSerialized($sIds))
		{
			foreach (unserialize($sIds) as $iId)
			{
				Notification_Service_Process::instance()->updateSeen($iId);
			}		
		}
	}

	public function getAll()
	{
		if (!Phpfox::isUser())
		{
			$this->call('<script type="text/javascript">window.location.href = \'' . Phpfox_Url::instance()->makeUrl('user.login') . '\';</script>');
		}
		else
		{
			// This function caches into a static so it shouldn't be an extra load
			Phpfox::getBlock('notification.link');
		}
	}
	
	public function delete()
	{
		Phpfox::isUser(true);
		
		if (Notification_Service_Process::instance()->deleteById($this->get('id')))
		{
			$this->slideUp('#js_notification_' . $this->get('id'));
		}
	}
	
	public function removeAll()
	{
		Phpfox::isUser(true);
		
		if (Notification_Service_Process::instance()->deleteAll())
		{
            $this->hide('#notification-panel-body .panel_rows');
            $this->append('#notification-panel-body', '<div class="message">'. _p('no_new_notifications') .'</div>');
		}
		
		$this->hide('.table_clear_ajax');
		$this->call("\$('.js_notification_trash > i').removeClass('fa-circle-o-notch').removeClass('fa-spin').addClass('fa-trash');");
	}
}