<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package 		Phpfox_Ajax
 * @version 		$Id: ajax.class.php 100 2009-01-26 15:15:26Z Raymond_Benc $
 */
class Egift_Component_Ajax_Ajax extends Phpfox_Ajax
{

	public function setOrder()
	{
		Phpfox::isAdmin(true);
		$aVals = $this->get('val');
		Egift_Service_Process::instance()->setOrder($aVals['ordering']);
	}
}