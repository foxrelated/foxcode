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
 * @version 		$Id: index.class.php 1522 2010-03-11 17:56:49Z Miguel_Espinoza $
 */
class Report_Component_Controller_Admincp_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		if ($iId = $this->request()->getInt('view'))
		{
			if ($sRedirect = Report_Service_Report::instance()->getRedirect($iId))
			{
				$this->url()->forward($sRedirect);	
			}
		}
		
		if ($aIds = $this->request()->getArray('id'))
		{
			if ($this->request()->get('ignore'))
			{
				foreach ($aIds as $iId)
				{
					if (!is_numeric($iId))
					{
						continue;
					}
                    
                    Report_Service_Data_Process::instance()->ignore($iId);
				}
				
				$this->url()->send('admincp.report', null, _p('report_s_successfully_ignored'));
			}
		}
		
		$iPage = $this->request()->getInt('page');
		
		$aPages = array(5, 10, 15, 20);
		$aDisplays = array();
		foreach ($aPages as $iPageCnt)
		{
			$aDisplays[$iPageCnt] = _p('per_page', array('total' => $iPageCnt));
		}		
		
		$aSorts = array(
			'added' => _p('time')
		);
		
		$aFilters = array(
			'search' => array(
				'type' => 'input:text',
				'search' => "AND c.name LIKE '%[VALUE]%'"
			),	
			'user' => array(
				'type' => 'input:text',
				'search' => "AND u.user_name LIKE '%[VALUE]%'"
			),						
			'display' => array(
				'type' => 'select',
				'options' => $aDisplays,
				'default' => '10'
			),
			'sort' => array(
				'type' => 'select',
				'options' => $aSorts,
				'default' => 'added',
				'alias' => 'rd'
			),
			'sort_by' => array(
				'type' => 'select',
				'options' => array(
					'DESC' => _p('descending'),
					'ASC' => _p('ascending')
				),
				'default' => 'DESC'
			)
		);		
		
		$oSearch = Phpfox_Search::instance()->set(array(
				'type' => 'reports',
				'filters' => $aFilters,
				'search' => 'search'
			)
		);
		
		$iLimit = $oSearch->getDisplay();
		
		list($iCnt, $aReports) = Report_Service_Report::instance()->get($oSearch->getConditions(), $oSearch->getSort(), $oSearch->getPage(), $iLimit);
		
		Phpfox_Pager::instance()->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $oSearch->getSearchTotal($iCnt)));
		
		$this->template()->setTitle(_p('reports'))
			->setBreadCrumb(_p('reports'), $this->url()->makeUrl('admincp.report'))
			->assign(array(
					'aReports' => $aReports
				)
			);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('report.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
	}
}