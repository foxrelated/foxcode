<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: add.html.php 6314 2013-07-19 07:16:21Z Miguel_Espinoza $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" action="{url link='admincp.ad.placement.add'}">
{if $bIsEdit}
	<div><input type="hidden" name="id" value="{$aForms.plan_id}" /></div>
{/if}
	<div class="table form-group">
		<div class="table_left">
			{_p var='title'}:
		</div>
		<div class="table_right">
			<input type="text" name="val[title]" id="title" value="{value id='title' type='input'}" size="40" />
		</div>
		<div class="clear"></div>
	</div>
	
	{if Phpfox::getParam('ad.multi_ad') != true}
		<div class="table form-group">
			<div class="table_left">
				{_p var='placement'}:
			</div>
			<div class="table_right">
				<select name="val[block_id]" id="location">	
					<option value="">{_p var='select'}:</option>
					{foreach from=$aPlanBlocks item=i}
						<option value="{$i}"{value type='select' id='block_id' default=$i}>{_p var='block' x=$i}</option>
					{/foreach}
				</select>
				<a href="#?call=ad.sample&amp;width=scan&amp;click=1" class="inlinePopup" title="{_p var='sample_layout'}">{_p var='view_site_layout'}</a>
				<div class="extra_info">
					{_p var='notice_the_ad_sizes_provided_is_a_recommendation'}
				</div>
			</div>
			<div class="clear"></div>
		</div>
		<div class="table form-group">
			<div class="table_left">
				{_p var='dimensions'}:
			</div>
			<div class="table_right">
				{_p var='width'}: <input type="text" name="val[d_width]" value="{value id='d_width' type='input'}" size="5" /> {_p var='height'}: <input type="text" name="val[d_height]" value="{value id='d_height' type='input'}" size="5" />
				<div class="extra_info">
					{_p var='ad_dimensions_are_in_pixels'}
				</div>
			</div>
			<div class="clear"></div>
		</div>
	{else}
		<div>
			<input type="hidden" name="val[block_id]" value="50" />
			<input type="hidden" name="val[d_width]" value="245" />
			<input type="hidden" name="val[d_height]" value="200" />
		</div>
	{/if}
	<div class="table form-group">
		<div class="table_left">
			{_p var='price'}:
		</div>
		<div class="table_right">
			{module name='core.currency' currency_field_name='val[cost]'}
		</div>
		<div class="clear"></div>
	</div>		
	
	<div class="table form-group">
		<div class="table_left">
			{_p var='placement_type'}:
		</div>
		<div class="table_right">
			<select name="val[is_cpm]" id="is_cpm">
				<option value="0">{_p var='select'}:</option>
				<option value="1"{value type='select' id='is_cpm' default='1'}>{_p var='cpm_cost_per_mille'}</option>
				<option value="0"{value type='select' id='is_cpm' default='0'}>{_p var='ppc_pay_per_click'}</option>
			</select>			
		</div>
	</div>
	
	<div class="table form-group-follow">
		<div class="table_left">
			{_p var='is_active'}:
		</div>
		<div class="table_right">	
			<div class="item_is_active_holder">		
				<span class="js_item_active item_is_active"><input type="radio" name="val[is_active]" value="1" {value type='radio' id='is_active' default='1' selected='true'}/> {_p var='yes'}</span>
				<span class="js_item_active item_is_not_active"><input type="radio" name="val[is_active]" value="0" {value type='radio' id='is_active' default='0'}/> {_p var='no'}</span>
			</div>
		</div>
		<div class="clear"></div>		
	</div>		
	<div class="table_clear">
		<input type="submit" value="{_p var='submit'}" class="button btn-primary" />
	</div>	
</form>