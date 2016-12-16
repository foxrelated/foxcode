<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="checkbox">
<label for="js_category{$aItem.category_id}" id="js_category_label{$aItem.category_id}">
	<input {if (isset($aItem.is_active))} checked="checked"{/if} value="{$aItem.category_id}" type="checkbox" name="val[category][]" id="js_category{$aItem.category_id}" class="checkbox v_middle" onclick="if (this.checked) $('#js_selected_categories').val($('#js_selected_categories').val() + this.value + ','); else $('#js_selected_categories').val($('#js_selected_categories').val().replace(this.value + ',', ''));" />
    
    {if Phpfox::isPhrase($this->_aVars['aItem']['name'])}
    {_p var=$aItem.name}
    {else}
    {$aItem.name|convert|clean}
    {/if}
</label>
</div>