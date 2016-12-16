<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Profile
 * @version 		$Id: pic.html.php 4710 2012-09-21 08:59:25Z Raymond_Benc $
 * @description		This template is used to display the Like/Join link in Pages with Timeline.
 */
 
defined('PHPFOX') or exit('NO DICE!'); 


?>
{if !Phpfox::getUserBy('profile_page_id') && Phpfox::isUser()}
	{if isset($aPage) && $aPage.reg_method == '2' && !isset($aPage.is_invited) && $aPage.page_type == '1'}
	{else}
		{if isset($aPage) && isset($aPage.is_reg) && $aPage.is_reg}
		{else}
			{if isset($aPage) && $aPage.is_liked}
			<a href="#" class="pages_like_join pages_unlike_unjoin" onclick="$(this).remove(); $.ajaxCall('like.delete', 'type_id=pages&amp;item_id={$aPage.page_id}'); return false;">
				{_p var='unlike'}
			</a>
			{else}
			<a href="#" class="pages_like_join" onclick="$(this).remove(); {if $aPage.page_type == '1' && $aPage.reg_method == '1'} $.ajaxCall('pages.signup', 'page_id={$aPage.page_id}'); {else}$.ajaxCall('like.add', 'type_id=pages&amp;item_id={$aPage.page_id}');{/if} return false;">
				{_p var='like'}
			</a>
			{/if}
		{/if}
	{/if}
{/if}

