<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: permission.html.php 1678 2010-07-20 11:05:43Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="table_header">
	{_p var='user_groups'}
</div>
<div class="table form-group">
	<div class="table_left">
		{_p var='user_group'}:
	</div>
	<div class="table_right">
		<select class="form-control" name="user_group_id" onchange="$('#js_display_perms').slideUp(); $('#js_form_user_group_id').val(this.value); $(this).ajaxCall('forum.loadPermissions', 'forum_id={$iForumId}');">
			<option value="">{_p var='select'}:</option>
		{foreach from=$aUserGroups item=aUserGroup}
			<option value="{$aUserGroup.user_group_id}">{$aUserGroup.title|clean}</option>
		{/foreach}
		</select>
		<div class="extra_info">
			{_p var='select_a_user_group_to_assign_special_permissions_for_this_specific_forum'}
		</div>
	</div>
	<div class="clear"></div>
</div>
<form method="post" action="#" onsubmit="$(this).ajaxCall('forum.savePerms'); return false;">	
	<div><input type="hidden" name="val[forum_id]" value="{$iForumId}" /></div>
	<div><input type="hidden" name="val[user_group_id]" value="" id="js_form_user_group_id" /></div>
	<div id="js_display_perms" style="display:none;">
		<div class="table_header">
			{_p var='forum_permissions'} - <span id="js_form_perm_group"></span>
		</div>
		<div id="js_display_list_perms"></div>
	</div>
	<div class="table_clear">
		<div id="js_save_perms" style="display:none;">
			<input name="save" type="submit" value="{_p var='save'}" class="button btn-primary" />
			<input name="button "type="submit" value="{_p var='reset'}" class="button btn-danger" onclick="$.ajaxCall('forum.permReset', 'forum_id={$iForumId}&amp;user_group_id=' + $('#js_form_user_group_id').val()); return false;" />
		</div>
	</div>
</form>