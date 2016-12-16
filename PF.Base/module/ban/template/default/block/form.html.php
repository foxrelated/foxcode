<?php
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Blog
 * @version 		$Id: entry.html.php 2298 2011-02-07 15:41:02Z Miguel_Espinoza $
 */
defined('PHPFOX') or exit('NO DICE!');
?>

{if !isset($bShow) || $bShow == false}
	<div class="table form-group-follow">
		<div class="table_left">
				{_p var='ban_user'}:
		</div>
		<div class="table_right">
			<div class="item_is_active_holder">
				<span class="js_item_active item_is_active" style="position: relative; margin:0;float:left;display:inline;">
					<input type="radio" name="aBan[bShow]" onclick="$('#showBanForm').show();" value="1" {value type='radio' id='is_active' default='1'}/>
						   {_p var='yes'}
				</span>
				<span class="js_item_active item_is_not_active" style="position: relative; margin:0;float:left;display:inline;">
					<input type="radio" name="aBan[bShow]" onclick="$('#showBanForm').hide();" value="0" {value type='radio' id='is_active' default='0' selected='true'}/>
						   {_p var='no'}
				</span>
			</div>

		</div>
		<div class="clear"></div>
	</div>
	<div id="showBanForm" style="display: none;">
{else}
	<div id="showBanForm">
{/if}

	<div class="table form-group">
		<div class="table_left">
		{_p var='reason'}:
		</div>
		<div class="table_right">
			<textarea name="aBan[reason]" cols="30" rows="3" ></textarea>
			<div class="extra_info">
				{_p var='phrase_variable_when_banning_explanation'}
			</div>
		</div>
		<div class="clear"></div>
	</div>

	<div class="table form-group">
		<div class="table_left">
			{_p var='ban_for_how_many_days'}
		</div>
		<div class="table_right">
			<input type="text" name="aBan[days_banned]" value="0">
			<div class="extra_info">
				{_p var='0_means_indefinite'}
			</div>
		</div>
		<div class="clear"></div>
	</div>

	<div class="table form-group">
		<div class="table_left">
		{_p var='user_group_to_move_the_user_when_the_ban_expires'}
		</div>
		<div class="table_right">
			<select name="aBan[return_user_group]">
				{foreach from=$aUserGroups item=aGroup}
					<option value="{$aGroup.user_group_id}">{$aGroup.title|convert}</option>
				{/foreach}
			</select>
		</div>
		<div class="clear"></div>
	</div>


	<div class="table form-group" {if isset($bHideAffected) && $bHideAffected == true}style="display:none;"{/if}>
		<div class="table_left">
		User groups affected: 
		</div>
		<div class="table_right">
				{foreach from=$aUserGroups item=aGroup}
					<input type="checkbox" name="aBan[user_groups_affected][]" value="{$aGroup.user_group_id}">{$aGroup.title} <br />
				{/foreach}
		</div>
		<div class="clear"></div>
	</div>
</div>