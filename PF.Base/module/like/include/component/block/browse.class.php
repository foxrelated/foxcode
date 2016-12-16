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
 * @version 		$Id: browse.class.php 5840 2013-05-09 06:14:35Z Raymond_Benc $
 */
class Like_Component_Block_Browse extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
        $aLikes = Like_Service_Like::instance()->getLikes($this->request()->get('type_id'), $this->request()->getInt('item_id'), $this->request()->getInt('feed_table_prefix', ''));

		$sErrorMessage = '';
		if ($this->request()->get('type_id') == 'pages')
		{
			$aPage = Pages_Service_Pages::instance()->getPage($this->request()->getInt('item_id'));
			if (!count($aLikes))
			{
				if ($aPage['type_id'] == 3)
				{
					$sErrorMessage = _p('this_group_has_no_members');				
				}
				else
				{
					$sErrorMessage = _p('nobody_likes_this');
				}
			}
		}
		
		$bIsPageAdmin = false;
		if ($this->request()->get('type_id') == 'pages' && Pages_Service_Pages::instance()->isAdmin($this->request()->getInt('item_id')))
		{
			$bIsPageAdmin = true;
		}

        (($sPlugin = Phpfox_Plugin::get('like.component_block_browse_process')) ? eval($sPlugin) : false);
		
		$this->template()->assign(array(
				'aLikes' => $aLikes,
				'sErrorMessage' => $sErrorMessage,
                'sItemType' => $this->request()->get('type_id'),
				'iItemId' => $this->request()->getInt('item_id'),
				'bIsPageAdmin' => $bIsPageAdmin
			)
		);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('like.component_block_browse_clean')) ? eval($sPlugin) : false);
	}
}