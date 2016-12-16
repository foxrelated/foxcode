<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: index.html.php 2197 2010-11-22 15:26:08Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !$sForumList}
<div class="extra_info">
	{_p var='no_forums_created_yet'}
	<ul class="action">
		<li><a href="{url link='admincp.forum.add'}">{_p var='create_a_new_forum'}</a></li>
	</ul>
</div>
{else}
<div id="js_menu_drop_down" style="display:none;">
	<div class="link_menu dropContent" style="display:block;">
		<ul>
		{if Phpfox::getUserParam('forum.can_edit_forum')}
			<li><a href="#" onclick="return $Core.forum.action(this, 'edit');">{_p var='edit_forum'}</a></li>
		{/if}
			<li><a href="#" onclick="return $Core.forum.action(this, 'view');">{_p var='view_forum'}</a></li>
		{if Phpfox::getUserParam('forum.can_add_new_forum')}
			<li><a href="#" onclick="return $Core.forum.action(this, 'add');">{_p var='add_child_forum'}</a></li>
		{/if}
		{if Phpfox::getUserParam('forum.can_manage_forum_moderators')}
			<li><a href="#" onclick="return $Core.forum.action(this, 'moderator');">{_p var='manage_moderators'}</a></li>
		{/if}
		{if Phpfox::getUserParam('forum.can_manage_forum_permissions')}
			<li><a href="#" onclick="return $Core.forum.action(this, 'permission');">{_p var='manage_permissions'}</a></li>
		{/if}
		{if Phpfox::getUserParam('forum.can_delete_forum')}
			<li><a href="#" onclick="return $Core.forum.action(this, 'delete');">{_p var='delete_forum'}</a></li>
		{/if}
		</ul>
	</div>
</div>

<div id="js_forum_edit_content"></div>
<div id="js_form_actual_content">
	<form method="post" action="{url link='admincp.forum'}">
		<div class="_table">
			<div class="sortable">
				{$sForumList}
			</div>
		</div>
		<div class="table_clear">
			<span id="js_update_order"></span><input type="submit" value="{_p var='update_order'}" class="button btn-primary" />
		</div>
	</form>
</div>
{/if}