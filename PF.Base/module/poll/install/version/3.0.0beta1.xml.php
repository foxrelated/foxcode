<upgrade>
	<phpfox_update_settings>
		<setting>
			<group>search_engine_optimization</group>
			<module_id>poll</module_id>
			<is_hidden>0</is_hidden>
			<type>large_string</type>
			<var_name>poll_meta_description</var_name>
			<phrase_var_name>setting_poll_meta_description</phrase_var_name>
			<ordering>9</ordering>
			<version_id>2.0.0rc1</version_id>
			<value>New polls on Site Name daily.</value>
		</setting>
	</phpfox_update_settings>
	<phpfox_update_menus>
		<menu>
			<module_id>poll</module_id>
			<parent_var_name />
			<m_connection>main</m_connection>
			<var_name>menu_poll</var_name>
			<ordering>24</ordering>
			<url_value>poll</url_value>
			<version_id>2.0.0alpha1</version_id>
			<disallow_access />
			<module>poll</module>
			<value />
		</menu>
	</phpfox_update_menus>
	<sql><![CDATA[a:3:{s:9:"ADD_FIELD";a:1:{s:11:"phpfox_poll";a:2:{s:15:"privacy_comment";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_like";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}}s:7:"ADD_KEY";a:2:{s:11:"phpfox_poll";a:3:{s:7:"item_id";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"item_id";i:1;s:7:"view_id";i:2;s:7:"privacy";}}s:9:"item_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:4:{i:0;s:7:"item_id";i:1;s:7:"user_id";i:2;s:7:"view_id";i:3;s:7:"privacy";}}s:9:"item_id_3";a:2:{i:0;s:5:"INDEX";i:1;a:4:{i:0;s:7:"item_id";i:1;s:7:"view_id";i:2;s:8:"question";i:3;s:7:"privacy";}}}s:18:"phpfox_poll_answer";a:1:{s:9:"answer_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:9:"answer_id";i:1;s:7:"poll_id";}}}}s:10:"REMOVE_KEY";a:1:{s:11:"phpfox_poll";a:3:{i:0;a:2:{i:0;s:5:"INDEX";i:1;s:12:"question_url";}i:1;a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:9:"module_id";i:1;s:7:"view_id";i:2;s:7:"privacy";}}i:2;a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:9:"module_id";i:1;s:7:"user_id";i:2;s:7:"view_id";}}}}}]]></sql>
</upgrade>