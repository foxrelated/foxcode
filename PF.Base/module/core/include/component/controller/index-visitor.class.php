<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Core
 * @version 		$Id: index-visitor.class.php 6754 2013-10-09 10:17:09Z Miguel_Espinoza $
 */
class Core_Component_Controller_Index_Visitor extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
	    if ($sPlugin = Phpfox_Plugin::get('core.component_controller_index_visitor_start'))
	    {
			eval($sPlugin);
	    }

		$image = [];
		list($total, $featured) = Photo_Service_Photo::instance()->getFeatured();
		if (is_array($featured) && isset($featured[0])) {
			$photo = $featured[0];
			$url = Phpfox_Image_Helper::instance()->display([
				'server_id' => $photo['server_id'],
				'path' => 'photo.url_photo',
				'file' => $photo['destination'],
				'suffix' => '_1024',
				'return_url' => true
			]);
			$image = [
				'image' => $url,
				'info' => strip_tags($photo['title']) . ' by ' . $photo['full_name']
			];
		}

		if (!$image) {
			$images = [
				'create-a-community-for-musicians.jpg' => _p('creating_communities_for_musicians'),
				'create-a-community-for-athletes.jpg' => _p('creating_communities_for_athletes'),
				'create-a-community-for-photographers.jpg' => _p('creating_communities_for_photographers'),
				'create-a-social-network-for-fine-cooking.jpg' => _p('creating_communities_for_fine_cooking')
			];
			$total = rand(1, (count($images)));
			$image = [];
			$cnt = 0;
			foreach ($images as $image => $info) {
				$cnt++;
				$image = [
					'image' => '//dvpydu2i4ja5m.cloudfront.net/' . $image,
					'info' => $info
				];
				if ($cnt === $total) {
					break;
				}
			}
		}

		$content = '';
		if ($sPlugin = Phpfox_Plugin::get('core.component_controller_index_visitor_end'))
		{
			eval($sPlugin);
		}

		$this->template()->setHeader('cache', array(
					'register.js' => 'module_user',
					'country.js' => 'module_core',
				)
			)
			->setBreadCrumb(Phpfox::getParam('core.site_title'))
			->setPhrase(array(
					'continue'
				)
			)->assign(array(
				'aSettings' => Custom_Service_Custom::instance()->getForEdit(array('user_main', 'user_panel', 'profile_panel'), null, null, true),
					'image' => $image,
					'content' => $content
			)
		);	
	}
}