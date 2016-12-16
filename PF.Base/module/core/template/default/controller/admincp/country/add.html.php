<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: add.html.php 982 2009-09-16 08:11:36Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" action="{url link='admincp.core.country.add'}">
{if $bIsEdit}
	<div><input type="hidden" name="id" value="{$aForms.country_iso}" /></div>
{/if}
	<div class="table form-group">
		<div class="table_left">
			{required}{_p var='iso'}:
		</div>
		<div class="table_right">
			<input type="text" name="val[country_iso]" value="{value id='country_iso' type='input'}" size="4" />
		</div>
		<div class="clear"></div>
	</div>
	<div class="table form-group">
		<div class="table_left">
			{required}{_p var='name'}:
		</div>
		<div class="table_right">
			<input type="text" name="val[name]" value="{value id='name' type='input'}" size="40" />
		</div>
		<div class="clear"></div>
	</div>	
	<div class="table_clear">
		<input type="submit" value="{if $bIsEdit}{_p var='update'}{else}{_p var='submit'}{/if}" class="button btn-primary" />
	</div>
</form>