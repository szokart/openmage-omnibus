<?php
class Septsite_Omnibus_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getPrice($id, $showpirce)
    {
        if (Mage::registry('current_product')) {
            $finalPrice = 0;
            $p = '';
            $storeId = Mage::app()->getStore()->getStoreId();
            $website = Mage::getModel('core/store')->load($storeId)->getWebsiteId();
            $groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
           
            $resource = Mage::getSingleton('core/resource')->getConnection('core_read');
            $lastDay = "(SELECT MAX(day) FROM `omnibus` 
            WHERE `product_id` = '".$id."' 
            AND `website` = '".$website ."' 
            AND `customer_group`='".$groupId."')";
            $query = "SELECT * FROM `omnibus` 
            WHERE `product_id` = '".$id."' 
            AND `day` >= DATE_SUB($lastDay, INTERVAL 30 DAY) 
            AND `website` = '".$website ."' 
            AND `customer_group`='".$groupId."'
            ORDER BY `omnibus_id` DESC";
            $rows = $resource->fetchAll($query);

            $step = 0;
            //$showpirce = number_format($showpirce, 3, '.', '');//
            //echo 'showprice:'.$showpirce;
            $dzien = 0;
            foreach ($rows as $row) {
                //echo $row['price'];
                $row['price'] = number_format($row['price'], 2, '.', '');
                if ($step == 0 && $showpirce != $row['price']) {
                    $finalPrice = $row['price'];
                    $dzien = $row['day'];
                }

                if ($step > 0) {
                    if ($row['price'] < $finalPrice) {
                        $finalPrice = $row['price'];
                        $dzien = $row['day'];
                    }

                    if ($finalPrice == 0) {
                        $finalPrice =  $row['price'];
                        $dzien = $row['day'];
                    }
                }
                $step++;
            }

            if ($finalPrice > 0) {
                $store_id = Mage::app()->getStore()->getStoreId();
                $dzien_w = date('Y-m-d', strtotime('-1 days'));
                $dzien_t = date('Y-m-d');

                
                if ($showpirce == $finalPrice && ($dzien_t == $dzien || $dzien_w == $dzien)) {
                    $write = Mage::getSingleton('core/resource')->getConnection('core_write');
                    $sql  = "DELETE FROM `omnibus` WHERE product_id='".$id."'";
                    $write->query($sql);

                    $updater = Mage::getSingleton('catalog/product_action');
                    $updater->updateAttributes(array($id), array( 'omnibus' => 0), 0);
                }
                

                $formattedPrice = Mage::helper('core')->currency($finalPrice, true, false);
                $p = '<p class="omnibus"> Najniższa cena z 30 dni przed obniżką: '.$showprice. $formattedPrice.'</p>';
            }

            return  $p;
        }
    }
}
