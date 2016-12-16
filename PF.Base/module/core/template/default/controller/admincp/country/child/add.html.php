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
<form method="post" action="{url link='admincp.core.country.child.add'}">
{if $bIsEdit}
	<div><input type="hidden" name="id" value="{$aForms.child_id}" /></div>
{else}
{if !empty($sIso)}
	<div><input type="hidden" name="val[country_iso]" value="{$sIso}" /></div>
{/if}
{/if}
	<div class="table_header">
		{_p var='state_province_details'}
	</div>
	{if empty($sIso)}
	<div class="table form-group">
		<div class="table_left">
			{required}{_p var='country'}:
		</div>
		<div class="table_right">
			{select_location}
		</div>
		<div class="clear"></div>
	</div>		
	{/if}
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