<?php

class Septsite_Omnibus_Model_Observer extends Varien_Object
{
    public function detectPriceChanges($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();
        $resource = Mage::getSingleton('core/resource')->getConnection('core_read');
        $customer = Mage::getModel('customer/group')->getCollection();

        if ($product->hasDataChanges()) {
            foreach (Mage::app()->getWebsites() as $website) {
                $itwebsite = $website->getId();

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
                } //END grup klienckich
            }
        }
    }
}
