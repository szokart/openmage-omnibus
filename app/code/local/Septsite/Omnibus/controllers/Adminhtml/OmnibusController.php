<?php
class Septsite_Omnibus_Adminhtml_OmnibusController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        //return Mage::getSingleton('admin/session')->isAllowed('omnibus/omnibusbackend');
        return true;
    }

    protected function _initAction()
    {
        $this->loadLayout()
        ->_setActiveMenu('omnibus/items')
        ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()->renderLayout();
    }

    public function massDeleteAction()
    {
        $omnibus_ids = $this->getRequest()->getParam('omnibusids');
        if (!is_array($omnibus_ids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Proszę wybierz wpisy'));
        } else {
            try {
                foreach ($omnibus_ids as $omnibus_id) {
                    $omnibus = Mage::getModel('omnibus/omnibus')->load($omnibus_id);
                    $omnibus->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Usunięto %d wpisów', count($omnibus_ids))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/');
    }
}
