<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: add.html.php 6474 2013-08-20 06:58:29Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" action="{url link='admincp.report.add'}">
{if $bIsEdit}
	<div><input type="hidden" name="id" value="{$aForms.report_id}" /></div>
{/if}
	<div class="table_header">
		{_p var='category_details'}
	</div>
	<div><input type="hidden" name="val[product_id]" value="phpfox" /></div>
	<div><input type="hidden" name="val[module_id]" value="core" /></div>
	<div class="table form-group">
		<div class="table_left">
		{required}{_p var='category_name'}:
		</div>
		<div class="table_right">
			<input type="text" name="val[message]" value="{value type='input' id='message'}" size="40" maxlength="100" />
		</div>
		<div class="clear"></div>
	</div>
	<div class="table_clear">
		<input type="submit" value="{if $bIsEdit}{_p var='update'}{else}{_p var='add'}{/if}" class="button btn-primary" />
	</div>
</form>