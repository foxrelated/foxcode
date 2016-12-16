<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Admincp_Component_Controller_Apps_index
 */
class Admincp_Component_Controller_Apps_index extends Phpfox_Component
{
	public function process()
    {
		$Apps = new Core\App();

		if (($token = $this->request()->get('m9token'))) {
			$response = (new Core\Home(PHPFOX_LICENSE_ID, PHPFOX_LICENSE_KEY))->token(['token' => $token]);
			if ($response->token) {
				$file = PHPFOX_DIR_SETTINGS . 'license.sett.php';
				$content = file_get_contents($file);
				$content = preg_replace('!define\(\'PHPFOX_LICENSE_ID\', \'(.*?)\'\);!s', 'define(\'PHPFOX_LICENSE_ID\', \'techie_' . $this->request()->get('m9id') . '\');', $content);
				$content = preg_replace('!define\(\'PHPFOX_LICENSE_KEY\', \'(.*?)\'\);!s', 'define(\'PHPFOX_LICENSE_KEY\', \'techie_' . $this->request()->get('m9key') . '\');', $content);

				file_put_contents($file, $content);

				$this->template()->assign('vendorCreated', true);
			}
		}

		$menu = [];
		if (defined('PHPFOX_IS_TECHIE') && PHPFOX_IS_TECHIE) {
			$menu['Import Module'] = [
				'url' => $this->url()->makeUrl('admincp.upload'),
				'class' => 'popup light'
			];

			$menu['New App'] = [
				'url' => $this->url()->makeUrl('admincp.app.add'),
				'class' => 'popup light'
			];
		}


		$menu[_p('Purchase History')] = [
			'url' => $this->url()->makeUrl('admincp.store.orders'),
			'class' => 'light'
		];

		$menu[_p('find_more_apps')] = [
			'url' => $this->url()->makeUrl('admincp.store', ['load' => 'apps']),
			'class' => ''
		];
		$this->template()->setActionMenu($menu);

		$allApps = $Apps->getForManage();
        $newInstalls = [];
        if (!defined('PHPFOX_TRIAL_MODE')) {
            $Home = new Core\Home(PHPFOX_LICENSE_ID, PHPFOX_LICENSE_KEY);
            $products = $Home->downloads(['type' => 0]);
            if (is_object($products)) {
                foreach ($products as $product) {
                    foreach ($allApps as $app) {
                        if (isset($app->internal_id) && isset($product->id) && $app->internal_id == $product->id) {
                            continue 2;
                        }
                    }

                    $newInstalls[] = (array)$product;
                }
            }
        }

        $appIdList =  array_map(function($item) {
            return ($item->is_phpfox_default) ? null : $item->id;
        }, $allApps);
        foreach ($appIdList as $keyApp => $value) {
            if (!isset($value) || empty($value)) {
                unset($appIdList[$keyApp]);
            }
        }
        $sendData =  ['apps'=> $appIdList];

        $response =  [];
        if (count($appIdList)) {
            $Home = new Core\Home(PHPFOX_LICENSE_ID, PHPFOX_LICENSE_KEY);
            $response = $Home->products(['products' => $sendData]);
        }
        foreach($allApps as $index=>$app){
            $id =  $app->id;
            if(isset($response->products) && isset($response->products->apps) && isset($response->products->apps->$id) && isset($response->products->apps->$id->version)){
                $app->latest_version = $response->products->apps->$id->version;
                if (version_compare($app->version, $app->latest_version, '<') && isset($response->products->apps->$id->link)) {
                    $allApps[$index]->have_new_version = $response->products->apps->$id->link;
                }
                else {
                    $allApps[$index]->have_new_version = false;
                }
            }else{
                $app->latest_version =  _p('n_a');
                $allApps[$index]->have_new_version = false;
            }
        }
        $warnings = [];

        if(!class_exists('ZipArchive')){
            $warnings[] = '<a href="http://php.net/manual/en/class.ziparchive.php" target="_blank">PHP ZipArchive</a> is required to install/update apps. <a href="http://support.phpfox.com/getting-started/requirements/" target="_blank">See phpFox requirements.</a>';
        }


		(($sPlugin = Phpfox_Plugin::get('admincp.component_controller_controller_apps_end')) ? eval($sPlugin) : false);
		$this->template()->setSectionTitle(_p('apps'))
          ->setBreadCrumb(_p('Manage Apps'))
          ->assign([
             'warning'=>implode('<br />', $warnings),
            'apps' => $allApps,
            'newInstalls' => $newInstalls,
		]);
	}
}