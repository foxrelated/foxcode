<module>
	<data>
		<module_id>forum</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>1</is_menu>
		<menu><![CDATA[a:2:{s:23:"forum.admin_menu_manage";a:1:{s:3:"url";a:1:{i:0;s:5:"forum";}}s:20:"forum.admin_menu_add";a:1:{s:3:"url";a:2:{i:0;s:5:"forum";i:1;s:3:"add";}}}]]></menu>
		<phrase_var_name>module_forum</phrase_var_name>
		<writable />
	</data>
	<menus>
		<menu module_id="forum" parent_var_name="" m_connection="main" var_name="menu_forum" ordering="5" url_value="forum" version_id="2.0.0alpha1" disallow_access="" module="forum" mobile_icon="comments" />
		<menu module_id="forum" parent_var_name="" m_connection="mobile" var_name="menu_forum_forum_532c28d5412dd75bf975fb951c740a30" ordering="117" url_value="forum" version_id="3.1.0rc1" disallow_access="" module="forum" mobile_icon="small_forum.png" />
	</menus>
	<settings>
		<setting group="" module_id="forum" is_hidden="0" type="integer" var_name="keep_active_posts" phrase_var_name="setting_keep_active_posts" ordering="1" version_id="2.0.0alpha1">60</setting>
		<setting group="time_stamps" module_id="forum" is_hidden="0" type="string" var_name="forum_time_stamp" phrase_var_name="setting_forum_time_stamp" ordering="1" version_id="2.0.0alpha1">M j, g:i a</setting>
		<setting group="time_stamps" module_id="forum" is_hidden="0" type="string" var_name="forum_user_time_stamp" phrase_var_name="setting_forum_user_time_stamp" ordering="2" version_id="2.0.0alpha1">F j, Y</setting>
		<setting group="" module_id="forum" is_hidden="0" type="integer" var_name="total_posts_per_thread" phrase_var_name="setting_total_posts_per_thread" ordering="1" version_id="2.0.0alpha1">15</setting>
		<setting group="" module_id="forum" is_hidden="0" type="integer" var_name="total_forum_tags_display" phrase_var_name="setting_total_forum_tags_display" ordering="1" version_id="2.0.0alpha1">100</setting>
		<setting group="" module_id="forum" is_hidden="0" type="boolean" var_name="rss_feed_on_each_forum" phrase_var_name="setting_rss_feed_on_each_forum" ordering="1" version_id="2.0.0beta5">1</setting>
		<setting group="" module_id="forum" is_hidden="0" type="boolean" var_name="enable_rss_on_threads" phrase_var_name="setting_enable_rss_on_threads" ordering="1" version_id="2.0.0beta5">1</setting>
		<setting group="" module_id="forum" is_hidden="0" type="boolean" var_name="forum_database_tracking" phrase_var_name="setting_forum_database_tracking" ordering="1" version_id="2.0.5dev2">1</setting>
		<setting group="" module_id="forum" is_hidden="0" type="integer" var_name="total_recent_posts_display" phrase_var_name="setting_total_recent_posts_display" ordering="5" version_id="4.3.0">20</setting>
		<setting group="" module_id="forum" is_hidden="0" type="integer" var_name="total_recent_discussions_display" phrase_var_name="setting_total_recent_discussions_display" ordering="4" version_id="4.3.0">20</setting>
		<setting group="" module_id="forum" is_hidden="0" type="boolean" var_name="enable_thanks_on_posts" phrase_var_name="setting_enable_thanks_on_posts" ordering="6" version_id="4.3.0">0</setting>
	</settings>
	<blocks>
		<block type_id="0" m_connection="forum.index" module_id="forum" component="recent" location="3" is_active="1" ordering="1" disallow_access="" can_move="0">
			<title>Recent Threads</title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="forum.forum" module_id="forum" component="recent" location="3" is_active="1" ordering="1" disallow_access="" can_move="0">
			<title>Recent Posts</title>
			<source_code />
			<source_parsed />
		</block>
	</blocks>
	<hooks>
		<hook module_id="forum" hook_type="controller" module="forum" call_name="forum.component_controller_search_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="forum" hook_type="controller" module="forum" call_name="forum.component_controller_index_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="forum" hook_type="controller" module="forum" call_name="forum.component_controller_admincp_index_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="forum" hook_type="controller" module="forum" call_name="forum.component_controller_admincp_add_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="forum" hook_type="controller" module="forum" call_name="forum.component_controller_tag_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="forum" hook_type="controller" module="forum" call_name="forum.component_controller_forum_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="forum" hook_type="controller" module="forum" call_name="forum.component_controller_post_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="forum" hook_type="controller" module="forum" call_name="forum.component_controller_thread_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="forum" hook_type="component" module="forum" call_name="forum.component_block_admincp_moderator_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="forum" hook_type="component" module="forum" call_name="forum.component_block_move_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="forum" hook_type="component" module="forum" call_name="forum.component_block_merge_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="forum" hook_type="component" module="forum" call_name="forum.component_block_copy_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_moderate_process__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_moderate_moderate__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_post_process__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_post_post__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_thread_process__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_thread_thread__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_forum__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_process__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_callback__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="forum" hook_type="controller" module="forum" call_name="forum.component_controller_read_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="forum" hook_type="controller" module="forum" call_name="forum.component_controller_group_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="forum" hook_type="controller" module="forum" call_name="forum.component_controller_rss_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_subscribe_process__call" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_subscribe_subscribe__call" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_process_add__end" added="1260366442" version_id="2.0.0rc11" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_thread_process_add__start" added="1260366442" version_id="2.0.0rc11" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_thread_process_add__end" added="1260366442" version_id="2.0.0rc11" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_thread_process_sponsor__end" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="forum" hook_type="component" module="forum" call_name="forum.component_ajax_get_text" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="forum" hook_type="template" module="forum" call_name="forum.template_controller_thread_form_quick_reply" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_moderate_moderate_getperms" added="1276177474" version_id="2.0.5" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_forum_getaccess" added="1286546859" version_id="2.0.7" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_forum_hasaccess" added="1286546859" version_id="2.0.7" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_callback_getnewsfeed_start" added="1286546859" version_id="2.0.7" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_thread_get_query" added="1286546859" version_id="2.0.7" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_thread_getthread_query" added="1286546859" version_id="2.0.7" />
		<hook module_id="forum" hook_type="controller" module="forum" call_name="forum.component_controller_admincp_permission_clean" added="1286546859" version_id="2.0.7" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_callback_updatecounterlist" added="1288281378" version_id="2.0.7" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_post_getpost" added="1288281378" version_id="2.0.7" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_post_process_thank" added="1288281378" version_id="2.0.7" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_post_process_deletethanks" added="1288281378" version_id="2.0.7" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_forum_hasaccess_check" added="1290072896" version_id="2.0.7" />
		<hook module_id="forum" hook_type="component" module="forum" call_name="forum.component_ajax_reply" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="forum" hook_type="template" module="forum" call_name="forum.template_controller_post_ajax_onsubmit" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="forum" hook_type="template" module="forum" call_name="forum.template_block_post_1" added="1323345637" version_id="3.0.0" />
		<hook module_id="forum" hook_type="template" module="forum" call_name="forum.template_block_post_2" added="1323345637" version_id="3.0.0" />
		<hook module_id="forum" hook_type="template" module="forum" call_name="forum.template_controller_post_1" added="1323345637" version_id="3.0.0" />
		<hook module_id="forum" hook_type="template" module="forum" call_name="forum.template_controller_post_2" added="1323345637" version_id="3.0.0" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.component_service_callback_getactivityfeed__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_post_process_add_1" added="1335951260" version_id="3.2.0" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_post_process_approve__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_process_approve__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_process_delete__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_process_close__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="forum" hook_type="service" module="forum" call_name="forum.service_thread_process_approve__1" added="1335951260" version_id="3.2.0" />
	</hooks>
	<components>
		<component module_id="forum" component="forum.index" m_connection="forum.index" module="forum" is_controller="1" is_block="0" is_active="1" />
		<component module_id="forum" component="forum" m_connection="forum.forum" module="forum" is_controller="1" is_block="0" is_active="1" />
		<component module_id="forum" component="recent" m_connection="" module="forum" is_controller="0" is_block="1" is_active="1" />
	</components>

    <rss_group>
		<group module_id="forum" group_id="3" name_var="forum.rss_group_name_3" is_active="1" />
	</rss_group>
	<rss>
		<feed module_id="forum" group_id="3" title_var="forum.rss_title_4" description_var="forum.rss_description_4" feed_link="forum" is_active="1" is_site_wide="1">
			<php_group_code></php_group_code>
			<php_view_code><![CDATA[$aRows = Forum_Service_Thread_Thread::instance()->getForRss(Phpfox::getParam('rss.total_rss_display'));]]></php_view_code>
		</feed>
	</rss>
	<user_group_settings>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="1" user="0" guest="0" staff="1" module="forum" ordering="0">can_stick_thread</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="1" user="0" guest="0" staff="1" module="forum" ordering="0">can_close_a_thread</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="1" user="0" guest="0" staff="1" module="forum" ordering="0">can_post_announcement</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="1" user="1" guest="0" staff="1" module="forum" ordering="0">can_delete_own_post</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="1" user="0" guest="0" staff="1" module="forum" ordering="0">can_delete_other_posts</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="1" user="0" guest="0" staff="1" module="forum" ordering="0">can_add_new_forum</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="1" user="0" guest="0" staff="1" module="forum" ordering="0">can_edit_forum</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="1" user="0" guest="0" staff="1" module="forum" ordering="0">can_manage_forum_moderators</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="1" user="0" guest="0" staff="0" module="forum" ordering="0">can_delete_forum</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="1" user="1" guest="0" staff="1" module="forum" ordering="0">can_edit_own_post</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="1" user="0" guest="0" staff="1" module="forum" ordering="0">can_edit_other_posts</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="1" user="0" guest="0" staff="1" module="forum" ordering="0">can_move_forum_thread</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="1" user="0" guest="0" staff="1" module="forum" ordering="0">can_copy_forum_thread</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="1" user="0" guest="0" staff="1" module="forum" ordering="0">can_merge_forum_threads</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="1" user="1" guest="0" staff="1" module="forum" ordering="0">can_reply_to_own_thread</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="1" user="1" guest="0" staff="1" module="forum" ordering="0">can_reply_on_other_threads</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="1" user="1" guest="0" staff="1" module="forum" ordering="0">can_add_new_thread</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="1" user="1" guest="0" staff="1" module="forum" ordering="0">can_add_forum_attachments</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="1" user="1" guest="0" staff="1" module="forum" ordering="0">can_add_tags_on_threads</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="0" user="0" guest="1" staff="0" module="forum" ordering="0">enable_captcha_on_posting</setting>
		<setting is_admin_setting="0" module_id="forum" type="integer" admin="0" user="1" guest="50" staff="0" module="forum" ordering="0">forum_thread_flood_control</setting>
		<setting is_admin_setting="0" module_id="forum" type="integer" admin="0" user="1" guest="50" staff="0" module="forum" ordering="0">forum_post_flood_control</setting>
		<setting is_admin_setting="0" module_id="forum" type="integer" admin="1" user="1" guest="0" staff="1" module="forum" ordering="0">points_forum</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="1" user="1" guest="1" staff="1" module="forum" ordering="0">can_view_forum</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="false" user="false" guest="false" staff="false" module="forum" ordering="0">can_sponsor_thread</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="false" user="false" guest="false" staff="false" module="forum" ordering="0">can_purchase_sponsor</setting>
		<setting is_admin_setting="0" module_id="forum" type="string" admin="null" user="null" guest="null" staff="null" module="forum" ordering="0">forum_thread_sponsor_price</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="true" user="false" guest="false" staff="false" module="forum" ordering="0">auto_publish_sponsored_thread</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="0" user="0" guest="0" staff="0" module="forum" ordering="0">approve_forum_thread</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="1" user="0" guest="0" staff="1" module="forum" ordering="0">can_approve_forum_thread</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="0" user="0" guest="0" staff="0" module="forum" ordering="0">approve_forum_post</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="1" user="0" guest="0" staff="1" module="forum" ordering="0">can_approve_forum_post</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="1" user="1" guest="0" staff="1" module="forum" ordering="0">can_thank_on_forum_posts</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="1" user="0" guest="0" staff="1" module="forum" ordering="0">can_delete_thanks_by_other_users</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="1" user="0" guest="0" staff="1" module="forum" ordering="0">can_manage_forum_permissions</setting>
		<setting is_admin_setting="0" module_id="forum" type="boolean" admin="1" user="1" guest="0" staff="1" module="forum" ordering="0">can_add_poll_to_forum_thread</setting>
	</user_group_settings>
	<tables><![CDATA[a:10:{s:12:"phpfox_forum";a:3:{s:7:"COLUMNS";a:14:{s:8:"forum_id";a:4:{i:0;s:5:"USINT";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:9:"parent_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"view_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:11:"is_category";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:4:"name";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"name_url";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"description";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"is_closed";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"thread_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"post_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:12:"last_user_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_post";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:12:"total_thread";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"ordering";a:4:{i:0;s:5:"USINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:8:"forum_id";s:4:"KEYS";a:3:{s:7:"view_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"view_id";}s:7:"post_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"post_id";}s:9:"thread_id";a:2:{i:0;s:5:"INDEX";i:1;s:9:"thread_id";}}}s:19:"phpfox_forum_access";a:2:{s:7:"COLUMNS";a:4:{s:8:"forum_id";a:4:{i:0;s:5:"USINT";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:13:"user_group_id";a:4:{i:0;s:6:"TINT:3";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"var_name";a:4:{i:0;s:9:"VCHAR:150";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"var_value";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:4:"KEYS";a:2:{s:8:"forum_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:8:"forum_id";i:1;s:13:"user_group_id";}}s:13:"user_group_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:13:"user_group_id";i:1;s:8:"var_name";}}}}s:25:"phpfox_forum_announcement";a:3:{s:7:"COLUMNS";a:3:{s:15:"announcement_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:8:"forum_id";a:4:{i:0;s:5:"USINT";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"thread_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:15:"announcement_id";s:4:"KEYS";a:2:{s:8:"forum_id";a:2:{i:0;s:5:"INDEX";i:1;s:8:"forum_id";}s:9:"thread_id";a:2:{i:0;s:5:"INDEX";i:1;s:9:"thread_id";}}}s:22:"phpfox_forum_moderator";a:3:{s:7:"COLUMNS";a:3:{s:12:"moderator_id";a:4:{i:0;s:5:"USINT";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:8:"forum_id";a:4:{i:0;s:5:"USINT";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:12:"moderator_id";s:4:"KEYS";a:1:{s:8:"forum_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:8:"forum_id";i:1;s:7:"user_id";}}}}s:29:"phpfox_forum_moderator_access";a:2:{s:7:"COLUMNS";a:2:{s:12:"moderator_id";a:4:{i:0;s:5:"USINT";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"var_name";a:4:{i:0;s:9:"VCHAR:150";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:4:"KEYS";a:1:{s:12:"moderator_id";a:2:{i:0;s:5:"INDEX";i:1;s:12:"moderator_id";}}}s:17:"phpfox_forum_post";a:3:{s:7:"COLUMNS";a:12:{s:7:"post_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:9:"thread_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"view_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:5:"title";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:16:"total_attachment";a:4:{i:0;s:7:"UINT:11";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"update_time";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:11:"update_user";a:4:{i:0;s:9:"VCHAR:100";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"total_like";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_dislike";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"cache_name";a:4:{i:0;s:9:"VCHAR:250";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:11:"PRIMARY_KEY";s:7:"post_id";s:4:"KEYS";a:3:{s:9:"thread_id";a:2:{i:0;s:5:"INDEX";i:1;s:9:"thread_id";}s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"user_id";}s:7:"view_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"view_id";}}}s:22:"phpfox_forum_post_text";a:2:{s:7:"COLUMNS";a:3:{s:7:"post_id";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:4:"text";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:11:"text_parsed";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:4:"KEYS";a:1:{s:7:"post_id";a:2:{i:0;s:6:"UNIQUE";i:1;s:7:"post_id";}}}s:22:"phpfox_forum_subscribe";a:3:{s:7:"COLUMNS";a:3:{s:12:"subscribe_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:9:"thread_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:12:"subscribe_id";s:4:"KEYS";a:1:{s:9:"thread_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:9:"thread_id";i:1;s:7:"user_id";}}}}s:18:"phpfox_forum_thank";a:3:{s:7:"COLUMNS";a:4:{s:8:"thank_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"post_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:8:"thank_id";s:4:"KEYS";a:1:{s:7:"post_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"post_id";i:1;s:7:"user_id";}}}}s:19:"phpfox_forum_thread";a:3:{s:7:"COLUMNS";a:19:{s:9:"thread_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:8:"forum_id";a:4:{i:0;s:5:"USINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"group_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"poll_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"view_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"start_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:15:"is_announcement";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_closed";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:5:"title";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"title_url";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"time_update";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"order_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"post_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:12:"last_user_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_post";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_view";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"cache_name";a:4:{i:0;s:9:"VCHAR:250";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:11:"PRIMARY_KEY";s:9:"thread_id";s:4:"KEYS";a:9:{s:8:"forum_id";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:8:"forum_id";i:1;s:8:"group_id";i:2;s:7:"view_id";}}s:8:"group_id";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:8:"group_id";i:1;s:7:"view_id";i:2;s:9:"title_url";}}s:10:"forum_id_2";a:2:{i:0;s:5:"INDEX";i:1;s:8:"forum_id";}s:10:"group_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:8:"group_id";i:1;s:7:"view_id";i:2;s:15:"is_announcement";}}s:10:"group_id_3";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:8:"group_id";i:1;s:9:"title_url";}}s:7:"view_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"view_id";}s:9:"thread_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:9:"thread_id";i:1;s:8:"group_id";}}s:8:"start_id";a:2:{i:0;s:5:"INDEX";i:1;s:8:"start_id";}s:9:"view_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"view_id";i:1;s:5:"title";}}}}}]]></tables>
	<install><![CDATA[
		$aForumCategories = array(
			'Discussions' => array(
				'url' => 'discussions',
				'sub_forums' => array(
					'General' => 'general',
					'Movies' => 'movies',
					'Music' => 'music'
				)
			),
			'Computers & Technology' => array(
				'url' => 'computers-technology',
				'sub_forums' => array(
					'Computers' => 'computers',
					'Electronics' => 'electronics',
					'Gadgets' => 'gadgets',
					'General' => 'general'
				)
			)
		);

		$iCategoryOrder = 0;
		foreach ($aForumCategories as $sCategory => $aForum)
		{
			$iCategoryOrder++;
			$iForumId = $this->database()->insert(Phpfox::getT('forum'), array(
					'is_category' => 1,
					'name' => $sCategory,
					'name_url' => $aForum['url'],
					'ordering' => $iCategoryOrder
				)
			);

			$iForumOrder = 0;
			foreach ($aForum['sub_forums'] as $sName => $sUrl)
			{
				$iForumOrder++;
				$this->database()->insert(Phpfox::getT('forum'), array(
						'parent_id' => $iForumId,
						'name' => $sName,
						'name_url' => $sUrl,
						'ordering' => $iForumOrder
					)
				);
			}

		}
	]]></install>
</module>
