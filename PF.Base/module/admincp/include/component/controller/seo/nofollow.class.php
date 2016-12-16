<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Add a new setting from the Admin CP
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Admincp
 * @version 		$Id: nofollow.class.php 4163 2012-05-14 08:45:16Z Raymond_Benc $
 */
class Admincp_Component_Controller_Seo_Nofollow extends Phpfox_Component 
{
	/**
	 * Controller
	 * @todo Complete the update routine...
	 */
	public function process()
	{
		$this->template()->setTitle(_p('nofollow_urls'))
			->setBreadCrumb(_p('nofollow_urls'), $this->url()->makeUrl('admincp.seo.nofollow'))
			->assign(array(
					'aNoFollows' => Admincp_Service_Seo_Seo::instance()->getNoFollows()
				)
			);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('admincp.component_controller_seo_nofollow_clean')) ? eval($sPlugin) : false);
	}	
}