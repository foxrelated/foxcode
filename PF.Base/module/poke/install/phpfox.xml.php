<module>
	<data>
		<module_id>poke</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>0</is_menu>
		<menu />
		<phrase_var_name>module_poke</phrase_var_name>
		<writable />
	</data>
	<settings>
		<setting group="" module_id="poke" is_hidden="0" type="boolean" var_name="add_to_feed" phrase_var_name="setting_add_to_feed" ordering="1" version_id="3.0.0beta1">0</setting>
	</settings>
	<blocks>
		<block type_id="0" m_connection="core.index-member" module_id="poke" component="display" location="3" is_active="1" ordering="6" disallow_access="a:1:{i:0;s:1:&quot;3&quot;;}" can_move="1">
			<title><![CDATA[{_p var=&#039;pokes&#039;}]]></title>
			<source_code />
			<source_parsed />
		</block>
	</blocks>
	<hooks>
		<hook module_id="poke" hook_type="service" module="poke" call_name="poke.service_callback__call" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="poke" hook_type="service" module="poke" call_name="poke.service_poke__call" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="poke" hook_type="service" module="poke" call_name="poke.service_process__call" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="poke" hook_type="service" module="poke" call_name="poke.component_service_callback_getactivityfeed__1" added="1335951260" version_id="3.2.0" />
	</hooks>
	<components>
		<component module_id="poke" component="display" m_connection="" module="poke" is_controller="0" is_block="1" is_active="1" />
	</components>
	<user_group_settings>
		<setting is_admin_setting="0" module_id="poke" type="boolean" admin="true" user="true" guest="false" staff="true" module="poke" ordering="0">can_poke</setting>
		<setting is_admin_setting="0" module_id="poke" type="boolean" admin="false" user="false" guest="true" staff="false" module="poke" ordering="0">can_only_poke_friends</setting>
	</user_group_settings>
	<tables><![CDATA[a:1:{s:16:"phpfox_poke_data";a:3:{s:7:"COLUMNS";a:6:{s:7:"poke_id";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"to_user_id";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"status_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"1";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_like";a:4:{i:0;s:7:"UINT:11";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_comment";a:4:{i:0;s:7:"UINT:11";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:7:"poke_id";s:4:"KEYS";a:3:{s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"user_id";i:1;s:10:"to_user_id";}}s:9:"user_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"user_id";i:1;s:10:"to_user_id";i:2;s:9:"status_id";}}s:10:"to_user_id";a:2:{i:0;s:5:"INDEX";i:1;s:10:"to_user_id";}}}}]]></tables>
</module>