<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: browse.html.php 5840 2013-05-09 06:14:35Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if count($aThanks)}
	<div class="label_flow">
		{foreach from=$aThanks item=aThank}
			<div id="js_post_{$iPostId}_thank_{$aThank.user_id}" class="m_top_5">

				<div class="go_left">
					{img user=$aThank suffix='_50_square' max_width=50 max_height=50}
				</div>
				<div>
					{$aThank|user:'':'':30}
					{if ($aThank.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('forum.can_delete_thanks_by_other_users')}
					<div>
						<a class="forum_thank_delete_link" href="#" onclick="$.ajaxCall('forum.removeThanks', 'thank_id={$aThank.thank_id}&user_id={$aThank.user_id}&popup=true');return false;">{_p var='delete'}</a>
					</div>
					{/if}
				</div>
				<div class="clear"></div>
			</div>
		{/foreach}
	</div>
{else}
	<div class="extra_info">
		{_p var='no_one_has_thanked_this_post'}
	</div>
{/if}