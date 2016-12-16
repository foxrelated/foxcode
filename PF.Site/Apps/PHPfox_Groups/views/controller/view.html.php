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
		{_p var='This group is pending an Admins approval before it can be displayed publicly.'}
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
					{if Phpfox::getService('groups')->isAdmin($this->_aVars['aPage']['page_id'])}
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
			{_p var='Unable to view this section due to privacy settings.'}
		{/if}
	{/if}
{else}
	<div class="message">
		{if isset($aPage.is_invited) && $aPage.is_invited}	
			{_p var='You have been invited to join this community.'}
		{else}
			{_p var='Due to privacy settings this page is not visible.'}
			{if $aPage.page_type == '1' && $aPage.reg_method == '2'}
				{_p var='This group is also "Invite Only".'}
			{/if}
		{/if}
	</div>
{/if}

{/if}