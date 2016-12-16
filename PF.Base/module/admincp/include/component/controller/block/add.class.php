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
 * @package  		Module_Admincp
 * @version 		$Id: add.class.php 3342 2011-10-21 12:59:32Z Raymond_Benc $
 */
class Admincp_Component_Controller_Block_Add extends Phpfox_Component 
{
	/**
	 * Controller
	 */
	public function process()
	{		
		Phpfox::getUserParam('admincp.can_add_new_block', true);
		$bIsEdit = false;
				
		if (($iEditId = $this->request()->getInt('id')) || ($iEditId = $this->request()->getInt('block_id')))
		{
			$aRow = Admincp_Service_Block_Block::instance()->getForEdit($iEditId);
			$bIsEdit = true;
			
			$this->template()->assign(array(
					'aForms' => $aRow,
					'aAccess' => (empty($aRow['disallow_access']) ? null : unserialize($aRow['disallow_access']))
				)
			);			
		}		
		
		$aValidation = array(
			'product_id' => _p('select_product'),
			'location' => _p('select_block_placement'),
			'is_active' => _p('specify_block_active')
		);		
		
		$oValid = Phpfox_Validator::instance()->set(array('sFormName' => 'js_form', 'aParams' => $aValidation));
		
		if ($aVals = $this->request()->getArray('val'))
		{			
			if ($oValid->isValid($aVals))
			{
				if ($bIsEdit)
				{
					$sMessage = _p('successfully_updated');
					$aUrl = array('block', 'add', 'id' => $aRow['block_id']);
                    Admincp_Service_Block_Process::instance()->update($aRow['block_id'], $aVals);
				}
				else 
				{
					$sMessage = _p('block_successfully_added');
					$aUrl = array('block');
                    Admincp_Service_Block_Process::instance()->add($aVals);
				}				
				
				$this->url()->send('admincp', $aUrl, $sMessage);
			}
		}
		
		if (Phpfox::getParam('core.enabled_edit_area'))
		{
			$this->template()->setHeader(array(
					'editarea/edit_area_full.js' => 'static_script',
					'<script type="text/javascript">				
						editAreaLoader.init({
							id: "source_code"	
							,start_highlight: true
							,allow_resize: "both"
							,allow_toggle: false
							,word_wrap: false
							,language: "en"
							,syntax: "php"
						});		
					</script>'
				)
			);
		}
		
		$aStyles = Theme_Service_Style_Style::instance()->getStyles();
		if ($bIsEdit)
		{		
			foreach ($aStyles as $iKey => $aStyle)
			{
				if (isset($aRow['style_id']) && isset($aRow['style_id'][$aStyle['style_id']]))
				{
					$aStyles[$iKey]['block_is_selected'] = $aRow['style_id'][$aStyle['style_id']];
				}
			}
		}
		
		$this->template()->assign(array(
            'aProducts' => Admincp_Service_Product_Product::instance()->get(),
            'aControllers' => Admincp_Service_Component_Component::instance()->get(true),
            'aComponents' => Admincp_Service_Component_Component::instance()->get(),
            'aUserGroups' => User_Service_Group_Group::instance()->get(),
            'sCreateJs' => $oValid->createJS(),
            'sGetJsForm' => $oValid->getJsForm(),
            'bIsEdit' => $bIsEdit,
            'aStyles' => $aStyles
        ))
			->setTitle(_p('block_manager'))
			->setBreadCrumb(_p('block_manager'), $this->url()->makeUrl('admincp.block'))
			->setBreadCrumb(($bIsEdit ? _p('editing') . ': ' . (empty($aRow['m_connection']) ? _p('site_wide') : $aRow['m_connection']) . (empty($aRow['component']) ? '' : '::' . rtrim(str_replace('|', '::', $aRow['component']), '::')) . (empty($aRow['title']) ? '' : ' (' . Phpfox_Locale::instance()->convert($aRow['title']) . ')') : _p('add_new_block')), $this->url()->makeUrl('admincp.block.add'), true)
			->setTitle(_p('add_new_block'));
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('admincp.component_controller_block_add_clean')) ? eval($sPlugin) : false);
	}
}