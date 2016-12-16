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
 * @package  		Module_Attachment
 * @version 		$Id: callback.class.php 1496 2010-03-05 17:15:05Z Raymond_Benc $
 */
class Attachment_Service_Callback extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('attachment');
	}

	/**
     * Action to take when user cancelled their account
	 * @param int $iUser
	 */
	public function onDeleteUser($iUser)
	{
		$aAttachments = $this->database()
			->select('attachment_id')
			->from($this->_sTable)
			->where('user_id = ' . (int)$iUser)
			->execute('getSlaveRows');
		foreach ($aAttachments as $aAttach)
		{
            Attachment_Service_Process::instance()->delete($iUser, $aAttach['attachment_id']);
		}
	}
    
    /**
     * @return array
     */
	public function getDashboardActivity()
	{
		$aUser = User_Service_User::instance()->get(Phpfox::getUserId(), true);
		
		return array(
			_p('attachment_activity') => $aUser['activity_attachment']
		);
	}
    
    /**
     * @return array
     */
	public function getActivityPointField()
	{
		return array(
			_p('attachments_activity') => 'activity_attachment'
		);
	}
    
    /**
     * @return string a string to parse url
     */
    public function getProfileLink()
    {
        return 'profile.attachment';
    }
    
    /**
     * @param array $aUser
     *
     * @return array|bool
     */
    public function getProfileMenu($aUser)
    {
        //Can view my attachments only
        if ($aUser['user_id'] != Phpfox::getUserId()){
            return false;
        }
        if (!Phpfox::getParam('profile.show_empty_tabs')) {
            if (!isset($aUser['activity_attachment'])) {
                return false;
            }

            if (isset($aUser['activity_attachment']) && (int)$aUser['activity_attachment'] === 0) {
                return false;
            }
        }

        $aMenus[] = [
            'phrase' => _p('attachments_activity'),
            'url'    => 'profile.attachment',
            'total'  => (int)(isset($aUser['activity_attachment']) ? $aUser['activity_attachment'] : 0),
            'icon'   => 'feed/attachment.png'
        ];

        return $aMenus;
    }

    /**
	 * If a call is made to an unknown method attempt to connect
	 * it to a specific plug-in with the same name thus allowing 
	 * plug-in developers the ability to extend classes.
	 *
	 * @param string $sMethod is the name of the method
	 * @param array $aArguments is the array of arguments of being passed
     * @return null
	 */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('attachment.service_callback__call'))
		{
			eval($sPlugin);
            return null;
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
}