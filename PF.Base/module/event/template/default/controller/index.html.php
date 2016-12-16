<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Event
 * @version 		$Id: index.html.php 5844 2013-05-09 08:00:59Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{if !count($aEvents)}
{if ! PHPFOX_IS_AJAX }
<div class="extra_info">
	{_p var='no_events_found'}
</div>
{/if}
{else}
{if ! PHPFOX_IS_AJAX }
<div class="collection-stage">
{/if}
{foreach from=$aEvents key=sDate item=aGroups}
	{foreach from=$aGroups name=events item=aEvent}
		<div class="collection-item-stage">
		{template file='event.block.item'}
		</div>
	{/foreach}
{/foreach}
{pager}
<!--		end foreach2-->
{if ! PHPFOX_IS_AJAX }
</div>
{/if}
{if !PHPFOX_IS_AJAX && Phpfox::getUserParam('event.can_approve_events') || Phpfox::getUserParam('event.can_delete_other_event')}
{moderation}
{/if}

{/if}
