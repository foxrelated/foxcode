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
 * @version 		$Id: index.class.php 2831 2011-08-12 19:44:19Z Raymond_Benc $
 */
class Admincp_Component_Controller_Block_Index extends Phpfox_Component 
{
	/**
	 * Controller
	 */
	public function process()
	{	
		if ($iDeleteId = $this->request()->getInt('delete'))
		{
			if (Admincp_Service_Block_Process::instance()->delete($iDeleteId))
			{
				$this->url()->send('admincp.block', null, _p('successfully_deleted'));
			}
		}
		
		if ($aVals = $this->request()->getArray('val'))
		{
			if (Admincp_Service_Block_Process::instance()->updateOrder($aVals))
			{
				$this->url()->send('admincp.block');	
			}			
		}
		
		$aBlocks = array();
		$aRows = Admincp_Service_Block_Block::instance()->get();
		
		foreach ($aRows as $iKey => $aRow)
		{
			if (!Phpfox::isModule($aRow['module_id']))
			{
				continue;
			}

			$aBlocks[$aRow['m_connection']][$aRow['location']][] = $aRow;
		}

		ksort($aBlocks);
				
		$this->template()
			->setSectionTitle(_p('blocks'))
			->setActionMenu([
				_p('add_block') => [
					'class' => 'popup',
					'url' => $this->url()->makeUrl('admincp.block.add')
				]
			])
			->setBreadCrumb(_p('block_manager'))
			->setTitle(_p('block_manager'))
			->setHeader('cache', array(
					'template.css' => 'style_css',
					'drag.js' => 'static_script',
					'jquery/plugin/jquery.scrollTo.js' => 'static_script'
				)
			)
			->assign(array(
				'aBlocks' => $aBlocks
			));
	}
}