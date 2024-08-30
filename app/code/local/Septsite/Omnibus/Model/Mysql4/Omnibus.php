<?php

class Septsite_Omnibus_Model_Mysql4_Omnibus extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('omnibus/omnibus', 'omnibus_id');
    }


    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $select->order('day DESC')->limit(1);

        return $select;
    }

  

    /**
     * Call-back function
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        $adapter = $this->_getReadAdapter();
        // 1. Delete banner/store

        return parent::_beforeDelete($object);
    }
}
