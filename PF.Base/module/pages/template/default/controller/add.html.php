<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: add.html.php 5569 2013-03-27 10:09:46Z Miguel_Espinoza $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $bIsEdit}
<div id="js_pages_add_holder">
	<form method="post" action="{url link='pages.add'}" enctype="multipart/form-data">
        {if $bIsEdit}
		<div><input type="hidden" name="id" value="{$aForms.page_id}" /></div>
        {/if}
		<div><input type="hidden" name="val[category_id]" value="{value type='input' id='category_id'}" id="js_category_pages_add_holder" /></div>

		<div id="js_pages_block_detail" class="js_pages_block page_section_menu_holder">
			<div class="table form-group">
				<div class="table_left">
					{_p var='category'}:
				</div>
				<div class="table_right">
					<div class="pages_add_category form-group">
						<select name="val[type_id]" class="form-control inline">
						{foreach from=$aTypes item=aType}
							<option value="{$aType.type_id}"{value type='select' id='type_id' default=$aType.type_id}>
                                {if Phpfox::isPhrase($this->_aVars['aType']['name'])}
                                {_p var=$aType.name}
                                {else}
                                {$aType.name|convert}
                                {/if}
                            </option>
						{/foreach}			
						</select>
					</div>
					<div class="pages_sub_category form-group">
						{foreach from=$aTypes item=aType}
							{if isset($aType.categories) && is_array($aType.categories) && count($aType.categories)}					
								<div class="js_pages_add_sub_category form-inline" id="js_pages_add_sub_category_{$aType.type_id}"{if $aType.type_id != $aForms.type_id} style="display:none;"{/if}>
									<select name="js_category_{$aType.type_id}" class="form-control inline">
										<option value="">{_p var='select'}</option>
										{foreach from=$aType.categories item=aCategory}
										<option value="{$aCategory.category_id}"{value type='select' id='category_id' default=$aCategory.category_id}>
                                            {if Phpfox::isPhrase($this->_aVars['aCategory']['name'])}
                                            {_p var=$aCategory.name}
                                            {else}
                                            {$aCategory.name|convert}
                                            {/if}
                                        </option>
										{/foreach}
									</select>
								</div>					
							{/if}			
						{/foreach}						
					</div>
					<div class="clear"></div>
				</div>
			</div>	
			
			<div class="table form-group">
				<div class="table_left">
					{_p var='name'}:
				</div>
				<div class="table_right">
					{if $aForms.is_app}
					<div><input type="hidden" name="val[title]" value="{$aForms.title|clean}" maxlength="200" size="40" /></div>
					<a href="{permalink module='apps' id=$aForms.app_id title=$aForms.title}">{$aForms.title|clean}</a>
					{else}
					<input type="text" name="val[title]" value="{value type='input' id='title'}" maxlength="200" size="40" class="form-control"/>
					{/if}
				</div>
			</div>
			
			<div class="table">
				<div class="table_left">
					{_p var='landing_page'}:
				</div>
				<div class="table_right">
					<select name="val[landing_page]" class="form-control">
						{foreach from=$aForms.landing_pages item=aLanding}
						{if isset($aLanding.landing)}
						<option value="{$aLanding.landing}"{if isset($aLanding.is_selected) && $aLanding.is_selected} selected="selected"{/if}>{$aLanding.phrase}</option>
						{/if}
						{/foreach}
					</select>
				</div>
			</div>			

			<div class="table_clear">
				<input type="submit" value="{_p var='update'}" class="button btn-primary" />
			</div>
		</div>
		
		<div id="js_pages_block_url" class="block js_pages_block page_section_menu_holder" style="display:none;">
			
			<div class="table form-group">
				<div class="table_left">
					{_p var='vanity_url'}:
				</div>
				<div class="table_right">
					<span class="extra_info">{param var='core.path'}</span><input type="text" name="val[vanity_url]" value="{value type='input' id='vanity_url'}" size="20" id="js_vanity_url_new" class="form-control"/>
				</div>
			</div>		
			
			<div class="table_clear" id="js_pages_vanity_url_button">
				<ul class="table_clear_button">
					<li>
						<div><input type="hidden" name="val[vanity_url_old]" value="{value type='input' id='vanity_url'}" size="20" id="js_vanity_url_old" /></div>
						<input type="button" value="{_p var='check_url'}" class="button btn-primary" onclick="if ($('#js_vanity_url_new').val() != $('#js_vanity_url_old').val()) {l} $Core.processForm('#js_pages_vanity_url_button'); $($(this).parents('form:first')).ajaxCall('pages.changeUrl'); {r} return false;" />
					</li>
					<li class="table_clear_ajax"></li>
				</ul>		
				<div class="clear"></div>
			</div>			
			
		</div>
		
		<div id="js_pages_block_photo" class="js_pages_block page_section_menu_holder" style="display:none;">
			<div id="js_pages_block_customize_holder">
				<div class="table form-group-follow">
					<div class="table_left">
						{_p var='photo'}:
					</div>
					<div class="table_right">
						{if $bIsEdit && !empty($aForms.image_path)}
						<div id="js_event_current_image">
							{img server_id=$aForms.image_server_id path='pages.url_image' file=$aForms.image_path suffix='_120' max_width='120' max_height='120'}
						</div>
                        <div class="table_info">
                            <a href="javascript:void(0);" onclick="tb_show('{_p var=\"Change thumbnail\"}', $.ajaxBox('pages.cropme', 'height=400&width=500&id={$aForms.page_id}'))">{_p var="Change thumbnail"}</a>
                        </div>
                        <div class="extra_info">
                            {_p var='click_here_to_change_this_photo'}
                        </div>
						{/if}
						<div id="js_event_upload_image"{if $bIsEdit && !empty($aForms.image_path)} style="display:none;"{/if}>
							<div id="js_progress_uploader"></div>
							<div class="extra_info">
								{_p var='you_can_upload_a_jpg_gif_or_png_file'}
								{if $iMaxFileSize !== null}
								<br />
								{_p var='the_file_size_limit_is_filesize_if_your_upload_does_not_work_try_uploading_a_smaller_picture' filesize=$iMaxFileSize}
								{/if}							
							</div>
						</div>
					</div>
				</div>

				<div id="js_submit_upload_image" class="table_clear"{if $bIsEdit && !empty($aForms.image_path)} style="display:none;"{/if}>
					<input type="submit" value="{_p var='upload_photo'}" class="button btn-primary" />
				</div>
			</div>	
		</div>		
		
		<div id="js_pages_block_info" class="js_pages_block page_section_menu_holder" style="display:none;">
			{plugin call='pages.template_controller_add_1'}
			<div class="table form-group">
				<div class="table_right">
					{editor id='text'}
				</div>
			</div>
			<div class="table_clear p_top_8">
				<input type="submit" value="{_p var='update'}" class="button btn-primary" />
			</div>			
		</div>
		
		<div id="js_pages_block_permissions" class="block js_pages_block page_section_menu_holder" style="display:none;">
			<div id="privacy_holder_table">
				{if $bIsEdit}
				<div class="table form-group-follow hidden">
					<div class="table_left">
						{_p var='page_privacy'}:
					</div>
					<div class="table_right extra_info_custom">	
						{module name='privacy.form' privacy_name='privacy' privacy_info='pages.control_who_can_see_this_page' privacy_no_custom=true}
						<div class="extra_info">
							{_p var='pages_privacy_information'}
						</div>
					</div>			
				</div>				
				{/if}

				{if $bIsEdit && $aForms.page_type == '1'}
				<div class="table form-group">
					<div class="table_left">
						{_p('Registration Method')}
					</div>
					<div class="table_right">
						<select name="val[reg_method]" class="form-control">
							<option value="0"{if $aForms.reg_method == '0'} selected="selected"{/if}>{_p var='anyone'}</option>
							<option value="1"{if $aForms.reg_method == '1'} selected="selected"{/if}>{_p var='approval_first'}</option>
							<option value="2"{if $aForms.reg_method == '2'} selected="selected"{/if}>{_p var='invite_only'}</option>
						</select>					
					</div>
				</div>
				{/if}
				{foreach from=$aPermissions item=aPerm}
				<div class="table form-group">
					<div class="table_left">
						{$aPerm.phrase}
					</div>
					<div class="table_right">
						<select name="val[perms][{$aPerm.id}]" class="form-control">
							<option value="0"{if $aPerm.is_active == '0'} selected="selected"{/if}>{_p var='anyone'}</option>
							<option value="1"{if $aPerm.is_active == '1'} selected="selected"{/if}>{_p var='members_only'}</option>
							<option value="2"{if $aPerm.is_active == '2'} selected="selected"{/if}>{_p var='admins_only'}</option>
						</select>					
					</div>
				</div>
				{/foreach}
				<div class="table_clear">
					<input type="submit" value="{_p var='update'}" class="button btn-primary" />
				</div>				
			</div>				
		</div>
		
		<div id="js_pages_block_admins" class="js_pages_block page_section_menu_holder" style="display:none;">

			<div class="table form-group">
				<div>
					<div id="js_custom_search_friend_placement">{if count($aForms.admins)}
						<div class="js_custom_search_friend_holder">
							<ul>
							{foreach from=$aForms.admins item=aAdmin}
								<li>
									<a href="#" class="friend_search_remove" title="Remove" onclick="$(this).parents('li:first').remove(); return false;">{_p var='remove'}</a>
									<div class="friend_search_image">{img user=$aAdmin suffix='_50_square' max_width='25' max_height='25'}</div>
									<div class="friend_search_name">{$aAdmin.full_name|clean}</div>
									<div class="clear"></div>
									<div><input type="hidden" name="admins[]" value="{$aAdmin.user_id}" /></div>
								</li>
							{/foreach}
							</ul>
						</div>
						{/if}</div>
				</div>
				<div>
					<div id="js_custom_search_friend"></div>
				</div>
			</div>

			<div class="table_clear">
				<input type="submit" value="{_p var='update'}" class="button btn-primary" />
			</div>
						
			<script type="text/javascript">
				$Behavior.pagesSearchFriends = function()
				{l}
					$Core.searchFriends({l}
						'id': '#js_custom_search_friend',
						'placement': '#js_custom_search_friend_placement',
						'width': '100%',
						'max_search': 10,
						'input_name': 'admins',
						
						'default_value': '{_p var='search_friends_by_their_name'}'
					{r});	
				{r};
			</script>						
		</div>
		
		<div id="js_pages_block_invite" class="js_pages_block page_section_menu_holder" style="display:none;">
			<div class="block">
				<div class="content">
					{if isset($aForms.page_id)}
					<div id="js_selected_friends" class="hide_it"></div>
					{module name='friend.search' input='invite' hide=true friend_item_id=$aForms.page_id friend_module_id='pages' in_form=true}
					{/if}
					<div class="p_top_8">
						<input type="submit" value="{_p var='send_invitations'}" class="button btn-primary" />
					</div>
				</div>
			</div>
		</div>		
		
		<div id="js_pages_block_widget" class="block js_pages_block page_section_menu_holder" style="display:none;">
			<div class="table form-group">
				<div class="pages_create_new_widget">
					<a href="#" onclick="$Core.box('pages.widget', 700, 'page_id={$aForms.page_id}'); return false;">{_p var='create_new_widget'}</a>
				</div>
				<ul class="pages_edit_widget">
				{foreach from=$aWidgetEdits item=aWidgetEdit}
					<li class="widget" id="js_pages_widget_{$aWidgetEdit.widget_id}">
						<div class="pages_edit_widget_tools">

							<div class="row_edit_bar_parent" style="display:block;">
								<div class="row_edit_bar">
									<a role="button" class="row_edit_bar_action" data-toggle="dropdown">
										<i class="fa fa-action"></i>
									</a>
									<ul class="dropdown-menu dropdown-menu-right">
										<li>
											<a href="#" onclick="$Core.box('pages.widget', 700, 'widget_id={$aWidgetEdit.widget_id}'); return false;">{_p var='edit'}</a>
										</li>
										<li class="item_delete">
											<a href="#" onclick="$Core.jsConfirm({l}{r}, function(){l} $.ajaxCall('pages.deleteWidget', 'widget_id={$aWidgetEdit.widget_id}'); {r}, function(){l}{r}); return false;">{_p var='delete'}</a>
										</li>
									</ul>
								</div>
							</div>

						</div>
						{$aWidgetEdit.title|clean}
					</li>
				{/foreach}
				</ul>
			</div>
		</div>
		
		
		{if Phpfox::getParam('core.ip_infodb_api_key') != '' || Phpfox::getParam('core.google_api_key')}
			<div id="js_pages_block_location" class="block js_pages_block page_section_menu_holder" style="display:none;">
				{_p var='place_your_page_in_the_map'}
				
				<div class="table form-group" id="js_location_enter">
					<div class="table_left">
						{_p var='you_can_also_write_your_address'}
					</div>
					<div class="table_right">
						<input type="text" name="val[location][name]" id="txt_location_name">
						<div id="js_add_location_suggestions"></div>
					</div>
					<div>
						<input type="hidden" name="val[location][latlng]" id="txt_location_latlng">
					</div>
				</div>
				
				<div class="table form-group">
					<div class="table_left">
					</div>
					
					<div class="table_right">
						<div id="js_location"></div>
					</div>
				</div>

					<div class="table_clear">
						<input type="submit" value="{_p var='update'}" class="button btn-primary" />
					</div>	
			</div>
		{/if}

	</form>
</div>
{else}
{if Phpfox::getUserBy('profile_page_id')}
{_p var='logged_in_as_a_page' full_name=$aGlobalProfilePageLogin.full_name}
{else}
<div id="js_pages_add_holder">
	<div class="extra_info">
		{_p var='connect_with_friends_associates_amp_fans'}
	</div>
	<div class="main_break"></div>
	{foreach from=$aTypes item=aType}
	<div class="pages_type_add_holder">
		<a href="#" class="pages_type_add_inner_link">
			<span>
            {if Phpfox::isPhrase($this->_aVars['aType']['name'])}
            {_p var=$aType.name}
            {else}
            {$aType.name|convert}
            {/if}
            </span>
		</a>
		<div class="pages_type_add_form">
			<div class="pages_type_add_form_holder">
				<form method="post" action="#">
					<div><input type="hidden" name="val[type_id]" value="{$aType.type_id}" /></div>
					{if isset($aType.categories) && is_array($aType.categories) && count($aType.categories)}
					<div class="table form-group">
						<div class="table_right">
							<select name="val[category_id]" class="form-control">
								<option value="">{_p var='choose_a_category'}</option>
								{foreach from=$aType.categories item=aCategory}
								<option value="{$aCategory.category_id}">
                                    {if Phpfox::isPhrase($this->_aVars['aCategory']['name'])}
                                    {_p var=$aCategory.name}
                                    {else}
                                    {$aCategory.name|convert}
                                    {/if}
                                </option>
								{/foreach}
							</select>
						</div>
					</div>					
					{/if}
					<div class="table form-group">
						<div class="table_right">
							<input type="text" name="val[title]" value="" class="form-control pages_type_add_input" placeholder="{_p var='Name'}" />
						</div>
					</div>

					<div class="table_clear" id="js_pages_add_submit_button">
						<input type="submit" value="{_p var='get_started'}" class="button btn-primary" />
					</div>

				</form>
			</div>
		</div>
	</div>
	{/foreach}
	<div class="clear"></div>
</div>
{/if}
{/if}