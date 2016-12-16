<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{foreach from=$aPerms key=sPerm item=aPerm}
<div class="table form-group-follow">
	<div class="table_left">
		{$aPerm.phrase}
	</div>
	<div class="table_right">
		<div class="item_is_active_holder">		
			<span class="js_item_active item_is_active"><input type="radio" name="val[perm][{$sPerm}]" value="1"{if $aPerm.value} checked="checked"{/if}/> {_p var='yes'}</span>
                <span class="js_item_active item_is_not_active"><input type="radio" name="val[perm][{$sPerm}]" value="0"{if !$aPerm.value} checked="checked"{/if}/> {_p var='no'}</span>
		</div>
	</div>
	<div class="clear"></div>
</div>
{/foreach}