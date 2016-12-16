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
 * @package  		Module_Page
 * @version 		$Id: add.class.php 2847 2011-08-19 07:47:27Z Raymond_Benc $
 */
class Page_Component_Controller_Admincp_Add extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{		
		$bIsEdit = false;
		$oSession = Phpfox::getLib('session');
		$aValidation = array(
			'product_id' => _p('select_product'),
			'title' => _p('missing_title'),
			'title_url' => _p('missing_url_title'),
			'is_active' => _p('specify_page_active'),
			'text' => _p('page_missing_data')
		);		

		$oValid = Phpfox_Validator::instance()->set(array('sFormName' => 'js_form', 'aParams' => $aValidation));
		
		if (($iPageId = $this->request()->getInt('id')) || ($iPageId = $this->request()->getInt('page_id')))
		{
			Phpfox::getUserParam('page.can_manage_custom_pages', true);
			
			$aPage = Page_Service_Page::instance()->getForEdit($iPageId);
			if (isset($aPage['page_id']))
			{
				$bIsEdit = true;
				if (Phpfox::isModule('tag'))
				{
					$aTags = Tag_Service_Tag::instance()->getTagsById('page', $aPage['page_id']);
					if (isset($aTags[$aPage['page_id']]))
					{
						$aPage['tag_list'] = '';					
						foreach ($aTags[$aPage['page_id']] as $aTag)
						{
							$aPage['tag_list'] .= ' ' . $aTag['tag_text'] . ',';	
						}
						$aPage['tag_list'] = trim(trim($aPage['tag_list'], ','));
					}
				}
					
				$this->template()->assign(array(
						'aForms' => $aPage,
						'aAccess' => (empty($aPage['disallow_access']) ? null : unserialize($aPage['disallow_access']))
					)
				);
			}
		}		
		
		if ($aVals = $this->request()->getArray('val'))
		{
			Phpfox::getLib('parse.input')->allowTitle(Phpfox::getLib('parse.input')->cleanTitle($aVals['title_url']), _p('invalid_title'));
			
			if ($oValid->isValid($aVals))
			{
				if ($bIsEdit)
				{
					$sMessage = _p('page_successfully_updated');
					$sReturn = Page_Service_Process::instance()->update($aPage['page_id'], $aVals, $aPage['user_id']);
					$aUrl = null;
				}
				else 
				{
					$sMessage = _p('successfully_added');
					$sReturn = Page_Service_Process::instance()->add($aVals);
					$aUrl = null;	
				}
				
				if ($sReturn)
				{
					return [
						'redirect' => $this->url()->makeUrl($sReturn, null, $sMessage)
					];
				}
			} else {
        $aError = Phpfox_Error::get();
        if (is_array($aError)){
          $sError = implode(' ', $aError);
        } else {
          $sError = $aError;
        }
        return [
          'error' => $sError
        ];
      }
		}		

		$this->template()
			->setSectionTitle('<a href="' . $this->url()->makeUrl('admincp.page') . '">'._p('custom_pages').'</a>')
			->setTitle(_p('add_new_page'))
			->setBreadCrumb(_p('add_new_page'))
			->assign(array(
					'aProducts' => Admincp_Service_Product_Product::instance()->get(),
					'aUserGroups' => User_Service_Group_Group::instance()->get(),
					'sCreateJs' => $oValid->createJS(),
					'sGetJsForm' => $oValid->getJsForm(),			
					'bIsEdit' => $bIsEdit,
					'aModules' => Phpfox_Module::instance()->getModules(),
					'bFormIsPosted' => (count($aVals) ? true : false)
				)
			)				
			->setEditor()
			->setHeader(array(
				'jquery/plugin/jquery.highlightFade.js' => 'static_script',
				'switch_menu.js' => 'static_script',	
				'<script type="text/javascript">var Attachment = {sCategory: "page", iItemId: "' . (isset($aPage['page_id']) ? $aPage['page_id'] : '') . '"};</script>'
			)
		);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('page.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
	}
}