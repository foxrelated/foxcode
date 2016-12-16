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
 * @package  		Module_Language
 * @version 		$Id: delete.class.php 6136 2013-06-24 12:28:43Z Miguel_Espinoza $
 */
class Language_Component_Controller_Admincp_Delete extends Phpfox_Component 
{
	/**
	 * Controller
	 */
	public function process()
	{	
		Phpfox::getUserParam('language.can_manage_lang_packs', true);
		
		if ($this->request()->get('no'))
		{
			$this->url()->send('admincp', 'language');
		}		
		
		$iId = $this->request()->get('id');
		
		if (!$iId)
		{
			return Phpfox_Error::display(_p('invalid_language'));	
		}
		
		$aLanguage = Language_Service_Language::instance()->getLanguage($iId);
		
		if (!isset($aLanguage['language_id']))
		{
			return Phpfox_Error::display(_p('invalid_language_package'));
		}
		
		if ($this->request()->get('yes'))
		{
			if (Language_Service_Process::instance()->delete($iId))
			{
				$this->url()->send('admincp', 'language', _p('language_package_successfully_deleted'));
			}
		}
		
		$this->template()->assign(array(
			'aLanguage' => $aLanguage
		))->setTitle(_p('manage_language_packages'))
			->setTitle(_p('delete'))
			->setBreadCrumb(_p('manage_language_packages'))
			->setBreadCrumb(_p('delete'));
        return null;
	}
}