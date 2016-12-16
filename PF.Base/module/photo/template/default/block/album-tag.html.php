<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: album-tag.html.php 2610 2011-05-19 18:43:08Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $sView == 'block'}
<div class="block_listing_inline album_tag">
	<ul>
{foreach from=$aTaggedUsers item=aTaggedUser}
		<li>{img user=$aTaggedUser suffix='_50_square' max_width=50 max_height=50 class='js_hover_title'}</li>
{/foreach}
	</ul>
	<div class="clear"></div>
</div>
{else}
{if $iPage == 1}
<div class="label_flow album_tag" id="js_album_tag_content">
{/if}
	{foreach from=$aTaggedUsers item=aTaggedUser}
	<div class="m_top_5">
		<div class="go_left">
			{img user=$aTaggedUser suffix='_50_square' max_width=50 max_height=50}
		</div>
		<div>
			{$aTaggedUser|user:'':'':30}
		</div>
		<div class="clear"></div>
	</div>
	{/foreach}
	{pager}
{if $iPage == 1}
</div>
{/if}
{/if}