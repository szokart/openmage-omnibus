<?php

class Septsite_Omnibus_Block_Adminhtml_Omnibus_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('omnibusGrid');
        $this->setDefaultSort('omnibus_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('omnibus/omnibus')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('omnibus_id', array(
            'header' => 'ID',
            'align' => 'right',
            'width' => '50px',
            'index' => 'omnibus_id',
        ));

        $this->addColumn('website', array(
            'header' => 'Id website',
            'align' => 'right',
            'width' => '50px',
            'index' => 'website',
        ));

        $this->addColumn('customer_group', array(
            'header' => 'Id Customer Group',
            'align' => 'right',
            'width' => '50px',
            'index' => 'customer_group',
        ));

        $this->addColumn('product_id', array(
            'header' => 'Id produktu',
            'align' => 'left',
            'index' => 'product_id',
        ));

        $this->addColumn('day', array(
            'header' => 'Dzień',
            'align' => 'left',
            'index' => 'day',
        ));

        $this->addColumn('price', array(
            'header' => 'Cena',
            'align' => 'left',
            'index' => 'price',
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('`main_table`.`omnibus_id`');
        $this->getMassactionBlock()->setFormFieldName('omnibusids');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('omnibus')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('omnibus')->__('Are you sure?')
        ));

        return $this;
    }

    // public function getRowUrl($row)
    // {
    //     return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    // }
}
