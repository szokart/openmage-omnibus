<?php
class Septsite_Omnibus_Model_Cron
{
    public function Omnibus()
    {
        $websites = array();
        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();
        $resource = Mage::getSingleton('core/resource')->getConnection('core_read');
        $customer = Mage::getModel('customer/group')->getCollection();

        //tablica widoków sklepów ////////
        foreach (Mage::app()->getWebsites() as $website) {
            $websites[] = $website->getId();
        }

        $products = Mage::getModel('catalog/product')->getCollection()
        //->addAttributeToSelect('price')
        ->addAttributeToSelect('entity_id')
        ->addAttributeToFilter('status', 1)
        ->addAttributeToFilter('visibility', 4)
        ->addAttributeToFilter('type_id', array('neq' => 'grouped'))
        ->addAttributeToFilter('omnibus', array('nlike' => '%'.date("Ymd").'%'))
        ->setPageSize(200)
        ->setCurPage(1);

        //petla produktów do weryfikacji
        foreach ($products as $product) {
            //pętla widoków sklepów
            foreach ($websites as $key => $itwebsite) {

                //zapisujemy date sprawdzania dla widoku default
                $updater = Mage::getSingleton('catalog/product_action');
                $updater->updateAttributes(array($product->getId()), array( 'omnibus' => date("Ymd")), 0);

                //pętla grup klienckich
                foreach ($customer as $type) {
                    $omnibuses = Mage::getModel('omnibus/omnibus')
                    ->getCollection()
                    ->addFieldToFilter('product_id', $product->getId())
                    ->addFieldToFilter('website', $itwebsite)
                    ->addFieldToFilter('customer_group', $type->getCustomerGroupId())
                    ->setOrder('omnibus_id', 'DESC')
                    ->setPageSize(1)
                    ->setCurPage(1);

                    $query = "SELECT * FROM `".$tablePrefix."catalog_product_index_price` 
                    WHERE `website_id` = '".$itwebsite."' 
                    AND `entity_id` = '".$product->getId()."'
                    AND `customer_group_id` = '".$type->getCustomerGroupId()."' ";
                    $rows = $resource->fetchAll($query);
                    $finalprice = 0;
                    //pobranie final price
                    foreach ($rows as $row) {
                        $finalprice =  $row["final_price"];
                        $regular_price = $row["price"];
                    }

                    if ($finalprice  != 0) {
                        if (count($omnibuses) == 0) {
                            if ($finalprice != $regular_price) {
                                $new = Mage::getModel('omnibus/omnibus');
                                $new->setDay(date("Y-m-d"));
                                $new->setProductId($product->getId());
                                $new->setPrice($regular_price);
                                $new->setCustomerGroup($type->getCustomerGroupId());
                                $new->setWebsite($itwebsite);
                                $new->save();
                            }

                            //wpisujemy jeśli w ogóle nie ma wpisu produktu
                            $new = Mage::getModel('omnibus/omnibus');
                            $new->setDay(date("Y-m-d"));
                            $new->setProductId($product->getId());
                            $new->setPrice($finalprice);
                            $new->setCustomerGroup($type->getCustomerGroupId());
                            $new->setWebsite($itwebsite);
                            $new->save();
                        } else {
                            //akutalizujemy jeśli jest inny od odstatniego
                            foreach ($omnibuses as $omnibus) {
                                if ($omnibus->getPrice() != $finalprice) {
                                    $yesterday = date('Y-m-d', strtotime('-1 days'));
                                    if ($omnibus->getDay() != $yesterday && $omnibus->getDay()!= date('Y-m-d')) {
                                        $new = Mage::getModel('omnibus/omnibus');
                                        $new->setDay($yesterday);
                                        $new->setProductId($product->getId());
                                        $new->setPrice($omnibus->getPrice());
                                        $new->setCustomerGroup($type->getCustomerGroupId());
                                        $new->setWebsite($itwebsite);
                                        $new->save();
                                    }

                                    $new = Mage::getModel('omnibus/omnibus');
                                    $new->setDay(date("Y-m-d"));
                                    $new->setProductId($product->getId());
                                    $new->setPrice($finalprice);
                                    $new->setCustomerGroup($type->getCustomerGroupId());
                                    $new->setWebsite($itwebsite);
                                    $new->save();
                                }
                            }
                        }
                    }

                    if (count($omnibuses) > 0) {
                        //Kasowanie starych wpisów

                        $date = date('Y-m-d', strtotime('-30 days'));

                        $omnibusesb = Mage::getModel('omnibus/omnibus')
                                ->getCollection()
                                ->addFieldToFilter('product_id', $product->getId())
                                ->addFieldToFilter('website', $itwebsite)
                                ->addFieldToFilter('customer_group', $type->getCustomerGroupId())
                                ->addFieldToFilter('day', array('lt'=>$date))
                                ->setOrder('day', 'DESC');

                        if (count($omnibusesb) > 1) {
                            $del = 0;
                            foreach ($omnibusesb as $omnibus) {
                                if ($del > 0) {
                                    $omnidel = Mage::getModel('omnibus/omnibus')->load($omnibus->getId());
                                    $omnidel->delete();
                                }
                                $del++;
                            }
                        }
                    }
                } //END grup klienckich
            } //END pętla widoków sklepów
        }
    }
}
