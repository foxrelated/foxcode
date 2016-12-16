<module>
	<data>
		<module_id>announcement</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>1</is_menu>
		<menu><![CDATA[a:1:{s:30:"announcement.admin_menu_manage";a:1:{s:3:"url";a:1:{i:0;s:12:"announcement";}}}]]></menu>
		<phrase_var_name>module_announcement</phrase_var_name>
		<writable />
	</data>
	<blocks>
		<block type_id="0" m_connection="core.index-member" module_id="announcement" component="index" location="7" is_active="1" ordering="10" disallow_access="" can_move="0">
			<title></title>
			<source_code />
			<source_parsed />
		</block>
	</blocks>
	<hooks>
		<hook module_id="announcement" hook_type="controller" module="announcement" call_name="announcement.component_controller_index_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="announcement" hook_type="controller" module="announcement" call_name="announcement.component_controller_admincp_add_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="announcement" hook_type="component" module="announcement" call_name="announcement.component_block_manage_start" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="announcement" hook_type="component" module="announcement" call_name="announcement.component_block_manage_end" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="announcement" hook_type="component" module="announcement" call_name="announcement.component_block_manage_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="announcement" hook_type="service" module="announcement" call_name="announcement.service_process__call" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="announcement" hook_type="service" module="announcement" call_name="announcement.service_announcement__call" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="announcement" hook_type="service" module="announcement" call_name="announcement.service_callback__call" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="announcement" hook_type="component" module="announcement" call_name="announcement.component_ajax_setactive__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="announcement" hook_type="component" module="announcement" call_name="announcement.component_ajax_setactive__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="announcement" hook_type="component" module="announcement" call_name="announcement.component_ajax_hide__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="announcement" hook_type="component" module="announcement" call_name="announcement.component_ajax_hide__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="announcement" hook_type="component" module="announcement" call_name="announcement.component_block_index__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="announcement" hook_type="component" module="announcement" call_name="announcement.component_block_index__end" added="1263387694" version_id="2.0.2" />
	</hooks>
	<components>
		<component module_id="announcement" component="index" m_connection="" module="announcement" is_controller="0" is_block="1" is_active="1" />
		<component module_id="announcement" component="index" m_connection="announcement.index" module="announcement" is_controller="1" is_block="0" is_active="1" />
	</components>
	<user_group_settings>
		<setting is_admin_setting="0" module_id="announcement" type="boolean" admin="true" user="true" guest="false" staff="true" module="announcement" ordering="0">can_close_announcement</setting>
		<setting is_admin_setting="0" module_id="announcement" type="boolean" admin="1" user="1" guest="1" staff="1" module="announcement" ordering="0">can_view_announcements</setting>
	</user_group_settings>
	<tables><![CDATA[a:2:{s:19:"phpfox_announcement";a:3:{s:7:"COLUMNS";a:17:{s:15:"announcement_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:11:"subject_var";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"intro_var";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:11:"content_var";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_active";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"can_be_closed";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"1";i:2;s:0:"";i:3;s:2:"NO";}s:17:"show_in_dashboard";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"1";i:2;s:0:"";i:3;s:2:"NO";}s:10:"start_date";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"location";a:4:{i:0;s:6:"TINT:2";i:1;s:1:"6";i:2;s:0:"";i:3;s:2:"NO";}s:11:"country_iso";a:4:{i:0;s:6:"CHAR:2";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:6:"gender";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"age_from";a:4:{i:0;s:6:"TINT:2";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:6:"age_to";a:4:{i:0;s:6:"TINT:2";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"user_group";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"gmt_offset";a:4:{i:0;s:8:"VCHAR:15";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:11:"PRIMARY_KEY";s:15:"announcement_id";s:4:"KEYS";a:2:{s:9:"is_active";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:9:"is_active";i:1;s:17:"show_in_dashboard";}}s:11:"is_active_2";a:2:{i:0;s:5:"INDEX";i:1;s:9:"is_active";}}}s:24:"phpfox_announcement_hide";a:2:{s:7:"COLUMNS";a:2:{s:15:"announcement_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:4:"KEYS";a:1:{s:15:"announcement_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:15:"announcement_id";i:1;s:7:"user_id";}}}}}]]></tables>
</module>