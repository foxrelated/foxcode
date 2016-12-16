<?php 
/*might not use*/
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="table form-group"{if !PHPFOX_IS_TECHIE} style="display:none;"{/if}>
	<div class="table_left">
	{if $bModuleFormRequired}{required}{/if}{$sModuleFormTitle}:
	</div>
	<div class="table_right">			
		<select name="val[{$sModuleFormId}]" {if $bUseClass}class{else}id{/if}="{$sModuleFormId}">
		<option value="">{$sModuleFormValue}</option>
		{foreach from=$aModules key=sModule item=iModuleId}
			<option value="{$sModule}"{value type='select' id=''$sModuleFormId'' default=$sModule}>{translate var=$sModule prefix='module'}</option>
		{/foreach}
		</select>	
	</div>
</div>