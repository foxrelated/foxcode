<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: default.html.php 2525 2011-04-13 18:03:20Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="block_search">
	<form method="post" action="{url link=$aBanFilter.url}">
		<div class="table_header">
			{_p var='add_filter'}
		</div>
		<div class="table form-group">
			<div class="table_left">
				{$aBanFilter.form}:
			</div>
			<div class="table_right">
				<input type="text" name="find_value" value="" size="30" />
				<div class="extra_info">
					{_p var='use_the_asterisk_for_wildcard_entries'}
				</div>
			</div>
			<div class="clear"></div>
		</div>
		{if isset($aBanFilter.replace)}
		<div class="table form-group">
			<div class="table_left">
				{_p var='replacement'}:
			</div>
			<div class="table_right">
				<input type="text" name="replacement" value="" size="30" />
			</div>
			<div class="clear"></div>
		</div>
		{/if}
		{module name='ban.form'}
		<div class="table_clear">
			<input type="submit" value="{_p var='add'}" class="button btn-primary" />
		</div>
	</form>
</div>

<div class="block_content">
	{if count($aFilters)}
	<table cellpadding="0" cellspacing="0">
		<tr>
			<th style="width:20px;"></th>
			<th>{$aBanFilter.form}</th>
			{if isset($aBanFilter.replace)}
			<th>{_p var='replacement'}</th>
			{/if}
			<th style="width:150px;">{_p var='added_by'}</th>
			<th style="width:150px;">{_p var='added_on'}</th>
			<th> Affects </th>
		</tr>
	{foreach from=$aFilters name=filters item=aFilter}
		<tr{if !is_int($phpfox.iteration.filters/2)} class="tr"{/if}>
			<td class="t_center">
				<a href="#" class="js_drop_down_link" title="Manage">{img theme='misc/bullet_arrow_down.png' alt=''}</a>
				<div class="link_menu">
					<ul>
						<li><a href="{url link=$aBanFilter.url delete={$aFilter.ban_id}"  data-message="{_p var='are_you_sure' phpfox_squote=true}" class="sJsConfirm">{_p var='delete'}</a></li>
					</ul>
				</div>
			</td>
			<td>{$aFilter.find_value}</td>
			{if isset($aBanFilter.replace)}
			<td>{$aFilter.replacement}</td>
			{/if}
			<td>{if empty($aFilter.user_id)}{_p var='n_a'}{else}{$aFilter|user}{/if}</td>
			<td>{$aFilter.time_stamp|date}</td>
			<td>{$aFilter.s_user_groups_affected}</td>
		</tr>
	{/foreach}
	</table>
	{else}
	<div class="message">
		{_p var='no_bans_found_dot'}
	</div>
	{/if}
</div>