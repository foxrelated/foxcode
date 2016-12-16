<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: private.html.php 5074 2012-12-06 10:37:26Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !Profile_Service_Profile::instance()->timeline()}
<div class="go_left t_center" style="width:125px;">
	{img user=$aUser suffix='_120' max_width='120' max_height='120'}
</div>
<div style="margin-left:125px;">
{/if}
	<div class="extra_info">
		{_p var='profile_is_private'}
	</div>

{if !Profile_Service_Profile::instance()->timeline()}
</div>
<div class="clear"></div>
{/if}