<module>
	<data>
		<module_id>music</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>1</is_menu>
		<menu><![CDATA[a:2:{s:26:"music.admin_menu_add_genre";a:1:{s:3:"url";a:2:{i:0;s:5:"music";i:1;s:3:"add";}}s:30:"music.admin_menu_manage_genres";a:1:{s:3:"url";a:1:{i:0;s:5:"music";}}}]]></menu>
		<phrase_var_name>module_music</phrase_var_name>
		<writable><![CDATA[a:2:{i:0;s:11:"file/music/";i:1;s:15:"file/pic/music/";}]]></writable>
	</data>
	<menus>
		<menu module_id="music" parent_var_name="" m_connection="main" var_name="menu_music" ordering="9" url_value="music" version_id="2.0.0alpha1" disallow_access="" module="music" mobile_icon="music" />
		<menu module_id="music" parent_var_name="" m_connection="music.index" var_name="menu_upload_a_song" ordering="74" url_value="music.upload" version_id="2.0.0beta1" disallow_access="" module="music" />
	</menus>
	<settings>
		<setting group="" module_id="music" is_hidden="0" type="integer" var_name="sponsored_songs_to_show" phrase_var_name="setting_sponsored_songs_to_show" ordering="1" version_id="2.0.5">5</setting>
		<setting group="" module_id="music" is_hidden="1" type="boolean" var_name="music_enable_mass_uploader" phrase_var_name="setting_music_enable_mass_uploader" ordering="1" version_id="2.0.8">0</setting>
	</settings>
	<blocks>
		<block type_id="0" m_connection="music.index" module_id="music" component="list" location="1" is_active="1" ordering="2" disallow_access="" can_move="0">
			<title></title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="music.browse.song" module_id="music" component="list" location="1" is_active="1" ordering="2" disallow_access="" can_move="0">
			<title></title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="music.index" module_id="music" component="sponsored-song" location="1" is_active="1" ordering="1" disallow_access="" can_move="0">
			<title>Sponsored Songs</title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="music.index" module_id="music" component="new-album" location="3" is_active="1" ordering="4" disallow_access="" can_move="0">
			<title>New Albums</title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="music.album" module_id="music" component="track" location="3" is_active="1" ordering="1" disallow_access="" can_move="0">
			<title>Manage Tracks for Albums</title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="music.view-album" module_id="music" component="track" location="3" is_active="1" ordering="4" disallow_access="" can_move="0">
			<title>Album Tracklist</title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="music.index" module_id="music" component="featured" location="3" is_active="1" ordering="5" disallow_access="" can_move="0">
			<title>Featured Songs</title>
			<source_code />
			<source_parsed />
		</block>
        <block type_id="0" m_connection="music.view" module_id="music" component="list" location="1" is_active="1" ordering="10" disallow_access="" can_move="0">
			<title>Genres</title>
			<source_code />
			<source_parsed />
		</block>
        <block type_id="0" m_connection="music.view-album" module_id="music" component="list" location="1" is_active="1" ordering="10" disallow_access="" can_move="0">
			<title>Genres</title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="music.browse.album" module_id="music" component="featured-album" location="3" is_active="1" ordering="4" disallow_access="" can_move="0">
			<title>Featured Albums</title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="music.browse.album" module_id="music" component="sponsored-album" location="3" is_active="1" ordering="3" disallow_access="" can_move="0">
			<title>Sponsored Albums</title>
			<source_code />
			<source_parsed />
		</block>
	</blocks>
	<hooks>
		<hook module_id="music" hook_type="controller" module="music" call_name="music.component_controller_index_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="music" hook_type="controller" module="music" call_name="music.component_controller_upload_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="music" hook_type="component" module="music" call_name="music.component_block_song_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="music" hook_type="service" module="music" call_name="music.service_process__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="music" hook_type="service" module="music" call_name="music.service_music__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="music" hook_type="service" module="music" call_name="music.service_callback__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="music" hook_type="component" module="music" call_name="music.component_block_genre_clean" added="1240688954" version_id="2.0.0beta1" />
		<hook module_id="music" hook_type="service" module="music" call_name="music.service_genre_genre__call" added="1240688954" version_id="2.0.0beta1" />
		<hook module_id="music" hook_type="component" module="music" call_name="music.component_block_genre_profile_clean" added="1240692039" version_id="2.0.0beta1" />
		<hook module_id="music" hook_type="service" module="music" call_name="music.service_genre_process__call" added="1240692039" version_id="2.0.0beta1" />
		<hook module_id="music" hook_type="component" module="music" call_name="music.component_block_track_clean" added="1242299564" version_id="2.0.0beta2" />
		<hook module_id="music" hook_type="component" module="music" call_name="music.component_block_list_clean" added="1242299564" version_id="2.0.0beta2" />
		<hook module_id="music" hook_type="controller" module="music" call_name="music.component_controller_admincp_index_clean" added="1242299564" version_id="2.0.0beta2" />
		<hook module_id="music" hook_type="controller" module="music" call_name="music.component_controller_admincp_add_clean" added="1242299564" version_id="2.0.0beta2" />
		<hook module_id="music" hook_type="controller" module="music" call_name="music.component_controller_album_clean" added="1242299564" version_id="2.0.0beta2" />
		<hook module_id="music" hook_type="controller" module="music" call_name="music.component_controller_view_clean" added="1242299564" version_id="2.0.0beta2" />
		<hook module_id="music" hook_type="controller" module="music" call_name="music.component_controller_player_clean" added="1242299564" version_id="2.0.0beta2" />
		<hook module_id="music" hook_type="controller" module="music" call_name="music.component_controller_view_album_clean" added="1242299564" version_id="2.0.0beta2" />
		<hook module_id="music" hook_type="controller" module="music" call_name="music.component_controller_profile_clean" added="1242299564" version_id="2.0.0beta2" />
		<hook module_id="music" hook_type="service" module="music" call_name="music.service_album_album__call" added="1242299564" version_id="2.0.0beta2" />
		<hook module_id="music" hook_type="service" module="music" call_name="music.service_album_process__call" added="1242299564" version_id="2.0.0beta2" />
		<hook module_id="music" hook_type="service" module="music" call_name="music.service_browse__call" added="1242299564" version_id="2.0.0beta2" />
		<hook module_id="music" hook_type="component" module="music" call_name="music.component_block_menu_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="music" hook_type="component" module="music" call_name="music.component_block_latest_album_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="music" hook_type="component" module="music" call_name="music.component_block_featured_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="music" hook_type="component" module="music" call_name="music.component_block_featured_album_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="music" hook_type="component" module="music" call_name="music.component_block_menu_album_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="music" hook_type="controller" module="music" call_name="music.component_controller_browse_album_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="music" hook_type="controller" module="music" call_name="music.component_controller_browse_song_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="music" hook_type="service" module="music" call_name="music.service_process_upload__end" added="1260366442" version_id="2.0.0rc11" />
		<hook module_id="music" hook_type="service" module="music" call_name="music.service_process_sponsorsong__end" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="music" hook_type="service" module="music" call_name="music.service_process_sponsoralbum__end" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="music" hook_type="service" module="music" call_name="music.service_callback_getnewsfeedsong_start" added="1286546859" version_id="2.0.7" />
		<hook module_id="music" hook_type="service" module="music" call_name="music.song_album_service_callback_getnewsfeed_start" added="1286546859" version_id="2.0.7" />
		<hook module_id="music" hook_type="template" module="music" call_name="music.template_block_menu" added="1286546859" version_id="2.0.7" />
		<hook module_id="music" hook_type="component" module="music" call_name="music.component_block_new_album_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="music" hook_type="controller" module="music" call_name="music.component_controller_frame_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="music" hook_type="controller" module="music" call_name="music.component_controller_upload_feed" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="music" hook_type="service" module="music" call_name="music.service_callback_getpagemenu" added="1323240479" version_id="3.0.0" />
		<hook module_id="music" hook_type="service" module="music" call_name="music.service_album_process_update__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="music" hook_type="service" module="music" call_name="music.service_album_process_deleteimage__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="music" hook_type="service" module="music" call_name="music.service_album_process_delete__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="music" hook_type="service" module="music" call_name="music.component_service_callback_getactivityfeedsong__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="music" hook_type="service" module="music" call_name="music.service_process_delete__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="music" hook_type="service" module="music" call_name="music.service_process_delete__2" added="1335951260" version_id="3.2.0" />
		<hook module_id="music" hook_type="service" module="music" call_name="music.service_process_update__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="music" hook_type="service" module="music" call_name="music.service_process_approve__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="music" hook_type="controller" module="music" call_name="music.component_controller_music_view" added="1395252715" version_id="3.7.6rc1" />
		<hook module_id="music" hook_type="controller" module="music" call_name="music.component_controller_music_index" added="1395252771" version_id="3.7.6rc1" />
	</hooks>
	<components>
		<component module_id="music" component="song" m_connection="" module="music" is_controller="0" is_block="1" is_active="1" />
		<component module_id="music" component="view" m_connection="music.view" module="music" is_controller="1" is_block="0" is_active="1" />
		<component module_id="music" component="view-album" m_connection="music.view-album" module="music" is_controller="1" is_block="0" is_active="1" />
		<component module_id="music" component="list" m_connection="" module="music" is_controller="0" is_block="1" is_active="1" />
		<component module_id="music" component="index" m_connection="music.index" module="music" is_controller="1" is_block="0" is_active="1" />
		<component module_id="music" component="browse" m_connection="music.browse" module="music" is_controller="1" is_block="0" is_active="1" />
		<component module_id="music" component="browse.song" m_connection="music.browse.song" module="music" is_controller="1" is_block="0" is_active="1" />
		<component module_id="music" component="browse.album" m_connection="music.browse.album" module="music" is_controller="1" is_block="0" is_active="1" />
		<component module_id="music" component="photo" m_connection="" module="music" is_controller="0" is_block="1" is_active="1" />
		<component module_id="music" component="featured" m_connection="" module="music" is_controller="0" is_block="1" is_active="1" />
		<component module_id="music" component="sponsored-song" m_connection="" module="music" is_controller="0" is_block="1" is_active="1" />
		<component module_id="music" component="sponsored-album" m_connection="" module="music" is_controller="0" is_block="1" is_active="1" />
		<component module_id="music" component="new-album" m_connection="" module="music" is_controller="0" is_block="1" is_active="1" />
		<component module_id="music" component="track" m_connection="" module="music" is_controller="0" is_block="1" is_active="1" />
		<component module_id="music" component="album" m_connection="music.album" module="music" is_controller="1" is_block="0" is_active="1" />
		<component module_id="music" component="tracklist" m_connection="" module="music" is_controller="0" is_block="1" is_active="1" />
		<component module_id="music" component="featured-album" m_connection="" module="music" is_controller="0" is_block="1" is_active="1" />
		<component module_id="music" component="profile" m_connection="music.profile" module="music" is_controller="1" is_block="0" is_active="1" />
	</components>
	<user_group_settings>
		<setting is_admin_setting="0" module_id="music" type="boolean" admin="1" user="1" guest="1" staff="1" module="music" ordering="0">can_upload_music_public</setting>
		<setting is_admin_setting="0" module_id="music" type="boolean" admin="1" user="1" guest="0" staff="1" module="music" ordering="0">can_add_comment_on_music_album</setting>
		<setting is_admin_setting="0" module_id="music" type="boolean" admin="1" user="1" guest="0" staff="1" module="music" ordering="0">can_add_comment_on_music_song</setting>
		<setting is_admin_setting="0" module_id="music" type="boolean" admin="1" user="0" guest="0" staff="1" module="music" ordering="0">can_edit_other_music_albums</setting>
		<setting is_admin_setting="0" module_id="music" type="boolean" admin="1" user="1" guest="0" staff="1" module="music" ordering="0">can_edit_own_albums</setting>
		<setting is_admin_setting="0" module_id="music" type="boolean" admin="1" user="1" guest="0" staff="1" module="music" ordering="0">can_delete_own_track</setting>
		<setting is_admin_setting="0" module_id="music" type="boolean" admin="1" user="0" guest="0" staff="1" module="music" ordering="0">can_delete_other_tracks</setting>
		<setting is_admin_setting="0" module_id="music" type="boolean" admin="1" user="1" guest="0" staff="1" module="music" ordering="0">can_delete_own_music_album</setting>
		<setting is_admin_setting="0" module_id="music" type="boolean" admin="1" user="0" guest="0" staff="1" module="music" ordering="0">can_delete_other_music_albums</setting>
		<setting is_admin_setting="0" module_id="music" type="integer" admin="10" user="8" guest="0" staff="10" module="music" ordering="0">music_max_file_size</setting>
		<setting is_admin_setting="0" module_id="music" type="boolean" admin="1" user="0" guest="0" staff="1" module="music" ordering="0">can_feature_songs</setting>
		<setting is_admin_setting="0" module_id="music" type="boolean" admin="1" user="0" guest="0" staff="1" module="music" ordering="0">can_approve_songs</setting>
		<setting is_admin_setting="0" module_id="music" type="boolean" admin="0" user="0" guest="0" staff="0" module="music" ordering="0">music_song_approval</setting>
		<setting is_admin_setting="0" module_id="music" type="boolean" admin="1" user="0" guest="0" staff="1" module="music" ordering="0">can_feature_music_albums</setting>
		<setting is_admin_setting="0" module_id="music" type="boolean" admin="1" user="1" guest="1" staff="1" module="music" ordering="0">can_access_music</setting>
		<setting is_admin_setting="0" module_id="music" type="boolean" admin="false" user="false" guest="false" staff="false" module="music" ordering="0">can_sponsor_song</setting>
		<setting is_admin_setting="0" module_id="music" type="boolean" admin="false" user="false" guest="false" staff="false" module="music" ordering="0">can_sponsor_album</setting>
		<setting is_admin_setting="0" module_id="music" type="boolean" admin="false" user="false" guest="false" staff="false" module="music" ordering="0">can_purchase_sponsor_album</setting>
		<setting is_admin_setting="0" module_id="music" type="boolean" admin="false" user="false" guest="false" staff="false" module="music" ordering="0">can_purchase_sponsor_song</setting>
		<setting is_admin_setting="0" module_id="music" type="string" admin="null" user="null" guest="null" staff="null" module="music" ordering="0">music_album_sponsor_price</setting>
		<setting is_admin_setting="0" module_id="music" type="string" admin="null" user="null" guest="null" staff="null" module="music" ordering="0">music_song_sponsor_price</setting>
		<setting is_admin_setting="0" module_id="music" type="boolean" admin="true" user="false" guest="false" staff="false" module="music" ordering="0">auto_publish_sponsored_album</setting>
		<setting is_admin_setting="0" module_id="music" type="boolean" admin="true" user="false" guest="false" staff="false" module="music" ordering="0">auto_publish_sponsored_song</setting>
		<setting is_admin_setting="0" module_id="music" type="boolean" admin="1" user="1" guest="0" staff="1" module="music" ordering="0">can_edit_own_song</setting>
		<setting is_admin_setting="0" module_id="music" type="boolean" admin="1" user="0" guest="0" staff="1" module="music" ordering="0">can_edit_other_song</setting>
		<setting is_admin_setting="0" module_id="music" type="integer" admin="1" user="1" guest="0" staff="1" module="music" ordering="0">points_music_song</setting>
	</user_group_settings>
	<tables><![CDATA[a:5:{s:18:"phpfox_music_album";a:3:{s:7:"COLUMNS";a:21:{s:8:"album_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"view_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"privacy";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:15:"privacy_comment";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:11:"is_featured";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"is_sponsor";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:4:"name";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:4:"year";a:4:{i:0;s:6:"CHAR:4";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"image_path";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"server_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:11:"total_track";a:4:{i:0;s:5:"USINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_play";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_comment";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_like";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_dislike";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:11:"total_score";a:4:{i:0;s:9:"DECIMAL:4";i:1;s:4:"0.00";i:2;s:0:"";i:3;s:2:"NO";}s:12:"total_rating";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"module_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:7:"item_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:8:"album_id";s:4:"KEYS";a:6:{s:7:"view_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"view_id";i:1;s:7:"privacy";}}s:9:"view_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"view_id";i:1;s:7:"privacy";i:2;s:11:"is_featured";}}s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"user_id";}s:9:"view_id_3";a:2:{i:0;s:5:"INDEX";i:1;a:5:{i:0;s:7:"view_id";i:1;s:7:"privacy";i:2;s:11:"total_track";i:3;s:9:"module_id";i:4;s:7:"item_id";}}s:9:"view_id_4";a:2:{i:0;s:5:"INDEX";i:1;a:4:{i:0;s:7:"view_id";i:1;s:7:"privacy";i:2;s:11:"total_track";i:3;s:7:"item_id";}}s:9:"view_id_5";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"view_id";i:1;s:7:"user_id";i:2;s:7:"item_id";}}}}s:23:"phpfox_music_album_text";a:2:{s:7:"COLUMNS";a:3:{s:8:"album_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:4:"text";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:11:"text_parsed";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:4:"KEYS";a:1:{s:8:"album_id";a:2:{i:0;s:6:"UNIQUE";i:1;s:8:"album_id";}}}s:18:"phpfox_music_genre";a:3:{s:7:"COLUMNS";a:7:{s:8:"genre_id";a:4:{i:0;s:5:"USINT";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:4:"name";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:5:"added";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:4:"used";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_active";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"1";i:2;s:0:"";i:3;s:2:"NO";}s:8:"ordering";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:8:"genre_id";s:4:"KEYS";a:1:{s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"user_id";}}}s:20:"phpfox_music_profile";a:3:{s:7:"COLUMNS";a:3:{s:7:"play_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"song_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:7:"play_id";s:4:"KEYS";a:1:{s:7:"song_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"song_id";i:1;s:7:"user_id";}}}}s:17:"phpfox_music_song";a:3:{s:7:"COLUMNS";a:25:{s:7:"song_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"view_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"privacy";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:15:"privacy_comment";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:11:"is_featured";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"is_sponsor";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"album_id";a:4:{i:0;s:4:"UINT";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"genre_id";a:4:{i:0;s:5:"USINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:5:"title";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"description";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"song_path";a:4:{i:0;s:8:"VCHAR:50";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"server_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"explicit";a:4:{i:0;s:6:"TINT:1";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"duration";a:4:{i:0;s:7:"VCHAR:5";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:8:"ordering";a:4:{i:0;s:6:"TINT:3";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_play";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_comment";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_like";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_dislike";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:11:"total_score";a:4:{i:0;s:9:"DECIMAL:4";i:1;s:4:"0.00";i:2;s:0:"";i:3;s:2:"NO";}s:12:"total_rating";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"module_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:7:"item_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:7:"song_id";s:4:"KEYS";a:6:{s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"user_id";}s:7:"view_id";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"view_id";i:1;s:7:"privacy";i:2;s:8:"genre_id";}}s:9:"view_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"view_id";i:1;s:7:"privacy";i:2;s:11:"is_featured";}}s:9:"view_id_4";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"view_id";i:1;s:7:"privacy";i:2;s:7:"user_id";}}s:9:"view_id_5";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"view_id";i:1;s:7:"privacy";i:2;s:5:"title";}}s:9:"view_id_6";a:2:{i:0;s:5:"INDEX";i:1;a:4:{i:0;s:7:"view_id";i:1;s:7:"privacy";i:2;s:9:"module_id";i:3;s:7:"item_id";}}}}}]]></tables>
    
	<install><![CDATA[
		$aGenres = array(
			'Hip Hop',
			'Rock',
			'Pop',
			'Alternative',
			'Country',
			'Indie',
			'Rap',
			'R&B',
			'Metal',
			'Punk',
			'Hardcore',
			'House',
			'Electronica',
			'Techno',
			'Reggae',
			'Latin',
			'Jazz',
			'Classic Rock',
			'Blues',
			'Folk',
			'Progressive',
		);

		foreach ($aGenres as $sName)
		{
			$this->database()->insert(Phpfox::getT('music_genre'), array('name' => $sName));
		}
	]]></install>
</module>
