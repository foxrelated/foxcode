<module>
	<data>
		<module_id>egift</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>1</is_menu>
		<menu><![CDATA[a:3:{s:34:"egift.admin_menu_manage_categories";a:1:{s:3:"url";a:2:{i:0;s:5:"egift";i:1;s:10:"categories";}}s:27:"egift.admin_menu_add_e_gifs";a:1:{s:3:"url";a:1:{i:0;s:5:"egift";}}s:25:"egift.admin_menu_invoices";a:1:{s:3:"url";a:2:{i:0;s:5:"egift";i:1;s:7:"invoice";}}}]]></menu>
		<phrase_var_name>module_egift</phrase_var_name>
		<writable><![CDATA[a:1:{i:0;s:15:"file/pic/egift/";}]]></writable>
	</data>
	<hooks>
		<hook module_id="egift" hook_type="service" module="egift" call_name="egift.service_callback__call" added="1299062480" version_id="2.0.8" />
		<hook module_id="egift" hook_type="service" module="egift" call_name="egift.service_egift__call" added="1299062480" version_id="2.0.8" />
		<hook module_id="egift" hook_type="service" module="egift" call_name="egift.service_process__call" added="1299062480" version_id="2.0.8" />
		<hook module_id="egift" hook_type="controller" module="egift" call_name="egift.component_controller_index_clean" added="1299062480" version_id="2.0.8" />
	</hooks>
	<tables><![CDATA[a:3:{s:12:"phpfox_egift";a:3:{s:7:"COLUMNS";a:7:{s:8:"egift_id";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:9:"file_path";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:11:"category_id";a:4:{i:0;s:6:"TINT:3";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:5:"title";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:5:"price";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:8:"egift_id";s:4:"KEYS";a:1:{s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"user_id";}}}s:21:"phpfox_egift_category";a:2:{s:7:"COLUMNS";a:5:{s:11:"category_id";a:4:{i:0;s:4:"UINT";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:6:"phrase";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"ordering";a:4:{i:0;s:7:"UINT:11";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_start";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:8:"time_end";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:11:"PRIMARY_KEY";s:11:"category_id";}s:20:"phpfox_egift_invoice";a:2:{s:7:"COLUMNS";a:10:{s:10:"invoice_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:9:"user_from";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_to";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"egift_id";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"birthday_id";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"currency_id";a:4:{i:0;s:6:"CHAR:3";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:5:"price";a:4:{i:0;s:10:"DECIMAL:14";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:18:"time_stamp_created";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:15:"time_stamp_paid";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:6:"status";a:4:{i:0;s:8:"VCHAR:20";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:11:"PRIMARY_KEY";s:10:"invoice_id";}}]]></tables>
</module>