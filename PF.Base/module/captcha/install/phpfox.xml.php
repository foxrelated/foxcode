<module>
	<data>
		<module_id>captcha</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>0</is_menu>
		<menu />
		<phrase_var_name>module_captcha_phrase</phrase_var_name>
		<writable />
	</data>
	<settings>
		<setting group="" module_id="captcha" is_hidden="0" type="string" var_name="captcha_code" phrase_var_name="setting_captcha_code" ordering="1" version_id="2.0.0alpha1">23456789bcdfghjkmnpqrstvwxyzABCDEFGHJKLMNPQRSTUVWXYZ</setting>
		<setting group="" module_id="captcha" is_hidden="0" type="integer" var_name="captcha_limit" phrase_var_name="setting_captcha_limit" ordering="2" version_id="2.0.0alpha1">5</setting>
		<setting group="" module_id="captcha" is_hidden="0" type="boolean" var_name="captcha_use_font" phrase_var_name="setting_captcha_use_font" ordering="4" version_id="2.0.0alpha1">0</setting>
		<setting group="" module_id="captcha" is_hidden="0" type="string" var_name="captcha_font" phrase_var_name="setting_captcha_font" ordering="4" version_id="2.0.0alpha1">HECK.TTF</setting>
		<setting group="" module_id="captcha" is_hidden="0" type="string" var_name="recaptcha_public_key" phrase_var_name="setting_recaptcha_public_key" ordering="2" version_id="2.0.0rc12" />
		<setting group="" module_id="captcha" is_hidden="0" type="string" var_name="recaptcha_private_key" phrase_var_name="setting_recaptcha_private_key" ordering="3" version_id="2.0.0rc12" />
		<setting group="" module_id="captcha" is_hidden="0" type="drop" var_name="captcha_type" phrase_var_name="setting_captcha_type" ordering="1" version_id="4.3.0"><![CDATA[a:2:{s:7:"default";s:7:"default";s:6:"values";a:3:{i:0;s:7:"default";i:1;s:9:"recaptcha";i:2;s:6:"qrcode";}}]]></setting>
	</settings>
	<hooks>
		<hook module_id="captcha" hook_type="component" module="captcha" call_name="captcha.component_block_form_process" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="captcha" hook_type="component" module="captcha" call_name="captcha.component_block_form_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="captcha" hook_type="service" module="captcha" call_name="captcha.service_captcha__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="captcha" hook_type="service" module="captcha" call_name="captcha.service_process__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="captcha" hook_type="service" module="captcha" call_name="captcha.service_callback__call" added="1240687633" version_id="2.0.0beta1" />
	</hooks>
	<components>
		<component module_id="captcha" component="form" m_connection="" module="captcha" is_controller="0" is_block="1" is_active="1" />
		<component module_id="captcha" component="image" m_connection="captcha.image" module="captcha" is_controller="1" is_block="0" is_active="1" />
		<component module_id="captcha" component="ajax" m_connection="" module="captcha" is_controller="0" is_block="0" is_active="1" />
	</components>
	<user_group_settings>
		<setting is_admin_setting="0" module_id="captcha" type="boolean" admin="0" user="0" guest="0" staff="0" module="captcha" ordering="9">captcha_on_blog_add</setting>
		<setting is_admin_setting="0" module_id="captcha" type="boolean" admin="0" user="0" guest="1" staff="0" module="captcha" ordering="0">captcha_on_comment</setting>
	</user_group_settings>
</module>