<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: add.html.php 1124 2009-10-02 14:07:30Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" {if $bIsEdit} action="{url link='admincp.forum.add' id=$iId}" {else} action="{url link='admincp.forum.add'}" {/if} id="js_form">
{if isset($aForms.forum_id)}
	<div><input type="hidden" name="val[edit_id]" value="{$aForms.forum_id}" /></div>
	<div><input type="hidden" name="val[name]" value="{$aForms.name}" /></div>
	<div><input type="hidden" name="val[description]" value="{$aForms.description}" /></div>
{/if}
	<div class="table_header">
	{_p var='forum_details'}
	</div>

    {foreach from=$aLanguages item=aLanguage}
    <div class="table form-group">
        <div class="table_left">
            {_p var='name'}&nbsp;<strong>{$aLanguage.title}</strong>:
        </div>
        <div class="table_right">
            {assign var='value_name' value="name_"$aLanguage.language_id}
            <input type="text" name="val[name_{$aLanguage.language_id}]" value="{value id=$value_name type='input'}" size="30" maxlength="100"/>
        </div>
        <div class="clear"></div>
    </div>
    {/foreach}

	{if !empty($sForumParents)}
	<div class="table form-group">
		<div class="table_left">
			{_p var='parent_forum'}:
		</div>
		<div class="table_right">
			<select name="val[parent_id]" class="form-control">
				<option value="">{_p var='select'}:</option>
				{$sForumParents}
			</select>
		</div>
	</div>
	{/if}
	<div class="table form-group">
		<div class="table_left">
			{_p var='is_a_category'}:
		</div>
		<div class="table_right">
			<label><input type="radio" name="val[is_category]" value="1" class="v_middle" {value type='radio' id='is_category' default='1'}/> {_p var='yes'}</label>
			<label><input type="radio" name="val[is_category]" value="0" class="v_middle" {value type='radio' id='is_category' default='0' selected='true'}/> {_p var='no'}</label>
		</div>
	</div>
	<div class="table form-group">
		<div class="table_left">
			{_p var='closed'}:
		</div>
		<div class="table_right">
			<label><input type="radio" name="val[is_closed]" value="1" class="v_middle" {value type='radio' id='is_closed' default='1'}/> {_p var='yes'}</label>
			<label><input type="radio" name="val[is_closed]" value="0" class="v_middle" {value type='radio' id='is_closed' default='0' selected='true'}/> {_p var='no'}</label>
		</div>
	</div>	
    {foreach from=$aLanguages item=aLanguage}
    <div class="table form-group">
        <div class="table_left">
            {_p var='description'}&nbsp;<strong>{$aLanguage.title}</strong>:
        </div>
        <div class="table_right">
            {assign var='value_description' value="description_"$aLanguage.language_id}
            <textarea name="val[description_{$aLanguage.language_id}]" cols="50" rows="8">{value type='textarea' id=$value_description}</textarea>
        </div>
        <div class="clear"></div>
    </div>
    {/foreach}

	<div class="table_clear">
		<input type="submit" value="{_p var='submit'}" class="button btn-primary" />
		{if isset($aForms)}
		<input type="button" name="cancel" value="{_p var='cancel_uppercase'}" class="button btn-danger" onclick="window.location.href = '{url link='admincp.forum'}';" />
		{/if}
	</div>
</form>