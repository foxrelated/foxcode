<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: breadcrumb.html.php 5844 2013-05-09 08:00:59Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!');
?>
{if isset($aBreadCrumbs) && count($aBreadCrumbs) > 0}
<div class="row hide-overflow breadcrumbs-holder">
	{if isset($aBreadCrumbs) && count($aBreadCrumbs) > 0}
	<div class="clearfix breadcrumbs-top">
		<div class="pull-left">
			<div class="breadcrumbs-list">
				{if isset($aBreadCrumbs)}
				{foreach from=$aBreadCrumbs key=sLink item=sCrumb name=link}

				<a {if !empty($sLink)}href="{$sLink}" {/if} class="ajax_link">
					{$sCrumb|clean}</a>
				{/foreach}
				{/if}
				{if !$bIsDetailPage && !defined('PHPFOX_APP_DETAIL_PAGE')}
				{if !empty($aBreadCrumbTitle)}
				<a href="{ $aBreadCrumbTitle[1] }" class="ajax_link">{ $aBreadCrumbTitle[0] }</a>
				{/if}
				{/if}
			</div>

		</div>
		<div class="pull-right breadcrumbs_right_section">
			{breadcrumb_menu}
		</div>
	</div>
	{/if}
	{if ($bIsDetailPage || defined('PHPFOX_APP_DETAIL_PAGE')) && !empty($aBreadCrumbTitle)}
	<h1 class="breadcrumbs-bottom">
		<a href="{ $aBreadCrumbTitle[1] }" class="ajax_link">{ $aBreadCrumbTitle[0] }</a>
	</h1>
	{/if}
</div>
{/if}