<module>
	<data>
		<module_id>rss</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>1</is_menu>
		<menu><![CDATA[a:4:{s:27:"rss.admin_menu_manage_feeds";a:1:{s:3:"url";a:1:{i:0;s:3:"rss";}}s:27:"rss.admin_menu_add_new_feed";a:1:{s:3:"url";a:2:{i:0;s:3:"rss";i:1;s:3:"add";}}s:28:"rss.admin_menu_manage_groups";a:1:{s:3:"url";a:2:{i:0;s:3:"rss";i:1;s:5:"group";}}s:28:"rss.admin_menu_add_new_group";a:1:{s:3:"url";a:3:{i:0;s:3:"rss";i:1;s:5:"group";i:2;s:3:"add";}}}]]></menu>
		<phrase_var_name>module_rss</phrase_var_name>
		<writable />
	</data>
	<settings>
		<setting group="" module_id="rss" is_hidden="0" type="integer" var_name="total_rss_display" phrase_var_name="setting_total_rss_display" ordering="1" version_id="2.0.0beta5">15</setting>
		<setting group="" module_id="rss" is_hidden="0" type="boolean" var_name="display_rss_count_on_profile" phrase_var_name="setting_display_rss_count_on_profile" ordering="1" version_id="2.0.0beta5">1</setting>
	</settings>
	<hooks>
		<hook module_id="rss" hook_type="controller" module="rss" call_name="rss.component_controller_index_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="rss" hook_type="service" module="rss" call_name="rss.service_rss__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="rss" hook_type="service" module="rss" call_name="rss.service_process__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="rss" hook_type="service" module="rss" call_name="rss.service_callback__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="rss" hook_type="component" module="rss" call_name="rss.component_block_info_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="rss" hook_type="component" module="rss" call_name="rss.component_block_log_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="rss" hook_type="controller" module="rss" call_name="rss.component_controller_admincp_index_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="rss" hook_type="controller" module="rss" call_name="rss.component_controller_admincp_log_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="rss" hook_type="controller" module="rss" call_name="rss.component_controller_admincp_group_index_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="rss" hook_type="controller" module="rss" call_name="rss.component_controller_admincp_group_add_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="rss" hook_type="controller" module="rss" call_name="rss.component_controller_admincp_add_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="rss" hook_type="controller" module="rss" call_name="rss.component_controller_log_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="rss" hook_type="controller" module="rss" call_name="rss.component_controller_profile_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="rss" hook_type="service" module="rss" call_name="rss.service_log_log__call" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="rss" hook_type="service" module="rss" call_name="rss.service_group_group__call" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="rss" hook_type="service" module="rss" call_name="rss.service_group_process__call" added="1258389334" version_id="2.0.0rc8" />
	</hooks>
	<components>
		<component module_id="rss" component="index" m_connection="rss.index" module="rss" is_controller="1" is_block="0" is_active="1" />
		<component module_id="rss" component="info" m_connection="" module="rss" is_controller="0" is_block="1" is_active="1" />
	</components>
	<tables><![CDATA[a:4:{s:10:"phpfox_rss";a:3:{s:7:"COLUMNS";a:13:{s:7:"feed_id";a:4:{i:0;s:5:"USINT";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:9:"module_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"product_id";a:4:{i:0;s:8:"VCHAR:25";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"group_id";a:4:{i:0;s:5:"USINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"title_var";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:15:"description_var";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"feed_link";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:14:"php_group_code";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:13:"php_view_code";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_active";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:12:"is_site_wide";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:16:"total_subscribed";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"ordering";a:4:{i:0;s:4:"UINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:7:"feed_id";s:4:"KEYS";a:3:{s:8:"group_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:8:"group_id";i:1;s:9:"is_active";}}s:7:"feed_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"feed_id";i:1;s:9:"is_active";}}s:9:"is_active";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:9:"is_active";i:1;s:12:"is_site_wide";}}}}s:16:"phpfox_rss_group";a:3:{s:7:"COLUMNS";a:6:{s:8:"group_id";a:4:{i:0;s:5:"USINT";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:9:"module_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"product_id";a:4:{i:0;s:8:"VCHAR:25";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"name_var";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"is_active";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"ordering";a:4:{i:0;s:5:"USINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:8:"group_id";s:4:"KEYS";a:1:{s:9:"is_active";a:2:{i:0;s:5:"INDEX";i:1;s:9:"is_active";}}}s:14:"phpfox_rss_log";a:3:{s:7:"COLUMNS";a:6:{s:6:"log_id";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"feed_id";a:4:{i:0;s:5:"USINT";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"id_hash";a:4:{i:0;s:7:"CHAR:32";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"ip_address";a:4:{i:0;s:8:"VCHAR:15";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"user_agent";a:4:{i:0;s:9:"VCHAR:100";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:6:"log_id";s:4:"KEYS";a:1:{s:7:"feed_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"feed_id";i:1;s:7:"id_hash";}}}}s:19:"phpfox_rss_log_user";a:3:{s:7:"COLUMNS";a:6:{s:6:"log_id";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"id_hash";a:4:{i:0;s:7:"CHAR:32";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"ip_address";a:4:{i:0;s:8:"VCHAR:15";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"user_agent";a:4:{i:0;s:9:"VCHAR:100";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:6:"log_id";s:4:"KEYS";a:1:{s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"user_id";i:1;s:7:"id_hash";}}}}}]]></tables>
</module>