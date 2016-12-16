<?php

/**
 *
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox_Service
 * @version 		$Id: pages.class.php 7234 2014-03-27 14:40:29Z Fern $
 */
class Pages_Service_Facade extends Phpfox_Pages_Facade
{

    /**
     * @return \Phpfox_Pages_Pages
     */
    public function getItems()
    {
        return Pages_Service_Pages::instance();
    }

    /**
     * @return \Phpfox_Pages_Category
     */
    public function getCategory()
    {
        return Pages_Service_Category_Category::instance();
    }

    /**
     * @return \Phpfox_Pages_Process
     */
    public function getProcess()
    {
        return Pages_Service_Process::instance();
    }

    /**
     * @return \Phpfox_Pages_Type
     */
    public function getType()
    {
        return Pages_Service_Type_Type::instance();
    }

    /**
     * @return \Phpfox_Pages_Browse
     */
    public function getBrowse()
    {
        return Pages_Service_Browse::instance();
    }

    /**
     * @return \Phpfox_Pages_Callback
     */
    public function getCallback()
    {
        return Pages_Service_Callback::instance();
    }

    public function getItemType()
    {
        return 'pages';
    }

    public function getItemTypeId()
    {
        return 0;
    }

    public function getPhrase($name, $params = [])
    {
        if (empty($params)) {
            return _p('' . $name);
        }
        return _p('' . $name, $params);

    }

    public function getUserParam($name)
    {
        return Phpfox::getUserParam('pages.' . $name);
    }
}
