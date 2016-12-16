<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: backup.html.php 1268 2009-11-23 20:45:36Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $bCanBackup}
<form method="post" action="{url link='admincp.sql.backup'}">
	<div class="table_header">
		{_p var='sql_backup_header'}
	</div>
	<div class="table form-group">
		<div class="table_left">
			{_p var='path'}:
		</div>
		<div class="table_right">
			<input type="text" name="path" value="{$sDefaultPath}" size="40" style="width:90%;" />
			<div class="extra_info">
				{_p var='provide_the_full_path_to_where_we_should_save_the_sql_backup'}
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="table_bottom">
		<input type="submit" value="{_p var='save'}" class="button btn-primary" />
	</div>
</form>
{else}
<div class="error_message">
	{_p var='your_operating_system_does_not_support_the_method_of_backup_we_provide'}
</div>
{/if}