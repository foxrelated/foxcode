<module>
	<data>
		<module_id>poll</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>0</is_menu>
		<menu />
		<phrase_var_name>module_poll</phrase_var_name>
		<writable><![CDATA[a:1:{i:0;s:14:"file/pic/poll/";}]]></writable>
	</data>
	<menus>
		<menu module_id="poll" parent_var_name="" m_connection="main" var_name="menu_poll" ordering="6" url_value="poll" version_id="2.0.0alpha1" disallow_access="" module="poll" mobile_icon="bar-chart" />
		<menu module_id="poll" parent_var_name="" m_connection="poll.index" var_name="menu_add_new_poll" ordering="39" url_value="poll.add" version_id="2.0.0alpha1" disallow_access="" module="poll" />
		<menu module_id="poll" parent_var_name="" m_connection="profile" var_name="menu_polls" ordering="41" url_value="profile.poll" version_id="2.0.0alpha1" disallow_access="" module="poll" />
		<menu module_id="poll" parent_var_name="" m_connection="mobile" var_name="menu_poll_polls_532c28d5412dd75bf975fb951c740a30" ordering="123" url_value="poll" version_id="3.1.0rc1" disallow_access="" module="poll" mobile_icon="small_polls.png" />
	</menus>
	<settings>
		<setting group="" module_id="poll" is_hidden="0" type="boolean" var_name="is_image_required" phrase_var_name="setting_is_image_required" ordering="1" version_id="2.0.0alpha1">0</setting>
		<setting group="time_stamps" module_id="poll" is_hidden="0" type="string" var_name="poll_view_time_stamp" phrase_var_name="setting_poll_view_time_stamp" ordering="1" version_id="2.0.0alpha1">F j, Y</setting>
		<setting group="seo" module_id="poll" is_hidden="0" type="large_string" var_name="poll_meta_description" phrase_var_name="setting_poll_meta_description" ordering="9" version_id="2.0.0rc1">New polls on Site Name daily.</setting>
		<setting group="seo" module_id="poll" is_hidden="0" type="large_string" var_name="poll_meta_keywords" phrase_var_name="setting_poll_meta_keywords" ordering="15" version_id="2.0.0rc1">poll, polls</setting>
	</settings>
	<hooks>
		<hook module_id="poll" hook_type="controller" module="poll" call_name="poll.component_controller_index_process_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="controller" module="poll" call_name="poll.component_controller_index_process_end" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="controller" module="poll" call_name="poll.component_controller_index_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="controller" module="poll" call_name="poll.component_controller_design_process_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="controller" module="poll" call_name="poll.component_controller_design_process_end" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="controller" module="poll" call_name="poll.component_controller_add_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="controller" module="poll" call_name="poll.component_controller_view_process_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="controller" module="poll" call_name="poll.component_controller_view_process_end" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="controller" module="poll" call_name="poll.component_controller_view_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="controller" module="poll" call_name="poll.component_controller_add_process_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="controller" module="poll" call_name="poll.component_controller_add_process_end" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="controller" module="poll" call_name="poll.component_controller_profile_process_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="controller" module="poll" call_name="poll.component_controller_profile_process_end" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="controller" module="poll" call_name="poll.component_controller_profile_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="component" module="poll" call_name="poll.component_ajax_addvote_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="component" module="poll" call_name="poll.component_ajax_addvote_end" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="component" module="poll" call_name="poll.component_ajax_moderatepoll_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="component" module="poll" call_name="poll.component_ajax_moderatepoll_end" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="component" module="poll" call_name="poll.component_block_new_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="component" module="poll" call_name="poll.component_block_vote_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="component" module="poll" call_name="poll.component_block_vote_end" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_process_add_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_process_add_end" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_process_moderate_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_process_moderate_end" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_process_hasuservoted_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_process_hasuservoted_end" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_process__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_poll_getpollbyid_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_poll_getpollbyid_end" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_poll_getpollbyurl_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_poll_getpollbyurl_end" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_poll_getpollbyuser_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_poll_getpolls_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_poll_getpolls_end" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_poll_getanswers_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_poll_getanswers_end" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_poll__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.component_service_callback_getprofilelink_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.component_service_callback_getajaxcommentvar_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.component_service_callback_getcommentitem_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.component_service_callback_addcomment_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.component_service_callback_addcomment_end" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.component_service_callback_deletecomment_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.component_service_callback_deletecomment_end" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.component_service_callback_processcommentmoderation_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.component_service_callback_processcommentmoderation_end" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.component_service_callback_getcommentnewsfeed_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.component_service_callback_getcommentnewsfeed_end" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.component_service_callback_addtrack_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.component_service_callback_addtrack_end" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.component_service_callback_getlatesttrackusers_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.component_service_callback_getlatesttrackusers_end" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.component_service_callback_getcommentitemname_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_callback__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="template" module="poll" call_name="poll.template_controller_add_end" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="poll" hook_type="component" module="poll" call_name="poll.component_ajax_deleteimage_start" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="poll" hook_type="component" module="poll" call_name="poll.component_ajax_deleteimage_end" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="poll" hook_type="component" module="poll" call_name="poll.component_block_votes_start" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="poll" hook_type="component" module="poll" call_name="poll.component_block_votes_end" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_process_deleteimage_start" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_process_deleteimage_end" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_poll_getVotedAnswersByUser_start" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_poll_getVotedAnswersByUser_end" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_poll_getnew_start" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_poll_getnew_end" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_process_add_ainsert" added="1286546859" version_id="2.0.7" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_process_add_insert_answer" added="1286546859" version_id="2.0.7" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_process_moderate_selected" added="1286546859" version_id="2.0.7" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_process_moderate_updated_activity" added="1286546859" version_id="2.0.7" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_callback_getnewsfeed_start" added="1286546859" version_id="2.0.7" />
		<hook module_id="poll" hook_type="component" module="poll" call_name="poll.component_block_share_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_browse__call" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.component_service_callback_getactivityfeed__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_process_moderatepoll__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_process_deleteimage_1" added="1335951260" version_id="3.2.0" />
		<hook module_id="poll" hook_type="service" module="poll" call_name="poll.service_process_addvote_1" added="1335951260" version_id="3.2.0" />
	</hooks>
	<components>
		<component module_id="poll" component="index" m_connection="poll.index" module="poll" is_controller="1" is_block="0" is_active="1" />
		<component module_id="poll" component="design" m_connection="poll.design" module="poll" is_controller="1" is_block="0" is_active="1" />
		<component module_id="poll" component="profile" m_connection="poll.profile" module="poll" is_controller="1" is_block="0" is_active="1" />
	</components>
	<stats>
		<stat module_id="poll" phrase_var="poll.stat_title_4" stat_link="poll" stat_image="poll.png" is_active="1"><![CDATA[$this->database()
->select('COUNT(*)')
->from(Phpfox::getT('poll'))
->where('view_id = 0')
->execute('getSlaveField');]]></stat>
	</stats>
	<user_group_settings>
		<setting is_admin_setting="0" module_id="poll" type="boolean" admin="1" user="1" guest="0" staff="1" module="poll" ordering="0">poll_can_upload_image</setting>
		<setting is_admin_setting="0" module_id="poll" type="boolean" admin="true" user="false" guest="false" staff="true" module="poll" ordering="0">view_poll_results_before_vote</setting>
		<setting is_admin_setting="0" module_id="poll" type="boolean" admin="true" user="false" guest="false" staff="true" module="poll" ordering="0">poll_can_change_own_vote</setting>
		<setting is_admin_setting="0" module_id="poll" type="integer" admin="0" user="1" guest="9999999" staff="1" module="poll" ordering="0">poll_flood_control</setting>
		<setting is_admin_setting="0" module_id="poll" type="boolean" admin="false" user="false" guest="true" staff="false" module="poll" ordering="0">poll_requires_admin_moderation</setting>
		<setting is_admin_setting="0" module_id="poll" type="boolean" admin="1" user="0" guest="0" staff="1" module="poll" ordering="0">poll_can_moderate_polls</setting>
		<setting is_admin_setting="0" module_id="poll" type="boolean" admin="false" user="false" guest="true" staff="false" module="poll" ordering="0">poll_require_captcha_challenge</setting>
		<setting is_admin_setting="0" module_id="poll" type="boolean" admin="true" user="true" guest="false" staff="true" module="poll" ordering="0">poll_can_edit_own_polls</setting>
		<setting is_admin_setting="0" module_id="poll" type="boolean" admin="true" user="false" guest="false" staff="true" module="poll" ordering="0">poll_can_edit_others_polls</setting>
		<setting is_admin_setting="0" module_id="poll" type="boolean" admin="true" user="true" guest="false" staff="true" module="poll" ordering="0">poll_can_delete_own_polls</setting>
		<setting is_admin_setting="0" module_id="poll" type="boolean" admin="true" user="false" guest="false" staff="true" module="poll" ordering="0">poll_can_delete_others_polls</setting>
		<setting is_admin_setting="0" module_id="poll" type="boolean" admin="true" user="true" guest="true" staff="true" module="poll" ordering="0">can_post_comment_on_poll</setting>
		<setting is_admin_setting="0" module_id="poll" type="boolean" admin="true" user="true" guest="false" staff="true" module="poll" ordering="0">view_poll_results_after_vote</setting>
		<setting is_admin_setting="0" module_id="poll" type="integer" admin="20" user="6" guest="0" staff="10" module="poll" ordering="0">maximum_answers_count</setting>
		<setting is_admin_setting="0" module_id="poll" type="boolean" admin="true" user="true" guest="false" staff="true" module="poll" ordering="0">can_vote_in_own_poll</setting>
		<setting is_admin_setting="0" module_id="poll" type="integer" admin="5" user="1" guest="0" staff="3" module="poll" ordering="0">points_poll</setting>
		<setting is_admin_setting="0" module_id="poll" type="boolean" admin="1" user="1" guest="0" staff="1" module="poll" ordering="0">can_view_user_poll_results_own_poll</setting>
		<setting is_admin_setting="0" module_id="poll" type="boolean" admin="1" user="1" guest="" staff="1" module="poll" ordering="0">can_view_user_poll_results_other_poll</setting>
		<setting is_admin_setting="0" module_id="poll" type="boolean" admin="true" user="true" guest="false" staff="true" module="poll" ordering="0">can_edit_title</setting>
		<setting is_admin_setting="0" module_id="poll" type="boolean" admin="true" user="false" guest="false" staff="false" module="poll" ordering="0">can_edit_question</setting>
		<setting is_admin_setting="0" module_id="poll" type="boolean" admin="true" user="true" guest="false" staff="true" module="poll" ordering="0">highlight_answer_voted_by_viewer</setting>
		<setting is_admin_setting="0" module_id="poll" type="boolean" admin="1" user="1" guest="1" staff="1" module="poll" ordering="0">can_access_polls</setting>
		<setting is_admin_setting="0" module_id="poll" type="boolean" admin="1" user="1" guest="0" staff="1" module="poll" ordering="0">can_create_poll</setting>
		<setting is_admin_setting="0" module_id="poll" type="boolean" admin="1" user="0" guest="0" staff="1" module="poll" ordering="0">can_view_hidden_poll_votes</setting>
	</user_group_settings>
	<tables><![CDATA[a:4:{s:11:"phpfox_poll";a:3:{s:7:"COLUMNS";a:17:{s:7:"poll_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:9:"module_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:7:"item_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"view_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"question";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"privacy";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:15:"privacy_comment";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"image_path";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_comment";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_like";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_dislike";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_view";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"server_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"randomize";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"1";i:2;s:0:"";i:3;s:2:"NO";}s:9:"hide_vote";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:7:"poll_id";s:4:"KEYS";a:3:{s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"user_id";}s:7:"item_id";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"item_id";i:1;s:7:"view_id";i:2;s:7:"privacy";}}s:9:"item_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:4:{i:0;s:7:"item_id";i:1;s:7:"user_id";i:2;s:7:"view_id";i:3;s:7:"privacy";}}}}s:18:"phpfox_poll_answer";a:3:{s:7:"COLUMNS";a:5:{s:9:"answer_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"poll_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:6:"answer";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"total_votes";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"ordering";a:4:{i:0;s:6:"TINT:3";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:9:"answer_id";s:4:"KEYS";a:2:{s:7:"poll_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"poll_id";}s:9:"answer_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:9:"answer_id";i:1;s:7:"poll_id";}}}}s:18:"phpfox_poll_result";a:2:{s:7:"COLUMNS";a:4:{s:7:"poll_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"answer_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:4:"KEYS";a:3:{s:7:"poll_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"poll_id";}s:10:"user_voted";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"poll_id";i:1;s:7:"user_id";}}s:9:"answer_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:9:"answer_id";i:1;s:7:"user_id";}}}}s:18:"phpfox_poll_design";a:2:{s:7:"COLUMNS";a:4:{s:7:"poll_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:10:"background";a:4:{i:0;s:7:"VCHAR:6";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"percentage";a:4:{i:0;s:7:"VCHAR:6";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:6:"border";a:4:{i:0;s:7:"VCHAR:6";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:11:"PRIMARY_KEY";s:7:"poll_id";}}]]></tables>
</module>
