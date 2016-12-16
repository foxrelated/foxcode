<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');


class Forum_Component_Block_Thanks extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$iPostId = (int) $this->request()->get('post_id', 0);
		if (!$iPostId) return false;
		$aThanks = Phpfox::getService('forum.post')->getThanksForPost($iPostId);
		
		$this->template()->assign(array(
				'aThanks' => $aThanks,
				'iPostId' => $iPostId
			)
		);
        return null;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('forum.component_block_thanks_clean')) ? eval($sPlugin) : false);
	}
}