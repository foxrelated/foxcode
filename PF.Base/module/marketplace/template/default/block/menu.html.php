<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author        Raymond Benc
 * @package        Phpfox
 * @version        $Id: menu.html.php 3346 2011-10-24 15:20:05Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{if ($aListing.user_id == Phpfox::getUserId() && Phpfox::getUserParam('marketplace.can_edit_own_listing')) || Phpfox::getUserParam('marketplace.can_edit_other_listing')}
<li><a href="{url link='marketplace.add' id=$aListing.listing_id}">{_p var='edit_listing'}</a></li>
<li><a href="{url link='marketplace.add.customize' id=$aListing.listing_id}">{_p var='manage_photos'}</a></li>
<li><a href="{url link='marketplace.add.invite' id=$aListing.listing_id}">{_p var='send_invitations'}</a></li>
<li><a href="{url link='marketplace.add.manage' id=$aListing.listing_id}">{_p var='manage_invites'}</a></li>
{/if}
{if Phpfox::isModule('ad') && Phpfox::isModule('feed') && (Phpfox::getUserParam('feed.can_purchase_sponsor') || Phpfox::getUserParam('feed.can_sponsor_feed')) && Phpfox::getUserParam('feed.feed_sponsor_price') && ($iSponsorId = Feed_Service_Feed::instance()->canSponsoredInFeed('marketplace', $aListing.listing_id))}
<li>
    {if $iSponsorId === true}
    <a href="{url link='ad.sponsor' where='feed' section='marketplace' item=$aListing.listing_id}">
        {_p var='sponsor_in_feed'}
    </a>
    {else}
    <a href="#"
       onclick="$.ajaxCall('ad.removeSponsor', 'type_id=marketplace&item_id={$aListing.listing_id}', 'GET'); return false;">
        {_p var="Unsponsor In Feed"}
    </a>
    {/if}
</li>
{/if}

{if Phpfox::getUserParam('marketplace.can_feature_listings')}
<li class="js_marketplace_is_feature" {if $aListing.is_featured} style="display:none;" {/if}><a href="#"
                                                                                                onclick="$('#js_featured_phrase_{$aListing.listing_id}').show(); $.ajaxCall('marketplace.feature', 'listing_id={$aListing.listing_id}&amp;type=1', 'GET'); $(this).parent().hide(); $(this).parents('ul:first').find('.js_marketplace_is_un_feature').show(); return false;">{_p
    var='feature'}</a></li>
<li class="js_marketplace_is_un_feature" {if !$aListing.is_featured} style="display:none;" {/if}><a href="#"
                                                                                                    onclick="$('#js_featured_phrase_{$aListing.listing_id}').hide(); $.ajaxCall('marketplace.feature', 'listing_id={$aListing.listing_id}&amp;type=0', 'GET'); $(this).parent().hide(); $(this).parents('ul:first').find('.js_marketplace_is_feature').show(); return false;">{_p
    var='un_feature'}</a></li>
{/if}
{if Phpfox::getUserParam('marketplace.can_sponsor_marketplace')}
<li>
	    <span id="js_sponsor_{$aListing.listing_id}">
			    {if $aListing.is_sponsor}
		<a href="#"
           onclick="$('#js_sponsor_phrase_{$aListing.listing_id}').hide(); $.ajaxCall('marketplace.sponsor','listing_id={$aListing.listing_id}&type=0', 'GET'); return false;">
			    {_p var='unsponsor_this_listing'}
		</a>
			    {else}
		<a href="#"
           onclick="$('#js_sponsor_phrase_{$aListing.listing_id}').show(); $.ajaxCall('marketplace.sponsor','listing_id={$aListing.listing_id}&type=1', 'GET'); return false;">
				    {_p var='sponsor_this_listing'}
		</a>
			    {/if}
	    </span>
</li>
{elseif Phpfox::getUserParam('marketplace.can_purchase_sponsor')
&& $aListing.user_id == Phpfox::getUserId()
&& $aListing.is_sponsor != 1}
<li>
    <a href="{permalink module='ad.sponsor' id=$aListing.listing_id}section_marketplace/">
        {_p var='sponsor_this_listing'}
    </a>
</li>
{/if}
{if ($aListing.user_id == Phpfox::getUserId() && Phpfox::getUserParam('marketplace.can_delete_own_listing')) || Phpfox::getUserParam('marketplace.can_delete_other_listings')}
<li class="item_delete"><a href="{url link='marketplace' delete=$aListing.listing_id}" class="sJsConfirm">{_p
        var='delete_listing'}</a></li>
{/if}

{plugin call='marketplace.template_block_entry_links_main'}