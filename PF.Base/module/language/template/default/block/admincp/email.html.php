<?php
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: form.html.php 704 2009-06-21 18:50:42Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form action="{url link='admincp.language.email'}" method="post">
	<div><input type="hidden" name="val[language_id]" value="{$sLanguage}"</div>
	<table cellpadding="0" cellspacing="0" class="table_for_phrases">
		<tr>
			<th style="width:20%;">{_p var='variable'}</th>
			<th style="width:55%;">{_p var='text'}</th>
		</tr>
		{foreach from=$aPhrases key=iKey item=aPhrase}
			<tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
				<td><input type="text" value="{$aPhrase.phrase_id}" size="35"></td>
				<td><textarea cols="75" rows="2" name="val[text][{$aPhrase.phrase_id}]">{_p var=$aPhrase.phrase_id language=$sLanguage}</textarea></td>
			</tr>
		{/foreach}
	</table>
	<div class="table_clear">
		<input type="submit" value="{_p var='save_all'}" class="button btn-primary">
	</div>
</form>