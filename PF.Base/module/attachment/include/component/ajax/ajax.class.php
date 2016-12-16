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
 * @version 		$Id: ajax.class.php 6495 2013-08-23 09:52:29Z Fern $
 */
class Attachment_Component_Ajax_Ajax extends Phpfox_Ajax
{	
	public function upload()
	{
		Phpfox::getBlock('attachment.upload', array(
				'sCategoryId' => $this->get('category_id')
			)
		);
	
		$this->call('$("#js_attachment_content").html("' . $this->getContent() . '");');
		$this->call("$('#swfUploaderContainer').css('top',70).css('z-index',880);");
		$this->call('$Core.loadInit();');
	}
	
	public function add()
	{
		if ($this->get('attachment_custom') == 'photo')
		{
			$this->setTitle(_p('attach_a_photo'));
		}
		elseif ($this->get('attachment_custom') == 'video')
		{
			$this->setTitle(_p('attach_a_video'));
		}
		else 
		{
			$this->setTitle(_p('attach_a_file'));
		}
				
				
		$aParams = array(
				'sAttachments' => $this->get('attachments'),
				'sCategoryId' => $this->get('category_id'),
				'iItemId' => $this->get('item_id'),
				'sAttachmentInput' => $this->get('input'),
                'attachment_custom' => $this->get('attachment_custom')
			);
			
		if ($this->get('input') == 'js_theme_url_body' && Phpfox::getParam('core.csrf_protection_level') == 'high')
		{
			$aParams['bFixToken'] = true;
		}		
		
		Phpfox::getBlock('attachment.add', $aParams);
		
		
	}
	
	public function browse()
	{
		Phpfox::getBlock('attachment.archive', array('sPage' => (int)$this->get('page')));
		$this->call('$("#js_attachment_content").html("' . $this->getContent() . '");');
		$this->call("$('#swfUploaderContainer').css('top',0).css('z-index',0);");
		
	}
	
	public function updateDescription()
	{
		if (($iUserId = Attachment_Service_Attachment::instance()->hasAccess($this->get('iId'), 'delete_own_attachment', 'delete_user_attachment')) && Attachment_Service_Process::instance()->updateDescription((int) $this->get('iId'), $iUserId, $this->get('info')))
		{
			$this->html('#js_description' . $this->get('iId'), Phpfox::getLib('parse.output')->clean(Phpfox::getLib('parse.input')->clean($this->get('info'))), '.highlightFade()');
		}
	}
	
	public function inline()
	{
        Attachment_Service_Process::instance()->updateInline($this->get('id'));
	}
	
	public function inlineRemove()
	{
		if (Attachment_Service_Process::instance()->updateInline($this->get('id'), true))
		{
			$sTxt = $this->get('text');
			$sTxt = preg_replace('/\[attachment="' . (int) $this->get('id') . ':(.*)"\](.*)\[\/attachment\]/is', '', $sTxt);
			$sTxt = preg_replace('/\[attachment="' . (int) $this->get('id') . '"\](.*)\[\/attachment\]/is', '', $sTxt);
			$sTxt = str_replace("'", "\\'", $sTxt);
			$this->call('Editor.setContent(\'' . $sTxt . '\');');	
		}		
	}

	public function delete()
	{		
		if (($iUserId = Attachment_Service_Attachment::instance()->hasAccess($this->get('id'), 'delete_own_attachment', 'delete_user_attachment')) && is_numeric($iUserId) && Attachment_Service_Process::instance()->delete($iUserId, $this->get('id')))
		{
			$this->call("$('#js_attachment_id_" . $this->get('id') . "').slideUp();");
                        $this->call("$('.extra_info').show();");
		}
	}
	
	public function updateActivity()
	{
        Attachment_Service_Process::instance()->updateActivity($this->get('id'), $this->get('active'));
	}

	public function addViaLink()
	{
		Phpfox::isUser(true);
		
		$aVals = $this->get('val');
		
		if (Link_Service_Process::instance()->add($aVals, true))
		{
			$iId = Link_Service_Process::instance()->getInsertId();
			
			$iAttachmentId = Attachment_Service_Process::instance()->add(array(
					'category' => $aVals['category_id'],
					'link_id' => $iId
				)
			);			
			
			Phpfox::getBlock('link.display', array(
					'link_id' => $iId
				)
			);
			
			$this->call('var $oParent = $(\'#' . $aVals['attachment_obj_id'] . '\');');
			$this->call('$oParent.find(\'.js_attachment:first\').val($oParent.find(\'.js_attachment:first\').val() + \'' . $iAttachmentId . ',\'); $oParent.find(\'.js_attachment_list:first\').show(); $oParent.find(\'.js_attachment_list_holder:first\').prepend(\'<div class="attachment_row">' . $this->getContent() . '</div>\');');
			if (isset($aVals['attachment_inline']))
			{
				$this->call('$Core.clearInlineBox();');
			}
			else
			{
				$this->call('tb_remove();');
			}
            $this->call("$('.extra_info').hide();");
		}
	}
	
	public function playVideo()
	{
		$aAttachment = Attachment_Service_Attachment::instance()->getForDownload($this->get('attachment_id'));
		
		$sVideoPath = Phpfox::getParam('core.url_attachment') . $aAttachment['destination'];
		if (Phpfox::getParam('core.allow_cdn') && !empty($aAttachment['server_id']))
		{
			$sVideoPath = Phpfox::getLib('cdn')->getUrl($sVideoPath, $aAttachment['server_id']);	
		}		
		
		$sDivId = 'js_tmp_avideo_player_' . $aAttachment['attachment_id'];
		$this->html('#js_attachment_id_' . $this->get('attachment_id') . '', '<div id="' . $sDivId . '" style="width:480px; height:295px;"></div>');
		$this->call('$Core.player.load({id: \'' . $sDivId . '\', auto: true, type: \'video\', play: \'' . $sVideoPath . '\'}); $Core.player.play(\'' . $sDivId . '\', \'' . $sVideoPath . '\');');		
	}
    public function deleteAttachment(){
        $iItemId = $this->get('item_id');
        if (($iUserId = Attachment_Service_Attachment::instance()->hasAccess($iItemId, 'delete_own_attachment', 'delete_user_attachment')) && is_numeric($iUserId) && Attachment_Service_Process::instance()->delete($iUserId, $iItemId))
        {
            $this->call("$('#content_attachment_" . $iItemId . "').remove();");
            $this->call("$('.attachment_time_same_block').not(':has(.content_attachment)').remove()");
        }
    }
}