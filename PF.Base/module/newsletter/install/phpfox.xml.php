<module>
	<data>
		<module_id>newsletter</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>1</is_menu>
		<menu><![CDATA[a:2:{s:39:"newsletter.admin_menu_create_newsletter";a:1:{s:3:"url";a:2:{i:0;s:10:"newsletter";i:1;s:3:"add";}}s:40:"newsletter.admin_menu_manage_newsletters";a:1:{s:3:"url";a:1:{i:0;s:10:"newsletter";}}}]]></menu>
		<phrase_var_name>module_newsletter</phrase_var_name>
		<writable />
	</data>
	<user_group_settings>
		<setting is_admin_setting="0" module_id="newsletter" type="boolean" admin="true" user="true" guest="false" staff="true" module="newsletter" ordering="0">show_privacy</setting>
	</user_group_settings>
	<tables><![CDATA[a:2:{s:17:"phpfox_newsletter";a:3:{s:7:"COLUMNS";a:15:{s:13:"newsletter_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"subject";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:5:"round";a:4:{i:0;s:6:"TINT:3";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:5:"total";a:4:{i:0;s:5:"USINT";i:1;s:2:"50";i:2;s:0:"";i:3;s:2:"NO";}s:5:"state";a:4:{i:0;s:6:"TINT:1";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"type_id";a:4:{i:0;s:6:"TINT:1";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"age_from";a:4:{i:0;s:6:"TINT:2";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:6:"age_to";a:4:{i:0;s:6:"TINT:2";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:13:"user_group_id";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"country_iso";a:4:{i:0;s:6:"CHAR:2";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:6:"gender";a:4:{i:0;s:6:"TINT:1";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:6:"UINT:9";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"archive";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"1";i:2;s:0:"";i:3;s:2:"NO";}s:7:"privacy";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"1";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:13:"newsletter_id";s:4:"KEYS";a:1:{s:5:"state";a:2:{i:0;s:5:"INDEX";i:1;s:5:"state";}}}s:22:"phpfox_newsletter_text";a:2:{s:7:"COLUMNS";a:3:{s:13:"newsletter_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:9:"text_html";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"text_plain";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:13:"newsletter_id";}}]]></tables>
</module>