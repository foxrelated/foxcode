<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: moderation.html.php 4086 2012-04-05 12:32:32Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" action="{url link='current'}" id="js_global_multi_form_holder">
	{if !empty($sCustomModerationFields)}
	{$sCustomModerationFields}
	{/if}
	<div id="js_global_multi_form_ids">{$sInputFields}</div>
	<div class="moderation_holder btn-group {if !$iTotalInputFields} not_active{/if}">
		<a role="button" class="btn btn-sm moderation_drop pull-left"><span>{_p var='with_selected'} (<strong class="js_global_multi_total">{$iTotalInputFields}</strong>)</span></a>
		<a role="button" class="moderation_action moderation_action_select btn btn-sm pull-right"
		   rel="select">{_p var='select_all'}
		</a>

		<ul class="dropdown-menu">
			<li>
				<a role="button" class="moderation_clear_all">{_p var='clear_all_selected'}</a>
			</li>
			{foreach from=$aModerationParams.menu item=aModerationMenu}
			<li>
				<a href="#{$aModerationMenu.action}" class="moderation_process_action" rel="{$aModerationParams.ajax}">{$aModerationMenu.phrase}</a>
			</li>
			{/foreach}
		</ul>
		<span class="moderation_process">{img theme='ajax/add.gif'}
		</span>
		<a role="button"
		   class="moderation_action moderation_action_unselect btn btn-sm btn-default pull-right"
		   rel="unselect">{_p var='un_select_all'}</a>
	</div>
</form>