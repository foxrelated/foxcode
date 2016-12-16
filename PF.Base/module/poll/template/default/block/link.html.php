<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Blog
 * @version 		$Id: link.html.php 2501 2011-04-04 20:13:13Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>		
    {if ($aPoll.bCanEdit)}
        {if !isset($bDesign) || $bDesign == false}
            <li>
                <a href="{url link='poll.add' id=$aPoll.poll_id}">
                    {_p var='edit'}
                </a>
            </li>
            <li>
                <a href="{url link='poll.design' id=$aPoll.poll_id}">
                    {_p var='design'}
                </a>
            </li>
        {/if}
    {/if}
    {if Phpfox::isModule('ad') && Phpfox::isModule('feed') && (Phpfox::getUserParam('feed.can_purchase_sponsor') || Phpfox::getUserParam('feed.can_sponsor_feed')) && Phpfox::getUserParam('feed.feed_sponsor_price') && ($iSponsorId = Feed_Service_Feed::instance()->canSponsoredInFeed('poll', $aPoll.poll_id))}
    <li>
        {if $iSponsorId === true}
        <a href="{url link='ad.sponsor' where='feed' section='poll' item=$aPoll.poll_id}">
            {_p var='sponsor_in_feed'}
        </a>
        {else}
        <a href="#" onclick="$.ajaxCall('ad.removeSponsor', 'type_id=poll&item_id={$aPoll.poll_id}', 'GET'); return false;">
            {_p var="Unsponsor In Feed"}
        </a>
        {/if}
    </li>
    {/if}
    {if !isset($bIsCustomPoll)}
    {if ((Phpfox::getUserParam('poll.poll_can_delete_own_polls') && $aPoll.user_id == Phpfox::getUserId())
        || Phpfox::getUserParam('poll.poll_can_delete_others_polls'))}
        {if !isset($bDesign) || $bDesign == false}
            <li class="item_delete">
                <a {if isset($bIsViewingPoll)}href="{url link='poll' delete=$aPoll.poll_id}" class="sJsConfirm"{else}href="#" onclick="deletePoll({$aPoll.poll_id}); return false;"{/if}>
                    {_p var='delete'}
                </a>
            </li>
        {/if}
    {/if}
    {/if}
{plugin call='poll.template_block_entry_links_main'}