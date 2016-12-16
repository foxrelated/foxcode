<module>
	<data>
		<module_id>quiz</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>0</is_menu>
		<menu />
		<phrase_var_name>module_quiz</phrase_var_name>
		<writable><![CDATA[a:1:{i:0;s:14:"file/pic/quiz/";}]]></writable>
	</data>
	<menus>
		<menu module_id="quiz" parent_var_name="" m_connection="main" var_name="menu_quiz" ordering="7" url_value="quiz" version_id="2.0.0alpha1" disallow_access="" module="quiz" mobile_icon="puzzle-piece" />
		<menu module_id="quiz" parent_var_name="" m_connection="profile" var_name="menu_profile_quiz" ordering="48" url_value="profile.quiz" version_id="2.0.0alpha1" disallow_access="" module="quiz" />
		<menu module_id="quiz" parent_var_name="" m_connection="quiz" var_name="menu_add_new_quiz" ordering="49" url_value="quiz.add" version_id="2.0.0alpha1" disallow_access="" module="quiz" />
		<menu module_id="quiz" parent_var_name="" m_connection="mobile" var_name="menu_quiz_quizzes_532c28d5412dd75bf975fb951c740a30" ordering="125" url_value="quiz" version_id="3.1.0rc1" disallow_access="" module="quiz" mobile_icon="small_quizzes.png" />
	</menus>
	<settings>
		<setting group="" module_id="quiz" is_hidden="0" type="integer" var_name="quizzes_to_show" phrase_var_name="setting_quizzes_to_show" ordering="1" version_id="2.0.0alpha1">10</setting>
		<setting group="" module_id="quiz" is_hidden="0" type="string" var_name="quiz_view_time_stamp" phrase_var_name="setting_quiz_view_time_stamp" ordering="1" version_id="2.0.0alpha1">F j, Y</setting>
		<setting group="" module_id="quiz" is_hidden="0" type="integer" var_name="default_answers_count" phrase_var_name="setting_default_answers_count" ordering="1" version_id="2.0.0alpha1">4</setting>
		<setting group="" module_id="quiz" is_hidden="0" type="boolean" var_name="show_percentage_in_track" phrase_var_name="setting_show_percentage_in_track" ordering="1" version_id="2.0.0alpha3">1</setting>
		<setting group="" module_id="quiz" is_hidden="0" type="boolean" var_name="show_percentage_in_results" phrase_var_name="setting_show_percentage_in_results" ordering="1" version_id="2.0.0alpha3">1</setting>
		<setting group="" module_id="quiz" is_hidden="0" type="integer" var_name="takers_to_show" phrase_var_name="setting_takers_to_show" ordering="1" version_id="2.0.0beta2">5</setting>
		<setting group="seo" module_id="quiz" is_hidden="0" type="large_string" var_name="quiz_meta_keywords" phrase_var_name="setting_quiz_meta_keywords" ordering="10" version_id="2.0.0rc1">quiz, test, online, quizzes, tests, free, cool, fun</setting>
		<setting group="seo" module_id="quiz" is_hidden="0" type="large_string" var_name="quiz_meta_description" phrase_var_name="setting_quiz_meta_description" ordering="16" version_id="2.0.0rc1"><![CDATA[Take Free Fun Quizzes & Tests. Cool Online Fun Quiz & Test. Fun Quizzes and Fun Tests by Site Name.]]></setting>
	</settings>
	<blocks>
		<block type_id="0" m_connection="quiz.view" module_id="quiz" component="stat" location="1" is_active="1" ordering="1" disallow_access="" can_move="0">
			<title></title>
			<source_code />
			<source_parsed />
		</block>
	</blocks>
	<hooks>
		<hook module_id="quiz" hook_type="controller" module="quiz" call_name="quiz.component_controller_index_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="quiz" hook_type="controller" module="quiz" call_name="quiz.component_controller_view_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="quiz" hook_type="controller" module="quiz" call_name="quiz.component_controller_add_process_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="quiz" hook_type="controller" module="quiz" call_name="quiz.component_controller_add_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="quiz" hook_type="controller" module="quiz" call_name="quiz.component_controller_profile_process_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="quiz" hook_type="controller" module="quiz" call_name="quiz.component_controller_profile_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="quiz" hook_type="component" module="quiz" call_name="quiz.component_block_stat_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="quiz" hook_type="service" module="quiz" call_name="quiz.service_quiz_get_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="quiz" hook_type="service" module="quiz" call_name="quiz.service_quiz_getquizbyurl_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="quiz" hook_type="service" module="quiz" call_name="quiz.service_quiz__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="quiz" hook_type="service" module="quiz" call_name="quiz.service_process__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="quiz" hook_type="service" module="quiz" call_name="quiz.component_service_callback_addtrack_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="quiz" hook_type="service" module="quiz" call_name="quiz.component_service_callback_addtrack_end" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="quiz" hook_type="service" module="quiz" call_name="quiz.service_callback__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="quiz" hook_type="component" module="quiz" call_name="quiz.component_ajax_deleteimage_start" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="quiz" hook_type="component" module="quiz" call_name="quiz.component_ajax_deleteimage_end" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="quiz" hook_type="service" module="quiz" call_name="quiz.service_process_deleteimage_start" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="quiz" hook_type="service" module="quiz" call_name="quiz.service_process_deleteimage_end" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="quiz" hook_type="service" module="quiz" call_name="quiz.service_process_add__end" added="1260366442" version_id="2.0.0rc11" />
		<hook module_id="quiz" hook_type="service" module="quiz" call_name="quiz.service_callback_getnewsfeed_start" added="1286546859" version_id="2.0.7" />
		<hook module_id="quiz" hook_type="controller" module="quiz" call_name="quiz.component_controller_profile_process_end" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="quiz" hook_type="service" module="quiz" call_name="quiz.service_browse__call" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="quiz" hook_type="service" module="quiz" call_name="quiz.component_service_callback_getactivityfeed__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="quiz" hook_type="service" module="quiz" call_name="quiz.service_process_answerquiz_1" added="1335951260" version_id="3.2.0" />
		<hook module_id="quiz" hook_type="service" module="quiz" call_name="quiz.service_process_approvequiz_1" added="1335951260" version_id="3.2.0" />
		<hook module_id="quiz" hook_type="service" module="quiz" call_name="quiz.service_process_update_1" added="1335951260" version_id="3.2.0" />
		<hook module_id="quiz" hook_type="service" module="quiz" call_name="quiz.service_process_deleteimage_1" added="1335951260" version_id="3.2.0" />
		<hook module_id="quiz" hook_type="service" module="quiz" call_name="quiz.service_process_deletequiz_1" added="1335951260" version_id="3.2.0" />
		<hook module_id="quiz" hook_type="controller" module="quiz" call_name="quiz.component_controller_view_process_end" added="1395674818" version_id="3.7.6rc1" />
	</hooks>
	<components>
		<component module_id="quiz" component="profile" m_connection="quiz.profile" module="quiz" is_controller="1" is_block="0" is_active="1" />
		<component module_id="quiz" component="view" m_connection="quiz.view" module="quiz" is_controller="1" is_block="0" is_active="1" />
		<component module_id="quiz" component="stat" m_connection="" module="quiz" is_controller="0" is_block="1" is_active="1" />
	</components>
	<user_group_settings>
		<setting is_admin_setting="0" module_id="quiz" type="integer" admin="9999" user="10" guest="0" staff="20" module="quiz" ordering="0">max_questions</setting>
		<setting is_admin_setting="0" module_id="quiz" type="integer" admin="1" user="5" guest="9999999" staff="2" module="quiz" ordering="0">min_questions</setting>
		<setting is_admin_setting="0" module_id="quiz" type="integer" admin="25" user="10" guest="1" staff="15" module="quiz" ordering="0">max_answers</setting>
		<setting is_admin_setting="0" module_id="quiz" type="integer" admin="2" user="2" guest="999999" staff="2" module="quiz" ordering="0">min_answers</setting>
		<setting is_admin_setting="0" module_id="quiz" type="boolean" admin="true" user="true" guest="false" staff="true" module="quiz" ordering="0">can_answer_own_quiz</setting>
		<setting is_admin_setting="0" module_id="quiz" type="boolean" admin="true" user="false" guest="false" staff="true" module="quiz" ordering="0">can_approve_quizzes</setting>
		<setting is_admin_setting="0" module_id="quiz" type="boolean" admin="true" user="false" guest="false" staff="true" module="quiz" ordering="0">can_delete_others_quizzes</setting>
		<setting is_admin_setting="0" module_id="quiz" type="boolean" admin="false" user="false" guest="true" staff="false" module="quiz" ordering="0">new_quizzes_need_moderation</setting>
		<setting is_admin_setting="0" module_id="quiz" type="boolean" admin="true" user="true" guest="false" staff="true" module="quiz" ordering="0">can_delete_own_quiz</setting>
		<setting is_admin_setting="0" module_id="quiz" type="boolean" admin="true" user="true" guest="true" staff="true" module="quiz" ordering="0">can_post_comment_on_quiz</setting>
		<setting is_admin_setting="0" module_id="quiz" type="boolean" admin="true" user="false" guest="false" staff="false" module="quiz" ordering="0">can_edit_own_questions</setting>
		<setting is_admin_setting="0" module_id="quiz" type="boolean" admin="true" user="false" guest="false" staff="false" module="quiz" ordering="0">can_edit_others_questions</setting>
		<setting is_admin_setting="0" module_id="quiz" type="boolean" admin="true" user="true" guest="false" staff="true" module="quiz" ordering="0">can_edit_own_title</setting>
		<setting is_admin_setting="0" module_id="quiz" type="boolean" admin="true" user="false" guest="false" staff="false" module="quiz" ordering="0">can_edit_others_title</setting>
		<setting is_admin_setting="0" module_id="quiz" type="integer" admin="10" user="5" guest="0" staff="7" module="quiz" ordering="0">points_quiz</setting>
		<setting is_admin_setting="0" module_id="quiz" type="boolean" admin="true" user="false" guest="false" staff="false" module="quiz" ordering="0">can_view_results_before_answering</setting>
		<setting is_admin_setting="0" module_id="quiz" type="boolean" admin="true" user="true" guest="false" staff="true" module="quiz" ordering="0">can_upload_picture</setting>
		<setting is_admin_setting="0" module_id="quiz" type="boolean" admin="false" user="false" guest="true" staff="false" module="quiz" ordering="0">is_picture_upload_required</setting>
		<setting is_admin_setting="0" module_id="quiz" type="boolean" admin="1" user="1" guest="1" staff="1" module="quiz" ordering="0">can_access_quiz</setting>
		<setting is_admin_setting="0" module_id="quiz" type="boolean" admin="1" user="1" guest="0" staff="1" module="quiz" ordering="0">can_create_quiz</setting>
		<setting is_admin_setting="0" module_id="quiz" type="integer" admin="0" user="0" guest="0" staff="0" module="quiz" ordering="0">flood_control_quiz</setting>
	</user_group_settings>
	<tables><![CDATA[a:4:{s:11:"phpfox_quiz";a:3:{s:7:"COLUMNS";a:14:{s:7:"quiz_id";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"view_id";a:4:{i:0;s:6:"TINT:1";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:5:"title";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"description";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"image_path";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:7:"privacy";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:15:"privacy_comment";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_view";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_comment";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_like";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_dislike";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"server_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:7:"quiz_id";s:4:"KEYS";a:4:{s:9:"view_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"view_id";i:1;s:7:"privacy";}}s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"user_id";}s:9:"view_id_3";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"view_id";i:1;s:7:"user_id";i:2;s:7:"privacy";}}s:9:"view_id_4";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"view_id";i:1;s:5:"title";i:2;s:7:"privacy";}}}}s:18:"phpfox_quiz_answer";a:3:{s:7:"COLUMNS";a:4:{s:9:"answer_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:11:"question_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:6:"answer";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"is_correct";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:9:"answer_id";s:4:"KEYS";a:1:{s:11:"question_id";a:2:{i:0;s:5:"INDEX";i:1;s:11:"question_id";}}}s:20:"phpfox_quiz_question";a:3:{s:7:"COLUMNS";a:3:{s:11:"question_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"quiz_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"question";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:11:"question_id";s:4:"KEYS";a:1:{s:7:"quiz_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"quiz_id";}}}s:18:"phpfox_quiz_result";a:2:{s:7:"COLUMNS";a:5:{s:7:"quiz_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"question_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"answer_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:4:"KEYS";a:1:{s:7:"quiz_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"quiz_id";i:1;s:7:"user_id";}}}}}]]></tables>
</module>