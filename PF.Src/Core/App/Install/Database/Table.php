<?php
namespace Core\App\Install\Database;

use Phpfox;

/**
 * Class Table
 * @author  Neil
 * @version 4.5.0
 * @package Core\App\Install\Database
 */
abstract class Table
{
    /**
     * @var string name of the table
     */
    protected $_table_name;
    
    /**
     * @var string engine of table
     */
    protected $_engine = 'InnoDB';
    
    /**
     * @var string charset of table
     */
    protected $_charset = 'latin1';
    
    /**
     * @var Field is a Field of auto increment
     *
     */
    private $_auto_increment = '';
    
    /**
     * @var string is the field name of primary key
     */
    private $_primary_key = '';
    
    /**
     * @var array of keys
     */
    private $_key = [];
    
    /**
     * @var array structure of table in array
     */
    protected $_aFieldParams = [];
    
    /**
     * @var array of Field, store all fields of this table
     */
    protected $_aFields = [];
    
    /**
     * Table constructor.
     */
    public function __construct()
    {
        $this->setTableName();
        $this->setFieldParams();
        $this->_table_name = Phpfox::getT($this->_table_name);
        foreach ($this->_aFieldParams as $key => $aParam) {
            $aParam['name'] = $key;
            $newField = new Field($aParam);
            if (!$newField->isValid()) {
                continue;
            }
            $this->addField($newField);
            if (isset($aParam['primary_key'])) {
                $bAuto = (isset($aParam['auto_increment'])) ? $aParam['auto_increment'] : false;
                $this->addPrimaryKey($newField, $bAuto);
            }
        }
    }
    
    /**
     * Set name of this table, can't missing
     */
    abstract protected function setTableName();
    
    /**
     * Set all fields of table
     */
    abstract protected function setFieldParams();
    
    /**
     * Add a new field for this table
     *
     * @param Field $field
     *
     * @return bool
     */
    private function addField($field)
    {
        if ($field->isValid()) {
            $this->_aFields[] = $field;
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Add a primary and auto increment for this table
     *
     * @param Field $field
     * @param bool  $bAutoIncrement
     */
    private function addPrimaryKey($field, $bAutoIncrement = true)
    {
        $this->_primary_key = $field->getName();
        if ($bAutoIncrement) {
            $this->_auto_increment = $field;
        }
    }
    
    /**
     * Add a key for this table
     *
     * @param string $name
     * @param array  $listKey of Field
     *
     * @return bool
     */
    protected function addKey($name, $listKey)
    {
        if (count($listKey) == 0) {
            return false;
        }
        $aKey = [];
        foreach ($listKey as $key) {
            if (!is_a($key, '\Apps\PHPfox_Backup_Restore\Install\Database\Field')) {
                continue;
            }
            $aKey[] = $key->getName();
        }
        if (count($aKey)) {
            $this->_key[$name] = $aKey;
        } else {
            //Don't have any valid key
            return false;
        }
        return true;
    }
    
    /**
     * Generate the sql code for create new database only
     *
     * @return bool|array
     */
    public function generate()
    {
        $aQuery = [];
        $sQuery = "CREATE TABLE `" . $this->_table_name . "` (";
        if (count($this->_aFields) == 0) {
            return false;
        }
        foreach ($this->_aFields as $field) {
            if (!is_a($field, '\Core\App\Install\Database\Field') || !$field->isValid()) {
                return false;
            }
            $sQuery .= $field->getCode() . ',';
        }
        $sQuery = rtrim($sQuery, ',');
        $sQuery .= ')';
        $sQuery .= "ENGINE=$this->_engine DEFAULT CHARSET=$this->_charset;";
        $aQuery[] = $sQuery;
        $sQuery = '';
        if (!empty($this->_primary_key)) {
            $sQuery .= "ALTER TABLE `" . $this->_table_name . "`
  ADD PRIMARY KEY (`" . $this->_primary_key . "`);";
        }
        $aQuery[] = $sQuery;
        $sQuery = '';
        if (is_a($this->_auto_increment, '\Core\App\Install\Database\Field')) {
            $sAutoType = $this->_auto_increment->getType();
            if ($this->_auto_increment->getTypeValue() > 0) {
                $sAutoType .= '(' . $this->_auto_increment->getTypeValue() . ')';
            }
            $sQuery .= "ALTER TABLE `" . $this->_table_name . "`
  MODIFY `" . $this->_auto_increment->getName() . "` " . $sAutoType . " AUTO_INCREMENT;";
        }
        if (!empty($sQuery)) {
            $aQuery[] = $sQuery;
        }
        return $aQuery;
    }
    
    /**
     * Truncate this table, use for reset
     */
    public function truncate()
    {
        db()->truncateTable($this->_table_name);
    }
    
    /**
     * Drop this table, use for uninstall
     */
    public function drop()
    {
        db()->dropTable($this->_table_name);
    }
    
    /**
     * Create/upgrade database when install/upgrade
     */
    public function install()
    {
        $sOldFieldName = '';
        if (!db()->tableExists($this->_table_name)) {
            $aQueryCode = $this->generate();
            foreach ($aQueryCode as $query) {
                //TODO @Neil convert $this->generate() to dba
                db()->query($query);
            }
        } else {
            foreach ($this->_aFields as $field) {
                if (!db()->isField($this->_table_name, $field->getName())) {
                    $aField = [
                        'table' => $this->_table_name,
                        'field' => $field->getName(),
                        'type' => $field->getFullType(),
                        'attribute' => $field->getOther()
                    ];

                    if (!empty($sOldFieldName)) {
                        $aField['after'] = $sOldFieldName;
                    }
                    //Add new field
                    db()->addField($aField);
                } else {
                    //Update an exist field
                    //Todo do this later
                }
                $sOldFieldName = $field->getName();
            }
        }
    }
    
    /**
     * Check configuration set is correct
     *
     * @return bool
     */
    public function isValid()
    {
        if (empty($this->_table_name)) {
            return false;
        }
        
        //a table at least have two fields
        if (count($this->_aFields) < 2) {
            return false;
        }
        
        //a table has to have a primary key
        if (!isset($this->_primary_key) || empty($this->_primary_key)) {
            return false;
        }
        
        foreach ($this->_aFields as $field) {
            if (!is_a($field, '\Core\App\Install\Database\Field')) {
                return false;
            }
        }
        return true;
    }
    
    public function insert($aParam)
    {
        foreach ($this->_aFieldParams as $sKey => $value) {
            if (!isset($value['auto_increment']) && !isset($aParam[$sKey])) {
                return false;
            }
        }
        $iId = db()->insert($this->_table_name, $aParam);
        return $iId;
    }
    
    public function search($aConditions, $bCount = false, $iLimit = null)
    {
        $sConds = '';
        foreach ($aConditions as $sKey => $aCondition) {
            if (!isset($this->_aFieldParams[$sKey])) {
                continue;
            }
            if (!isset($aCondition['operator']) || !isset($aCondition['data'])) {
                continue;
            }
            if (!empty($sConds)) {
                $sConds .= ' AND ';
            }
            $sConds .= "`$sKey` " .  $aCondition['operator'] . " \"" . $aCondition['data'] ."\"";
        }
        if (empty($sConds)) {
            $sConds = "true";
        }
        $oDb = db();
        if ($bCount) {
            $oDb->select('COUNT(*)');
        } else {
            $oDb->select('*');
        }
        $oDb->from($this->_table_name)
            ->where($sConds);
        if ($bCount){
            $aResults = $oDb->execute('getSlaveField');
        } else {
            if (isset($iLimit) && $iLimit) {
                $oDb->limit($iLimit);
            }
            $aResults = $oDb->execute('getSlaveRows');
        }
        return $aResults;
    }
}