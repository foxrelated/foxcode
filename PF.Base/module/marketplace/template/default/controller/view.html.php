<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: view.html.php 5032 2012-11-19 13:58:57Z Miguel_Espinoza $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>

{if (Phpfox::getParam('marketplace.days_to_expire_listing') > 0) && ( $aListing.time_stamp < (PHPFOX_TIME - (Phpfox::getParam('marketplace.days_to_expire_listing') * 86400)) )}
	<div class="error_message">
		{_p var='listing_expired_and_not_available_main_section'}
	</div>
{/if}
{if $aListing.view_id == '1'}
<div class="message js_moderation_off">
	{_p var='listing_is_pending_approval'}
</div>
{/if}
<div class="item_view">

	<div class="item_info">
		<span>{$aListing.time_stamp|date:'marketplace.marketplace_view_time_stamp'}</span>
		<span>{_p var='by'} {$aListing|user:'':'':50}</span>
		<span>{_p var='at'} {$aListing.country_iso|location}
			{if !empty($aListing.country_child_id)}
			&raquo; {$aListing.country_child_id|location_child}
			{/if}
			{if !empty($aListing.city)}
			&raquo; {$aListing.city|clean|split:50}
			{/if}</span>
	</div>

	<div class="item_info_more clearfix">
		<span class="listing_view_price" itemprop="price">{$sListingPrice}</span>
		{if $aListing.user_id != Phpfox::getUserId()}
		<div class="listing_purchase">
			<div>
			<a href="#" class="btn btn-info marketplace_contact_seller" onclick="$Core.composeMessage({l}user_id: {$aListing.user_id}, listing_id: {$aListing.listing_id}{r}); return false;">
				<i class="fa fa-comment"></i>
				<span>{_p var='contact_seller'}</span>
			</a>
			</div>
			{if ($aListing.is_sell && $aListing.view_id != '2' && $aListing.price != '0.00')}
			<div class="marketplace_price_holder_button">
				<form method="post" action="{url link='marketplace.purchase'}">
					<div><input type="hidden" name="id" value="{$aListing.listing_id}" /></div>
					<button type="submit" value="{_p var='buy_it_now'}" class="btn btn-success">
						<i class="fa fa-cart-plus"></i>
						<span>{_p var='buy_it_now'}</span>
					</button>
				</form>
			</div>
			{/if}
		</div>
		{/if}
	</div>

	{if ($aListing.user_id == Phpfox::getUserId() && Phpfox::getUserParam('marketplace.can_edit_own_listing')) || Phpfox::getUserParam('marketplace.can_edit_other_listing')
	|| ($aListing.user_id == Phpfox::getUserId() && Phpfox::getUserParam('marketplace.can_delete_own_listing')) || Phpfox::getUserParam('marketplace.can_delete_other_listings')
	|| (Phpfox::getUserParam('marketplace.can_feature_listings'))
	}
	<div class="item_bar" style="margin-top: -4px">
		<div class="item_bar_action_holder">
			{if (Phpfox::getUserParam('marketplace.can_approve_listings') && $aListing.view_id == '1')}
			<a href="#" class="item_bar_approve item_bar_approve_image" onclick="return false;" style="display:none;" id="js_item_bar_approve_image">{img theme='ajax/add.gif'}</a>
			<a href="#" class="item_bar_approve" onclick="$(this).hide(); $('#js_item_bar_approve_image').show(); $.ajaxCall('marketplace.approve', 'inline=true&amp;listing_id={$aListing.listing_id}'); return false;">{_p var='approve'}</a>
			{/if}
			<a role="button" data-toggle="dropdown" class="item_bar_action"><span>{_p var='actions'}</span></a>
			<ul class="dropdown-menu dropdown-menu-right">
				{template file='marketplace.block.menu'}
			</ul>
		</div>
	</div>
	{/if}

	{if $aImages}

	<input type="hidden" name="marketdetail_load_slider" value="1" id="marketdetail_load_slider"/>

	<div class="ms-marketplace-detail-showcase dont-unbind">
		<div class="master-slider ms-skin-default dont-unbind" id="marketplace_slider-detail">
			{foreach from=$aImages name=images item=aImage}
			<div class="ms-slide dont-unbind">
				<img src="{$core_path}module/core/static/masterslider/blank.gif"
					 data-src="{img server_id=$aImage.server_id path='marketplace.url_image' file=$aImage.image_path return_url=true}"/>
				<img class="ms-thumb dont-unbind" src="{img server_id=$aImage.server_id path='marketplace.url_image' file=$aImage.image_path suffix='_120_square' return_url=true}" alt="thumb" />
			</div>
			{/foreach}
		</div>
	</div>
	{/if}

	{module name='marketplace.info'}

	{plugin call='marketplace.template_default_controller_view_extra_info'}

	<div {if $aListing.view_id != 0}style="display:none;" class="js_moderation_on"{/if}>
		{module name='feed.comment'}
	</div>
</div>

{literal}
<script type="text/javascript">
	(function(){
		var
				_debug = true,
				_stageSlider = '#marketdetail_load_slider',
				_required = function(){
					return !/undefined/i.test(typeof MasterSlider)
				},

				_initDetailSlide_flag = false,
				initDetailSlide = function (){
					var stageSlider =  $(_stageSlider);
					if(!stageSlider.length) return;
					if(_initDetailSlide_flag) return;
					if(!_required()) return;

					if($('#marketdetail_load_slider').val() == 1)
					{
						var slider = new MasterSlider();

						slider.control('arrows');
						slider.control('thumblist' , {
							autohide:false ,
							dir:'h',
							arrows:true,
							align:'bottom',
							width:60,
							height:60,
							margin:5,
							space:5
						});
						slider.setup('marketplace_slider-detail' , {
							width: $('#marketplace_slider-detail').width(),
							height:330,
							space:5,
							view:'fadeWave',
							fillMode: 'center',
						});
						$('#marketdetail_load_slider').val(0);
					}

					_initDetailSlide_flag = true;
				}

		$Behavior.initDetailSlide = function() {
			function checkCondition(){
				var stageSlider =  $(_stageSlider);
				if(!stageSlider.length) return;
				if(_initDetailSlide_flag) return;
				if(!_required()){
					window.setTimeout(checkCondition, 1700);
				}
				else
				{
					initDetailSlide();
				}
			}
			window.setTimeout(checkCondition, 1700);
		}

	})();

</script>
{/literal}