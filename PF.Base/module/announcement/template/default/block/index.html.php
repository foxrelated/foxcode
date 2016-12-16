<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Display the image details when viewing an image.
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package  		Module_Announcement
 * @version 		$Id: index.html.php 3830 2011-12-19 12:55:57Z Miguel_Espinoza $
 */
?>
<div id="announcement">
	{if $aAnnouncement.can_be_closed == 1 && Phpfox::getUserParam('announcement.can_close_announcement')}
		<div class="js_announcement_close">
			<a href="#" onclick="$.ajaxCall('announcement.hideAnnouncement', 'id={$aAnnouncement.announcement_id}'); return false;">
			<i class="fa fa-close"></i>
			</a>
		</div>
	{/if}	
		<div class="js_announcement_subject">
			<div class="announcement_date">
				{$aAnnouncement.time_stamp|date}
			</div>
			{_p var=$aAnnouncement.subject_var}
		</div>
	
	
	<div class="js_announcement_content">
		{if isset($aAnnouncement.intro_var) && !empty($aAnnouncement.intro_var)}
			{_p var=$aAnnouncement.intro_var}
		{else}
			{_p var=$aAnnouncement.content_var}
		{/if}
			
		{if !empty($aAnnouncement.content_var) && !isset($bHideViewMore)}
		<div class="js_announcement_more">
			( <a href="{url link='announcement.view' id=$aAnnouncement.announcement_id}">{_p var='read_more'}</a> )
		</div>
		{/if}		
	</div>
</div>