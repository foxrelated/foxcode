<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

define('PHPFOX_SKIP_POST_PROTECTION', true);

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox_Component
 * @version 		$Id: index.class.php 1558 2010-05-04 12:51:22Z Raymond_Benc $
 */
class Marketplace_Component_Controller_Invoice_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::isUser(true);
		
		$aCond = array();
		
		$aCond[] = 'AND mi.user_id = ' . Phpfox::getUserId();
		
		list($iCnt, $aInvoices) = Marketplace_Service_Marketplace::instance()->getInvoices($aCond);

		$aFilterMenu = array();
		if (!defined('PHPFOX_IS_USER_PROFILE'))
		{
			$sInviteTotal = '';
			if (Phpfox::isUser() && ($iTotalInvites = Marketplace_Service_Marketplace::instance()->getTotalInvites()))
			{
				$sInviteTotal = '<span class="invited">' . $iTotalInvites . '</span>';
			}

			$aFilterMenu = array(
					_p('all_listings') => '',
					_p('my_listings') => 'my',
					_p('listing_invites') . $sInviteTotal => 'invites',
					_p('invoices') => 'marketplace.invoice'
			);

			if (Phpfox::getUserParam('marketplace.can_view_expired'))
			{
				$aFilterMenu[_p('expired')] = 'expired';
			}
			if (Phpfox::isModule('friend') && !Phpfox::getParam('core.friends_only_community'))
			{
				$aFilterMenu[_p('friends_listings')] = 'friend';
			}

			if (Phpfox::isModule('event') && Phpfox::getUserParam('event.can_approve_events'))
			{
				$iPendingTotal = Marketplace_Service_Marketplace::instance()->getPendingTotal();

				if ($iPendingTotal)
				{
					$aFilterMenu[_p('pending_listings') . '<span class="pending">' . $iPendingTotal . '</span>'] = 'pending';
				}
			}
		}

		$this->template()->setTitle(_p('marketplace_invoices'))
			->setBreadCrumb(_p('marketplace'), $this->url()->makeUrl('marketplace'))
			->assign(array(
					'aInvoices' => $aInvoices
				)
			);

		$this->template()->buildSectionMenu('marketplace', $aFilterMenu);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('marketplace.component_controller_invoice_index_clean')) ? eval($sPlugin) : false);
	}
}