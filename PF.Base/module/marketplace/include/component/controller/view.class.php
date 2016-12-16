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
 * @version 		$Id: view.class.php 5844 2013-05-09 08:00:59Z Raymond_Benc $
 */
class Marketplace_Component_Controller_View extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		if ($this->request()->get('req2') == 'view' && ($sLegacyTitle = $this->request()->get('req3')) && !empty($sLegacyTitle))
		{
            Core_Service_Core::instance()->getLegacyItem(array(
					'field' => array('listing_id', 'title'),
					'table' => 'marketplace',		
					'redirect' => 'marketplace',
					'title' => $sLegacyTitle
				)
			);
		}		
		
		Phpfox::getUserParam('marketplace.can_access_marketplace', true);
		
		if (!($iListingId = $this->request()->get('req2')))
		{
			$this->url()->send('marketplace');
		}
		
		if (!($aListing = Marketplace_Service_Marketplace::instance()->getListing($iListingId)))
		{
			return Phpfox_Error::display(_p('the_listing_you_are_looking_for_either_does_not_exist_or_has_been_removed'));
		}

        if (Phpfox::isUser() && User_Service_Block_Block::instance()->isBlocked(null, $aListing['user_id']))
        {
            return Phpfox_Module::instance()->setController('error.invalid');
        }

		$this->setParam('aListing', $aListing);
		
		if (Phpfox::isUser() && $aListing['invite_id'] && !$aListing['visited_id'] && $aListing['user_id'] != Phpfox::getUserId())
		{
            Marketplace_Service_Process::instance()->setVisit($aListing['listing_id'], Phpfox::getUserId());
		}		
		
		if (Phpfox::isUser() && Phpfox::isModule('notification'))
		{
			Notification_Service_Process::instance()->delete('comment_marketplace', $this->request()->getInt('req2'), Phpfox::getUserId());
			Notification_Service_Process::instance()->delete('marketplace_like', $this->request()->getInt('req2'), Phpfox::getUserId());
		}
		
		if (Phpfox::isModule('notification') && $aListing['user_id'] == Phpfox::getUserId())
		{
			Notification_Service_Process::instance()->delete('marketplace_approved', $aListing['listing_id'], Phpfox::getUserId());
		}		
		
		if (Phpfox::isModule('privacy'))
		{
			Privacy_Service_Privacy::instance()->check('marketplace', $aListing['listing_id'], $aListing['user_id'], $aListing['privacy'], $aListing['is_friend']);
		}

		$this->setParam('aRatingCallback', array(
				'type' => 'user',
				'default_rating' => $aListing['total_score'],
				'item_id' => $aListing['user_id'],
				'stars' => range(1, 10)
			)
		);			
		
		$this->setParam('aFeed', array(				
				'comment_type_id' => 'marketplace',
				'privacy' => $aListing['privacy'],
				'comment_privacy' => $aListing['privacy_comment'],
				'like_type_id' => 'marketplace',
				'feed_is_liked' => $aListing['is_liked'],
				'feed_is_friend' => $aListing['is_friend'],
				'item_id' => $aListing['listing_id'],
				'user_id' => $aListing['user_id'],
				'total_comment' => $aListing['total_comment'],
				'total_like' => $aListing['total_like'],
				'feed_link' => $this->url()->permalink('marketplace', $aListing['listing_id'], $aListing['title']),
				'feed_title' => $aListing['title'],
				'feed_display' => 'view',
				'feed_total_like' => $aListing['total_like'],
				'report_module' => 'marketplace',
				'report_phrase' => _p('report_this_listing_lowercase')
			)
		);

		$sExchangeRate = '';
		if ($aListing['currency_id'] != Core_Service_Currency_Currency::instance()->getDefault())
		{
			if (($sAmount = Core_Service_Currency_Currency::instance()->getXrate($aListing['currency_id'], $aListing['price'])))
			{
				$sExchangeRate .= ' (' . Core_Service_Currency_Currency::instance()->getCurrency($sAmount) . ')';
			}
		}
		
		$this->template()->setTitle($aListing['title'] . ($aListing['view_id'] == '2' ? ' (' . _p('sold') . ')' : ''))
			->setBreadCrumb(_p('marketplace'), $this->url()->makeUrl('marketplace'))
			->setMeta('description', $aListing['description'])
			->setMeta('keywords', $this->template()->getKeywords($aListing['title'] . $aListing['description']))
			->setMeta('og:image', Phpfox::getLib('image.helper')->display(array(
						'server_id' => $aListing['listing_id'],
						'path' => 'marketplace.url_image',
						'file' => $aListing['image_path'],
						'suffix' => '_400',
						'return_url' => true
					)
				)
			)			
			->setBreadCrumb($aListing['title'] . ($aListing['view_id'] == '2' ? ' (' . _p('sold') . ')' : ''), $this->url()->permalink('marketplace', $aListing['listing_id'], $aListing['title']), true)
			->setHeader('cache', array(
					'jquery/plugin/star/jquery.rating.js' => 'static_script',
					'jquery/plugin/jquery.highlightFade.js' => 'static_script',
					'jquery/plugin/jquery.scrollTo.js' => 'static_script',
					'masterslider.min.js'=>'module_core',
					'switch_legend.js' => 'static_script',
					'switch_menu.js' => 'static_script',
					'view.js' => 'module_marketplace',
					'view.css' => 'module_marketplace',
					'masterslider.css' => 'module_core',
				)
			)			
			
			->setEditor(array(
					'load' => 'simple'
				)
			)
			->assign(array(
					'core_path'=>Phpfox::getParam('core.path'),
					'aListing' => $aListing,
					'sMicroPropType' => 'Product',
					'aImages' => Marketplace_Service_Marketplace::instance()->getImages($aListing['listing_id']),
					'sListingPrice' => ($aListing['price'] == '0.00' ? _p('free') : Core_Service_Currency_Currency::instance()->getCurrency(number_format($aListing['price'], 2), $aListing['currency_id'])) . $sExchangeRate . ($aListing['view_id'] == '2' ? ' (' . _p('sold') . ')' : '')
				)
			);
		if (Phpfox::isModule('rate'))
		{
			$this->template()
				->setHeader(array(
					'rate.js' => 'module_rate',
					'<script type="text/javascript">$Behavior.rateMarketplaceUser = function() { $Core.rate.init({display: false}); }</script>',		
				)		
			);
		}

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
		$this->template()->buildSectionMenu('marketplace', $aFilterMenu);
		
		(($sPlugin = Phpfox_Plugin::get('marketplace.component_controller_view_process_end')) ? eval($sPlugin) : false);
        return null;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('marketplace.component_controller_view_clean')) ? eval($sPlugin) : false);
	}
}