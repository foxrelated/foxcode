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
 * @package  		Module_Attachment
 * @version 		$Id: archive.class.php 877 2009-08-20 11:21:32Z Raymond_Benc $
 */
class Attachment_Component_Block_Archive extends Phpfox_Component 
{
	/**
	 * Controller
	 */
	public function process()
	{
		$iPage = $this->getParam('sPage');
		$iPageSize = 12;
		
		list($iCnt, $aItems) = Attachment_Service_Attachment::instance()->get(array("attachment.user_id = " . Phpfox::getUserId() . ""), 'attachment.time_stamp DESC');
	
		Phpfox_Pager::instance()->set(array('page' => $iPage, 'size' => $iPageSize, 'count' => $iCnt, 'ajax' => 'attachment.browse'));
		
		$aUser = User_Service_User::instance()->get(Phpfox::getUserId(), true);
        
        $this->template()->assign([
            'aItems'           => $aItems,
            'sUrlPath'         => Phpfox::getParam('core.url_attachment'),
            'sThumbPath'       => Phpfox::getParam('core.url_thumb'),
            'sUsage'           => $aUser['space_total'],
            'bCanUseInline'    => false,
            'sAttachmentInput' => $this->request()->get('input')
        ]);
    }
}