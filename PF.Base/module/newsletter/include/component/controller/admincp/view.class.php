<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package  		Module_Newsletter
 * @version 		$Id: view.class.php 1168 2009-10-09 14:20:37Z Raymond_Benc $
 */

class Newsletter_Component_Controller_Admincp_View extends Phpfox_Component
{
	public function process()
	{
		$iId = $this->request()->getInt('id', 0);
		if (!$iId)
		{
			$this->url()->send('admincp.newsletter', null, _p('that_newsletter_does_not_exist'));
		}
		$sMode = $this->request()->get('mode', 'html');
		if ($sMode != 'html' && $sMode != 'plain')
		{
			$this->url()->send('newsletter', null, _p('please_choose_either_html_or_plain_text'));
		}
		$aNewsletter = Newsletter_Service_Newsletter::instance()->get($iId);
		if (!$aNewsletter)
		{
			$this->url()->send('admincp.newsletter', null, _p('that_newsletter_does_not_exist'));
		}
		$aNewsletter['mode'] = $sMode;

		$this->template()->errorClearAll();
		$this->template()
			->setTitle($aNewsletter['subject'])
			->setTemplate('blank')
			->assign(array(
				'aNewsletter' => $aNewsletter,
			))
		;
	}
}