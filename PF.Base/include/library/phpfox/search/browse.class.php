<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Parent browse routine
 * Controls how we browse all the sectons on the site.
 *
 * @copyright         [PHPFOX_COPYRIGHT]
 * @author            Raymond Benc
 * @package           Phpfox
 * @version           $Id: browse.class.php 7264 2014-04-09 21:00:49Z Fern $
 */
class Phpfox_Search_Browse
{
    /**
     * Item count.
     *
     * @var int
     */
    private $_iCnt = 0;

    /**
     * ARRAY of items
     *
     * @var array
     */
    private $_aRows = [];

    /**
     * ARRAY of params we are going to work with.
     *
     * @var array
     */
    private $_aParams = [];

    /**
     * Service object for the specific module we are working with
     *
     * @var object
     */
    private $_oBrowse = null;

    /**
     * Short access to the "view" request.
     *
     * @var string
     */
    private $_sView = '';

    /**
     * Conditions
     *
     * @var array
     */
    private $_aConditions = [];

    /**
     * Class constructor.
     *
     */
    public function __construct()
    {
    }

    /**
     * Set the params for the browse routine.
     *
     * @param array $aParams ARRAY of params.
     *
     * @return Phpfox_Search_Browse
     */
    public function params($aParams)
    {
        $this->_aParams = $aParams;
        $this->_aParams['service'] = $aParams['module_id'] . '.browse';

        $this->_oBrowse = Phpfox::getService($this->_aParams['service']);

        $this->_sView = Phpfox_Request::instance()->get('view');

        if ($this->_sView == 'friend') {
            Phpfox::isUser(true);
        }

        return $this;
    }


    /**
     *
     * Execute the browse routine. Runs the SQL query.
     */
    public function execute(\Closure $callback = null)
    {
        $sPagingVar = Phpfox_Request::instance()->get('sPagingVar');
        $aActualConditions = (array)$this->search()->getConditions();
        $aPagingVar = [];
        $iPage =  $this->search()->getPage();
        $bContinueLoad = false;

        if ($this->search()->isContinueSearch() and Phpfox::getParam('core.section_privacy_item_browsing')
            && (isset($this->_aParams['hide_view']) && !in_array($this->_sView, $this->_aParams['hide_view']))
            && (strpos($this->search()->getSort(),'.time_stamp') > 0
//                or strpos($this->search()->getSort(),'.start_time') > 0
                or strpos($this->search()->getSort(),'.photo_id') > 0
            )
        ) {
            $bContinueLoad =  true;
        }

        if ($sPagingVar and $bContinueLoad) {
            $aPagingVar = json_decode(base64_decode($sPagingVar), true);

            if (!empty($aPagingVar)) {
                $this->_aParams[] =  $aPagingVar['where'];
                $this->search()->setPagingCondition($aPagingVar['where']);
            }
            $iPage = 0;
        }


        $this->_aConditions = [];
        foreach ($aActualConditions as $sCond) {
            switch ($this->_sView) {
                case 'friend':
                    $this->_aConditions[] = str_replace('%PRIVACY%', '0,1,2', $sCond);
                    break;
                case 'my':
                    $this->_aConditions[] = str_replace('%PRIVACY%', '0,1,2,3,4', $sCond);
                    break;
                case 'pages_member':
                    $this->_aConditions[] = str_replace('%PRIVACY%', '0,1', $sCond);
                    break;
                case 'pages_admin':
                    $this->_aConditions[] = str_replace('%PRIVACY%', '0,1,2', $sCond);
                    break;
                default:
                    $this->_aConditions[] = str_replace('%PRIVACY%', '0', $sCond);
                    break;
            }
        }

        $sUserJoinCond = 'u.user_id = ' . $this->_aParams['alias'] . '.user_id';
        if (Phpfox::isUser() && $this->search()->isIgnoredBlocked()) {
            $aBlockedUserIds = User_Service_Block_Block::instance()->get(null, true);
            if (!empty($aBlockedUserIds)) {
                $sUserJoinCond .= ' AND u.user_id NOT IN (' . implode(',', $aBlockedUserIds) . ')';
            }
        }

        if (Phpfox::getParam('core.section_privacy_item_browsing')
            && (isset($this->_aParams['hide_view']) && !in_array($this->_sView, $this->_aParams['hide_view']))
        ) {
            Privacy_Service_Privacy::instance()->buildPrivacy($this->_aParams, $this->search()->getSort(), $iPage, $this->search()->getDisplay(), null, $bContinueLoad);

            $this->database()->unionFrom($this->_aParams['alias']);
        } else {
            $this->_oBrowse->getQueryJoins();

            $this->database()->from($this->_aParams['table'], $this->_aParams['alias'])->where($this->_aConditions);
        }

        if ($callback && $callback instanceof \Closure) {
            call_user_func($callback, $this);
        }

        $this->_oBrowse->query();
        $this->_aRows = $this->database()->select(Phpfox::getUserField() . ', ' . (isset($this->_aParams['select']) ? $this->_aParams['select'] : '') . $this->_aParams['alias'] . '.*')
            ->join(Phpfox::getT('user'), 'u', $sUserJoinCond)
            ->order($this->search()->getSort())
            ->limit($iPage, $this->search()->getDisplay(), null, false, false)
            ->execute('getSlaveRows');

       if($bContinueLoad){
           $aLastRow = end($this->_aRows);
           reset($this->_aRows);
           $aParams = $this->_aParams;

           if ((isset($this->_aParams['hide_view']) && !in_array($this->_sView, $this->_aParams['hide_view']))) {

               if (!empty($aLastRow)) {
                   $sOrder = $this->search()->getSort();
                   $sSubSort = $aParams['field'];
                   $aPagingVar[ $sSubSort ] = $aLastRow[ $sSubSort ];

                   $fields = array_map(function($str){return trim(strtolower($str));},preg_split('#\W+#', $sOrder));

                   $where = '';
                   if(!empty($fields[1]) && !empty($aLastRow[$fields[1]])){
                       $where .= ' AND ' . $aParams['alias'] . '.' . $fields[1] . '<' . $aLastRow[ $fields[1] ];
                   }

                   if(!empty($sSubSort) and !empty($aLastRow[$sSubSort])){
//                    $where .= ' AND ' . $aParams['alias'] . '.' . $aParams['field'] . '<=' . $aLastRow[ $sSubSort ];
                   }

                   $aPagingVar['where'] =  $where;


                   Phpfox_Template::instance()
                       ->assign(['sPagingVar' => base64_encode(json_encode($aPagingVar))]);
               }
           }
       }


        // using "load more", we does not need to use total item any more.
        //$this->database()->getCount();
        $this->_iCnt = 1;

        if ($this->search()->getPage() > 0 && count($this->_aRows) < 1) {
//            throw error('no_items');
        }

        if (method_exists($this->_oBrowse, 'processRows')) {
            $this->_oBrowse->processRows($this->_aRows);
        }
    }

    /**
     * Gets the count.
     *
     * @return int Total items.
     */
    public function getCount()
    {
        return (int)$this->_iCnt;
    }

    /**
     * Get items
     *
     * @return array ARRAY of items.
     */
    public function getRows()
    {
        return (array)$this->_aRows;
    }

    /**
     * Extends database class
     *
     * @see Phpfox_Database
     * @return object Returns database object
     */
    public function database()
    {
        return Phpfox_Database::instance();
    }

    /**
     * Extends search class
     *
     * @see Phpfox_Search
     * @return Phpfox_Search
     */
    public function search()
    {
        return Phpfox_Search::instance();
    }

    /**
     * Reset the search
     *
     */
    public function reset()
    {
        $this->_aRows = [];
        $this->_iCnt = 0;
        $this->_aConditions = [];
        $this->_aParams = [];

        Phpfox_Search::instance()->reset();
    }
}