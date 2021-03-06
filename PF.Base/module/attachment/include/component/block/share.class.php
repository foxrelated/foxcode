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
 * @version 		$Id: share.class.php 2766 2011-07-29 11:58:31Z Raymond_Benc $
 */
class Attachment_Component_Block_Share extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$mAttachmentShare = $this->getParam('attachment_share', null);

        $id  = $this->getParam('id');


		if ($mAttachmentShare === null)
		{
			return false;
		}
		
		if (!is_array($mAttachmentShare))
		{
			$mAttachmentShare = array('type' => $mAttachmentShare);
		}
		
		if (!isset($mAttachmentShare['inline']))
		{
			$mAttachmentShare['inline'] = false;
		}		
		
		$this->template()->assign(array(
				'aAttachmentShare' => $mAttachmentShare,
                'id'=> $id,
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
		(($sPlugin = Phpfox_Plugin::get('attachment.component_block_share_clean')) ? eval($sPlugin) : false);
//
//		$this->clearParam('attachment_share');
	}
}