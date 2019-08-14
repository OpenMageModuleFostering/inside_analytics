<?php

set_time_limit(0);

/* @var $this Mage_Eav_Model_Entity_Setup */
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer = $this;
$installer->startSetup();

// Create custom track route table
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('insideanalytics_route')};
CREATE TABLE {$this->getTable('insideanalytics_route')} (
  `id` int(13) UNSIGNED NOT NULL AUTO_INCREMENT,
  `module` varchar(100) NOT NULL,
  `controller` varchar(100) NULL DEFAULT NULL,
  `action` varchar(100) NULL DEFAULT NULL,
  `full_qualifier` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  `search_param` varchar(50) NULL DEFAULT NULL,
  `user_defined` boolean NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQUE_ROUTE_NAME` (`full_qualifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO {$this->getTable('insideanalytics_route')} (`id`, `module`, `controller`, `action`, `full_qualifier`, `type`, `search_param`, `user_defined`) VALUES
(1, 'cms', 'index', 'index', 'cms_index_index', 'homepage', NULL, 0),
(2, 'cms', 'page', 'view', 'cms_page_view', 'article', NULL, 0),
(3, 'catalog', 'seo_sitemap', 'category', 'catalog_seo_sitemap_category', 'article', NULL, 0),
(4, 'catalogsearch', 'result', 'index', 'catalogsearch_result_index', 'search', 'q', 0),
(5, 'catalogsearch', 'advanced', 'index', 'catalogsearch_advanced_index', 'search', 'q', 0),
(6, 'sli', 'search', NULL, 'sli_search', 'search', 'w', 0),
(7, 'catalog', 'category', 'view', 'catalog_category_view', 'productcategory', NULL, 0),
(8, 'amshopby', 'index', 'index', 'amshopby_index_index', 'productcategory', NULL, 0),
(9, 'catalog', 'product', 'view', 'catalog_product_view', 'product', NULL, 0),
(10, 'customer', 'account', 'login', 'customer_account_login', 'login', NULL, 0),
(11, 'checkout', 'multishipping', 'login', 'checkout_multishipping_login', 'login', NULL, 0),
(12, 'checkout', 'cart', 'index', 'checkout_cart_index', 'checkout', NULL, 0),
(13, 'checkout', 'onepage', 'index', 'checkout_onepage_index', 'checkout', NULL, 0),
(14, 'onestepcheckout', NULL, NULL, 'onestepcheckout', 'checkout', NULL, 0),
(15, 'checkout', 'multishipping', 'index', 'checkout_multishipping', 'checkout', NULL, 0),
(16, 'checkout', 'onepage', 'success', 'checkout_onepage_success', 'orderconfirmed', NULL, 0),
(17, 'checkout', 'multishipping', 'success', 'checkout_multishipping_success', 'orderconfirmed', NULL, 0),
(18, 'cms', 'index', 'noRoute', 'cms_index_noRoute', 'pagenotfound', NULL, 0)
");

$installer->endSetup();
