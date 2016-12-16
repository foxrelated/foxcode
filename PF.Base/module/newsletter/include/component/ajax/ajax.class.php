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
 * @version 		$Id: ajax.class.php 3860 2012-01-19 11:58:49Z Raymond_Benc $
 */
class Newsletter_Component_Ajax_Ajax extends Phpfox_Ajax
{	
	public function showPlain()
	{
		$sText = $this->get('sText');
		$aToStrip = array('[b]', '[i]', '[/b]', '[/i]', '[u]', '[/u]', '[ul]', '[/ul]');
		$sText = str_replace('</p>', "\n", $sText);
		$this->call('$("#txtPlain").val("'.str_replace($aToStrip, '', strip_tags($sText)).'");');
	}
}