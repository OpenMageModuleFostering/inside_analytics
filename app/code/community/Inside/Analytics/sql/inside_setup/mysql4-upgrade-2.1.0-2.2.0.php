<?php

set_time_limit(0);

/* @var $this Mage_Eav_Model_Entity_Setup */
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer = $this;
$installer->startSetup();

// Add support for Ajax requests
$installer->run("
ALTER TABLE {$this->getTable('insideanalytics_route')}
    ADD `is_active` boolean NOT NULL DEFAULT 1
       ; 
");

$installer->endSetup();
