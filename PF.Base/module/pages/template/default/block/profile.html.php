<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: profile.html.php 5840 2013-05-09 06:14:35Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="user_rows_mini pages_listing">
{foreach from=$aPagesList name=pages item=aUser}
	<div class="user_rows">
		<div class="user_rows_image">
			{img user=$aUser suffix='_120_square'}
		</div>
		<div class="page_info">
			{$aUser|user}
			<div class="like_count">{$aUser.total_like} {if $aUser.total_like == 1}{_p var='like'}{else}{_p var='likes'}{/if}</div>
		</div>
	</div>
{/foreach}
</div>