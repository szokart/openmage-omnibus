<?php
class Septsite_Omnibus_Model_Omnibus extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('omnibus/omnibus');
    }
}
