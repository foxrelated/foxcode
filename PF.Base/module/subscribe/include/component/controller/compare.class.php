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
class Subscribe_Component_Controller_Compare extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$aForCompare = Subscribe_Service_Subscribe::instance()->getPackagesForUserCompare();
        $iBootstrapCol = 3;
        if (isset($aForCompare)){
            $iNumberPackage = count($aForCompare);
            switch ($iNumberPackage){
                case 2:
                    $iBootstrapCol = 6;
                    break;
                case 3:
                    $iBootstrapCol = 4;
                    break;
                default:
                    $iBootstrapCol = 3;
                    break;
            }
        }
		foreach ($aForCompare as $iKey => $aPackage)
		{
			$iMatch = preg_match("/\{phrase var='(.*)'/i", $aPackage['description'], $aMatch);
			if ($iMatch)
			{
				$aForCompare[$iKey]['description'] = _p($aMatch[1]);
			}			
		}
		
		$this->template()
			->setHeader(array(
					'compare.css' => 'module_subscribe'
				)
			)
			->assign(array(
					'aPackages' => $aForCompare,
					'bIsDisplay' => true,
                    'iBootstrapCol' => $iBootstrapCol
				)
			)
			->setFullSite()
			->setBreadCrumb(_p('membership_packages'), $this->url()->makeUrl('subscribe'))
			->setTitle(_p('compare_subscription_packages'));
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('subscribe.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
	}
}