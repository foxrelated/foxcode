<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Language
 * @version 		$Id: add.html.php 1161 2009-10-09 07:42:41Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $sCachePhrase}
<div class="p_4">
	<div class="p_4">	
		<div class="go_left t_right" style="width:150px;"><b>{_p var='php'}</b>:</div>
		<div><input type="text" name="php" value="_p('{$sCachePhrase}')" size="40" onclick="this.select();" /></div>
		<div class="clear"></div>
	</div>
	<div class="p_4">	
		<div class="go_left t_right" style="width:150px;"><b>{_p var='php_single_quoted'}</b>:</div>
		<div><input type="text" name="php" value="' . _p('{$sCachePhrase}') . '" size="40" onclick="this.select();" /></div>
		<div class="clear"></div>
	</div>	
	<div class="p_4">	
		<div class="go_left t_right" style="width:150px;"><b>{_p var='php_double_quoted'}</b>:</div>
		<div><input type="text" name="php" value="&quot; . _p('{$sCachePhrase}') . &quot;" size="40" onclick="this.select();" /></div>
		<div class="clear"></div>
	</div>		
	<div class="p_4">
		<div class="go_left t_right" style="width:150px;"><b>{_p var='html'}</b>:</div>
		<div><input type="text" name="html" value="{literal}{{/literal}phrase var='{$sCachePhrase}'{literal}}{/literal}" size="40" onclick="this.select();" /></div>
		<div class="clear"></div>
	</div>
	<div class="p_4">
		<div class="go_left t_right" style="width:150px;"><b>{_p var='js'}</b>:</div>
		<div><input type="text" name="html" value="oTranslations['{$sCachePhrase}']" size="40" onclick="this.select();" /></div>
		<div class="clear"></div>
	</div>	
	<div class="p_4">
		<div class="go_left t_right" style="width:150px;"><b>{_p var='text'}</b>:</div>
		<div><input type="text" name="html" value="{$sCachePhrase}" size="40" onclick="this.select();" /></div>
		<div class="clear"></div>
	</div>		
</div>
{/if}
{$sCreateJs}
<form method="post" action="{url link='admincp.language.phrase.add' last-module=$sLastModuleId}" id="js_phrase_form" onsubmit="{$sGetJsForm}">
{token}
{if $sReturn}
<div><input type="hidden" name="return" value="{$sReturn}" /></div>
{/if}
{if $sVar}
<div><input type="hidden" name="val[is_help]" value="true" /></div>
{/if}
<div class="table form-group">
	<div class="table_left">
		{_p var='varname'}:
	</div>
	<div class="table_right">
		<input type="text" name="val[var_name]" value="{$sVar}" size="40" id="var_name" maxlength="100" />
		{help var='admincp.language_add_phrase_varname'}
	</div>
	<div class="clear"></div>
</div>
<div class="table form-group">
	<div class="table_left">
		{_p var='text'}:
	</div>
	<div class="table_right_text">
	{foreach from=$aLanguages item=aLanguage}
		<b>{$aLanguage.title}</b>
		<div class="p_4">
			<textarea cols="50" rows="8" name="val[text][{$aLanguage.language_id}]"></textarea>
			{help var='admincp.language_add_phrase_text'}
		</div>
	{/foreach}
	</div>
	<div class="clear"></div>
</div>
<div class="table_clear">
	<input type="submit" value="{_p var='submit'}" class="button" />
</div>
</form>