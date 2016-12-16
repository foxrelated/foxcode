<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if $bIsEdit}
<div id="js_groups_add_holder">
	<form method="post" action="{url link='groups.add'}" enctype="multipart/form-data">
		<div><input type="hidden" name="id" value="{$aForms.page_id}" /></div>
		<div><input type="hidden" name="val[category_id]" value="{value type='input' id='category_id'}" id="js_category_groups_add_holder" /></div>

		<div id="js_groups_block_detail" class="js_groups_block page_section_menu_holder">
			<div class="table form-group">
				<div class="table_left">
					{_p var='Category'}:
				</div>
				<div class="table_right">
					{if $aForms.is_app}
						{_p var='App'}
					{else}
					<div class="groups_add_category form-group">
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
					<div class="groups_sub_category form-group">
						{foreach from=$aTypes item=aType}
							{if isset($aType.categories) && is_array($aType.categories) && count($aType.categories)}
								<div class="js_groups_add_sub_category form-inline" id="js_groups_add_sub_category_{$aType.type_id}"{if $aType.type_id != $aForms.type_id} style="display:none;"{/if}>
									<select name="js_category_{$aType.type_id}" class="form-control inline">
										<option value="">{_p var='select'}</option>
										{foreach from=$aType.categories item=aCategory}
										<option value="{$aCategory.category_id}" {value type='select' id='category_id' default=$aCategory.category_id}>
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
					{/if}
				</div>
			</div>

			<div class="table hide_it">
				<div class="table_left">
					{_p var='Use timeline'}
				</div>
				<div class="table_right">
					<input type="radio" value="1" name="val[use_timeline]" {value type='radio' id='use_timeline' default='1' selected='true'} id="rdo_timeline_1"> <label for="rdo_timeline_1">{_p var='yes'}</label>
					<input type="radio" value="0" name="val[use_timeline]" {value type='radio' id='use_timeline' default='0'}id="rdo_timeline_0"> <label for="rdo_timeline_0">{_p var='no'}</label>
				</div>
			</div>

			<div class="table form-group">
				<div class="table_left">
					{_p var='Name'}:
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

			<div class="table_clear">
				<input type="submit" value="{_p var='Update'}" class="button btn-primary" />
			</div>
		</div>

		<div id="js_groups_block_url" class="block js_groups_block page_section_menu_holder" style="display:none;">

			<div class="table form-group">
				<div class="table_left">
					{_p var='Vanity url'}:
				</div>
				<div class="table_right">
					<span class="extra_info">{param var='core.path'}</span><input type="text" name="val[vanity_url]" value="{value type='input' id='vanity_url'}" size="20" id="js_vanity_url_new" class="form-control"/>
				</div>
			</div>

			<div class="table_clear" id="js_groups_vanity_url_button">
				<ul class="table_clear_button">
					<li>
						<div><input type="hidden" name="val[vanity_url_old]" value="{value type='input' id='vanity_url'}" size="20" id="js_vanity_url_old" /></div>
						<input type="button" value="{_p var='Check url'}" class="button btn-primary" onclick="if ($('#js_vanity_url_new').val() != $('#js_vanity_url_old').val()) {l} $Core.processForm('#js_groups_vanity_url_button'); $($(this).parents('form:first')).ajaxCall('groups.changeUrl'); {r} return false;" />
					</li>
					<li class="table_clear_ajax"></li>
				</ul>
				<div class="clear"></div>
			</div>

		</div>

		<div id="js_groups_block_photo" class="js_groups_block page_section_menu_holder" style="display:none;">
			<div id="js_groups_block_customize_holder">
				<div class="table form-group-follow">
					<div class="table_left">
						{_p var='Photo'}:
					</div>
					<div class="table_right">
						{if $bIsEdit && !empty($aForms.image_path)}
						    <div id="js_event_current_image">
							{img server_id=$aForms.image_server_id path='pages.url_image' file=$aForms.image_path suffix='_120' max_width='120' max_height='120'}
                            </div>
                            <div class="table_info">
                                <a href="javascript:void(0);" onclick="tb_show('{_p var=\"Change thumbnail\"}', $.ajaxBox('groups.cropme', 'height=400&width=500&id={$aForms.page_id}'))">{_p var="Change thumbnail"}</a>
                            </div>
							<div class="extra_info">
								{_p var='Click <a href="#" id="js_groups_add_change_photo">here</a> to change this photo.'}
							</div>
						{/if}
						<div id="js_event_upload_image"{if $bIsEdit && !empty($aForms.image_path)} style="display:none;"{/if}>
							<div id="js_progress_uploader"></div>
							<div class="extra_info">
								{_p var='You can upload a JPG, GIF or PNG file.'}
								{if $iMaxFileSize !== null}
								<br />
								{_p var='the_file_size_limit_is_filesize_if_your_upload_does_not_work_try_uploading_a_smaller_picture' filesize=$iMaxFileSize}
								{/if}
							</div>
						</div>
					</div>
				</div>

				<div id="js_submit_upload_image" class="table_clear"{if $bIsEdit && !empty($aForms.image_path)} style="display:none;"{/if}>
					<input type="submit" value="{_p var='Upload photo'}" class="button btn-primary" />
				</div>
			</div>
		</div>

		<div id="js_groups_block_info" class="js_groups_block page_section_menu_holder" style="display:none;">
			{plugin call='groups.template_controller_add_1'}
			<div class="table form-group">
				<div class="table_right">
					{editor id='text'}
				</div>
			</div>
			<div class="table_clear p_top_8">
				<input type="submit" value="{_p var='Update'}" class="button btn-primary" />
			</div>
		</div>

		<div id="js_groups_block_permissions" class="block js_groups_block page_section_menu_holder" style="display:none;">
			<div id="privacy_holder_table">
				{if $bIsEdit}
				<div class="table form-group-follow hidden">
					<div class="table_left">
						{_p var='Group privacy'}:
					</div>
					<div class="table_right extra_info_custom">
						{module name='privacy.form' privacy_name='privacy' privacy_info='groups.control_who_can_see_this_page' privacy_no_custom=true}
						<div class="extra_info">
							{_p var='Group privacy information'}
						</div>
					</div>
				</div>
				{/if}

				{if $bIsEdit }
				<div class="table form-group">
					<div class="table_left">
						{_p('Groups privacy')}
					</div>
					<div class="table_right">
                        <ul class="list-group">
                            <li class="list-group-item">
                                <label><input type="radio" name="val[reg_method]" id="reg_method" value="0" {if $aForms.reg_method == '0'} checked{/if}>&nbsp;<i class="fa fa-privacy fa-privacy-0"></i>&nbsp;{_p var='Public'}</label>
                                <div class="extra_info">{_p var="Anyone can see the group, its members and their posts."}</div>
                            </li>
                            <li class="list-group-item">
                                <label><input type="radio" name="val[reg_method]" id="reg_method" value="1" {if $aForms.reg_method == '1'} checked{/if}>&nbsp;<i class="fa fa-unlock-alt" aria-hidden="true"></i>&nbsp;{_p var='Closed'}</label>
                                <div class="extra_info">{_p var="Anyone can find the group and see who's in it. Only members can see posts."}</div>
                            </li>
                            <li class="list-group-item">
                                <label><input type="radio" name="val[reg_method]" id="reg_method" value="2" {if $aForms.reg_method == '2'} checked{/if}>&nbsp;<i class="fa fa-lock" aria-hidden="true"></i>&nbsp;{_p var='Secret'}</label>
                                <div class="extra_info">{_p var="Only members can find the group and see posts."}</div>
                            </li>
                        </ul>
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
							<option value="1"{if $aPerm.is_active == '1'} selected="selected"{/if}>{_p var='Members only'}</option>
							<option value="2"{if $aPerm.is_active == '2'} selected="selected"{/if}>{_p var='Admins only'}</option>
						</select>
					</div>
				</div>
				{/foreach}
				<div class="table_clear">
					<input type="submit" value="{_p var='Update'}" class="button btn-primary" />
				</div>
			</div>
		</div>

		<div id="js_groups_block_admins" class="js_groups_block page_section_menu_holder" style="display:none;">

			<div class="table form-group">
				<div>
					<div id="js_custom_search_friend_placement">{if count($aForms.admins)}
						<div class="js_custom_search_friend_holder">
							<ul>
							{foreach from=$aForms.admins item=aAdmin}
								<li>
									<a href="#" class="friend_search_remove" title="Remove" onclick="$(this).parents('li:first').remove(); return false;">{_p var='Remove'}</a>
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
				<input type="submit" value="{_p var='Update'}" class="button btn-primary" />
			</div>

			<script type="text/javascript">
				$Behavior.groupsSearchFriends = function()
				{l}
					$Core.searchFriends({l}
						'id': '#js_custom_search_friend',
						'placement': '#js_custom_search_friend_placement',
						'width': '100%',
						'max_search': 10,
						'input_name': 'admins',

						'default_value': '{_p var='Search friends by their name...'}'
					{r});
				{r};
			</script>
		</div>

		<div id="js_groups_block_invite" class="js_groups_block page_section_menu_holder" style="display:none;">
			<div class="block">
				<div class="content">
					{if isset($aForms.page_id)}
					<div id="js_selected_friends" class="hide_it"></div>
					{module name='friend.search' input='invite' hide=true friend_item_id=$aForms.page_id friend_module_id='groups' in_form=true}
					{/if}
					<div class="p_top_8">
						<input type="submit" value="{_p var='Send invitations'}" class="button btn-primary" />
					</div>
				</div>
			</div>
		</div>

		<div id="js_groups_block_widget" class="block js_groups_block page_section_menu_holder" style="display:none;">
			<div class="table form-group">
				<div class="groups_create_new_widget">
					<a href="#" onclick="$Core.box('groups.widget', 700, 'page_id={$aForms.page_id}'); return false;">{_p var='Create new widget'}</a>
				</div>
				<ul class="groups_edit_widget">
				{foreach from=$aWidgetEdits item=aWidgetEdit}
					<li class="widget" id="js_groups_widget_{$aWidgetEdit.widget_id}">
						<div class="groups_edit_widget_tools">

							<div class="row_edit_bar_parent" style="display:block;">
								<div class="row_edit_bar">
									<a role="button" class="row_edit_bar_action" data-toggle="dropdown">
										<i class="fa fa-action"></i>
									</a>
									<ul class="dropdown-menu">
										<li>
											<a href="#" onclick="$Core.box('groups.widget', 700, 'widget_id={$aWidgetEdit.widget_id}'); return false;">{_p var='Edit'}</a>
										</li>
										<li class="item_delete">
											<a href="#" onclick="$Core.jsConfirm({l}{r}, function(){l} $.ajaxCall('groups.deleteWidget', 'widget_id={$aWidgetEdit.widget_id}'); {r}, function(){l}{r}); return false;">{_p var='Delete'}</a>
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

	</form>
</div>
{else}
{if Phpfox::getUserBy('profile_page_id')}

{else}
<div id="js_groups_add_holder">
	<div class="extra_info">
		{_p var='Connect with friends, associates & fans.'}
	</div>
	<div class="main_break"></div>
	{foreach from=$aTypes item=aType}
	<div class="groups_type_add_holder">
		<a href="#" class="groups_type_add_inner_link">
			<span>
                {if Phpfox::isPhrase($this->_aVars['aType']['name'])}
                    {_p var=$aType.name}
                {else}
                    {$aType.name|convert}
                {/if}
            </span>
		</a>
		<div class="groups_type_add_form">
			<div class="groups_type_add_form_holder">
				<form method="post" action="#">
					<div><input type="hidden" name="val[type_id]" value="{$aType.type_id}" /></div>
					{if isset($aType.categories) && is_array($aType.categories) && count($aType.categories)}
					<div class="table form-group">
						<div class="table_right">
							<select name="val[category_id]" class="form-control">
								<option value="">{_p var='Choose a category'}</option>
								{foreach from=$aType.categories item=aCategory}
								<option value="{$aCategory.category_id}">
                                    {softPhrase var=$aCategory.name}
                                </option>
								{/foreach}
							</select>
						</div>
					</div>
					{/if}
					<div class="table form-group">
						<div class="table_right">
							<input type="text" name="val[title]" value="" class="form-control groups_type_add_input" placeholder="{_p var='Name'}" />
						</div>
					</div>

					<div class="table_clear" id="js_groups_add_submit_button">
						<input type="submit" value="{_p var='Get started'}" class="button btn-primary" />
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