<?php

/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = Mage::getResourceModel('catalog/setup','catalog_setup');

$installer->startSetup();

$installer->addAttribute(
    'catalog_product',
    'total_price',
    array(
      'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
      'input' => '',
      'type' => 'decimal',
      'backend' => '',    // backend_model
      'frontend' => '',   // fronted_model
      'label' => 'Total Product Price with Metal Adjustment',
      'class' => '',
      'user_defined' => true,
      'required' => false,
    ) //note the column alias must be used!
);

$installer->addAttribute(
    'catalog_product',
    'precious_materials_total',
    array(
      'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
      'input' => '',
      'type' => 'decimal',
      'backend' => '',    // backend_model
      'frontend' => '',   // fronted_model
      'label' => 'Total of Precious Metal Adjustment',
      'class' => '',
      'user_defined' => true,
      'required' => false,
    ) //note the column alias must be used!
);

$installer->addAttribute(
    'catalog_product',
    'markup',
    array(
      'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
      'input' => '',
      'type' => 'decimal',
      'backend' => '',    // backend_model
      'frontend' => '',   // fronted_model
      'label' => 'Markup (%)',
      'class' => '',
      'user_defined' => true,
      'required' => false,
    ) //note the column alias must be used!
);

$installer->addAttribute(
    'catalog_product',
    'precious_materials_weight',
    array(
      'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
      'input' => '',
      'type' => 'decimal',
      'backend' => '',    // backend_model
      'frontend' => '',   // fronted_model
      'label' => 'Product Material Weight (ounces)',
      'class' => '',
      'user_defined' => true,
      'required' => false,
    ) //note the column alias must be used!
);

$installer->addAttribute(
    'catalog_product',
    'purity',
    array(
      'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
      'input' => '',
      'type' => 'decimal',
      'backend' => '',    // backend_model
      'frontend' => '',   // fronted_model
      'label' => 'Purity of Product (0.9999)',
      'class' => '',
      'user_defined' => true,
      'required' => false,
    ) //note the column alias must be used!
);

$installer->addAttribute(
    'catalog_product',
    'market_price',
    array(
      'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
      'input' => '',
      'type' => 'decimal',
      'backend' => '',    // backend_model
      'frontend' => '',   // fronted_model
      'label' => 'Current Precious Material Market Price',
      'class' => '',
      'user_defined' => true,
      'required' => false,
    ) //note the column alias must be used!
);

$installer->addAttribute(
    'catalog_product',
    'precious_material',
    array(
      'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
      'input' => '',
      'type' => 'varchar',
      'backend' => '',    // backend_model
      'frontend' => '',   // fronted_model
      'label' => 'Select Precious Material',
      'class' => '',
      'user_defined' => true,
      'required' => false,
    ) //note the column alias must be used!
);

$installer->addAttribute(
    'catalog_product',
    'base_sell_price',
    array(
      'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
      'input' => '',
      'type' => 'decimal',
      'backend' => '',    // backend_model
      'frontend' => '',   // fronted_model
      'label' => 'Base Sell Price',
      'class' => '',
      'user_defined' => true,
      'required' => true,
    ) //note the column alias must be used!
);

$installer->endSetup();

/*
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE IF NOT EXISTS `mag_pedigree_owner_555` (
  `owner_id` int(11) NOT NULL AUTO_INCREMENT,
  `owner` text,
  `address` text,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` smallint(6) NOT NULL DEFAULT '1',
  PRIMARY KEY (`owner_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `mag_pedigree_transaction` (
  `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `transaction_date` timestamp NOT NULL,
  `transaction_statement` text,
  `direct_purchase_statement` text,
  `receipt_file` text,
  `sort_order` int(11) NOT NULL,
  `created` timestamp NOT NULL,
  PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

SQLTEXT;

$installer->run($sql);
//Mage::getModel('core/url_rewrite')->setId(null);
$installer->endSetup();
*/