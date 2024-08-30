<?php

class Septsite_Omnibus_Block_Adminhtml_Omnibus extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_omnibus';
        $this->_blockGroup = 'omnibus';
        $this->_headerText = Mage::helper('omnibus')->__('Omnibus');
        parent::__construct();
    }
}
