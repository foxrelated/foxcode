<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: add.html.php 5387 2013-02-19 12:19:37Z Miguel_Espinoza $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="table_header">
	{_p var='category_details'}
</div>
<form method="post" action="{url link='admincp.pages.add'}">
	{if $bIsEdit}
        {if $bIsEdit && isset($aForms.category_id)}
        <div><input type="hidden" name="sub" value="{$iEditId}" /></div>
        {else}
        <div><input type="hidden" name="id" value="{$iEditId}" /></div>
        {/if}
        <div><input type="hidden" name="val[name]" value="{$aForms.name}" /></div>
	{/if}
	{if $bIsEdit && !isset($aForms.category_id)}{else}
	<div class="table form-group">
		<div class="table_left">
			{_p var='parent_category'}:
		</div>
		<div class="table_right">
			<select name="val[type_id]" id="add_select" class="form-control">
				{if !$bIsEdit}
				<option value="0">{_p var='none'}</option>
				{/if}
				{foreach from=$aTypes item=aType}
				<option value="{$aType.type_id}"{value type='select' id='type_id' default=$aType.type_id}>
                    {if Phpfox::isPhrase($this->_aVars['aType']['name'])}
                    {_p var=$aType.name}
                    {else}
                    {$aType.name|convert}
                    {/if}
                </option>
				{/foreach}
			</select>
		</div>
		<div class="clear"></div>
	</div>
	{/if}
    {foreach from=$aLanguages item=aLanguage}
    <div class="table form-group">
        <div class="table_left">
            {_p var='name'}&nbsp;<strong>{$aLanguage.title}</strong>:
        </div>
        <div class="table_right">
            {assign var='value_name' value="name_"$aLanguage.language_id}
            <input type="text" name="val[name_{$aLanguage.language_id}]" value="{value id=$value_name type='input'}" size="30" />
        </div>
        <div class="clear"></div>
    </div>
    {/foreach}
	<div class="table_clear">
		<input type="submit" value="{_p var='submit'}" class="button btn-primary" />
	</div>
</form>