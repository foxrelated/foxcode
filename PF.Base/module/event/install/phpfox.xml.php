<module>
	<data>
		<module_id>event</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>1</is_menu>
		<menu><![CDATA[a:2:{s:29:"event.admin_menu_add_category";a:1:{s:3:"url";a:2:{i:0;s:5:"event";i:1;s:3:"add";}}s:34:"event.admin_menu_manage_categories";a:1:{s:3:"url";a:1:{i:0;s:5:"event";}}}]]></menu>
		<phrase_var_name>module_event</phrase_var_name>
		<writable><![CDATA[a:1:{i:0;s:15:"file/pic/event/";}]]></writable>
	</data>
	<menus>
		<menu module_id="event" parent_var_name="" m_connection="main" var_name="menu_event" ordering="8" url_value="event" version_id="2.0.0alpha1" disallow_access="" module="event" mobile_icon="calendar" />
		<menu module_id="event" parent_var_name="" m_connection="event.index" var_name="menu_create_new_event" ordering="62" url_value="event.add" version_id="2.0.0alpha4" disallow_access="" module="event" />
		<menu module_id="event" parent_var_name="" m_connection="mobile" var_name="menu_event_events_532c28d5412dd75bf975fb951c740a30" ordering="115" url_value="event" version_id="3.1.0rc1" disallow_access="" module="event" mobile_icon="small_events.png" />
	</menus>
	<settings>
		<setting group="time_stamps" module_id="event" is_hidden="0" type="string" var_name="event_view_time_stamp_profile" phrase_var_name="setting_event_view_time_stamp_profile" ordering="1" version_id="2.0.0alpha4">F j, Y</setting>
		<setting group="time_stamps" module_id="event" is_hidden="0" type="string" var_name="event_browse_time_stamp" phrase_var_name="setting_event_browse_time_stamp" ordering="2" version_id="2.0.0alpha4">l, F j</setting>
		<setting group="time_stamps" module_id="event" is_hidden="0" type="string" var_name="event_basic_information_time" phrase_var_name="setting_event_basic_information_time" ordering="3" version_id="2.0.5">l, F j, Y g:i a</setting>
		<setting group="time_stamps" module_id="event" is_hidden="0" type="string" var_name="event_basic_information_time_short" phrase_var_name="setting_event_basic_information_time_short" ordering="4" version_id="2.0.5">g:i a</setting>
		<setting group="" module_id="event" is_hidden="0" type="boolean" var_name="cache_events_per_user" phrase_var_name="setting_cache_events_per_user" ordering="1" version_id="3.6.0rc1">0</setting>
		<setting group="" module_id="event" is_hidden="0" type="integer" var_name="cache_upcoming_events_info" phrase_var_name="setting_cache_upcoming_events_info" ordering="2" version_id="3.6.0rc1">8</setting>
	</settings>
	<blocks>
		<block type_id="0" m_connection="event.view" module_id="event" component="info" location="4" is_active="1" ordering="3" disallow_access="" can_move="0">
			<title>Event Information</title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="event.view" module_id="event" component="rsvp" location="1" is_active="1" ordering="4" disallow_access="" can_move="0">
			<title></title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="event.index" module_id="event" component="category" location="1" is_active="1" ordering="4" disallow_access="" can_move="0">
			<title></title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="event.index" module_id="event" component="sponsored" location="3" is_active="1" ordering="3" disallow_access="" can_move="0">
			<title></title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="event.view" module_id="event" component="attending" location="1" is_active="1" ordering="6" disallow_access="" can_move="0">
			<title>Attending</title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="event.index" module_id="event" component="invite" location="3" is_active="1" ordering="2" disallow_access="" can_move="0">
			<title>Event Invites</title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="event.index" module_id="event" component="featured" location="3" is_active="1" ordering="5" disallow_access="" can_move="0">
			<title>Featured Events</title>
			<source_code />
			<source_parsed />
		</block>
	</blocks>
	<hooks>
		<hook module_id="event" hook_type="controller" module="event" call_name="event.component_controller_index_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="event" hook_type="controller" module="event" call_name="event.component_controller_admincp_index_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="event" hook_type="controller" module="event" call_name="event.component_controller_admincp_add_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="event" hook_type="controller" module="event" call_name="event.component_controller_view_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="event" hook_type="controller" module="event" call_name="event.component_controller_add_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="event" hook_type="controller" module="event" call_name="event.component_controller_group_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="event" hook_type="component" module="event" call_name="event.component_block_category_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="event" hook_type="component" module="event" call_name="event.component_block_menu_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="event" hook_type="component" module="event" call_name="event.component_block_profile_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="event" hook_type="component" module="event" call_name="event.component_block_rsvp_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="event" hook_type="component" module="event" call_name="event.component_block_list_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="event" hook_type="service" module="event" call_name="event.service_category_category__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="event" hook_type="service" module="event" call_name="event.service_category_process__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="event" hook_type="service" module="event" call_name="event.service_process__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="event" hook_type="service" module="event" call_name="event.service_event__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="event" hook_type="service" module="event" call_name="event.service_callback__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="event" hook_type="service" module="event" call_name="event.service_browse__call" added="1242299564" version_id="2.0.0beta2" />
		<hook module_id="event" hook_type="service" module="event" call_name="event.service_process_add__end" added="1260366442" version_id="2.0.0rc11" />
		<hook module_id="event" hook_type="service" module="event" call_name="event.service_process_sponsor__end" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="event" hook_type="component" module="event" call_name="event.component_block_sponsored_clean" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="event" hook_type="template" module="event" call_name="event.template_default_controller_view_extra_info" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="event" hook_type="service" module="event" call_name="event.service_callback_getnewsfeed_start" added="1286546859" version_id="2.0.7" />
		<hook module_id="event" hook_type="service" module="event" call_name="event.service_process_delete__start" added="1298455495" version_id="2.0.8" />
		<hook module_id="event" hook_type="service" module="event" call_name="event.service_process_delete__pre_unlink" added="1298455495" version_id="2.0.8" />
		<hook module_id="event" hook_type="service" module="event" call_name="event.service_process_delete__pre_space_update" added="1298455495" version_id="2.0.8" />
		<hook module_id="event" hook_type="service" module="event" call_name="event.service_process_delete__pre_deletes" added="1298455495" version_id="2.0.8" />
		<hook module_id="event" hook_type="service" module="event" call_name="event.service_process_delete__end" added="1298455495" version_id="2.0.8" />
		<hook module_id="event" hook_type="service" module="event" call_name="event.service_process_add__start" added="1298455786" version_id="2.0.8" />
		<hook module_id="event" hook_type="service" module="event" call_name="event.service_process_update__start" added="1298455786" version_id="2.0.8" />
		<hook module_id="event" hook_type="service" module="event" call_name="event.service_process_massemail__start" added="1298455786" version_id="2.0.8" />
		<hook module_id="event" hook_type="service" module="event" call_name="event.service_process_massemail__end" added="1298455786" version_id="2.0.8" />
		<hook module_id="event" hook_type="component" module="event" call_name="event.component_block_attending_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="event" hook_type="component" module="event" call_name="event.component_block_browse_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="event" hook_type="component" module="event" call_name="event.component_block_featured_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="event" hook_type="component" module="event" call_name="event.component_block_invite_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="event" hook_type="component" module="event" call_name="event.component_block_rsvp_entry_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="event" hook_type="controller" module="event" call_name="event.component_controller_profile_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="event" hook_type="controller" module="event" call_name="event.component_controller_index_set_filter_menu_1" added="1335951260" version_id="3.2.0" />
		<hook module_id="event" hook_type="service" module="event" call_name="event.component_service_callback_getactivityfeed__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="event" hook_type="service" module="event" call_name="event.service_process_update__end" added="1335951260" version_id="3.2.0" />
		<hook module_id="event" hook_type="service" module="event" call_name="event.service_process_deleteimage__end" added="1335951260" version_id="3.2.0" />
		<hook module_id="event" hook_type="service" module="event" call_name="event.service_process_approve__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="event" hook_type="component" module="event" call_name="event.component_block_mini_clean" added="1372931660" version_id="3.6.0" />
		<hook module_id="event" hook_type="controller" module="event" call_name="event.component_controller_view_process_end" added="1395674818" version_id="3.7.6rc1" />
	</hooks>
	<components>
		<component module_id="event" component="menu" m_connection="" module="event" is_controller="0" is_block="1" is_active="1" />
		<component module_id="event" component="view" m_connection="event.view" module="event" is_controller="1" is_block="0" is_active="1" />
		<component module_id="event" component="index" m_connection="event.index" module="event" is_controller="1" is_block="0" is_active="1" />
		<component module_id="event" component="rsvp" m_connection="" module="event" is_controller="0" is_block="1" is_active="1" />
		<component module_id="event" component="category" m_connection="" module="event" is_controller="0" is_block="1" is_active="1" />
		<component module_id="event" component="profile" m_connection="" module="event" is_controller="0" is_block="1" is_active="1" />
		<component module_id="event" component="info" m_connection="" module="event" is_controller="0" is_block="1" is_active="1" />
		<component module_id="event" component="sponsored" m_connection="" module="event" is_controller="0" is_block="1" is_active="1" />
		<component module_id="event" component="list" m_connection="" module="event" is_controller="0" is_block="1" is_active="1" />
		<component module_id="event" component="attending" m_connection="" module="event" is_controller="0" is_block="1" is_active="1" />
		<component module_id="event" component="invite" m_connection="" module="event" is_controller="0" is_block="1" is_active="1" />
		<component module_id="event" component="profile" m_connection="event.profile" module="event" is_controller="1" is_block="0" is_active="1" />
		<component module_id="event" component="featured" m_connection="" module="event" is_controller="0" is_block="1" is_active="1" />
	</components>
	<rss_group>
		<group module_id="event" group_id="2" name_var="event.rss_group_name_2" is_active="1" />
	</rss_group>
	<rss>
		<feed module_id="event" group_id="2" title_var="event.rss_title_3" description_var="event.rss_description_3" feed_link="event" is_active="1" is_site_wide="1">
			<php_group_code></php_group_code>
			<php_view_code><![CDATA[$aRows = Event_Service_Event::instance()->getForRssFeed();]]></php_view_code>
		</feed>
	</rss>
	<user_group_settings>
		<setting is_admin_setting="0" module_id="event" type="boolean" admin="1" user="1" guest="0" staff="1" module="event" ordering="0">can_edit_own_event</setting>
		<setting is_admin_setting="0" module_id="event" type="boolean" admin="1" user="0" guest="0" staff="1" module="event" ordering="0">can_edit_other_event</setting>
		<setting is_admin_setting="0" module_id="event" type="boolean" admin="1" user="1" guest="0" staff="1" module="event" ordering="0">can_delete_own_event</setting>
		<setting is_admin_setting="0" module_id="event" type="boolean" admin="1" user="0" guest="0" staff="1" module="event" ordering="0">can_delete_other_event</setting>
		<setting is_admin_setting="0" module_id="event" type="integer" admin="500" user="500" guest="500" staff="500" module="event" ordering="0">max_upload_size_event</setting>
        <setting is_admin_setting="0" module_id="event" type="boolean" admin="1" user="1" guest="0" staff="1" module="event" ordering="0">can_post_comment_on_event</setting>
		<setting is_admin_setting="0" module_id="event" type="boolean" admin="1" user="0" guest="0" staff="1" module="event" ordering="0">can_view_pirvate_events</setting>
		<setting is_admin_setting="0" module_id="event" type="boolean" admin="1" user="0" guest="0" staff="1" module="event" ordering="0">can_approve_events</setting>
		<setting is_admin_setting="0" module_id="event" type="boolean" admin="1" user="0" guest="0" staff="1" module="event" ordering="0">can_feature_events</setting>
		<setting is_admin_setting="0" module_id="event" type="boolean" admin="0" user="0" guest="0" staff="0" module="event" ordering="0">event_must_be_approved</setting>
		<setting is_admin_setting="0" module_id="event" type="integer" admin="0" user="60" guest="60" staff="0" module="event" ordering="0">total_mass_emails_per_hour</setting>
		<setting is_admin_setting="0" module_id="event" type="boolean" admin="1" user="1" guest="0" staff="1" module="event" ordering="0">can_mass_mail_own_members</setting>
		<setting is_admin_setting="0" module_id="event" type="boolean" admin="1" user="1" guest="1" staff="1" module="event" ordering="0">can_access_event</setting>
		<setting is_admin_setting="0" module_id="event" type="boolean" admin="1" user="1" guest="0" staff="1" module="event" ordering="0">can_create_event</setting>
		<setting is_admin_setting="0" module_id="event" type="boolean" admin="false" user="false" guest="false" staff="false" module="event" ordering="0">can_sponsor_event</setting>
		<setting is_admin_setting="0" module_id="event" type="boolean" admin="false" user="false" guest="false" staff="false" module="event" ordering="0">can_purchase_sponsor</setting>
		<setting is_admin_setting="0" module_id="event" type="string" admin="null" user="null" guest="null" staff="null" module="event" ordering="0">event_sponsor_price</setting>
		<setting is_admin_setting="0" module_id="event" type="boolean" admin="true" user="false" guest="false" staff="false" module="event" ordering="0">auto_publish_sponsored_item</setting>
		<setting is_admin_setting="0" module_id="event" type="integer" admin="0" user="0" guest="0" staff="0" module="event" ordering="0">flood_control_events</setting>
		<setting is_admin_setting="0" module_id="event" type="integer" admin="1" user="1" guest="0" staff="1" module="event" ordering="0">points_event</setting>
	</user_group_settings>
	<tables><![CDATA[a:7:{s:12:"phpfox_event";a:3:{s:7:"COLUMNS";a:28:{s:8:"event_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"view_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:11:"is_featured";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"is_sponsor";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"privacy";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:15:"privacy_comment";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"module_id";a:4:{i:0;s:8:"VCHAR:75";i:1;s:5:"event";i:2;s:0:"";i:3;s:2:"NO";}s:7:"item_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:5:"title";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"location";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:11:"country_iso";a:4:{i:0;s:6:"CHAR:2";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:16:"country_child_id";a:4:{i:0;s:4:"UINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:11:"postal_code";a:4:{i:0;s:8:"VCHAR:20";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:4:"city";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"start_time";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"end_time";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"image_path";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"server_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_comment";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_like";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_dislike";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"mass_email";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:16:"start_gmt_offset";a:4:{i:0;s:8:"VCHAR:15";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:14:"end_gmt_offset";a:4:{i:0;s:8:"VCHAR:15";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:4:"gmap";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:7:"address";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:11:"PRIMARY_KEY";s:8:"event_id";s:4:"KEYS";a:6:{s:9:"module_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:9:"module_id";i:1;s:7:"item_id";}}s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"user_id";}s:7:"view_id";a:2:{i:0;s:5:"INDEX";i:1;a:4:{i:0;s:7:"view_id";i:1;s:7:"privacy";i:2;s:7:"item_id";i:3;s:10:"start_time";}}s:9:"view_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:5:{i:0;s:7:"view_id";i:1;s:7:"privacy";i:2;s:7:"item_id";i:3;s:7:"user_id";i:4;s:10:"start_time";}}s:9:"view_id_5";a:2:{i:0;s:5:"INDEX";i:1;a:5:{i:0;s:7:"view_id";i:1;s:7:"privacy";i:2;s:9:"module_id";i:3;s:7:"item_id";i:4;s:10:"start_time";}}s:10:"start_time";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:10:"start_time";i:1;s:7:"view_id";}}}}s:21:"phpfox_event_category";a:3:{s:7:"COLUMNS";a:8:{s:11:"category_id";a:4:{i:0;s:4:"UINT";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:9:"parent_id";a:4:{i:0;s:4:"UINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_active";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:4:"name";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"name_url";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:4:"used";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"ordering";a:4:{i:0;s:7:"UINT:11";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:11:"category_id";s:4:"KEYS";a:2:{s:9:"parent_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:9:"parent_id";i:1;s:9:"is_active";}}s:9:"is_active";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:9:"is_active";i:1;s:8:"name_url";}}}}s:26:"phpfox_event_category_data";a:2:{s:7:"COLUMNS";a:2:{s:8:"event_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"category_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:4:"KEYS";a:2:{s:11:"category_id";a:2:{i:0;s:5:"INDEX";i:1;s:11:"category_id";}s:8:"event_id";a:2:{i:0;s:5:"INDEX";i:1;s:8:"event_id";}}}s:17:"phpfox_event_feed";a:3:{s:7:"COLUMNS";a:11:{s:7:"feed_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"privacy";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:15:"privacy_comment";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"type_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:14:"parent_user_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"item_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:14:"parent_feed_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:16:"parent_module_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:11:"time_update";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:7:"feed_id";s:4:"KEYS";a:2:{s:14:"parent_user_id";a:2:{i:0;s:5:"INDEX";i:1;s:14:"parent_user_id";}s:11:"time_update";a:2:{i:0;s:5:"INDEX";i:1;s:11:"time_update";}}}s:25:"phpfox_event_feed_comment";a:3:{s:7:"COLUMNS";a:9:{s:15:"feed_comment_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:14:"parent_user_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"privacy";a:4:{i:0;s:6:"TINT:3";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:15:"privacy_comment";a:4:{i:0;s:6:"TINT:3";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"content";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_comment";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_like";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:15:"feed_comment_id";s:4:"KEYS";a:1:{s:14:"parent_user_id";a:2:{i:0;s:5:"INDEX";i:1;s:14:"parent_user_id";}}}s:19:"phpfox_event_invite";a:3:{s:7:"COLUMNS";a:8:{s:9:"invite_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:8:"event_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"type_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"rsvp_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:15:"invited_user_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"invited_email";a:4:{i:0;s:9:"VCHAR:100";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:9:"invite_id";s:4:"KEYS";a:3:{s:8:"event_id";a:2:{i:0;s:5:"INDEX";i:1;s:8:"event_id";}s:10:"event_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:8:"event_id";i:1;s:15:"invited_user_id";}}s:15:"invited_user_id";a:2:{i:0;s:5:"INDEX";i:1;s:15:"invited_user_id";}}}s:17:"phpfox_event_text";a:2:{s:7:"COLUMNS";a:3:{s:8:"event_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"description";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:18:"description_parsed";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:4:"KEYS";a:1:{s:8:"event_id";a:2:{i:0;s:5:"INDEX";i:1;s:8:"event_id";}}}}]]></tables>
	<install><![CDATA[
		$aCategories = array(
			'Arts',
			'Party',
			'Comedy',			
			'Sports',			
			'Music',
			'TV',
			'Movies',
			'Other'
		);		
		
		$iCategoryOrder = 0;
		foreach ($aCategories as $sCategory)
		{
			$iCategoryOrder++;
			$iCategoryId = $this->database()->insert(Phpfox::getT('event_category'), array(					
					'name' => $sCategory,					
					'is_active' => 1,
					'ordering' => $iCategoryOrder			
				)
			);			
		}
	]]></install>
</module>