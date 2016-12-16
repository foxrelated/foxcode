<module>
	<data>
		<module_id>photo</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>1</is_menu>
		<menu><![CDATA[a:2:{s:33:"photo.admin_menu_add_new_category";a:1:{s:3:"url";a:1:{i:0;s:9:"photo.add";}}s:27:"photo.admin_menu_categories";a:1:{s:3:"url";a:1:{i:0;s:5:"photo";}}}]]></menu>
		<phrase_var_name>module_photo</phrase_var_name>
		<writable><![CDATA[a:1:{i:0;s:15:"file/pic/photo/";}]]></writable>
	</data>
	<menus>
		<menu module_id="photo" parent_var_name="" m_connection="main" var_name="menu_photo" ordering="4" url_value="photo" version_id="2.0.0alpha1" disallow_access="" module="photo" mobile_icon="photo" />
		<menu module_id="photo" parent_var_name="" m_connection="profile" var_name="menu_photos" ordering="38" url_value="profile.photo" version_id="2.0.0alpha1" disallow_access="" module="photo" />
		<menu module_id="photo" parent_var_name="" m_connection="photo.index" var_name="menu_photo_upload_a_new_image_714586c73197300f65ba08f7dee8cb4a" ordering="128" url_value="photo.add" version_id="3.3.0beta2" disallow_access="" module="photo" />
		<menu module_id="photo" parent_var_name="" m_connection="mobile" var_name="menu_photo_photos_532c28d5412dd75bf975fb951c740a30" ordering="122" url_value="photo" version_id="3.1.0rc1" disallow_access="" module="photo" mobile_icon="small_photos.png" />
		<menu module_id="photo" parent_var_name="" m_connection="photo.albums" var_name="menu_photo_upload_a_new_image_0df7df42d810e7978c535292f273fc91" ordering="129" url_value="photo.add" version_id="3.5.0beta1" disallow_access="" module="photo" />
	</menus>
	<settings>
		<setting group="" module_id="photo" is_hidden="0" type="array" var_name="photo_pic_sizes" phrase_var_name="setting_photo_pic_sizes" ordering="1" version_id="2.0.0alpha1"><![CDATA[s:93:"array(
  0 => '75',
  1 => '100',
  2 => '150',
  3 => '240',
  4 => '500',
  5 => '1024'
);";]]></setting>
		<setting group="time_stamps" module_id="photo" is_hidden="0" type="string" var_name="photo_image_details_time_stamp" phrase_var_name="setting_photo_image_details_time_stamp" ordering="1" version_id="2.0.0alpha1">F j, Y</setting>
		<setting group="" module_id="photo" is_hidden="0" type="boolean" var_name="allow_photo_category_selection" phrase_var_name="setting_allow_photo_category_selection" ordering="1" version_id="3.1.0rc1">0</setting>
		<setting group="" module_id="photo" is_hidden="0" type="boolean" var_name="enabled_watermark_on_photos" phrase_var_name="setting_enabled_watermark_on_photos" ordering="1" version_id="2.0.0rc1">0</setting>
		<setting group="seo" module_id="photo" is_hidden="0" type="large_string" var_name="photo_meta_description" phrase_var_name="setting_photo_meta_description" ordering="8" version_id="2.0.0rc1" />
		<setting group="seo" module_id="photo" is_hidden="0" type="large_string" var_name="photo_meta_keywords" phrase_var_name="setting_photo_meta_keywords" ordering="14" version_id="2.0.0rc1" />
		<setting group="" module_id="photo" is_hidden="0" type="boolean" var_name="ajax_refresh_on_featured_photos" phrase_var_name="setting_ajax_refresh_on_featured_photos" ordering="1" version_id="2.0.0">0</setting>
		<setting group="" module_id="photo" is_hidden="0" type="boolean" var_name="display_profile_photo_within_gallery" phrase_var_name="setting_display_profile_photo_within_gallery" ordering="1" version_id="3.1.0beta1">0</setting>
		<setting group="" module_id="photo" is_hidden="1" type="boolean" var_name="rename_uploaded_photo_names" phrase_var_name="setting_rename_uploaded_photo_names" ordering="1" version_id="2.0.0alpha3">0</setting>
		<setting group="" module_id="photo" is_hidden="1" type="boolean" var_name="photo_upload_process" phrase_var_name="setting_photo_upload_process" ordering="1" version_id="3.3.0beta1">1</setting>
		<setting group="" module_id="photo" is_hidden="1" type="drop" var_name="in_main_photo_section_show" phrase_var_name="setting_in_main_photo_section_show" ordering="1" version_id="3.5.0beta1"><![CDATA[a:2:{s:7:"default";s:6:"photos";s:6:"values";a:2:{i:0;s:6:"photos";i:1;s:6:"albums";}}]]></setting>
		<setting group="" module_id="photo" is_hidden="1" type="boolean" var_name="show_info_on_mouseover" phrase_var_name="setting_show_info_on_mouseover" ordering="1" version_id="3.5.0beta1">0</setting>
		<setting group="" module_id="photo" is_hidden="1" type="boolean" var_name="html5_upload_photo" phrase_var_name="setting_html5_upload_photo" ordering="1" version_id="3.7.0rc1">1</setting>
	</settings>
	<blocks>
		<block type_id="0" m_connection="group.view" module_id="photo" component="parent" location="2" is_active="1" ordering="5" disallow_access="" can_move="1">
			<title></title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="photo.view" module_id="photo" component="stream" location="7" is_active="1" ordering="1" disallow_access="" can_move="0">
			<title>Viewing Photo</title>
			<source_code />
			<source_parsed />
		</block>
        <block type_id="0" m_connection="photo.index" module_id="photo" component="sponsored" location="3" is_active="1" ordering="2" disallow_access="" can_move="0">
			<title>Sponsored photo</title>
			<source_code />
			<source_parsed />
		</block>
        <block type_id="0" m_connection="photo.index" module_id="photo" component="featured" location="3" is_active="1" ordering="1" disallow_access="" can_move="0">
			<title>Featured photo</title>
			<source_code />
			<source_parsed />
		</block>
        <block type_id="0" m_connection="photo.index" module_id="photo" component="category" location="1" is_active="1" ordering="1" disallow_access="" can_move="0">
			<title>Categories</title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="photo.album" module_id="photo" component="album-tag" location="3" is_active="1" ordering="2" disallow_access="" can_move="0">
			<title>In This Album</title>
			<source_code />
			<source_parsed />
		</block>
        <block type_id="0" m_connection="profile.index" module_id="photo" component="my-photo" location="3" is_active="1" ordering="1" disallow_access="" can_move="0">
			<title>Photos</title>
			<source_code />
			<source_parsed />
		</block>
	</blocks>
	<hooks>
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_index_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_admincp_index_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_tag_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_frame_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_frame_process_photo" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_frame_process_photos_done" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_frame_process_photos_done_javascript" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_frame_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_album_process_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_album_process_album" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_album_process_conditions" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_album_process_end" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_album_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_view_process_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_view_process_photo" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_view_process_controller" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_view_process_end" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_view_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_edit_image_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_edit_album_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_size_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_download_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_upload_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_upload_process_global" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_upload_process_photos" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_upload_process_display_batch" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_upload_end" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_upload_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_profile_process_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_profile_process_album_conditions" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_profile_process_album_viewer" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_profile_process_photo_album_viewer" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_profile_process_end" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_profile_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="component" module="photo" call_name="photo.component_ajax_getphotosforrating_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="component" module="photo" call_name="photo.component_ajax_getphotosforrating_end" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="component" module="photo" call_name="photo.component_block_menu_album_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="component" module="photo" call_name="photo.component_block_category_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="component" module="photo" call_name="photo.component_block_detail_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="component" module="photo" call_name="photo.component_block_drop_down_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="component" module="photo" call_name="photo.component_block_stat_process" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="component" module="photo" call_name="photo.component_block_stat_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="component" module="photo" call_name="photo.component_block_album_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="component" module="photo" call_name="photo.component_block_menu_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="component" module="photo" call_name="photo.component_block_new_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="component" module="photo" call_name="photo.component_block_filter_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="component" module="photo" call_name="photo.component_block_featured_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="component" module="photo" call_name="photo.component_block_stream_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="component" module="photo" call_name="photo.component_block_warning_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="component" module="photo" call_name="photo.component_block_edit_photo_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.service_photo__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.service_category__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.service_category_process__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.service_rate__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.service_album_album_get_count" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.service_album_album_get_query" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.service_album_album_getall" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.service_album_album_getalbumcount" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.service_album_album_getalbum" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.service_album_album_getnextphoto" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.service_album_album_getpreviousphoto" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.service_album__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.service_album_process__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.service_callback__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="template" module="photo" call_name="photo.template_controller_upload_form_process_hidden" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="template" module="photo" call_name="photo.template_controller_upload_form_actions" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="template" module="photo" call_name="photo.template_controller_upload_form" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="template" module="photo" call_name="photo.template_controller_upload_form_extra" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="template" module="photo" call_name="photo.template_default_controller_view_title" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="template" module="photo" call_name="photo.template_default_controller_view_extra_info" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="template" module="photo" call_name="photo.template_block_album_entry_extra_info" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="template" module="photo" call_name="photo.template_block_stat" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="template" module="photo" call_name="photo.template_default_block_photo_entry_tool" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="template" module="photo" call_name="photo.template_default_block_photo_entry_info" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="template" module="photo" call_name="photo.template_block_menu_album" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.service_tag_tag__call" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.service_tag_process__call" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="photo" hook_type="component" module="photo" call_name="photo.component_block_profile_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="photo" hook_type="component" module="photo" call_name="photo.component_block_parent_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_group_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.service_album_album_getforedit" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.service_process_add__end" added="1260366442" version_id="2.0.0rc11" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_public_album_clean" added="1260366442" version_id="2.0.0rc11" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.service_process_sponsor__end" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.service_callback_getnewsfeedalbum_start" added="1286546859" version_id="2.0.7" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.service_callback_getnewsfeed_start" added="1286546859" version_id="2.0.7" />
		<hook module_id="photo" hook_type="template" module="photo" call_name="photo.template_default_block_photo_entry_hover_end" added="1286546859" version_id="2.0.7" />
		<hook module_id="photo" hook_type="template" module="photo" call_name="photo.template_block_menu" added="1286546859" version_id="2.0.7" />
		<hook module_id="photo" hook_type="component" module="photo" call_name="photo.component_ajax_process_done" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="photo" hook_type="component" module="photo" call_name="photo.component_block_album_tag_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="photo" hook_type="component" module="photo" call_name="photo.component_block_attachment_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="photo" hook_type="component" module="photo" call_name="photo.component_block_share_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_add_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_albums_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.service_album_browse__call" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.service_api__call" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.service_browse__call" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="photo" hook_type="component" module="photo" call_name="photo.component_ajax_ajax_process__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_album__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_converting_clean" added="1335951260" version_id="3.2.0" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_index_brunplugin1" added="1335951260" version_id="3.2.0" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_index_plugin1" added="1335951260" version_id="3.2.0" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_view__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_view__2" added="1335951260" version_id="3.2.0" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.service_album_process_delete__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.component_service_callback_getactivityfeed__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.service_process_delete__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.service_process_approve__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.service_tag_process_add__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.service_tag_process_delete__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="photo" hook_type="template" module="photo" call_name="photo.template_block_share_1" added="1358258443" version_id="3.5.0beta1" />
		<hook module_id="photo" hook_type="template" module="photo" call_name="photo.template_block_share_2" added="1358258443" version_id="3.5.0beta1" />
		<hook module_id="photo" hook_type="template" module="photo" call_name="photo.template_block_share_3" added="1358258443" version_id="3.5.0beta1" />
		<hook module_id="photo" hook_type="template" module="photo" call_name="photo.template_controller_view_view_box_comment_1" added="1358258443" version_id="3.5.0beta1" />
		<hook module_id="photo" hook_type="template" module="photo" call_name="photo.template_controller_view_view_box_comment_2" added="1358258443" version_id="3.5.0beta1" />
		<hook module_id="photo" hook_type="template" module="photo" call_name="photo.template_controller_view_view_box_comment_3" added="1358258443" version_id="3.5.0beta1" />
		<hook module_id="photo" hook_type="service" module="photo" call_name="photo.service_callback_getprofilemenu_1" added="1358258443" version_id="3.5.0beta1" />
		<hook module_id="photo" hook_type="controller" module="photo" call_name="photo.component_controller_profile_1" added="1358258443" version_id="3.5.0beta1" />
	</hooks>
	<components>
		<component module_id="photo" component="category" m_connection="" module="photo" is_controller="0" is_block="1" is_active="1" />
		<component module_id="photo" component="index" m_connection="photo.index" module="photo" is_controller="1" is_block="0" is_active="1" />
		<component module_id="photo" component="featured" m_connection="" module="photo" is_controller="0" is_block="1" is_active="1" />
		<component module_id="photo" component="detail" m_connection="" module="photo" is_controller="0" is_block="1" is_active="1" />
		<component module_id="photo" component="menu" m_connection="" module="photo" is_controller="0" is_block="1" is_active="1" />
		<component module_id="photo" component="view" m_connection="photo.view" module="photo" is_controller="1" is_block="0" is_active="1" />
		<component module_id="photo" component="profile" m_connection="photo.profile" module="photo" is_controller="1" is_block="0" is_active="1" />
		<component module_id="photo" component="stream" m_connection="" module="photo" is_controller="0" is_block="1" is_active="1" />
		<component module_id="photo" component="menu-album" m_connection="" module="photo" is_controller="0" is_block="1" is_active="1" />
		<component module_id="photo" component="album" m_connection="photo.album" module="photo" is_controller="1" is_block="0" is_active="1" />
		<component module_id="photo" component="stat" m_connection="" module="photo" is_controller="0" is_block="1" is_active="1" />
		<component module_id="photo" component="parent" m_connection="" module="photo" is_controller="0" is_block="1" is_active="1" />
		<component module_id="photo" component="profile" m_connection="" module="photo" is_controller="0" is_block="1" is_active="1" />
		<component module_id="photo" component="upload" m_connection="photo.upload" module="photo" is_controller="1" is_block="0" is_active="1" />
		<component module_id="photo" component="sponsored" m_connection="" module="photo" is_controller="0" is_block="1" is_active="1" />
		<component module_id="photo" component="album-tag" m_connection="" module="photo" is_controller="0" is_block="1" is_active="1" />
		<component module_id="photo" component="add" m_connection="photo.add" module="photo" is_controller="1" is_block="0" is_active="1" />
		<component module_id="photo" component="albums" m_connection="photo.albums" module="photo" is_controller="1" is_block="0" is_active="1" />
        <component module_id="photo" component="my-photo" m_connection="" module="photo" is_controller="0" is_block="1" is_active="1" />
	</components>
	<stats>
		<stat module_id="photo" phrase_var="photo.stat_title_3" stat_link="photo" stat_image="photo.png" is_active="1"><![CDATA[$this->database()
->select('COUNT(*)')
->from(Phpfox::getT('photo'))
->where('view_id = 0')
->execute('getSlaveField');]]></stat>
	</stats>
	<feed_share>
		<share module_id="photo" title="{_p var='photo'}" description="{_p var='say_something_about_this_photo'}" block_name="share" no_input="0" is_frame="1" ajax_request="" no_profile="0" icon="photo.png" ordering="1" />
	</feed_share>
    <user_group_settings>
        <setting is_admin_setting="0" module_id="photo" type="boolean" admin="1" user="1" guest="0" staff="1" module="photo" ordering="0">can_create_photo_album</setting>
        <setting is_admin_setting="0" module_id="photo" type="string" admin="null" user="20" guest="0" staff="30" module="photo" ordering="0">max_number_of_albums</setting>
        <setting is_admin_setting="0" module_id="photo" type="integer" admin="1" user="1" guest="0" staff="1" module="photo" ordering="0">points_photo</setting>
        <setting is_admin_setting="0" module_id="photo" type="boolean" admin="1" user="1" guest="0" staff="1" module="photo" ordering="0">can_upload_photos</setting>
        <setting is_admin_setting="0" module_id="photo" type="boolean" admin="1" user="1" guest="0" staff="1" module="photo" ordering="0">can_use_privacy_settings</setting>
        <setting is_admin_setting="0" module_id="photo" type="integer" admin="10" user="10" guest="0" staff="10" module="photo" ordering="0">max_images_per_upload</setting>
        <setting is_admin_setting="0" module_id="photo" type="boolean" admin="1" user="1" guest="0" staff="1" module="photo" ordering="0">can_add_tags_on_photos</setting>
        <setting is_admin_setting="0" module_id="photo" type="boolean" admin="1" user="1" guest="0" staff="1" module="photo" ordering="0">can_add_mature_images</setting>
        <setting is_admin_setting="0" module_id="photo" type="boolean" admin="1" user="1" guest="0" staff="1" module="photo" ordering="0">can_search_for_photos</setting>
        <setting is_admin_setting="0" module_id="photo" type="array" admin="s:22:&quot;array('20','40','60');&quot;;" user="s:22:&quot;array('20','40','60');&quot;;" guest="s:22:&quot;array('20','40','60');&quot;;" staff="s:22:&quot;array('20','40','60');&quot;;" module="photo" ordering="0">total_photos_displays</setting>
        <setting is_admin_setting="0" module_id="photo" type="string" admin="1 min" user="1 min" guest="1 min" staff="1 min" module="photo" ordering="0">refresh_featured_photo</setting>
        <setting is_admin_setting="0" module_id="photo" type="boolean" admin="1" user="1" guest="0" staff="1" module="photo" ordering="0">can_download_user_photos</setting>
        <setting is_admin_setting="0" module_id="photo" type="boolean" admin="1" user="1" guest="0" staff="1" module="photo" ordering="0">can_edit_own_photo_album</setting>
        <setting is_admin_setting="0" module_id="photo" type="boolean" admin="1" user="0" guest="0" staff="1" module="photo" ordering="0">can_edit_other_photo_albums</setting>
        <setting is_admin_setting="0" module_id="photo" type="boolean" admin="1" user="1" guest="0" staff="1" module="photo" ordering="0">can_delete_own_photo</setting>
        <setting is_admin_setting="0" module_id="photo" type="boolean" admin="1" user="0" guest="0" staff="1" module="photo" ordering="0">can_delete_other_photos</setting>
        <setting is_admin_setting="0" module_id="photo" type="boolean" admin="1" user="1" guest="0" staff="1" module="photo" ordering="0">can_edit_own_photo</setting>
        <setting is_admin_setting="0" module_id="photo" type="boolean" admin="1" user="0" guest="0" staff="1" module="photo" ordering="0">can_edit_other_photo</setting>
        <setting is_admin_setting="0" module_id="photo" type="integer" admin="18" user="18" guest="18" staff="18" module="photo" ordering="0">photo_mature_age_limit</setting>
        <setting is_admin_setting="0" module_id="photo" type="boolean" admin="1" user="0" guest="0" staff="1" module="photo" ordering="0">can_edit_photo_categories</setting>
        <setting is_admin_setting="0" module_id="photo" type="boolean" admin="1" user="0" guest="0" staff="1" module="photo" ordering="0">can_add_public_categories</setting>
        <setting is_admin_setting="0" module_id="photo" type="boolean" admin="0" user="0" guest="1" staff="0" module="photo" ordering="0">photo_must_be_approved</setting>
        <setting is_admin_setting="0" module_id="photo" type="boolean" admin="1" user="0" guest="0" staff="1" module="photo" ordering="0">can_approve_photos</setting>
        <setting is_admin_setting="0" module_id="photo" type="boolean" admin="1" user="0" guest="0" staff="1" module="photo" ordering="0">can_feature_photo</setting>
        <setting is_admin_setting="0" module_id="photo" type="boolean" admin="1" user="1" guest="0" staff="1" module="photo" ordering="0">can_delete_own_photo_album</setting>
        <setting is_admin_setting="0" module_id="photo" type="boolean" admin="1" user="0" guest="0" staff="1" module="photo" ordering="0">can_delete_other_photo_albums</setting>
        <setting is_admin_setting="0" module_id="photo" type="boolean" admin="1" user="1" guest="0" staff="1" module="photo" ordering="0">can_tag_own_photo</setting>
        <setting is_admin_setting="0" module_id="photo" type="boolean" admin="1" user="1" guest="0" staff="1" module="photo" ordering="0">can_tag_other_photos</setting>
        <setting is_admin_setting="0" module_id="photo" type="integer" admin="40" user="40" guest="0" staff="40" module="photo" ordering="0">how_many_tags_on_own_photo</setting>
        <setting is_admin_setting="0" module_id="photo" type="integer" admin="4" user="4" guest="0" staff="4" module="photo" ordering="0">how_many_tags_on_other_photo</setting>
        <setting is_admin_setting="0" module_id="photo" type="integer" admin="500" user="500" guest="500" staff="500" module="photo" ordering="0">photo_max_upload_size</setting>
        <setting is_admin_setting="0" module_id="photo" type="boolean" admin="1" user="1" guest="1" staff="1" module="photo" ordering="0">can_view_photos</setting>
        <setting is_admin_setting="0" module_id="photo" type="boolean" admin="1" user="1" guest="0" staff="1" module="photo" ordering="0">can_post_on_photos</setting>
        <setting is_admin_setting="0" module_id="photo" type="boolean" admin="false" user="false" guest="false" staff="false" module="photo" ordering="0">can_sponsor_photo</setting>
        <setting is_admin_setting="0" module_id="photo" type="boolean" admin="false" user="false" guest="false" staff="false" module="photo" ordering="0">can_purchase_sponsor</setting>
        <setting is_admin_setting="0" module_id="photo" type="string" admin="null" user="null" guest="null" staff="null" module="photo" ordering="0">photo_sponsor_price</setting>
        <setting is_admin_setting="0" module_id="photo" type="boolean" admin="true" user="false" guest="false" staff="false" module="photo" ordering="0">auto_publish_sponsored_item</setting>
        <setting is_admin_setting="0" module_id="photo" type="boolean" admin="1" user="1" guest="1" staff="1" module="photo" ordering="0">can_view_photo_albums</setting>
        <setting is_admin_setting="0" module_id="photo" type="integer" admin="0" user="0" guest="0" staff="0" module="photo" ordering="0">flood_control_photos</setting>
        <setting is_admin_setting="0" module_id="photo" type="integer" admin="9" user="9" guest="9" staff="9" module="photo" ordering="0">total_photo_display_profile</setting>
        <setting is_admin_setting="0" module_id="photo" type="integer" admin="1500" user="1200" guest="1200" staff="1500" module="photo" ordering="0">maximum_image_width_keeps_in_server</setting>
    </user_group_settings>
    <tables><![CDATA[a:8:{s:12:"phpfox_photo";a:3:{s:7:"COLUMNS";a:31:{s:8:"photo_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:8:"album_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"view_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"module_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:8:"group_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"type_id";a:4:{i:0;s:6:"TINT:3";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"privacy";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:15:"privacy_comment";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:5:"title";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:14:"parent_user_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:11:"destination";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"server_id";a:4:{i:0;s:6:"TINT:3";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:6:"mature";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"allow_comment";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"allow_rate";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_view";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_comment";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:14:"total_download";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:12:"total_rating";a:4:{i:0;s:9:"DECIMAL:3";i:1;s:4:"0.00";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_vote";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:12:"total_battle";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_like";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_dislike";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:11:"is_featured";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"is_cover";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:14:"allow_download";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"is_sponsor";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"ordering";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:16:"is_profile_photo";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:8:"photo_id";s:4:"KEYS";a:12:{s:8:"album_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:8:"album_id";i:1;s:7:"view_id";}}s:8:"photo_id";a:2:{i:0;s:5:"INDEX";i:1;a:5:{i:0;s:8:"photo_id";i:1;s:8:"album_id";i:2;s:7:"view_id";i:3;s:8:"group_id";i:4;s:7:"privacy";}}s:9:"view_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:4:{i:0;s:7:"view_id";i:1;s:8:"group_id";i:2;s:7:"type_id";i:3;s:7:"privacy";}}s:10:"photo_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:6:{i:0;s:8:"photo_id";i:1;s:8:"album_id";i:2;s:7:"view_id";i:3;s:8:"group_id";i:4;s:7:"type_id";i:5;s:7:"privacy";}}s:7:"view_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"view_id";}s:7:"privacy";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"privacy";i:1;s:10:"allow_rate";}}s:9:"view_id_3";a:2:{i:0;s:5:"INDEX";i:1;a:4:{i:0;s:7:"view_id";i:1;s:8:"group_id";i:2;s:7:"type_id";i:3;s:7:"user_id";}}s:10:"album_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:8:"album_id";i:1;s:7:"view_id";i:2;s:8:"is_cover";}}s:9:"view_id_4";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"view_id";i:1;s:7:"privacy";i:2;s:5:"title";}}s:9:"view_id_5";a:2:{i:0;s:5:"INDEX";i:1;a:4:{i:0;s:7:"view_id";i:1;s:9:"module_id";i:2;s:8:"group_id";i:3;s:7:"privacy";}}s:16:"is_profile_photo";a:2:{i:0;s:5:"INDEX";i:1;s:16:"is_profile_photo";}s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"user_id";}}}s:18:"phpfox_photo_album";a:3:{s:7:"COLUMNS";a:16:{s:8:"album_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"view_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"module_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:8:"group_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"privacy";a:4:{i:0;s:6:"TINT:1";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:15:"privacy_comment";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:4:"name";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:17:"time_stamp_update";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:11:"total_photo";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_comment";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_like";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_dislike";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"profile_id";a:4:{i:0;s:7:"UINT:11";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"cover_id";a:4:{i:0;s:7:"UINT:11";i:1;i:0;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:8:"album_id";s:4:"KEYS";a:6:{s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"user_id";}s:7:"view_id";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"view_id";i:1;s:8:"group_id";i:2;s:7:"user_id";}}s:8:"album_id";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:8:"album_id";i:1;s:7:"view_id";i:2;s:7:"privacy";}}s:9:"view_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"view_id";i:1;s:8:"group_id";i:2;s:7:"privacy";}}s:9:"view_id_3";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"view_id";i:1;s:7:"privacy";i:2;s:7:"user_id";}}s:9:"view_id_4";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"view_id";i:1;s:7:"privacy";i:2;s:11:"total_photo";}}}}s:23:"phpfox_photo_album_info";a:2:{s:7:"COLUMNS";a:2:{s:8:"album_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"description";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:4:"KEYS";a:1:{s:8:"album_id";a:2:{i:0;s:6:"UNIQUE";i:1;s:8:"album_id";}}}s:21:"phpfox_photo_category";a:3:{s:7:"COLUMNS";a:8:{s:11:"category_id";a:4:{i:0;s:4:"UINT";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:9:"parent_id";a:4:{i:0;s:4:"UINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:4:"name";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"name_url";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:4:"used";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_active";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"1";i:2;s:0:"";i:3;s:2:"No";}s:8:"ordering";a:4:{i:0;s:7:"UINT:11";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:11:"category_id";s:4:"KEYS";a:2:{s:9:"parent_id";a:2:{i:0;s:5:"INDEX";i:1;s:9:"parent_id";}s:8:"name_url";a:2:{i:0;s:5:"INDEX";i:1;s:8:"name_url";}}}s:26:"phpfox_photo_category_data";a:2:{s:7:"COLUMNS";a:2:{s:8:"photo_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"category_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:4:"KEYS";a:2:{s:8:"photo_id";a:2:{i:0;s:5:"INDEX";i:1;s:8:"photo_id";}s:11:"category_id";a:2:{i:0;s:5:"INDEX";i:1;s:11:"category_id";}}}s:17:"phpfox_photo_feed";a:2:{s:7:"COLUMNS";a:3:{s:7:"feed_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"photo_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"feed_table";a:4:{i:0;s:9:"VCHAR:255";i:1;s:4:"feed";i:2;s:0:"";i:3;s:2:"NO";}}s:4:"KEYS";a:2:{s:7:"feed_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"feed_id";}s:8:"photo_id";a:2:{i:0;s:5:"INDEX";i:1;s:8:"photo_id";}}}s:17:"phpfox_photo_info";a:2:{s:7:"COLUMNS";a:8:{s:8:"photo_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"file_name";a:4:{i:0;s:9:"VCHAR:100";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"file_size";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"mime_type";a:4:{i:0;s:9:"VCHAR:150";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"extension";a:4:{i:0;s:8:"VCHAR:20";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"description";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:5:"width";a:4:{i:0;s:5:"USINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:6:"height";a:4:{i:0;s:5:"USINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:4:"KEYS";a:1:{s:8:"photo_id";a:2:{i:0;s:6:"UNIQUE";i:1;s:8:"photo_id";}}}s:16:"phpfox_photo_tag";a:3:{s:7:"COLUMNS";a:10:{s:6:"tag_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:8:"photo_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"tag_user_id";a:4:{i:0;s:7:"UINT:11";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"content";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"position_x";a:4:{i:0;s:5:"USINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"position_y";a:4:{i:0;s:5:"USINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:5:"width";a:4:{i:0;s:5:"USINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:6:"height";a:4:{i:0;s:5:"USINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:6:"tag_id";s:4:"KEYS";a:4:{s:8:"photo_id";a:2:{i:0;s:5:"INDEX";i:1;s:8:"photo_id";}s:10:"photo_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:5:{i:0;s:8:"photo_id";i:1;s:10:"position_x";i:2;s:10:"position_y";i:3;s:5:"width";i:4;s:6:"height";}}s:10:"photo_id_3";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:8:"photo_id";i:1;s:11:"tag_user_id";}}s:10:"photo_id_4";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:8:"photo_id";i:1;s:7:"user_id";}}}}}]]></tables>
    
	<install><![CDATA[
		$aPhotoCategories = array(
			'Comedy',
			'Digital Art',
			'Photography',
			'Traditional Art',
			'Film & Animation',
			'Designs & Interfaces',
			'Game Development Art',
			'Artisan Crafts',
			'Customization',
			'Fractal Art',
			'Cartoons & Comics',
			'Contests',
			'Resources & Stock Images',
			'Literature',
			'Fan Art',
			'Anthro',
			'Community Projects',
			'People',
			'Pets & Animals',
			'Science & Technology',
			'Sports'
		);
		sort($aPhotoCategories);
		$iCategoryOrder = 0;
		foreach ($aPhotoCategories as $sCategory)
		{
			$iCategoryOrder++;
			$this->database()->insert(Phpfox::getT('photo_category'), array(
					'name' => $sCategory,
					'ordering' => $iCategoryOrder
				)
			);
		}
	]]></install>
</module>
