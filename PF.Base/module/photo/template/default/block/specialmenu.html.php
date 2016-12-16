<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Photo
 * @version 		$Id: index.html.php 5083 2012-12-20 11:00:06Z Miguel_Espinoza $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="page_section_menu page_section_menu_header">
	<div>
		<ul class="nav nav-tabs nav-justified">
			<li {if $bShowPhotos} class="active"{/if}>
				<a href="{$sLinkPhotos}">
					{_p var='photos'}
				</a>
			</li>

			<li {if !$bShowPhotos} class="active"{/if}>
			<a href="{$sLinkAlbums}">
				{_p var='albums'}
			</a>
			</li>
		</ul>
	</div>
	<div class="clear"></div>
</div>