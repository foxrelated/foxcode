<upgrade>
	<settings>
		<setting>
			<group />
			<module_id>pages</module_id>
			<is_hidden>0</is_hidden>
			<type>integer</type>
			<var_name>admin_in_charge_of_page_claims</var_name>
			<phrase_var_name>setting_admin_in_charge_of_page_claims</phrase_var_name>
			<ordering>1</ordering>
			<version_id>3.4.0beta1</version_id>
			<value>0</value>
		</setting>
		<setting>
			<group />
			<module_id>pages</module_id>
			<is_hidden>0</is_hidden>
			<type>boolean</type>
			<var_name>show_page_admins</var_name>
			<phrase_var_name>setting_show_page_admins</phrase_var_name>
			<ordering>1</ordering>
			<version_id>3.4.0beta1</version_id>
			<value>0</value>
		</setting>
	</settings>
	<user_group_settings>
		<setting>
			<is_admin_setting>0</is_admin_setting>
			<module_id>pages</module_id>
			<type>boolean</type>
			<admin>true</admin>
			<user>false</user>
			<guest>false</guest>
			<staff>false</staff>
			<module>pages</module>
			<ordering>0</ordering>
			<value>can_claim_page</value>
		</setting>
	</user_group_settings>
	<components>
		<component>
			<module_id>pages</module_id>
			<component>admin</component>
			<m_connection />
			<module>pages</module>
			<is_controller>0</is_controller>
			<is_block>1</is_block>
			<is_active>1</is_active>
			<value />
		</component>
		<component>
			<module_id>pages</module_id>
			<component>coverphoto</component>
			<m_connection />
			<module>pages</module>
			<is_controller>0</is_controller>
			<is_block>1</is_block>
			<is_active>1</is_active>
			<value />
		</component>
		<component>
			<module_id>pages</module_id>
			<component>profile</component>
			<m_connection>pages.profile</m_connection>
			<module>pages</module>
			<is_controller>1</is_controller>
			<is_block>0</is_block>
			<is_active>1</is_active>
			<value />
		</component>
	</components>
	<blocks>
		<block>
			<type_id>0</type_id>
			<m_connection>pages.view</m_connection>
			<module_id>pages</module_id>
			<component>admin</component>
			<location>3</location>
			<is_active>1</is_active>
			<ordering>5</ordering>
			<disallow_access />
			<can_move>1</can_move>
			<title>Page Admins</title>
			<source_code />
			<source_parsed />
		</block>
	</blocks>
	<sql><![CDATA[a:2:{s:9:"ADD_FIELD";a:3:{s:12:"phpfox_pages";a:2:{s:14:"cover_photo_id";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:20:"cover_photo_position";a:4:{i:0;s:7:"VCHAR:4";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:17:"phpfox_pages_feed";a:1:{s:11:"time_update";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:19:"phpfox_pages_widget";a:2:{s:10:"image_path";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:15:"image_server_id";a:4:{i:0;s:6:"TINT:3";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}}s:7:"ADD_KEY";a:1:{s:17:"phpfox_pages_feed";a:1:{s:11:"time_update";a:2:{i:0;s:5:"INDEX";i:1;s:11:"time_update";}}}}]]></sql>
</upgrade>