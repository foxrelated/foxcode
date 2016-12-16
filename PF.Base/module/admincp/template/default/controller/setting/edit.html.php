<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Admincp
 * @version 		$Id: edit.html.php 7128 2014-02-19 13:21:59Z Fern $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if count($aSettings)}

<script type="text/javascript">

function addInput(oObj, sVarName)
{l}
	var sValue = $(oObj).parents('.js_array_holder:first').find('.js_add_to_array').val();
	var iCnt = (parseInt($(oObj).parents('.js_array_holder:first').find('#js_array_count').html()) + 1);
	$(oObj).parents('.js_array_holder:first').find('.js_array_data').append('<div class="p_4" id="js_array' + iCnt + '"><input type="text" name="val[value][' + sVarName + '][]" value="' + sValue + '" size="30" /> - <a href="#" onclick="$(this).parent().remove(); return false;">{_p var='remove' phpfox_squote=true}</a></div>');
	$(oObj).parents('.js_array_holder:first').find('.js_array_count').html(iCnt);
	$(oObj).parents('.js_array_holder:first').find('.js_add_to_array').val('').focus();
  {literal}
  var t = $(oObj).parents('form:first');
  if (t.attr('action') == '#') {
    $(oObj).parents('form:first').trigger('submit');
  }
  else {
    $Core.processing();
    $.ajax({
      url: t.attr('action'),
      type: 'POST',
      data: t.serialize(),
      success: function(e) {
        $('.ajax_processing').fadeOut();
      }
    });
  }
{/literal}
	return false;
{r}
</script>
<form method="post" action="{url link='current'}" enctype="multipart/form-data" class="on_change_submit">
{foreach from=$aSettings item=aSetting}
<div id="{$aSetting.var_name}"></div>
<div class="table_header2 settings">
		{if PHPFOX_DEBUG}<div class="go_left"> <input type="text" name="val[order][{$aSetting.var_name}]" value="{$aSetting.ordering}" style="font-size:9pt; padding:0px; text-align:center;" onclick="this.select();" size="2" /> {/if} <a name="#{$aSetting.var_name}"></a>{$aSetting.setting_title}{if PHPFOX_DEBUG}</div><div class="t_right">{if isset($aSetting.group_title)} ({$aSetting.group_title}) {/if}<input type="text" name="param{$aSetting.var_name}" value="{$aSetting.module_id}.{$aSetting.var_name}" style="font-size:9pt; padding:0px; width: 200px" onclick="this.select();" /></div>{/if}
</div>
<div class="table3 settings">

	<div class="row_right">
		{if $aSetting.type_id == 'multi_text'}
		{foreach from=$aSetting.values key=mKey item=sDropValue}
		<div class="p_4">
			{$mKey}: <input type="text" name="val[value][{$aSetting.var_name}][{$mKey}]" value="{$sDropValue|clean}" size="8" />
		</div>
		{/foreach}
		{elseif $aSetting.type_id == 'large_string'}
		<textarea cols="60" rows="8" name="val[value][{$aSetting.var_name}]">{$aSetting.value_actual|htmlspecialchars}</textarea>
		{elseif ($aSetting.type_id == 'string')}
		<div><input type="text" name="val[value][{$aSetting.var_name}]" value="{$aSetting.value_actual|clean}" size="40" /></div>		
		{elseif ($aSetting.type_id == 'password')}
		<div><input type="password" name="val[value][{$aSetting.var_name}]" value="{$aSetting.value_actual}" size="40" autocomplete="off" /></div>
		{elseif ($aSetting.type_id == 'drop')}
		<div><input type="hidden" name="val[value][{$aSetting.var_name}][real]" value="{$aSetting.value_actual}" size="40" /></div>
		<select name="val[value][{$aSetting.var_name}][value]">
		{foreach from=$aSetting.values.values key=mKey item=sDropValue}
			<option value="{$sDropValue}" {if $aSetting.values.default == $sDropValue}selected="selected"{/if}>
				{if !empty($sDropValue) && !stripos( $sDropValue, ' ') && !stripos($sDropValue, '.')}
					{php}{$this->_aVars['sDropValue'] = strtolower($this->_aVars['sDropValue']);}{/php}
					{_p var=$sDropValue}
				{else}
					{$sDropValue}
				{/if}
			</option>
		{/foreach}
		</select>
		{elseif ($aSetting.type_id == 'drop_with_key')}
		<select name="val[value][{$aSetting.var_name}]">
		{foreach from=$aSetting.values key=mKey item=sDropValue}
			<option value="{$mKey}"{if $aSetting.value_actual == $mKey} selected="selected"{/if}>{$sDropValue}</option>
		{/foreach}
		</select>	
		{elseif ($aSetting.type_id == 'integer')}
		<input type="text" name="val[value][{$aSetting.var_name}]" value="{$aSetting.value_actual}" size="40" onclick="this.select();" />
		{elseif ($aSetting.type_id == 'boolean')}
		<div class="item_is_active_holder">
			<span class="js_item_active item_is_active">
				<input type="radio" value="1" name="val[value][{$aSetting.var_name}]"{if $aSetting.value_actual == 1} checked="checked"{/if}> Yes
			</span>
			<span class="js_item_active item_is_not_active">
				<input type="radio" value="0" name="val[value][{$aSetting.var_name}]"{if $aSetting.value_actual != 1} checked="checked"{/if}> No
			</span>
		</div>
		{elseif ($aSetting.type_id == 'array')}
		<div class="js_array_holder">
			{if is_array($aSetting.value_actual)}
			{foreach from=$aSetting.value_actual key=iKey item=sValue}
				<div class="p_4" class="js_array{$iKey}"><input type="text" name="val[value][{$aSetting.var_name}][]" value="{$sValue}" size="30" /> - <a href="#" onclick="var obj = $(this); $Core.jsConfirm({left_curly}{right_curly},function(){left_curly} $.ajaxCall('admincp.removeSettingFromArray', 'setting={$aSetting.var_name}&amp;value={$sValue}'); obj.parent().remove(); {right_curly},function(){left_curly}{right_curly}); return false;">{_p var='remove'}</a></div>
			{/foreach}		
			{/if}
			<div class="js_array_data"></div>
			<div class="js_array_count" style="display:none;">{if isset($iKey)}{$iKey+1}{/if}</div>
			<br />
			<div class="p_4">
				<input type="text" name="" value="{_p var='add_a_new_value'}" onclick="if(this.value=='{_p var='add_a_new_value' phpfox_squote=true}')this.value='';" onblur="if(this.value=='')this.value='{_p var='add_a_new_value' phpfox_squote=true}';" size="30" class="js_add_to_array" /> <input type="button" value="{_p var='add'}" class="button btn-primary" onclick="return addInput(this, '{$aSetting.var_name}');" />
			</div>
		</div>
		{/if}
	</div>

	<div class="extra_info">
		{$aSetting.setting_info}
	</div>

</div>
{if $aSetting.var_name == 'watermark_option'}
<div class="table_header2">
	{_p var='image'}
</div>
<div class="table3">
	<div class="row_left">		
		{_p var='your_current_watermark_image'}:
		<div class="p_4">
			<img src="{$sWatermarkImage}" alt="Watermark Image" />
		</div>
		<div class="p_4">
			{_p var='b_notice_b_advised_image_is_a_transparent_png_with_a_max_width_height_of_52_pixels'}
		</div>
	</div>
	<div class="row_right" style="margin-bottom:20px;">
		<input type="file" name="watermark" size="30" />
		<div class="extra_info">
			{_p var='you_can_upload_a_jpg_gif_or_png_file'}
		</div>
	</div>
	<div class="clear"></div>
</div>
{/if}
{/foreach}
	<div class="table_clear submit_btn" {if $bAutoSaveSettings}style="display: none;"{/if}>
		<input type="submit" class="btn btn-danger" value="{_p var='Save Changes'}">
	</div>
</form>
{else}
<p>{_p var='setting_group_avaliable_settings'}</p>
{/if}
{if $sGroupId == 'mail'}
    <form method="post" action="{url link='current'}&test=true" enctype="multipart/form-data">
        <div class="table">
            <div class="table_left">
                {_p var="Send a Test Email"}
            </div>
            <div class="table_right">
                <input type="text" name="val[email_send_test]" placeholder="{_p var='To'}"/>
            </div>
            <div class="extra_info">
                {_p var="Type an email address here and then click Send Test to generate a test email"}
            </div>
            <div class="table_clear">
                <input type="submit" class="button" value="{_p var='Send Test'}">
            </div>
        </div>
    </form>
{/if}
