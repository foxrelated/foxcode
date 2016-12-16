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
 * @package  		Module_Theme
 * @version 		$Id: index.class.php 1179 2009-10-12 13:56:40Z Raymond_Benc $
 */
class Theme_Component_Controller_Admincp_Index extends Phpfox_Component {
	public function process()
	{
		if (($iDeleteId = $this->request()->getInt('delete')))
		{
			if (Theme_Service_Process::instance()->delete($iDeleteId))
			{
				$this->url()->send('admincp.theme', null, _p('theme_successfully_deleted'));
			}
		}

		$themes = [];
		$default = [];
		$rows = $this->template()->theme()->all();
		foreach ($rows as $row) {
			if ($row->is_default) {
				$default = $row;

				continue;
			}

			$themes[] = $row;
		}

		if ($default) {
			$themes = array_merge([$default], $themes);
		}

        $newInstalls = [];
        if (!defined('PHPFOX_TRIAL_MODE')) {
            $Home = new Core\Home(PHPFOX_LICENSE_ID, PHPFOX_LICENSE_KEY);
            $products = $Home->downloads(['type' => 2]);
            if (is_object($products)) {
                foreach ($products as $product) {
                    foreach ($themes as $theme) {
                        if (isset($theme->internal_id) && isset($product->id) && $theme->internal_id == $product->id) {
                            continue 2;
                        }
                    }

                    $newInstalls[] = (array)$product;
                }
            }
        }

		(($sPlugin = Phpfox_Plugin::get('theme.component_controller_admincp_index')) ? eval($sPlugin) : false);

		$this->template()->setTitle(_p('themes'))
			->setSectionTitle(_p('themes'))
			->setActionMenu([
                _p('create_new_theme') => [
						'url' => $this->url()->makeUrl('admincp.theme.add'),
						'class' => 'popup light'
					],
				_p('find_more_themes') => [
					'url' => $this->url()->makeUrl('admincp.store', ['load' => 'themes']),
					'class' => ''
				]
			])
			->setBreadCrumb(_p('themes'), $this->url()->makeUrl('admincp.theme'))
			->assign(array(
					'newInstalls' => $newInstalls,
					'themes' => $themes
				)
			);
	}
}