<?php

class Septsite_Omnibus_Model_Mysql4_Omnibus_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('omnibus/omnibus');
    }

    public function toOptionArray()
    {
        $options = array();
        foreach ($this as $item) {
            $options[] = array(
               'value' => $item->getTmptitle(),
               'label' => $item->getTitle()
            );
        }
        return $options;
    }
}
