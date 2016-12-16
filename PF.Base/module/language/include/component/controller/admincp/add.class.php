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
 * @package 		Phpfox_Component
 * @version 		$Id: controller.class.php 103 2009-01-27 11:32:36Z Raymond_Benc $
 */
class Language_Component_Controller_Admincp_Add extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$bIsEdit = false;
		if (($sLangId = $this->request()->get('id')))
		{
			if (($aLanguage = Language_Service_Language::instance()->getLanguage($sLangId)) && isset($aLanguage['language_id']))
			{
				$bIsEdit = true;
				$this->template()->assign(array(
						'aForms' => $aLanguage
					)
				);
			}
		}
		
		if (($sLanguageId = $this->request()->get('import-phrase')))
		{
			$iPage = $this->request()->getInt('page', 0);
			$iLimit = 500;
			
			$iCnt = Language_Service_Phrase_Process::instance()->importPhrases($sLanguageId, $iPage, $iLimit);
			
			Phpfox_Pager::instance()->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $iCnt));
			
			$iTotalPages = (int) Phpfox_Pager::instance()->getTotalPages();
			$iCurrentPage = (int) Phpfox_Pager::instance()->getCurrentPage();
			$iPage = (int) Phpfox_Pager::instance()->getNextPage();
			
			if ($iTotalPages === $iCurrentPage || $iTotalPages === 0)
			{
				$this->url()->send('admincp.language', null, _p('language_package_successfully_added'));
			}			
			
			$this->template()
				->setHeader('<meta http-equiv="refresh" content="2;url=' . $this->url()->makeUrl('admincp.language.add', array('import-phrase' => $sLanguageId, 'page' => $iPage)) . '">')
				->assign(array(
					'bImportingPhrases' => true,
					'iCurrentPage' => $iCurrentPage,
					'iTotalPages' => $iTotalPages
				)
			);						
		}
		else 
		{
			if (($aVals = $this->request()->getArray('val')))
			{
				if ($bIsEdit)
				{
					if (Language_Service_Process::instance()->update($sLangId, $aVals))
					{
						$this->url()->send('admincp.language.add', array('id' => $sLangId), _p('language_package_successfully_updated'));
					}
				}
				else 
				{
					if (($sLanguageId = Language_Service_Process::instance()->add($aVals)))
					{
						$this->url()->send('admincp.language.add', array('import-phrase' => $sLanguageId));
					}
				}
			}		
		}
		
		$this->template()->setTitle(($bIsEdit ? _p('editing_language_package') . ': ' . $aLanguage['title'] : _p('create_a_new_language_package')))
			->setBreadCrumb(($bIsEdit ? _p('editing_language_package') . ': ' . $aLanguage['title'] : _p('create_language_package')), $this->url()->current(), true)
			->assign(array(
					'aLanguages' => Language_Service_Language::instance()->getAll(),
					'bIsEdit' => $bIsEdit
				)
			);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('language.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
	}
}

?>