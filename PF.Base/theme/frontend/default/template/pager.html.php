<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: pager.html.php 5844 2013-05-09 08:00:59Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>

{if !empty($bPopup)}
{if !empty($aPager.nextAjaxUrl)}
<div class="js_pager_popup_view_more_link">
	<a href="{$aPager.nextUrl}" class="button btn-small no_ajax_link" onclick="$.ajaxCall('{$sAjax}', 'page={$aPager.nextAjaxUrl}{$aPager.sParamsAjax}', 'GET'); return false;">
		{if !empty($aPager.icon)}
		{img theme=$aPager.icon class='v_middle'}
		{/if}
		{if !empty($aPager.phrase)}{$aPager.phrase}{else}{_p var='view_more'}{/if}
	</a>
</div>
{/if}
{else}
<div class="js_pager_view_more_link">
	{if !empty($bIsAdminCp) && Phpfox::isAdminPanel()}
	<div class="pager_view_more_holder">
		<div class="pager_view_more_link">
			{if !empty($aPager.nextAjaxUrl)}
			<a href="{$aPager.nextUrl}" class="pager_view_more no_ajax_link" onclick="$.ajaxCall('{$sAjax}', 'page={$aPager.nextAjaxUrl}{$aPager.sParamsAjax}', 'GET'); return false;">
				{if !empty($aPager.icon)}
				{img theme=$aPager.icon class='v_middle'}
				{/if}
				{if !empty($aPager.phrase)}{$aPager.phrase}{else}{_p var='view_more'}{/if}
				<span>{_p var='displaying_of_total' displaying=$aPager.displaying total=$aPager.totalRows}</span>
			</a>
			{/if}
		</div>
	</div>
	{else}
	<a href="{$sCurrentUrl}" class="next_page" data-paging="{if isset($sPagingVar)}{$sPagingVar}{/if}">
		<i class="fa fa-spin fa-circle-o-notch"></i>
		<span>View More</span>
	</a>
	{/if}
</div>
{/if}