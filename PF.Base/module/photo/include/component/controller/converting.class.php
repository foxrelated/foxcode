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
 * @package 		Phpfox_Component
 * @version 		$Id: add.class.php 3954 2012-02-28 14:54:21Z Raymond_Benc $
 */ 
class Photo_Component_Controller_Converting extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::isUser(true);
		$aImage = Photo_Service_Photo::instance()->getForConverting(Phpfox::getUserId());
		if (empty($aImage))
		{
			die('aImage is empty and userId: ' . Phpfox::getUserId());
		}
		
		foreach ($aImage as $iKey => $aImg)
		{
			$aImage[$iKey]['completed'] = 'false';
			$aImage[$iKey]['picup'] = '1';
		}
		$sImage = urlencode(base64_encode(json_encode($aImage)));
		$this->template()
			->setHeader(array(
				'<script type="text/javascript"> $Behavior.imageRun = function(){$.ajaxCall("photo.process", "photos=' . $sImage . '&action=picup"); } </script>"'
				
			))
			->setTemplate('blank');
		
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('photo.component_controller_converting_clean')) ? eval($sPlugin) : false);
	}
}