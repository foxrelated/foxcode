<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: title.html.php 1614 2010-06-01 10:01:18Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="error_message">
	{_p var='b_notice_b_this_routine_is_highly_experimental'}
</div>
<div class="message">
	{_p var='all_items_on_the_site_store_certain_information_in_the_database'}
</div>
<div class="p_4">
	<form method="post" action="{url link='admincp.sql.title'}">
		<div><input type="hidden" name="update" value="1" /></div>
		<input type="submit" value="{_p var='update_database_tables'}" class="button btn-primary" />
	</form>
</div>