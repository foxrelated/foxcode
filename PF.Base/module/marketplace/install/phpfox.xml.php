<module>
	<data>
		<module_id>marketplace</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>1</is_menu>
		<menu><![CDATA[a:2:{s:35:"marketplace.admin_menu_add_category";a:1:{s:3:"url";a:2:{i:0;s:11:"marketplace";i:1;s:3:"add";}}s:40:"marketplace.admin_menu_manage_categories";a:1:{s:3:"url";a:1:{i:0;s:11:"marketplace";}}}]]></menu>
		<phrase_var_name>module_marketplace</phrase_var_name>
		<writable><![CDATA[a:1:{i:0;s:21:"file/pic/marketplace/";}]]></writable>
	</data>
	<menus>
		<menu module_id="marketplace" parent_var_name="" m_connection="main" var_name="menu_marketplace" ordering="10" url_value="marketplace" version_id="2.0.0alpha4" disallow_access="" module="marketplace" mobile_icon="usd" />
		<menu module_id="marketplace" parent_var_name="" m_connection="marketplace.index" var_name="menu_add_new_listing" ordering="60" url_value="marketplace.add" version_id="2.0.0alpha4" disallow_access="" module="marketplace" />
		<menu module_id="marketplace" parent_var_name="" m_connection="mobile" var_name="menu_marketplace_marketplace_532c28d5412dd75bf975fb951c740a30" ordering="120" url_value="marketplace" version_id="3.1.0rc1" disallow_access="" module="marketplace" mobile_icon="small_marketplace.png" />
	</menus>
	<settings>
		<setting group="time_stamps" module_id="marketplace" is_hidden="0" type="string" var_name="marketplace_view_time_stamp" phrase_var_name="setting_marketplace_view_time_stamp" ordering="1" version_id="2.0.0alpha4">F j, Y</setting>
		<setting group="" module_id="marketplace" is_hidden="0" type="integer" var_name="total_listing_more_from" phrase_var_name="setting_total_listing_more_from" ordering="1" version_id="2.0.0rc1">10</setting>
		<setting group="" module_id="marketplace" is_hidden="0" type="integer" var_name="how_many_sponsored_listings" phrase_var_name="setting_how_many_sponsored_listings" ordering="1" version_id="2.0.5">5</setting>
		<setting group="" module_id="marketplace" is_hidden="0" type="integer" var_name="days_to_expire_listing" phrase_var_name="setting_days_to_expire_listing" ordering="1" version_id="3.5.0beta1">0</setting>
		<setting group="" module_id="marketplace" is_hidden="0" type="integer" var_name="days_to_notify_expire" phrase_var_name="setting_days_to_notify_expire" ordering="1" version_id="3.5.0beta1">0</setting>
	</settings>
	<blocks>
		<block type_id="0" m_connection="marketplace.view" module_id="marketplace" component="my" location="3" is_active="1" ordering="1" disallow_access="" can_move="0">
			<title></title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="marketplace.index" module_id="marketplace" component="category" location="1" is_active="1" ordering="2" disallow_access="" can_move="0">
			<title></title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="marketplace.index" module_id="marketplace" component="sponsored" location="3" is_active="1" ordering="1" disallow_access="" can_move="0">
			<title></title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="marketplace.index" module_id="marketplace" component="featured" location="3" is_active="1" ordering="5" disallow_access="" can_move="0">
			<title>Featured Listings</title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="marketplace.index" module_id="marketplace" component="invite" location="3" is_active="1" ordering="4" disallow_access="" can_move="0">
			<title>Users Invites</title>
			<source_code />
			<source_parsed />
		</block>
        <block type_id="0" m_connection="marketplace.view" module_id="marketplace" component="category" location="1" is_active="1" ordering="2" disallow_access="" can_move="0">
            <title>Categories</title>
            <source_code />
            <source_parsed />
        </block>
	</blocks>
	<hooks>
		<hook module_id="marketplace" hook_type="controller" module="marketplace" call_name="marketplace.component_controller_index_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="marketplace" hook_type="controller" module="marketplace" call_name="marketplace.component_controller_admincp_index_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="marketplace" hook_type="controller" module="marketplace" call_name="marketplace.component_controller_admincp_add_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="marketplace" hook_type="controller" module="marketplace" call_name="marketplace.component_controller_view_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="marketplace" hook_type="controller" module="marketplace" call_name="marketplace.component_controller_add_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="marketplace" hook_type="controller" module="marketplace" call_name="marketplace.component_controller_profile_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="marketplace" hook_type="component" module="marketplace" call_name="marketplace.component_block_photo_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="marketplace" hook_type="component" module="marketplace" call_name="marketplace.component_block_menu_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="marketplace" hook_type="component" module="marketplace" call_name="marketplace.component_block_info_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="marketplace" hook_type="component" module="marketplace" call_name="marketplace.component_block_profile_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="marketplace" hook_type="component" module="marketplace" call_name="marketplace.component_block_list_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="marketplace" hook_type="service" module="marketplace" call_name="marketplace.service_category_category__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="marketplace" hook_type="service" module="marketplace" call_name="marketplace.service_category_process__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="marketplace" hook_type="service" module="marketplace" call_name="marketplace.service_process__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="marketplace" hook_type="service" module="marketplace" call_name="marketplace.service_browse__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="marketplace" hook_type="service" module="marketplace" call_name="marketplace.service_callback__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="marketplace" hook_type="service" module="marketplace" call_name="marketplace.service_marketplace__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="marketplace" hook_type="component" module="marketplace" call_name="marketplace.component_block_my_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="marketplace" hook_type="component" module="marketplace" call_name="marketplace.component_block_category_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="marketplace" hook_type="service" module="marketplace" call_name="marketplace.service_process_add__start" added="1260366442" version_id="2.0.0rc11" />
		<hook module_id="marketplace" hook_type="service" module="marketplace" call_name="marketplace.service_process_add__end" added="1260366442" version_id="2.0.0rc11" />
		<hook module_id="marketplace" hook_type="service" module="marketplace" call_name="marketplace.service_sponsor__end" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="marketplace" hook_type="controller" module="marketplace" call_name="marketplace.component_controller_invoice_index_clean" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="marketplace" hook_type="controller" module="marketplace" call_name="marketplace.component_controller_purchase_clean" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="marketplace" hook_type="template" module="marketplace" call_name="marketplace.template_default_controller_view_extra_info" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="marketplace" hook_type="service" module="marketplace" call_name="marketplace.service_process_add" added="1286546859" version_id="2.0.7" />
		<hook module_id="marketplace" hook_type="service" module="marketplace" call_name="marketplace.service_process_update" added="1286546859" version_id="2.0.7" />
		<hook module_id="marketplace" hook_type="service" module="marketplace" call_name="marketplace.service_browse_execute_query" added="1286546859" version_id="2.0.7" />
		<hook module_id="marketplace" hook_type="service" module="marketplace" call_name="marketplace.service_browse_execute" added="1286546859" version_id="2.0.7" />
		<hook module_id="marketplace" hook_type="service" module="marketplace" call_name="marketplace.service_callback_getnewsfeed_start" added="1286546859" version_id="2.0.7" />
		<hook module_id="marketplace" hook_type="service" module="marketplace" call_name="marketplace.service_category_getforbrowse" added="1286546859" version_id="2.0.7" />
		<hook module_id="marketplace" hook_type="service" module="marketplace" call_name="marketplace.service_marketplace_getlisting" added="1286546859" version_id="2.0.7" />
		<hook module_id="marketplace" hook_type="service" module="marketplace" call_name="marketplace.service_marketplace_getforedit" added="1286546859" version_id="2.0.7" />
		<hook module_id="marketplace" hook_type="service" module="marketplace" call_name="marketplace.service_marketplace_getforprofileblock" added="1286546859" version_id="2.0.7" />
		<hook module_id="marketplace" hook_type="service" module="marketplace" call_name="marketplace.service_marketplace_getuserlistings_count" added="1286546859" version_id="2.0.7" />
		<hook module_id="marketplace" hook_type="service" module="marketplace" call_name="marketplace.service_marketplace_getuserlistings_query" added="1286546859" version_id="2.0.7" />
		<hook module_id="marketplace" hook_type="controller" module="marketplace" call_name="marketplace.component_controller_add_process_update_complete" added="1286546859" version_id="2.0.7" />
		<hook module_id="marketplace" hook_type="controller" module="marketplace" call_name="marketplace.component_controller_add_process" added="1286546859" version_id="2.0.7" />
		<hook module_id="marketplace" hook_type="controller" module="marketplace" call_name="marketplace.component_controller_index_process_search" added="1286546859" version_id="2.0.7" />
		<hook module_id="marketplace" hook_type="controller" module="marketplace" call_name="marketplace.component_controller_index_process_filter" added="1286546859" version_id="2.0.7" />
		<hook module_id="marketplace" hook_type="controller" module="marketplace" call_name="marketplace.component_controller_process_end" added="1286546859" version_id="2.0.7" />
		<hook module_id="marketplace" hook_type="controller" module="marketplace" call_name="marketplace.component_controller_view_process_end" added="1286546859" version_id="2.0.7" />
		<hook module_id="marketplace" hook_type="component" module="marketplace" call_name="marketplace.component_block_profile_process" added="1286546859" version_id="2.0.7" />
		<hook module_id="marketplace" hook_type="component" module="marketplace" call_name="marketplace.component_block_category_section_name" added="1286546859" version_id="2.0.7" />
		<hook module_id="marketplace" hook_type="service" module="marketplace" call_name="marketplace.service_callback_getfeedredirect" added="1290072896" version_id="2.0.7" />
		<hook module_id="marketplace" hook_type="component" module="marketplace" call_name="marketplace.component_block_featured_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="marketplace" hook_type="component" module="marketplace" call_name="marketplace.component_block_invite_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="marketplace" hook_type="service" module="marketplace" call_name="marketplace.component_service_callback_getactivityfeed__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="marketplace" hook_type="service" module="marketplace" call_name="marketplace.service_process_update__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="marketplace" hook_type="service" module="marketplace" call_name="marketplace.service_process_delete__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="marketplace" hook_type="service" module="marketplace" call_name="marketplace.service_process_setdefault__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="marketplace" hook_type="service" module="marketplace" call_name="marketplace.service_process_deleteimage__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="marketplace" hook_type="service" module="marketplace" call_name="marketplace.service_process_approve__1" added="1335951260" version_id="3.2.0" />
	</hooks>
	<components>
		<component module_id="marketplace" component="view" m_connection="marketplace.view" module="marketplace" is_controller="1" is_block="0" is_active="1" />
		<component module_id="marketplace" component="menu" m_connection="" module="marketplace" is_controller="0" is_block="1" is_active="1" />
		<component module_id="marketplace" component="index" m_connection="marketplace.index" module="marketplace" is_controller="1" is_block="0" is_active="1" />
		<component module_id="marketplace" component="profile" m_connection="" module="marketplace" is_controller="0" is_block="1" is_active="1" />
		<component module_id="marketplace" component="info" m_connection="" module="marketplace" is_controller="0" is_block="1" is_active="1" />
		<component module_id="marketplace" component="my" m_connection="" module="marketplace" is_controller="0" is_block="1" is_active="1" />
		<component module_id="marketplace" component="category" m_connection="" module="marketplace" is_controller="0" is_block="1" is_active="1" />
		<component module_id="marketplace" component="sponsored" m_connection="" module="marketplace" is_controller="0" is_block="1" is_active="1" />
		<component module_id="marketplace" component="invoice" m_connection="marketplace.invoice" module="marketplace" is_controller="1" is_block="0" is_active="1" />
		<component module_id="marketplace" component="featured" m_connection="" module="marketplace" is_controller="0" is_block="1" is_active="1" />
		<component module_id="marketplace" component="profile" m_connection="marketplace.profile" module="marketplace" is_controller="1" is_block="0" is_active="1" />
		<component module_id="marketplace" component="invite" m_connection="" module="marketplace" is_controller="0" is_block="1" is_active="1" />
	</components>
	<user_group_settings>
		<setting is_admin_setting="0" module_id="marketplace" type="boolean" admin="1" user="1" guest="0" staff="1" module="marketplace" ordering="0">can_post_comment_on_listing</setting>
		<setting is_admin_setting="0" module_id="marketplace" type="boolean" admin="1" user="1" guest="0" staff="1" module="marketplace" ordering="0">can_edit_own_listing</setting>
		<setting is_admin_setting="0" module_id="marketplace" type="boolean" admin="1" user="0" guest="0" staff="1" module="marketplace" ordering="0">can_edit_other_listing</setting>
		<setting is_admin_setting="0" module_id="marketplace" type="boolean" admin="1" user="1" guest="0" staff="1" module="marketplace" ordering="0">can_delete_own_listing</setting>
		<setting is_admin_setting="0" module_id="marketplace" type="boolean" admin="1" user="0" guest="0" staff="1" module="marketplace" ordering="0">can_delete_other_listings</setting>
		<setting is_admin_setting="0" module_id="marketplace" type="integer" admin="500" user="500" guest="500" staff="500" module="marketplace" ordering="0">max_upload_size_listing</setting>
		<setting is_admin_setting="0" module_id="marketplace" type="boolean" admin="1" user="0" guest="0" staff="1" module="marketplace" ordering="0">can_feature_listings</setting>
		<setting is_admin_setting="0" module_id="marketplace" type="boolean" admin="0" user="0" guest="0" staff="0" module="marketplace" ordering="0">listing_approve</setting>
		<setting is_admin_setting="0" module_id="marketplace" type="boolean" admin="1" user="0" guest="0" staff="1" module="marketplace" ordering="0">can_approve_listings</setting>
		<setting is_admin_setting="0" module_id="marketplace" type="boolean" admin="1" user="1" guest="1" staff="1" module="marketplace" ordering="0">can_access_marketplace</setting>
		<setting is_admin_setting="0" module_id="marketplace" type="boolean" admin="1" user="1" guest="0" staff="1" module="marketplace" ordering="0">can_create_listing</setting>
		<setting is_admin_setting="0" module_id="marketplace" type="boolean" admin="false" user="false" guest="false" staff="false" module="marketplace" ordering="0">can_sponsor_marketplace</setting>
		<setting is_admin_setting="0" module_id="marketplace" type="boolean" admin="false" user="false" guest="false" staff="false" module="marketplace" ordering="0">can_purchase_sponsor</setting>
		<setting is_admin_setting="0" module_id="marketplace" type="string" admin="null" user="null" guest="null" staff="null" module="marketplace" ordering="0">marketplace_sponsor_price</setting>
		<setting is_admin_setting="0" module_id="marketplace" type="boolean" admin="true" user="false" guest="false" staff="false" module="marketplace" ordering="0">auto_publish_sponsored_item</setting>
		<setting is_admin_setting="0" module_id="marketplace" type="boolean" admin="0" user="0" guest="0" staff="0" module="marketplace" ordering="0">can_sell_items_on_marketplace</setting>
		<setting is_admin_setting="0" module_id="marketplace" type="integer" admin="0" user="0" guest="0" staff="0" module="marketplace" ordering="0">flood_control_marketplace</setting>
		<setting is_admin_setting="0" module_id="marketplace" type="integer" admin="1" user="1" guest="0" staff="1" module="marketplace" ordering="0">points_marketplace</setting>
		<setting is_admin_setting="0" module_id="marketplace" type="integer" admin="6" user="6" guest="6" staff="6" module="marketplace" ordering="0">total_photo_upload_limit</setting>
		<setting is_admin_setting="0" module_id="marketplace" type="boolean" admin="true" user="false" guest="false" staff="false" module="marketplace" ordering="0">can_view_expired</setting>
	</user_group_settings>
	<tables><![CDATA[a:7:{s:18:"phpfox_marketplace";a:3:{s:7:"COLUMNS";a:26:{s:10:"listing_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"view_id";a:4:{i:0;s:6:"TINT:3";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"privacy";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:15:"privacy_comment";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"group_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"is_featured";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"is_sponsor";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:5:"title";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"currency_id";a:4:{i:0;s:6:"CHAR:3";i:1;s:3:"USD";i:2;s:0:"";i:3;s:2:"NO";}s:5:"price";a:4:{i:0;s:10:"DECIMAL:14";i:1;s:4:"0.00";i:2;s:0:"";i:3;s:2:"NO";}s:11:"country_iso";a:4:{i:0;s:6:"CHAR:2";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:16:"country_child_id";a:4:{i:0;s:4:"UINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:11:"postal_code";a:4:{i:0;s:8:"VCHAR:20";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:4:"city";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"image_path";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"server_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_comment";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_like";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_dislike";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"is_sell";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_closed";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"auto_sell";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:16:"mini_description";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:11:"is_notified";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:10:"listing_id";s:4:"KEYS";a:5:{s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"user_id";}s:7:"view_id";a:2:{i:0;s:5:"INDEX";i:1;a:4:{i:0;s:7:"view_id";i:1;s:7:"privacy";i:2;s:10:"is_sponsor";i:3;s:10:"time_stamp";}}s:9:"view_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"view_id";i:1;s:7:"privacy";i:2;s:11:"is_featured";}}s:10:"listing_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:10:"listing_id";i:1;s:7:"view_id";}}s:11:"is_notified";a:2:{i:0;s:5:"INDEX";i:1;s:11:"is_notified";}}}s:27:"phpfox_marketplace_category";a:3:{s:7:"COLUMNS";a:8:{s:11:"category_id";a:4:{i:0;s:4:"UINT";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:9:"parent_id";a:4:{i:0;s:4:"UINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_active";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:4:"name";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"name_url";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:4:"used";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"ordering";a:4:{i:0;s:7:"UINT:11";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:11:"category_id";s:4:"KEYS";a:2:{s:9:"parent_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:9:"parent_id";i:1;s:9:"is_active";}}s:9:"is_active";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:9:"is_active";i:1;s:8:"name_url";}}}}s:32:"phpfox_marketplace_category_data";a:2:{s:7:"COLUMNS";a:2:{s:10:"listing_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"category_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:4:"KEYS";a:2:{s:11:"category_id";a:2:{i:0;s:5:"INDEX";i:1;s:11:"category_id";}s:10:"listing_id";a:2:{i:0;s:5:"INDEX";i:1;s:10:"listing_id";}}}s:24:"phpfox_marketplace_image";a:3:{s:7:"COLUMNS";a:5:{s:8:"image_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:10:"listing_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"image_path";a:4:{i:0;s:8:"VCHAR:50";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"server_id";a:4:{i:0;s:6:"TINT:1";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"ordering";a:4:{i:0;s:6:"TINT:3";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:8:"image_id";s:4:"KEYS";a:1:{s:10:"listing_id";a:2:{i:0;s:5:"INDEX";i:1;s:10:"listing_id";}}}s:25:"phpfox_marketplace_invite";a:3:{s:7:"COLUMNS";a:8:{s:9:"invite_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:10:"listing_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"type_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"visited_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:15:"invited_user_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"invited_email";a:4:{i:0;s:9:"VCHAR:100";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:9:"invite_id";s:4:"KEYS";a:6:{s:10:"listing_id";a:2:{i:0;s:5:"INDEX";i:1;s:10:"listing_id";}s:12:"listing_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:10:"listing_id";i:1;s:15:"invited_user_id";}}s:15:"invited_user_id";a:2:{i:0;s:5:"INDEX";i:1;s:15:"invited_user_id";}s:12:"listing_id_3";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:10:"listing_id";i:1;s:10:"visited_id";}}s:12:"listing_id_4";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:10:"listing_id";i:1;s:10:"visited_id";i:2;s:15:"invited_user_id";}}s:10:"visited_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:10:"visited_id";i:1;s:15:"invited_user_id";}}}}s:26:"phpfox_marketplace_invoice";a:3:{s:7:"COLUMNS";a:8:{s:10:"invoice_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:10:"listing_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"currency_id";a:4:{i:0;s:6:"CHAR:3";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:5:"price";a:4:{i:0;s:10:"DECIMAL:14";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:6:"status";a:4:{i:0;s:8:"VCHAR:20";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:15:"time_stamp_paid";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:10:"invoice_id";s:4:"KEYS";a:4:{s:10:"listing_id";a:2:{i:0;s:5:"INDEX";i:1;s:10:"listing_id";}s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"user_id";}s:12:"listing_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:10:"listing_id";i:1;s:6:"status";}}s:12:"listing_id_3";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:10:"listing_id";i:1;s:7:"user_id";i:2;s:6:"status";}}}}s:23:"phpfox_marketplace_text";a:2:{s:7:"COLUMNS";a:3:{s:10:"listing_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"description";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:18:"description_parsed";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:4:"KEYS";a:1:{s:10:"listing_id";a:2:{i:0;s:5:"INDEX";i:1;s:10:"listing_id";}}}}]]></tables>
	<install><![CDATA[
		$aCategories = array(
			'Community',
			'Houses',
			'Jobs',
			'Pets',
			'Rentals',
			'Services',
			'Stuff',
			'Tickets',
			'Vehicle'
		);		
		
		$iCategoryOrder = 0;
		foreach ($aCategories as $sCategory)
		{
			$iCategoryOrder++;
			$iCategoryId = $this->database()->insert(Phpfox::getT('marketplace_category'), array(					
					'name' => $sCategory,
					'is_active' => 1,
					'ordering' => $iCategoryOrder			
				)
			);
		}
	]]></install>
</module>