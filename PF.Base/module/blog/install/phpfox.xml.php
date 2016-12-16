<module>
	<data>
		<module_id>blog</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>1</is_menu>
		<menu><![CDATA[a:2:{s:36:"admincp.admin_menu_manage_categories";a:1:{s:3:"url";a:1:{i:0;s:4:"blog";}}s:31:"admincp.admin_menu_add_category";a:1:{s:3:"url";a:2:{i:0;s:4:"blog";i:1;s:3:"add";}}}]]></menu>
		<phrase_var_name>module_blog_phrase</phrase_var_name>
		<writable />
	</data>
	<menus>
		<menu module_id="blog" parent_var_name="" m_connection="main" var_name="menu_blogs" ordering="2" url_value="blog" version_id="2.0.0alpha1" disallow_access="" module="blog" mobile_icon="pencil-square" />
		<menu module_id="blog" parent_var_name="" m_connection="blog.index" var_name="menu_add_new_blog" ordering="3" url_value="blog.add" version_id="2.0.0alpha1" disallow_access="" module="blog" />
		<menu module_id="blog" parent_var_name="" m_connection="profile" var_name="menu_blogs" ordering="2" url_value="profile.blog" version_id="2.0.0alpha1" disallow_access="" module="blog" />
		<menu module_id="blog" parent_var_name="" m_connection="mobile" var_name="menu_blog_blogs_532c28d5412dd75bf975fb951c740a30" ordering="114" url_value="blog" version_id="3.1.0rc1" disallow_access="" module="blog" mobile_icon="small_blogs.png" />
	</menus>
	<settings>
		<setting group="time_stamps" module_id="blog" is_hidden="0" type="string" var_name="blog_time_stamp" phrase_var_name="setting_blog_time_stamp" ordering="2" version_id="2.0.0alpha1">F j, Y</setting>
		<setting group="" module_id="blog" is_hidden="0" type="integer" var_name="top_bloggers_display_limit" phrase_var_name="setting_top_bloggers_display_limit" ordering="0" version_id="2.0.0alpha1">8</setting>
		<setting group="" module_id="blog" is_hidden="0" type="integer" var_name="top_bloggers_min_post" phrase_var_name="setting_top_bloggers_min_post" ordering="0" version_id="2.0.0alpha1">10</setting>
		<setting group="" module_id="blog" is_hidden="0" type="boolean" var_name="cache_top_bloggers" phrase_var_name="setting_cache_top_bloggers" ordering="0" version_id="2.0.0alpha1">1</setting>
		<setting group="" module_id="blog" is_hidden="0" type="integer" var_name="cache_top_bloggers_limit" phrase_var_name="setting_cache_top_bloggers_limit" ordering="0" version_id="2.0.0alpha1">180</setting>
		<setting group="" module_id="blog" is_hidden="0" type="boolean" var_name="display_post_count_in_top_bloggers" phrase_var_name="setting_display_post_count_in_top_bloggers" ordering="0" version_id="2.0.0alpha1">1</setting>
		<setting group="spam" module_id="blog" is_hidden="1" type="boolean" var_name="spam_check_blogs" phrase_var_name="setting_spam_check_blogs" ordering="5" version_id="2.0.0rc1">1</setting>
		<setting group="spam" module_id="blog" is_hidden="1" type="boolean" var_name="allow_links_in_blog_title" phrase_var_name="setting_allow_links_in_blog_title" ordering="2" version_id="2.0.0rc1">1</setting>
		<setting group="seo" module_id="blog" is_hidden="0" type="large_string" var_name="blog_meta_description" phrase_var_name="setting_blog_meta_description" ordering="6" version_id="2.0.0rc1">Read up on the latest blogs on Site Name.</setting>
		<setting group="seo" module_id="blog" is_hidden="0" type="large_string" var_name="blog_meta_keywords" phrase_var_name="setting_blog_meta_keywords" ordering="13" version_id="2.0.0rc1">blog, blogs, journals</setting>
	</settings>
	<blocks>
		<block type_id="0" m_connection="blog.index" module_id="blog" component="categories" location="1" is_active="1" ordering="3" disallow_access="" can_move="0">
			<title>Categories</title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="blog.index" module_id="blog" component="top" location="3" is_active="1" ordering="1" disallow_access="" can_move="0">
			<title>Top Bloggers</title>
			<source_code />
			<source_parsed />
		</block>
        <block type_id="0" m_connection="blog.view" module_id="blog" component="categories" location="1" is_active="1" ordering="10" disallow_access="" can_move="0">
			<title>Categories</title>
			<source_code />
			<source_parsed />
		</block>
	</blocks>
	<hooks>
		<hook module_id="blog" hook_type="component" module="blog" call_name="blog.component_block_add_category_list_process" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="component" module="blog" call_name="blog.component_block_add_category_list_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="component" module="blog" call_name="blog.component_block_menu_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="component" module="blog" call_name="blog.component_block_preview_process" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="component" module="blog" call_name="blog.component_block_preview_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="component" module="blog" call_name="blog.component_block_top_process" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="component" module="blog" call_name="blog.component_block_top_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="component" module="blog" call_name="blog.component_block_categories_process" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="component" module="blog" call_name="blog.component_block_categories_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="controller" module="blog" call_name="blog.component_controller_admincp_index_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="controller" module="blog" call_name="blog.component_controller_admincp_add_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="controller" module="blog" call_name="blog.component_controller_profile_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="controller" module="blog" call_name="blog.component_controller_index_process_start" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="controller" module="blog" call_name="blog.component_controller_index_process_search" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="controller" module="blog" call_name="blog.component_controller_index_process_middle" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="controller" module="blog" call_name="blog.component_controller_index_process_end" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="controller" module="blog" call_name="blog.component_controller_index_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="controller" module="blog" call_name="blog.component_controller_add_process_edit" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="controller" module="blog" call_name="blog.component_controller_add_process_validation" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="controller" module="blog" call_name="blog.component_controller_add_process" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="controller" module="blog" call_name="blog.component_controller_add_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="controller" module="blog" call_name="blog.component_controller_view_process_start" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="controller" module="blog" call_name="blog.component_controller_view_process_middle" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="controller" module="blog" call_name="blog.component_controller_view_process_end" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="controller" module="blog" call_name="blog.component_controller_view_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_blog___construct" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_blog_get" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_blog_getblog" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_blog_hasaccess_start" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_blog_hasaccess_end" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_blog__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_callback__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_category_category_get_start" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_category_category_get_end" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_category_category_getcategories_start" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_category_category_getcategories_end" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_category_category_getblogsbycategory_count" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_category_category_getblogsbycategory_query" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_category_category_getsearch" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_category_category__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_category_process__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_process_add_start" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_process_add_end" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_process_update" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_process_delete" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_process__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="template" module="blog" call_name="blog.template_block_displayoptions" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="template" module="blog" call_name="blog.template_block_entry_date_end" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="template" module="blog" call_name="blog.template_block_entry_text_end" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="template" module="blog" call_name="blog.template_block_entry_links_main" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="template" module="blog" call_name="blog.template_block_entry_end" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="template" module="blog" call_name="blog.template_controller_view_end" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="template" module="blog" call_name="blog.template_controller_add_hidden_form" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="template" module="blog" call_name="blog.template_controller_add_textarea_start" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="template" module="blog" call_name="blog.template_controller_add_submit_buttons" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="template" module="blog" call_name="blog.template_controller_add_additional_options" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="blog" hook_type="component" module="blog" call_name="blog.component_block_new_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_process__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_process__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_process_update__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_process_update__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_process_updateblogtitle__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_process_updateblogtitle__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_process_updateblogtext__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_process_updateblogtext__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_process_deleteinline__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_process_deleteinline__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_process_delete__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_gettags__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_gettags__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_gettagsearch__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_gettagsearch__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_gettagcloud__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_getnewsfeed__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_getnewsfeed__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_getcommentnewsfeed__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_getcommentnewsfeed__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_gettopusers__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_gettopusers__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_gettaglinkprofile__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_gettaglink__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_addtrack__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_getlatesttrackusers__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_getlatesttrackusers__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_getfeedredirect__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_getfeedredirect__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_addcomment__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_addcomment__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_processcommentmoderation__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_processcommentmoderation__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_globalsearch__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_globalsearch__return" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_globalsearch__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_getfavorite__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_getfavorite__return" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_getfavorite__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_ondeleteuser__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_ondeleteuser__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_updatecounter__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_blog_get__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_blog_get__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_blog_getsearch__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_blog_getsearch__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_blog_getdraftscount__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_blog_getnewblogs__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_blog_getnewblogs__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_blog_getblogsforedit__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_blog_getblog__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_blog_getblog__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_blog_preparetitle__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_blog_getextra__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_blog_getextra__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_blog_getnew__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_blog_getnew__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_blog_getspamtotal__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="blog" hook_type="component" module="blog" call_name="blog.component_ajax_get_text" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="blog" hook_type="template" module="blog" call_name="blog.template_block_entry_left_item_menu" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_blog_getpendingtotal" added="1276177474" version_id="2.0.5" />
		<hook module_id="blog" hook_type="component" module="blog" call_name="blog.component_ajax_addviastatusupdate" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="blog" hook_type="component" module="blog" call_name="blog.component_block_share_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_blog_gettotaldrafts" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_browse__call" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_getactivityfeedcomment__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.component_service_callback_getactivityfeed__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="blog" hook_type="service" module="blog" call_name="blog.service_process_approve__1" added="1335951260" version_id="3.2.0" />
	</hooks>
	<components>
		<component module_id="blog" component="add" m_connection="blog.add" module="blog" is_controller="1" is_block="0" is_active="1" />
		<component module_id="blog" component="add-category-list" m_connection="" module="blog" is_controller="0" is_block="1" is_active="1" />
		<component module_id="blog" component="ajax" m_connection="" module="blog" is_controller="0" is_block="0" is_active="1" />
		<component module_id="blog" component="index" m_connection="blog.index" module="blog" is_controller="1" is_block="0" is_active="1" />
		<component module_id="blog" component="view" m_connection="blog.view" module="blog" is_controller="1" is_block="0" is_active="1" />
		<component module_id="blog" component="categories" m_connection="" module="blog" is_controller="0" is_block="1" is_active="1" />
		<component module_id="blog" component="top" m_connection="" module="blog" is_controller="0" is_block="1" is_active="1" />
		<component module_id="blog" component="profile" m_connection="blog.profile" module="blog" is_controller="1" is_block="0" is_active="1" />
		<component module_id="blog" component="profile.index" m_connection="" module="blog" is_controller="0" is_block="1" is_active="1" />
		<component module_id="blog" component="preview" m_connection="" module="blog" is_controller="0" is_block="1" is_active="1" />
		<component module_id="blog" component="admincp.index" m_connection="" module="blog" is_controller="0" is_block="0" is_active="1" />
		<component module_id="blog" component="admincp.add" m_connection="" module="blog" is_controller="0" is_block="0" is_active="1" />
		<component module_id="blog" component="delete" m_connection="blog.delete" module="blog" is_controller="1" is_block="0" is_active="1" />
	</components>
	<stats>
		<stat module_id="blog" phrase_var="blog.stat_title_2" stat_link="blog" stat_image="blog.png" is_active="1"><![CDATA[$this->database()
->select('COUNT(*)')
->from(Phpfox::getT('blog'))
->where('is_approved = 1 AND post_status = 1')
->execute('getSlaveField');]]></stat>
	</stats>
	<rss_group>
		<group module_id="blog" group_id="1" name_var="blog.rss_group_name_1" is_active="1" />
	</rss_group>
	<rss>
		<feed module_id="blog" group_id="1" title_var="blog.rss_title_1" description_var="blog.rss_description_1" feed_link="blog" is_active="1" is_site_wide="1">
			<php_group_code></php_group_code>
			<php_view_code><![CDATA[$aRows = $this->database()->select('bt.text_parsed AS text, b.blog_id, b.title, u.user_name, u.full_name, b.time_stamp')
	->from(Phpfox::getT('blog'), 'b')
        ->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
	->join(Phpfox::getT('blog_text'), 'bt','bt.blog_id = b.blog_id')
	->where('b.is_approved = 1 AND b.privacy = 0 AND b.post_status = 1')
	->limit(Phpfox::getParam('rss.total_rss_display'))
	->order('b.blog_id DESC')
	->execute('getSlaveRows');
$iCnt = count($aRows);

foreach ($aRows as $iKey => $aRow)
{
	$aRows[$iKey]['description'] = $aRow['text'];
	$aRows[$iKey]['link'] = Phpfox::permaLink('blog', $aRow['blog_id'], $aRow['title']);
	$aRows[$iKey]['creator'] = $aRow['full_name'];
}]]></php_view_code>
		</feed>
		<feed module_id="blog" group_id="1" title_var="blog.rss_title_2" description_var="blog.rss_description_2" feed_link="blog.category.{TITLE_URL}" is_active="1" is_site_wide="0">
			<php_group_code><![CDATA[$aCategories = $this->database()->select('category_id, name')
	->from(Phpfox::getT('blog_category'))
	->where('user_id = 0')
	->execute('getSlaveRows');
if (count($aCategories))
{
	foreach ($aCategories as $aCategory)
	{
		$aRow['child'][Phpfox::getLib('phpfox.url')->makeUrl('rss', array('id' => $aRow['feed_id'], 'category' => $aCategory['category_id']))] = $aCategory['name'];
	}
}]]></php_group_code>
			<php_view_code><![CDATA[list($iCnt, $aRows) = Phpfox::getService('blog.category')->getBlogsByCategory(Phpfox::getLib('phpfox.request')->get('category'), 0, array('AND blog.is_approved = 1 AND blog.privacy = 0 AND blog.post_status = 1'), 'blog.time_stamp DESC', 0, Phpfox::getParam('rss.total_rss_display'));

foreach ($aRows as $iKey => $aRow)
{
	$aRows[$iKey]['description'] = $aRow['text'];
	$aRows[$iKey]['link'] = Phpfox::permalink('blog', $aRow['blog_id'], $aRow['title']);
	$aRows[$iKey]['creator'] = $aRow['full_name'];
}


$aCategory = $this->database()->select('*')
	->from(Phpfox::getT('blog_category'))
	->where('category_id = ' . (int) Phpfox::getLib('phpfox.request')->get('category'))
	->execute('getSlaveRow');

$aFeed['feed_link'] = Phpfox::permalink('blog.category', $aCategory['category_id'], $aCategory['name']);
$sDescription = $aCategory['name'];]]></php_view_code>
		</feed>
	</rss>
	<user_group_settings>
		<setting is_admin_setting="0" module_id="blog" type="boolean" admin="1" user="1" guest="1" staff="1" module="blog" ordering="1">view_blogs</setting>
		<setting is_admin_setting="0" module_id="blog" type="boolean" admin="1" user="1" guest="0" staff="1" module="blog" ordering="2">edit_own_blog</setting>
		<setting is_admin_setting="0" module_id="blog" type="boolean" admin="1" user="0" guest="0" staff="1" module="blog" ordering="3">edit_user_blog</setting>
		<setting is_admin_setting="0" module_id="blog" type="boolean" admin="1" user="1" guest="0" staff="1" module="blog" ordering="4">delete_own_blog</setting>
		<setting is_admin_setting="0" module_id="blog" type="boolean" admin="1" user="0" guest="0" staff="1" module="blog" ordering="5">delete_user_blog</setting>
		<setting is_admin_setting="0" module_id="blog" type="boolean" admin="1" user="1" guest="0" staff="1" module="blog" ordering="7">add_new_blog</setting>
		<setting is_admin_setting="0" module_id="blog" type="integer" admin="1" user="1" guest="1" staff="1" module="blog" ordering="8">points_blog</setting>
		<setting is_admin_setting="0" module_id="blog" type="boolean" admin="1" user="1" guest="0" staff="1" module="blog" ordering="0">can_post_comment_on_blog</setting>
		<setting is_admin_setting="0" module_id="blog" type="boolean" admin="1" user="0" guest="0" staff="1" module="blog" ordering="0">can_approve_blogs</setting>
		<setting is_admin_setting="0" module_id="blog" type="boolean" admin="0" user="0" guest="0" staff="0" module="blog" ordering="0">approve_blogs</setting>
		<setting is_admin_setting="0" module_id="blog" type="integer" admin="0" user="0" guest="0" staff="0" module="blog" ordering="0">flood_control_blog</setting>
	</user_group_settings>
	<tables><![CDATA[a:4:{s:11:"phpfox_blog";a:3:{s:7:"COLUMNS";a:16:{s:7:"blog_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:5:"title";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"time_update";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:11:"is_approved";a:4:{i:0;s:6:"TINT:1";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"privacy";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:15:"privacy_comment";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:11:"post_status";a:4:{i:0;s:6:"TINT:1";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_comment";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:16:"total_attachment";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_view";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_like";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_dislike";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"module_id";a:4:{i:0;s:8:"VCHAR:75";i:1;s:4:"blog";i:2;s:0:"";i:3;s:2:"NO";}s:7:"item_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:7:"blog_id";s:4:"KEYS";a:5:{s:11:"public_view";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:11:"is_approved";i:1;s:7:"privacy";i:2;s:11:"post_status";}}s:9:"user_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:4:{i:0;s:7:"user_id";i:1;s:11:"is_approved";i:2;s:7:"privacy";i:3;s:11:"post_status";}}s:10:"time_stamp";a:2:{i:0;s:5:"INDEX";i:1;a:4:{i:0;s:10:"time_stamp";i:1;s:11:"is_approved";i:2;s:7:"privacy";i:3;s:11:"post_status";}}s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;a:5:{i:0;s:7:"user_id";i:1;s:10:"time_stamp";i:2;s:11:"is_approved";i:3;s:7:"privacy";i:4;s:11:"post_status";}}s:5:"title";a:2:{i:0;s:5:"INDEX";i:1;a:4:{i:0;s:5:"title";i:1;s:11:"is_approved";i:2;s:7:"privacy";i:3;s:11:"post_status";}}}}s:20:"phpfox_blog_category";a:3:{s:7:"COLUMNS";a:7:{s:11:"category_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:4:"name";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:5:"added";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:4:"used";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_active";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"1";i:2;s:0:"";i:3;s:2:"No";}s:8:"ordering";a:4:{i:0;s:7:"UINT:11";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:11:"category_id";s:4:"KEYS";a:3:{s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"user_id";}s:11:"category_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:11:"category_id";i:1;s:7:"user_id";}}s:8:"name_url";a:2:{i:0;s:5:"INDEX";i:1;s:7:"user_id";}}}s:25:"phpfox_blog_category_data";a:2:{s:7:"COLUMNS";a:2:{s:7:"blog_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"category_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:4:"KEYS";a:2:{s:7:"blog_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"blog_id";}s:11:"category_id";a:2:{i:0;s:5:"INDEX";i:1;s:11:"category_id";}}}s:16:"phpfox_blog_text";a:2:{s:7:"COLUMNS";a:3:{s:7:"blog_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:4:"text";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:11:"text_parsed";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:11:"PRIMARY_KEY";s:7:"blog_id";}}]]></tables>
    
	<install><![CDATA[
		$aBlogCategories = array(
			'Business' => 'business',
			'Education' => 'education',
			'Entertainment' => 'entertainment',
			'Family & Home' => 'family-home',
			'Health' => 'health',
			'Recreation' => 'recreation',
			'Shopping' => 'shopping',
			'Society' => 'society',
			'Sports' => 'sports',
			'Technology' => 'technology'
		);
		foreach ($aBlogCategories as $sName => $sUrl)
		{
			$this->database()->insert(Phpfox::getT('blog_category'), array(
					'name' => $sName,
					'added' => PHPFOX_TIME
				)
			);
		}
	]]></install>
</module>
