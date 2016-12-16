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
 * @package 		Phpfox_Component
 * @version 		$Id: add.class.php 3860 2012-01-19 11:58:49Z Raymond_Benc $
 */
class Newsletter_Component_Controller_Admincp_Add extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		// When they first submit the newsletter this block adds it to the ongoing or scheduling
		if ($aVals = $this->request()->getArray('val'))
		{			
			$aNewsletter = Newsletter_Service_Process::instance()->add($aVals, Phpfox::getUserId());
			if ($aNewsletter['state'] == 1)
			{
				$this->url()->send('admincp.newsletter.add', array('job' => $aNewsletter['newsletter_id']), _p('processing_job_newsletter_id', array('newsletter_id' => $aNewsletter['newsletter_id'])));
			}
			elseif ($aNewsletter === false)
			{
			}
			else
			{
				$this->url()->send('admincp.newsletter', null, null);
			}
		}
		// when refreshed by the flow we should get an integer here pointing to the pending job
		elseif ($iJob = $this->request()->getInt('job'))
		{
			list($iContinue,$iPerc) = Newsletter_Service_Process::instance()->processJob($iJob);
			if (is_int($iContinue) && $iPerc < 100)
			{
				$sMessage = _p('5_seconds_break_processing_job_continue_total_completed_perc', array('continue' => $iContinue, 'perc' => $iPerc));
				$sLink = $this->url()->makeUrl('admincp.newsletter.add', array('job' => $iContinue));
				$this->template()->setHeader('<META HTTP-EQUIV="refresh" content="5;URL='.$sLink.'">')
					->assign(array('sMessage' => $sMessage));
			}
			elseif ($iContinue === true || $iPerc >= 100) // completed successfully
			{
				$this->url()->send('admincp.newsletter', null, _p('job_completed_successfully'));
			}
			elseif ($iContinue === false)
			{
				$this->url()->send('admincp.newsletter', null, _p('there_was_a_problem_with_this_job_feel_free_to_resume_it_at_any_time'));
			}
		}
		if ($iId = $this->request()->getInt('id') || $iId = $this->request()->getInt('job'))
		{
			$aNewsletter = Newsletter_Service_Newsletter::instance()->get($iId);
			$this->template()->assign(array(
					'aForms' => $aNewsletter
				)
			);
		}
		$aValidation = array(
			'type_id' => array(
				'title' => _p('select_a_newsletter_type'),
				'def' => 'int'
			),
		);

		// 2 = html; 1 = plain text;
		$oValidator = Phpfox_Validator::instance()->set(array('sFormName' => 'js_form', 'aParams' => $aValidation));
		$aAge = array();
		for ($i = 18; $i <= 68; $i++)
		{
			$aAge[$i] = $i;
		}
		$this->template()->assign(array(
				'aAge' => $aAge,
				'aUserGroups' => User_Service_Group_Group::instance()->get(),
				'sCreateJs' => $oValidator->createJS(),
				'sGetJsForm' => $oValidator->getJsForm()
			)
		)
            ->setEditor([])
		->setTitle(_p('newsletter'))
        ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
		->setBreadCrumb(_p('newsletter'),  $this->url()->makeUrl('admincp.newsletter'))
		->setBreadCrumb(_p('add_newsletter'), null, true)
		->setPhrase(array(
				'min_age_cannot_be_higher_than_max_age',
				'max_age_cannot_be_lower_than_the_min_age'
			)
		)		
		->setHeader(array('add.js' => 'module_newsletter'));
	}
}