<?php
$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('omnibus')};
CREATE TABLE `{$this->getTable('omnibus')}` (
`omnibus_id` int(15) unsigned NOT NULL UNIQUE auto_increment primary key,
`day` date NOT NULL,
`product_id` int(15) NOT NULL,
`price` decimal(12,4) NOT NULL,
`customer_group` int(11) NOT NULL,
`website` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
");

$installer->endSetup();
