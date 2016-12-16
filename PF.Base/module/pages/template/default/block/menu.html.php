<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: menu.html.php 4871 2012-10-10 05:51:05Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
	<ul class="list-group page-action">
		{if $aPage.is_admin}
			<li><a href="{url link='pages.add' id=$aPage.page_id}"><i class="fa fa-pencil-square-o"></i>{_p var='edit_page'}</a></li>
		{/if}
		{module name='share.link' type='pages' url=$aPage.link title=$aPage.title display='menu' sharefeedid=$aPage.page_id sharemodule='pages' extra_content='<i class="fa fa-share"></i>'}
		{if !Phpfox::getUserBy('profile_page_id')}
			<li id="js_add_pages_unlike" {if !$aPage.is_liked} style="display:none;"{/if}><a href="#" onclick="$(this).parent().hide(); $('#pages_like_join_position').show(); $.ajaxCall('like.delete', 'type_id=pages&amp;item_id={$aPage.page_id}'); return false;">{if $aPage.page_type == '1'}<i class="fa fa-sign-out"></i>{_p var='remove_membership'}{else}<i class="fa fa-thumbs-o-down"></i>{_p var='unlike'}{/if}</a></li>
		{/if}		
		{if !$aPage.is_admin && Phpfox::getUserParam('pages.can_claim_page') && empty($aPage.claim_id)}
			<li>
				<a href="#?call=contact.showQuickContact&amp;height=600&amp;width=600&amp;page_id={$aPage.page_id}" class="inlinePopup" title="{_p var='claim_page'}">
					<i class="fa fa-paper-plane"></i>{_p var='claim_page'}
				</a>
			</li>
		{/if}
		{if Phpfox::isModule('report') && ($aPage.user_id != Phpfox::getUserId())}
		<li><a href="#?call=report.add&amp;height=210&amp;width=400&amp;type=pages&amp;id={$aPage.page_id}" class="inlinePopup" title="{_p var='Report this Page'}"><i class="fa fa-flag"></i>{_p var='report'}</a></li>
		{/if}
	</ul>