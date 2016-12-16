<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Process class for photo categories.
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Photo
 * @version 		$Id: process.class.php 2525 2011-04-13 18:03:20Z Raymond_Benc $
 */
class Photo_Service_Category_Process extends Core_Service_Systems_Category_Process
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('photo_category');
        $this->_sTableData = Phpfox::getT('photo_category_data');
        $this->_sModule = 'photo';
        parent::__construct();
	}
	
	/**
	 * Update categories based on the item id.
	 *
	 * @param int $iPhoto ID of the photo.
	 * @param int $iCategory ID of the category.
	 *
	 * @return boolean ID of the new item we added.
	 */
	public function updateForItem($iPhoto, $iCategory)
	{
		static $bCache = false;
		
		if ($bCache === false)
		{
			$aCategories = $this->database()->select('photo_id, category_id')
				->from(Phpfox::getT('photo_category_data'))
				->where('photo_id = ' . (int) $iPhoto)
				->execute('getSlaveRow');
			
			foreach ($aCategories as $aCategory)
			{
				$this->database()->updateCounter('photo_category', 'used', 'category_id', $aCategory['category_id'], true);
			}
			
			$this->database()->delete(Phpfox::getT('photo_category_data'), 'photo_id = ' . (int) $iPhoto);
		}
		
		$bCache = true;
		
		// Lets add it again
		return $this->addForItem($iPhoto, $iCategory);
	}
	
	/**
	 * Add a new category for an item.
	 *
	 * @param int $iPhoto ID of the photo.
	 * @param int $iCategory ID of the category.
	 *
	 * @return boolean ID of the new item we added.
	 */
	public function addForItem($iPhoto, $iCategory)
	{
		$this->database()->update($this->_sTable, array('used' => array('= used +', 1)), 'category_id = ' . (int) $iCategory);		
		
		// Add the category data
		return $this->database()->insert(Phpfox::getT('photo_category_data'), array(
				'photo_id' => (int) $iPhoto,
				'category_id' => (int) $iCategory
			)
		);		
	}
	
	/**
	 * If a call is made to an unknown method attempt to connect
	 * it to a specific plug-in with the same name thus allowing 
	 * plug-in developers the ability to extend classes.
	 *
	 * @param string $sMethod is the name of the method
	 * @param array $aArguments is the array of arguments of being passed
	 * @return mixed`
	 */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('photo.service_category_process__call'))
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