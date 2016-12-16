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
 * @package 		Phpfox_Ajax
 * @version 		$Id: ajax.class.php 3642 2011-12-02 10:01:15Z Miguel_Espinoza $
 */
class Marketplace_Component_Ajax_Ajax extends Phpfox_Ajax
{
	public function delete()
	{
		if (Marketplace_Service_Process::instance()->delete($this->get('id')))
		{
			$this->call('$(\'#js_mp_item_holder_' . $this->get('id') . '\').html(\'<div class="message" style="margin:0px;">' . _p('successfully_deleted_listing') . '</div>\').fadeOut(5000);');
		}
	}
	
	public function setDefault()
	{
		if (Marketplace_Service_Process::instance()->setDefault($this->get('id')))
		{
			
		}
	}
	
	public function deleteImage()
	{
		if (Marketplace_Service_Process::instance()->deleteImage($this->get('id')))
		{
			
		}
	}

	public function listInvites()
	{
		Phpfox::getBlock('marketplace.list');
		
		$this->html('#js_mp_item_holder', $this->getContent(false));
	}

	public function feature()
	{
		if (Marketplace_Service_Process::instance()->feature($this->get('listing_id'), $this->get('type')))
		{
			// js_mp_item_holder_4
			if ($this->get('type'))
			{
				$this->addClass('#js_mp_item_holder_' . $this->get('listing_id'), 'row_featured');
				$this->alert(_p('listing_successfully_featured'), _p('feature'), 300, 150, true);
			}
			else 
			{
				$this->removeClass('#js_mp_item_holder_' . $this->get('listing_id'), 'row_featured');
				$this->alert(_p('listing_successfully_un_featured'), _p('un_feature'), 300, 150, true);
			}				
		}
	}	

	public function sponsor()
	{
	    if (Marketplace_Service_Process::instance()->sponsor($this->get('listing_id'), $this->get('type')))
	    {
			if ($this->get('type') == '1')
			{
				Phpfox::getService('ad.process')->addSponsor(array('module' => 'marketplace', 'item_id' => $this->get('listing_id')));
				// listing was sponsored
				$sHtml = '<a href="#" title="' . _p('unsponsor_this_listing') . '" onclick="$(\'#js_sponsor_phrase_' . $this->get('listing_id') . '\').hide(); $.ajaxCall(\'marketplace.sponsor\', \'listing_id=' . $this->get('listing_id') . '&amp;type=0\', \'GET\'); return false;">'._p('unsponsor_this_listing').'</a>';
			}
			else
			{
				Phpfox::getService('ad.process')->deleteAdminSponsor('marketplace', $this->get('listing_id'));
				$sHtml = '<a href="#" title="' . _p('unsponsor_this_listing') . '" onclick="$(\'#js_sponsor_phrase_' . $this->get('listing_id') . '\').show(); $.ajaxCall(\'marketplace.sponsor\', \'listing_id=' . $this->get('listing_id') . '&amp;type=1\', \'GET\'); return false;">'._p('sponsor_this_listing').'</a>';
			}
			$this->html('#js_sponsor_' . $this->get('listing_id'), $sHtml)->alert($this->get('type') == '1' ? _p('listing_successfully_sponsored') : _p('listing_successfully_un_sponsored'));
			if($this->get('type') == '1')
			{
				$this->addClass('#js_mp_item_holder_' . $this->get('listing_id'), 'row_sponsored');
			}
			else
			{
				$this->removeClass('#js_mp_item_holder_' . $this->get('listing_id'), 'row_sponsored');
			}
	    }
	}

	public function approve()
	{
		if (Marketplace_Service_Process::instance()->approve($this->get('listing_id')))
		{
			$this->alert(_p('listing_has_been_approved'), _p('listing_approved'), 300, 100, true);
			$this->hide('#js_item_bar_approve_image');
			$this->hide('.js_moderation_off'); 
			$this->show('.js_moderation_on');	
		}
	}
	
	public function moderation()
	{
		Phpfox::isUser(true);	
		
		switch ($this->get('action'))
		{
			case 'approve':
				Phpfox::getUserParam('marketplace.can_approve_listings', true);
				foreach ((array) $this->get('item_moderate') as $iId)
				{
                    Marketplace_Service_Process::instance()->approve($iId);
					$this->remove('#js_mp_item_holder_' . $iId);					
				}				
				$this->updateCount();
				$sMessage = _p('listing_s_successfully_approved');
				break;			
			case 'delete':
				Phpfox::getUserParam('marketplace.can_delete_other_listings', true);
				foreach ((array) $this->get('item_moderate') as $iId)
				{
                    Marketplace_Service_Process::instance()->delete($iId);
					$this->slideUp('#js_mp_item_holder_' . $iId);
				}				
				$sMessage = _p('listing_s_successfully_deleted');
				break;
			case 'feature':
				Phpfox::getUserParam('marketplace.can_feature_listings', true);
				foreach ((array) $this->get('item_moderate') as $iId)
				{
                    Marketplace_Service_Process::instance()->feature($iId, 1);
					$this->addClass('#js_mp_item_holder_' . $iId, 'row_featured');
				}				
				$sMessage = _p('listing_s_successfully_featured');
				break;
			case 'un-feature':
				Phpfox::getUserParam('marketplace.can_feature_listings', true);
				foreach ((array) $this->get('item_moderate') as $iId)
				{
                    Marketplace_Service_Process::instance()->feature($iId, 0);
					$this->removeClass('#js_mp_item_holder_' . $iId, 'row_featured');
				}				
				$sMessage = _p('listing_s_successfully_un_featured');
				break;
            default:
                $sMessage = '';
                break;
		}
		
		$this->alert($sMessage, _p('Moderation'), 300, 150, true);
		$this->hide('.moderation_process');			
	}

    //Category
    public function categoryOrdering(){
        Phpfox::isAdmin(true);
        $aVals = $this->get('val');
        Core_Service_Process::instance()->updateOrdering([
            'table' => 'marketplace_category',
            'key' => 'category_id',
            'values' => $aVals['ordering']
        ]);
        Phpfox::getLib('cache')->remove('marketplace_category', 'substr');
    }

    public function toggleActiveCategory(){
        $iCategoryId = $this->get('id');
        $iActive = $this->get('active');
        Marketplace_Service_Category_Process::instance()->toggleActiveCategory($iCategoryId, $iActive);
    }
}