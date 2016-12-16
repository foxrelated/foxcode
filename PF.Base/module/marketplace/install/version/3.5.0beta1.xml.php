<upgrade>
	<settings>
		<setting>
			<group />
			<module_id>marketplace</module_id>
			<is_hidden>0</is_hidden>
			<type>integer</type>
			<var_name>days_to_expire_listing</var_name>
			<phrase_var_name>setting_days_to_expire_listing</phrase_var_name>
			<ordering>1</ordering>
			<version_id>3.5.0beta1</version_id>
			<value>0</value>
		</setting>
		<setting>
			<group />
			<module_id>marketplace</module_id>
			<is_hidden>0</is_hidden>
			<type>integer</type>
			<var_name>days_to_notify_expire</var_name>
			<phrase_var_name>setting_days_to_notify_expire</phrase_var_name>
			<ordering>1</ordering>
			<version_id>3.5.0beta1</version_id>
			<value>0</value>
		</setting>
	</settings>
	<user_group_settings>
		<setting>
			<is_admin_setting>0</is_admin_setting>
			<module_id>marketplace</module_id>
			<type>boolean</type>
			<admin>true</admin>
			<user>false</user>
			<guest>false</guest>
			<staff>false</staff>
			<module>marketplace</module>
			<ordering>0</ordering>
			<value>can_view_expired</value>
		</setting>
	</user_group_settings>
	<sql><![CDATA[a:2:{s:9:"ADD_FIELD";a:1:{s:18:"phpfox_marketplace";a:2:{s:13:"total_dislike";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:11:"is_notified";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}}s:7:"ADD_KEY";a:1:{s:18:"phpfox_marketplace";a:1:{s:11:"is_notified";a:2:{i:0;s:5:"INDEX";i:1;s:11:"is_notified";}}}}]]></sql>
</upgrade>