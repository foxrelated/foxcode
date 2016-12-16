<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: controller.html.php 64 2009-01-19 15:05:54Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>

{if (isset($app_content))}
{$app_content}
{else}

{if $aPage.view_id == '1'}
	<div class="message js_moderation_off" id="js_approve_message">
		{_p var='this_page_is_pending_an_admins_approval_before_it_can_be_displayed_publicly'}
	</div>
{/if}

{if $bCanViewPage}
	{if isset($aWidget)}
		<div class="block p_10 item_view_content">
			{$aWidget.text|parse}
		</div>
	{elseif $sCurrentModule == 'info' && !$iViewCommentId}
		<div class="block p_10 item_view_content">
			{$aPage.text|parse}
		</div>
	{elseif $sCurrentModule == 'pending'}
		{if isset($aPendingUsers) && count($aPendingUsers)}
			{foreach from=$aPendingUsers name=pendingusers item=aPendingUser}
				<div id="js_pages_user_entry_{$aPendingUser.signup_id}" class="user_rows">
					{if Pages_Service_Pages::instance()->isAdmin($this->_aVars['aPage']['page_id'])}
					<div class="_moderator">
						<a href="#{$aPendingUser.signup_id}" class="moderate_link built" rel="pages"><i class="fa"></i></a>
					</div>
					{/if}
					<div class="user_rows_image">
						{img user=$aPendingUser suffix='_120_square' max_width='120' max_height='120'}
					</div>
					{$aPendingUser|user|shorten:50:'...'}
				</div>
			{/foreach}
			{moderation}
		{else}
		{/if}
	{else}
		{if $bHasPermToViewPageFeed}
			
		{else}
			{_p var='unable_to_view_this_section_due_to_privacy_settings'}
		{/if}
	{/if}
{else}
	<div class="message">
		{if isset($aPage.is_invited) && $aPage.is_invited}	
			{_p var='you_have_been_invited_to_join_this_community'}
		{else}
			{_p var='due_to_privacy_settings_this_page_is_not_visible'}
			{if $aPage.page_type == '1' && $aPage.reg_method == '2'}
				{_p var='this_page_is_also_invite_only'}
			{/if}
		{/if}
	</div>
{/if}

{/if}