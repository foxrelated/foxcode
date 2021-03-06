<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_User
 * @version 		$Id: add.html.php 6374 2013-07-27 12:05:58Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !isset($aForms)}
<form method="post" action="{url link='admincp.user.group.add'}" enctype="multipart/form-data">
	{template file='user.block.admincp.entry'}
	<div class="table form-group">
		<div class="table_left">
			{_p var='inherit'}
		</div>
		<div class="table_right">
			<select name="val[inherit_id]">
			{foreach from=$aGroups key=iKey item=aGroup}
				<option value="{$aGroup.user_group_id}" {if $aGroup.user_group_id == 2} selected="selected"{/if}>{$aGroup.title|convert}</option>
			{/foreach}		
			</select>
		</div>
		<div class="clear"></div>
	</div>
	<div class="table_clear">
		<input type="submit" value="{_p var='add_user_group'}" class="button btn-primary" />
	</div>
</form>
{else}
{if $aForms.user_group_id == GUEST_USER_ID}
	{module name='help.info' phrase='admincp.not_allowed_for_guests'}
{/if}
{if !$bEditSettings}
<form method="post" action="{url link='admincp.user.group.add'}" enctype="multipart/form-data">
	<div><input type="hidden" name="id" value="{$aForms.user_group_id}" /></div>
	{template file='user.block.admincp.entry'}
	<div class="table_clear">
		<input type="submit" value="{_p var='submit'}" class="button btn-primary" />
	</div>
</form>
{else}
<form method="post" action="#" class="on_change_submit" onsubmit="$Core.ajaxMessage(); $(this).ajaxCall('user.updateSettings'); return false;">
	<div><input type="hidden" name="id" value="{$aForms.user_group_id}" /></div>	

	<div id="content_editor_holder">
		<div id="content_editor_menu">
			<ul>
				{foreach from=$aModules item=aModule}
				<li><a href="#" onclick="$.ajaxCall('user.getSettings', 'group_id={$aForms.user_group_id}&amp;module_id={$aModule.module_id}', 'GET'); $(this).blur(); $('#content_editor_menu a').removeClass('cem_active'); $(this).addClass('cem_active'); return false;">{$aModule.module_id|translate:'module'}</a></li>
				{/foreach}
			</ul>
		</div>
		<div id="content_editor_text" style="display:none;">
			<div class="table_header2" id="js_module_title" style="display:none;"></div>
			<div id="js_setting_block" style="position:relative;"></div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="table_clear table_hover_action" style="display: none;">
	<input type="submit" name="val[submit]" class="btn btn-danger" value="{_p var='Save Changes'}">
	</div>
</form>	
{/if}
{/if}