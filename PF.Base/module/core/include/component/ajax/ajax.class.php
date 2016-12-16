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
 * @package  		Module_Core
 * @version 		$Id: ajax.class.php 6742 2013-10-07 15:07:33Z Raymond_Benc $
 */
class Core_Component_Ajax_Ajax extends Phpfox_Ajax
{	
	public function buildStats()
	{
		Phpfox::getBlock('core.admin-stat');
		
		$this->remove('#js_core_site_stat_build');
		$this->append('#js_core_site_stat', $this->getContent(false));
	}
	
	public function message()
	{
		Phpfox::getBlock('core.message', array(
				'sMessage' => ''
			)
		);
		$this->call('<script type="text/javascript">$(\'#js_custom_core_message\').html(sCustomMessageString);</script>');
	}

	public function info()
	{
		Phpfox::getBlock('core.info');

		$this->html('#' . $this->get('temp_id') . '', $this->getContent(false));
		$this->call('$(\'#' . $this->get('temp_id') . '\').parent().show();');
	}

	public function dashboard()
	{
		Phpfox::getBlock('core.dashboard');

		$this->html('#js_core_dashboard', $this->getContent(false));
	}

	public function activity()
	{
		Phpfox::getBlock('core.activity');

		$this->html('#' . $this->get('temp_id') . '', $this->getContent(false));
		$this->call('$(\'#' . $this->get('temp_id') . '\').parent().show();');
	}

	/**
	 * Core progress bar using apc_fetch.
	 */
	public function progress()
	{
		return false;
	}

	public function updateComponentSetting()
	{
		$aVals = $this->get('val');

		if (Core_Service_Process::instance()->updateComponentSetting($aVals))
		{
			Phpfox::getBlock($aVals['load_block']);
			
			if (isset($aVals['load_entire_block']))
			{
				$this->call('$(\'#' . $aVals['block_id'] . '\').before(\'' . $this->getContent() . '\').remove();');
			}
			else
			{
				$this->call('$(\'#' . $aVals['block_id'] . '\').find(\'.content\').html(\'' . $this->getContent() . '\');');
			}

			if (isset($aVals['load_init']))
			{
				$this->call('$Core.loadInit();');
			}
		}
	}

	public function hideBlock()
	{
		if ($this->get('sController') == 'pages.view')
		{
			Theme_Service_Process::instance()->updateBlock(array(
					'cache_id' => $this->get('type_id'),
					'item_id' => $this->get('custom_item_id'),
					'type_id' => 'pages',
					'is_installed' => '1'
				)
			);
		}
		else
		{
            Core_Service_Process::instance()->hideBlock($this->get('block_id'), $this->get('type_id'), $this->get('sController'));
		}
		
		$this->softNotice('Block was hidden');
	}

	public function getEditBarNew()
	{
		Phpfox::getBlock('core.new-setting');
		$this->html('#js_edit_block_' . $this->get('block_id'), $this->getContent(false))->slideDown('#js_edit_block_' . $this->get('block_id'));
	}

	public function getChildren()
	{
		Phpfox::getBlock('core.country-child', array('country_child_value' => $this->get('country_iso'), 'country_child_id' => $this->get('country_child_id')));

		$this->remove('#js_cache_country_iso')->html('#js_country_child_id', $this->getContent(false));
	}

	public function statOrdering()
	{
		if (Core_Service_Stat_Process::instance()->updateOrder($this->get('val')))
		{

		}
	}

	/**
	 * Clone of statOrdering to change the order of the items shown when cancelling an account
	 */
	public function cancellationsOrdering()
	{
		if (User_Service_Cancellations_Process::instance()->updateOrder($this->get('val')))
		{

		}
	}
	/**
	 * Clone of updateStatActivity, activates/deactivates a cancellation
	 */
	public function updateCancellationsActivity()
	{
		if (User_Service_Cancellations_Process::instance()->updateActivity($this->get('id'), $this->get('active')))
		{

		}
	}

	public function updateStatActivity()
	{
		if (Core_Service_Stat_Process::instance()->updateActivity($this->get('id'), $this->get('active')))
		{

		}
	}
	
	public function ftpPathSearch()
	{
		if (($aVals = $this->get('val')))
		{
			define('PHPFOX_FTP_LOGIN_PASS', true);
			
			$this->error(false);
			
			if (Phpfox::getLib('ftp')->connect($aVals['host'], $aVals['user_name'], $aVals['password']))
			{				
				$sPath = Phpfox::getLib('ftp')->getPath();
			
               if ($sPath === false)
               {
                    $this->html('#js_ftp_check_process', '')->html('#js_ftp_error', implode('', Phpfox_Error::get()))->show('#js_ftp_error');
               	
               		return;
               }               

               if (Phpfox::getLib('ftp')->test($sPath))
               {
               		$this->hide('#js_ftp_form')->show('#js_ftp_path')->val('#js_ftp_actual_path', str_replace('\\', '/', $sPath))->html('#js_ftp_check_process', '');
                    if (empty($sPath))
                    {
                        $this->show('#js_empty_ftp_path');
                    }
               }				
			}
			
			$this->html('#js_ftp_check_process', '')->html('#js_ftp_error', implode('', Phpfox_Error::get()))->show('#js_ftp_error');
			
			return;
		}	
		
		Phpfox::getBlock('core.ftp');
	}
	
	public function countryOrdering()
	{
		Phpfox::isAdmin(true);
		$aVals = $this->get('val');
        Core_Service_Process::instance()->updateOrdering(array(
				'table' => 'country',
				'key' => 'country_iso',
				'values' => $aVals['ordering']
			)
		);
		Phpfox::getLib('cache')->remove('currency');
		Phpfox::getLib('cache')->remove('country', 'substr');
	}
	
	public function currencyOrdering()
	{
		Phpfox::isAdmin(true);
		$aVals = $this->get('val');
        Core_Service_Process::instance()->updateOrdering(array(
				'table' => 'currency',
				'key' => 'currency_id',
				'values' => $aVals['ordering']
			)
		);
	}	
	
	public function updateCurrencyDefault()
	{
		if (Core_Service_Currency_Process::instance()->updateDefault($this->get('id'), $this->get('active')))
		{

		}		
	}
	
	public function updateCurrencyActivity()
	{
		if (Core_Service_Currency_Process::instance()->updateActivity($this->get('id'), $this->get('active')))
		{

		}		
	}	
	
	public function countryChildOrdering()
	{
		Phpfox::isAdmin(true);
		$aVals = $this->get('val');
        Core_Service_Process::instance()->updateOrdering(array(
				'table' => 'country_child',
				'key' => 'child_id',
				'values' => $aVals['ordering']
			)
		);		
	}
	
	public function prompt()
	{
		$sPhrase = '';
		$sTitle = '';
		$sCommand = '';
		$sError = '';

		switch ($this->get('type'))
		{
			case 'url':
				$sPhrase = _p('enter_the_url_of_your_link');
				$sCommand = 'Editor.createBBtag(\'[link=\\\'\' + $(\'#js_global_prompt_value\').val() + \'\\\']\', \'[/link]\', \'' . $this->get('editor') . '\', $(\'#js_global_prompt_value\').val());';
				$sError = _p('fill_in_a_proper_url');
				$sTitle = _p('url');
				break;
			case 'img':
				$sPhrase = _p('enter_the_url_of_your_image');
				$sCommand = 'Editor.createBBtag(\'[img]\' + $(\'#js_global_prompt_value\').val() + \'\', \'[/img]\', \'' . $this->get('editor') . '\');';
				$sError = _p('provide_a_proper_image_path');
				$sTitle = _p('image');
				break;
		}		
		
		echo '<div class="main_break"></div>';
		echo '<div id="js_prompt_error_message" class="error_message" style="display:none;">' . $sError . '</div>';
		echo $sPhrase;
		echo '<div class="p_4"><input type="text" name="url" value="http://" style="width:80%;" id="js_global_prompt_value" /><div class="p_top_4"><input type="submit" value="' . _p('submit') . '" class="button btn-primary" onclick="if (empty($(\'#js_global_prompt_value\').val()) || $(\'#js_global_prompt_value\').val() == \'http://\') { $(\'#js_prompt_error_message\').show(); } else { ' . $sCommand . ' tb_remove(); }" /></div></div>';
		echo '<script type="text/javascript">$(\'#TB_ajaxWindowTitle\').html(\'' . str_replace("'", "\'", $sTitle) . '\');</script>';
	}
	
	public function showGiftPoints()
	{
		Phpfox::getBlock('core.giftpoints', array('user_id' => $this->get('user_id')));
	}
	
	public function doGiftPoints()
	{
		if (User_Service_Activity::instance()->doGiftPoints($this->get('user_id'), $this->get('amount')))
		{			
			$this->html('#div_show_gift_points', _p('gift_sent_successfully'));
		}
		else
		{
			$sError = Phpfox_Error::get();
			$this->html('#div_show_gift_points', 'An error occurred: ' . array_pop($sError));
		}
	}
	
	
	public function getMyCity()
	{
		$sInfo = Phpfox_Request::instance()->send('http://freegeoip.net/json/' . Phpfox_Request::instance()->getIp(), array(), 'GET');
		$oInfo = json_decode($sInfo);
		if ($this->get('section') == 'feed')
		{
			// during testing latlng wont work
			if (empty($oInfo->latitude))
			{
				$oInfo->latitude = '-43.132123';
				$oInfo->longitude = '9.140625';
			}
			$this->call('$Core.Feed.gMyLatLng = new google.maps.LatLng("' . $oInfo->latitude . '","' . $oInfo->longitude .'");');
			$this->call('setCookie("core_places_location", "' . $oInfo->latitude .',' . $oInfo->longitude . '");');
			$this->call('$("#hdn_location_name, #val_location_name").val("' . $oInfo->city . ', ' . $oInfo->country_name . '"); ');
			$this->call('$Core.Feed.getNewLocations();');
			$this->call('$Core.Feed.createMap();');
		}
		
		if ($this->get('saveLocation'))
		{
            User_Service_Process::instance()->saveMyLatLng(array('latitude' => $oInfo->latitude, 'longitude' => $oInfo->longitude));
		}
	}
	
	/* Called from main.js loads the blocks from an ajax call after the controller has loaded */
	public function loadDelayedBlocks()
	{
		// These are blocks intentionally delayed
		$aLocations = explode(',',$this->get('locations'));
		$oModule = Phpfox_Module::instance();
		$aParams = $this->get('params');
		define('PHPFOX_LOADING_DELAYED', true);
		if ($this->get('locations') != null && count($aLocations) > 0)
		{	
			$oModule->loadBlocks();
			if ($oModule->getFullControllerName() == 'core.index' && Phpfox::isUser())
			{
				$oModule->setController('core.index-member');
			}
			foreach ($aLocations as $iLocation)
			{
				$aBlocks = $oModule->getModuleBlocks($iLocation, true);
				foreach ($aBlocks as $sBlock)
				{
					Phpfox::getBlock($sBlock);
					$this->html('#delayed_block_' . $iLocation , $this->getContent(false));
				}
			}
		}
		else if ($this->get('loadContent') != null) // Then we are loading the 'content'
		{
			$sController = $this->get('loadContent');
			if (!empty($aParams))
			{
				$oRequest = Phpfox_Request::instance();
				foreach ($aParams as $sIndex => $sKey)
				{
					$oRequest->set($sIndex, $sKey);
				}
			}		
			$oModule->getComponent($sController, $aParams, 'controller');
			
			$this->hide('#delayed_block_image');
			$this->html('#delayed_block', $this->getContent(false) );
			$this->show('#delayed_block');
		}
		else if ($this->get('delayedTemplates') != null)
		{
			
			$aTemplates = $this->get('delayedTemplates');
			
			foreach ($aTemplates as $sIndex => $sKey)
			{
				$aTemplate = explode('=', $sKey);
				$sTemplate = Phpfox_Template::instance()->getBuiltFile($aTemplate[1]);
				$this->html('#' . $aTemplate[1], $sTemplate);
			}
			
		}
		$this->call('$Behavior.loadDelayedBlocks = function(){}; $Core.loadInit();');
	}
	
	/** Called from rewrite.js in the AdminCP -> SEO -> Rewrite URL */
	public function updateRewrites()
	{
		Phpfox::isAdmin(true);
		$aRewrites = json_decode($this->get('aRewrites'), true);
		
		Core_Service_Redirect_Process::instance()->updateRewrites($aRewrites);
		
		if (Phpfox_Error::isPassed())
		{
			$this->call('$Core.AdminCP.Rewrite.saveSuccessful();');
			$this->softNotice('Saved Successfully');
		}
		else
		{
			$this->call('$("#processing").hide();');
		}
		
	}

    public function updateNotification(){
        //Check friend
        if (Phpfox::isModule('friend')){
            $iNumberRequest = Friend_Service_Request_Request::instance()->getTotal();
            if ($iNumberRequest > 0){
                $this->call('$("#js_total_new_friend_requests").html("'.$iNumberRequest.'").show();');
            } else {
                $this->call('$("#js_total_new_friend_requests").html("'.$iNumberRequest.'").hide();');
            }
        }
        //Check notification
        if (Phpfox::isModule('notification')){
            $iNumberNotification = Notification_Service_Notification::instance()->getUnseenTotal();
            if ($iNumberNotification > 0){
                $this->call('$("#js_total_new_notifications").html("'.$iNumberNotification.'").show();');
            } else {
                $this->call('$("#js_total_new_notifications").html("'.$iNumberNotification.'").hide();');
            }
        }

        if ($sPlugin = Phpfox_Plugin::get('notification.component_ajax_update_1')){eval($sPlugin);}
    }
    
	public function removeRewrite()
	{
		Phpfox::isAdmin(true);

		Core_Service_Redirect_Process::instance()->removeRewrite($this->get('id'));
	}
}