<upgrade>
	<settings>
		<setting>
			<group />
			<module_id>photo</module_id>
			<is_hidden>0</is_hidden>
			<type>drop</type>
			<var_name>in_main_photo_section_show</var_name>
			<phrase_var_name>setting_in_main_photo_section_show</phrase_var_name>
			<ordering>1</ordering>
			<version_id>3.5.0beta1</version_id>
			<value><![CDATA[a:2:{s:7:"default";s:6:"photos";s:6:"values";a:2:{i:0;s:6:"photos";i:1;s:7:" albums";}}]]></value>
		</setting>
		<setting>
			<group />
			<module_id>photo</module_id>
			<is_hidden>0</is_hidden>
			<type>boolean</type>
			<var_name>show_info_on_mouseover</var_name>
			<phrase_var_name>setting_show_info_on_mouseover</phrase_var_name>
			<ordering>1</ordering>
			<version_id>3.5.0beta1</version_id>
			<value>0</value>
		</setting>
	</settings>
	<menus>
		<menu>
			<module_id>photo</module_id>
			<parent_var_name />
			<m_connection>photo.albums</m_connection>
			<var_name>menu_photo_upload_a_new_image_0df7df42d810e7978c535292f273fc91</var_name>
			<ordering>129</ordering>
			<url_value>photo.add</url_value>
			<version_id>3.5.0beta1</version_id>
			<disallow_access />
			<module>photo</module>
			<value />
		</menu>
	</menus>
	<hooks>
		<hook>
			<module_id>photo</module_id>
			<hook_type>template</hook_type>
			<module>photo</module>
			<call_name>photo.template_block_share_1</call_name>
			<added>1358258443</added>
			<version_id>3.5.0beta1</version_id>
			<value />
		</hook>
		<hook>
			<module_id>photo</module_id>
			<hook_type>template</hook_type>
			<module>photo</module>
			<call_name>photo.template_block_share_2</call_name>
			<added>1358258443</added>
			<version_id>3.5.0beta1</version_id>
			<value />
		</hook>
		<hook>
			<module_id>photo</module_id>
			<hook_type>template</hook_type>
			<module>photo</module>
			<call_name>photo.template_block_share_3</call_name>
			<added>1358258443</added>
			<version_id>3.5.0beta1</version_id>
			<value />
		</hook>
		<hook>
			<module_id>photo</module_id>
			<hook_type>template</hook_type>
			<module>photo</module>
			<call_name>photo.template_controller_view_view_box_comment_1</call_name>
			<added>1358258443</added>
			<version_id>3.5.0beta1</version_id>
			<value />
		</hook>
		<hook>
			<module_id>photo</module_id>
			<hook_type>template</hook_type>
			<module>photo</module>
			<call_name>photo.template_controller_view_view_box_comment_2</call_name>
			<added>1358258443</added>
			<version_id>3.5.0beta1</version_id>
			<value />
		</hook>
		<hook>
			<module_id>photo</module_id>
			<hook_type>template</hook_type>
			<module>photo</module>
			<call_name>photo.template_controller_view_view_box_comment_3</call_name>
			<added>1358258443</added>
			<version_id>3.5.0beta1</version_id>
			<value />
		</hook>
		<hook>
			<module_id>photo</module_id>
			<hook_type>service</hook_type>
			<module>photo</module>
			<call_name>photo.service_callback_getprofilemenu_1</call_name>
			<added>1358258443</added>
			<version_id>3.5.0beta1</version_id>
			<value />
		</hook>
		<hook>
			<module_id>photo</module_id>
			<hook_type>controller</hook_type>
			<module>photo</module>
			<call_name>photo.component_controller_profile_1</call_name>
			<added>1358258443</added>
			<version_id>3.5.0beta1</version_id>
			<value />
		</hook>
	</hooks>
	<components>
		<component>
			<module_id>photo</module_id>
			<component>albums</component>
			<m_connection>photo.albums</m_connection>
			<module>photo</module>
			<is_controller>1</is_controller>
			<is_block>0</is_block>
			<is_active>1</is_active>
			<value />
		</component>
	</components>
	<sql><![CDATA[a:1:{s:9:"ADD_FIELD";a:2:{s:12:"phpfox_photo";a:1:{s:13:"total_dislike";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:18:"phpfox_photo_album";a:1:{s:13:"total_dislike";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}}}]]></sql>
</upgrade>