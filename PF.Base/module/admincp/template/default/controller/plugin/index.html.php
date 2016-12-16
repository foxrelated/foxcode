<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Admincp
 * @version 		$Id: index.html.php 979 2009-09-14 14:05:38Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if count($aPlugins)}
<form method="post" action="{url link='admincp.plugin'}">
	<table>
	<tr>
		<th>{_p var='name'}</th>
		<th style="width:60px;">{_p var='active'}</th>
		<th style="width:200px;">{_p var='actions'}</th>
	</tr>
	{foreach from=$aPlugins key=iKey item=aPlugin}
	<tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
		<td>{$aPlugin.title}</td>
		<td class="t_center">
			<div><input type="hidden" name="val[{$aPlugin.plugin_id}][id]" value="1" /></div>
			<div><input type="checkbox" name="val[{$aPlugin.plugin_id}][is_active]" value="1" {if $aPlugin.is_active}checked="checked" {/if}/></div>
		</td>
		<td>
			<select name="action" class="goJump" style="width:140px;">
				<option value="">{_p var='select'}</option>
				<option value="{url link='admincp.plugin.add' id=$aPlugin.plugin_id}">{_p var='edit'}</option>
				<option value="{url link='admincp.plugin' delete=$aPlugin.plugin_id}" style="color:red;">{_p var='delete'}</option>
			</select>
		</td>
	</tr>
	{/foreach}
	</table>
	<div class="table_bottom">
		<input type="submit" value="{_p var='update'}" class="button btn-primary" />
	</div>
</form>
{else}
<div class="extra_info">
	{_p var='no_plugins_have_been_added'}
</div>
{/if}