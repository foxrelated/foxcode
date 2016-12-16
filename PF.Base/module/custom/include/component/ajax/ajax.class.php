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
 * @package 		Phpfox_Ajax
 * @version 		$Id: ajax.class.php 7288 2014-04-28 18:08:00Z Fern $
 */
class Custom_Component_Ajax_Ajax extends Phpfox_Ajax
{
	public function edit()
	{	
		if (($sContent = Custom_Service_Custom::instance()->getFieldForEdit($this->get('field_id'), $this->get('item_id'), $this->get('edit_user_id'))))
		{		
			$this->call('$(\'#js_custom_field_' . $this->get('field_id') . '\').html(\'' . str_replace(array("'", '<br />'), array("\'", "\n"), $sContent) . '\');')
				->show('#js_custom_field_' . $this->get('field_id'));
			(($sPlugin = Phpfox_Plugin::get('custom.component_ajax_edit')) ? eval($sPlugin) : false);
		}
	}
	
	public function update()
	{
		if (($sContent = Custom_Service_Process::instance()->updateField($this->get('field_id'), $this->get('item_id'), $this->get('edit_user_id'), $this->get('custom_field_value'))))
		{
			$this->hide('#js_custom_field_' . $this->get('field_id'))
				->html('#js_custom_content_' . $this->get('field_id'), $sContent)
				->show('#js_custom_content_' . $this->get('field_id'));			
		}
		else 
		{
			$this->call('$(\'#js_custom_field_' . $this->get('field_id') . '\').parents(\'.block:first\').remove();');			
		}
	}
	
	public function addGroup()
	{
		if (($iId = Custom_Service_Group_Process::instance()->add($this->get('val'))) && ($aGroup = Custom_Service_Group_Group::instance()->getGroup($iId)))
		{			
			$this->append('#js_group_listing', '<option value="' . $aGroup['group_id'] . '" selected="selected">' . _p($aGroup['phrase_var_name']) . '</option>')
				->hide('#js_group_holder')
				->show('#js_field_holder');
		}
	}
	
	public function toggleActiveGroup()
	{
		if (Custom_Service_Group_Process::instance()->toggleActivity($this->get('id')))
		{
			$this->call('$Core.custom.toggleGroupActivity(' . $this->get('id') . ')');
		}		
	}
	
	public function toggleActiveField()
	{
		if (Custom_Service_Process::instance()->toggleActivity($this->get('id')))
		{
			$this->call('$Core.custom.toggleFieldActivity(' . $this->get('id') . ')');
		}
	}
	
	public function deleteField()
	{
		if (Custom_Service_Process::instance()->delete($this->get('id')))
		{
			$this->call('$(\'#js_field_' . $this->get('id') . '\').parents(\'li:first\').remove();');
		}
	}
	
	public function deleteOption()
	{
		if (Custom_Service_Process::instance()->deleteOption($this->get('id')))
		{
			$this->call('$(\'#js_current_value_' . $this->get('id') . '\').remove();');
		}
		else
		{
		    $this->alert(_p('could_not_delete'));
		}
	}
	
	public function updateFields()
	{
        define('NO_TWO_FEEDS_THIS_ACTION', true);
		$aVals = $this->get('custom');
		if (empty($aVals))
		{
			$aVals = $this->get('val');
		}
		if (!(empty($aVals)))
		{
			$aCustomFields = Custom_Service_Custom::instance()->getForEdit(array('user_main', 'user_panel', 'profile_panel'), Phpfox::getUserId(), Phpfox::getUserBy('user_group_id'), false, Phpfox::getUserId());
			foreach ($aCustomFields as $aCustomField)
			{				
				if (empty($aVals[$aCustomField['field_id']]) && $aCustomField['is_required'])
				{
					Phpfox_Error::set(_p('the_field_field_is_required', array('field' => _p($aCustomField['phrase_var_name']))));
				}
				else if ((!isset($aVals[$aCustomField['field_id']]) || empty($aVals[$aCustomField['field_id']])) && !$aCustomField['is_required'])
				{
                    Custom_Service_Process::instance()->updateField($aCustomField, Phpfox::getUserId(), Phpfox::getUserId(),'');
				}
			}			
			
            if ($sPlugin = Phpfox_Plugin::get('custom.component_ajax_updatefields__1')){eval($sPlugin);if (isset($aPluginReturn)){return $aPluginReturn;}}
			if (Phpfox_Error::isPassed())
			{
				$bReturnCustom = Custom_Service_Process::instance()->updateFields(Phpfox::getUserId(), Phpfox::getUserId(), $aVals);
				$aUser = $this->get('val');
				$aUser['language_id'] = Phpfox::getUserBy('language_id');
				define('PHPFOX_IS_CUSTOM_FIELD_UPDATE', true);

                if (Phpfox::getParam('user.require_basic_field')){
                    $aUserFieldsRequired =
                        array(
                            'location' => array('user.location' => $aUser['country_iso']),
                            'day' => array('user.date_of_birth' => $aUser['day']),
                            'month' => array('user.date_of_birth' => $aUser['month']),
                            'year' => array('user.date_of_birth' => $aUser['year'])
                        );
                    if (Phpfox::getUserParam('user.can_edit_gender_setting')){
                        $aUserFieldsRequired['gender'] = ['user.gender' => (isset($aUser['gender']) ? $aUser['gender'] : '')];
                    }

                    foreach($aUserFieldsRequired as $aFieldRequired)
                    {
                        foreach($aFieldRequired as $sLangId => $mValue)
                        {
                            if(empty($mValue))
                            {
                                Phpfox_Error::set(_p('the_field_field_is_required', array('field' => _p($sLangId))) . " ");
                            }
                        }
                    }
                }
                $month = isset($aUser['month']) ? (int) $aUser['month'] : 0;
                $day = isset($aUser['day']) ? (int) $aUser['day'] : 0;
                $year = isset($aUser['year']) ? (int) $aUser['year'] : 0;
                if ($month && $day && $year && !checkdate($month, $day, $year)){
                    Phpfox_Error::set(_p('Not a valid date'));
                }
				$bReturnUser = false;
				if(Phpfox_Error::isPassed())
				{
					$bReturnUser = User_Service_Process::instance()->update(Phpfox::getUserId(), $aUser);
				}

				if ($bReturnCustom && $bReturnUser)
				{
					$this->call('$(\'#public_message\').html(\''. _p('profile_successfully_updated').'\'); $Core.processingEnd(); $Core.loadInit();');
                    $this->call('$("#relation").val('. $aUser['relation'] .');');
					return true;
				}
			}
			$this->call('$(\'#js_custom_submit_button\').attr(\'disabled\', false).removeClass(\'disabled\'); $Core.processingEnd();');
			
		}
        return null;
	}
	
	public function processRelationship()
	{
		Phpfox::isUser(true);
		
		$aRelationship = Custom_Service_Relation_Relation::instance()->getDataById($this->get('relation_data_id'));
		
		if (isset($aRelationship['with_user_id']) && $aRelationship['with_user_id'] == Phpfox::getUserId())
		{
			if ($this->get('type') == 'accept')
			{
				Custom_Service_Relation_Process::instance()->updateRelationship(0, $aRelationship['user_id'], $aRelationship['with_user_id']);
				$this->remove('#drop_down_' . $this->get('request_id'));
			}
			else
			{
                Custom_Service_Relation_Process::instance()->denyStatus($this->get('relation_data_id'), $aRelationship['with_user_id']);
                if (Phpfox::isModule('friend')){
                    Friend_Service_Request_Process::instance()->delete($this->get('request_id'), $aRelationship['user_id']);
                }
				$this->remove('#drop_down_' . $this->get('request_id'));
			}
		}
		else if (empty($aRelationship))
		{
            Custom_Service_Relation_Process::instance()->checkRequest($this->get('relation_data_id'));
			$this->remove('#drop_down_' . $this->get('request_id'));
		}
	}
}