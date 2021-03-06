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
 * @package  		Module_Subscribe
 * @version 		$Id: ajax.class.php 7107 2014-02-11 19:46:17Z Fern $
 */
class Subscribe_Component_Ajax_Ajax extends Phpfox_Ajax
{
	public function upgrade()
	{
		$this->error(false);
		
		Phpfox::getBlock('subscribe.upgrade', array('bIsThickBox' => true));
		
		if (!Phpfox_Error::isPassed())
		{
			echo '<div class="error_message">' . implode('<br />', Phpfox_Error::get()) . '</div>';
		}
	}
	
	public function listUpgrades()
	{
		Phpfox::getBlock('subscribe.list');
		
		$this->html('#' . $this->get('temp_id') . '', $this->getContent(false));
		$this->call('$(\'#' . $this->get('temp_id') . '\').parent().show();');
	}
	
	public function listUpgradesOnSignup()
	{
		Phpfox::getBlock('subscribe.list', array('on_signup' => true));

		$this->call('<script> $Core.loadInit(); </script>');
	}
	
	public function ordering()
	{		
		if (Subscribe_Service_Process::instance()->updateOrder($this->get('val')))
		{
			
		}
	}
	
	public function updateActivity()
	{		
		if (Subscribe_Service_Process::instance()->updateActivity($this->get('package_id'), $this->get('active')))
		{
			
		}
	}	
	
	public function deleteImage()
	{
        Subscribe_Service_Process::instance()->deleteImage($this->get('package_id'));
	}
	
	public function updatePurchase()
	{
		if (Subscribe_Service_Purchase_Process::instance()->updatePurchase($this->get('purchase_id'), $this->get('status')))
		{
			
		}	
	}
}