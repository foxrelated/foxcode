<upgrade>
	<user_group_settings>
		<setting>
			<is_admin_setting>0</is_admin_setting>
			<module_id>pages</module_id>
			<type>boolean</type>
			<admin>1</admin>
			<user>1</user>
			<guest>0</guest>
			<staff>1</staff>
			<module>pages</module>
			<ordering>0</ordering>
			<value>can_add_new_pages</value>
		</setting>
	</user_group_settings>
	<phpfox_update_blocks>
		<block>
			<type_id>0</type_id>
			<m_connection>profile.info</m_connection>
			<module_id>pages</module_id>
			<component>profile</component>
			<location>2</location>
			<is_active>1</is_active>
			<ordering>2</ordering>
			<disallow_access />
			<can_move>1</can_move>
			<title>Pages</title>
			<source_code />
			<source_parsed />
		</block>
	</phpfox_update_blocks>
	<sql><![CDATA[a:1:{s:7:"ADD_KEY";a:1:{s:12:"phpfox_pages";a:1:{s:7:"page_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"page_id";i:1;s:7:"view_id";}}}}}]]></sql>
</upgrade>