<?php

class Cybernetikz_Metalprice_IndexController extends Mage_Core_Controller_Front_Action
{

	protected function pr($data='',$die=0)
	{
		if ($data=='') {
			return;
		}
		echo '<pre>';
		print_r($data);
		echo '</pre>';
		if ($die) {
			die();
		}
	}

	protected function _getMetalPrice($precious_material)
	{
		/*$data = file_get_contents('https://gold-feed.com/mag_gold.php');
		$xml = simplexml_load_string($data);
		return $xml->gold->price;*/
		if ($precious_material=='gold') {
			$rand = mt_rand(2000,2000);
		}
		
		if ($precious_material=='silver') {
			$rand = mt_rand(24,24);
		}

		//$this->log($rand);
		return $rand;
	}

	protected function _getAttributeIdByCode($attributeCode='')
	{
		if ($attributeCode=='') {
			return;
		}
		$attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $attributeCode);
		$attributeId = $attribute->getId();
		return $attributeId;
	}

    public function indexAction() { 

		$time_start = microtime(true);
    	$reindexPrice = true;
    	$allow_metarial = array('gold','silver');
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');

    	/*$productCollections = Mage::getModel('catalog/product')
    				->getCollection()
    				->load();*/

    	$productCollections = Mage::getResourceModel('catalog/product_collection')
    				//->addAttributeToSelect('*')
    				->addAttributeToSelect('base_sell_price')
    				->addAttributeToSelect('precious_material')
    				->addAttributeToSelect('market_price')
    				->addAttributeToSelect('purity')
    				->addAttributeToSelect('precious_materials_weight')
    				->addAttributeToSelect('markup')
    				->addAttributeToSelect('total_price')
    				->addAttributeToSelect('precious_materials_total')
    				->addFieldToFilter('status',1)
    				//->addFieldToFilter('not_for_sale',0)
    				->addFieldToFilter('price',array('gteq'=>1))
    				//->getSelect()
    				->load();

    	foreach ($productCollections as $product) {
    		//$this->pr($product->getData());
    		//die();
    		//$_product = Mage::getModel('catalog/product')->load($product->getId());

			// 	Array
			// (
			//     [price_id] => 145
			//     [website_id] => 0
			//     [all_groups] => 1
			//     [cust_group] => 32000
			//     [price_qty] => 5.0000
			//     [price] => 1500.5000
			//     [percentage] => 8.0000
			//     [website_price] => 1500.5000
			// )    		


    		$precious_material = $product['precious_material'];
    		//if metal is not gold or silver then skip
    		if (!in_array($precious_material, $allow_metarial)) {
    			continue;
    		}

    		//get current precious metal market price
    		$market_price = $this->_getMetalPrice($precious_material);

    		//assign value from array to variable
    		$precious_materials_weight = $product['precious_materials_weight'];
    		$purity = $product['purity'];
    		$markup = ($product['markup']==0 || $product['markup']=='') ? 1 : $product['markup'];
    		$base_sell_price = $product['base_sell_price'];

    		//calculate metal price 
    		$precious_materials_total = $market_price * $purity * $precious_materials_weight * $markup;
			$total_price = $precious_materials_total + $base_sell_price;

			//get attr id from attr name
			$price_attr_id = $this->_getAttributeIdByCode('price');
			$precious_materials_total_attr_id = $this->_getAttributeIdByCode('precious_materials_total');
			$total_price_attr_id = $this->_getAttributeIdByCode('total_price');
			$market_price_attr_id = $this->_getAttributeIdByCode('market_price');

			//set entity id
			$entity_id = $product->getId();

			//update price and total_price
			$data = array("value" => $total_price);
			$where = "entity_id = {$entity_id} AND attribute_id in ({$price_attr_id},{$total_price_attr_id})";
			$write->update("catalog_product_entity_decimal", $data, $where);

			//update precious_materials_total
			$data = array("value" => $precious_materials_total);
			$where = "entity_id = {$entity_id} AND attribute_id in ({$precious_materials_total_attr_id})";
			$write->update("catalog_product_entity_decimal", $data, $where);

			//update current metal market price
			$data = array("value" => $market_price);
			$where = "entity_id = {$entity_id} AND attribute_id in ({$market_price_attr_id})";
			$write->update("catalog_product_entity_decimal", $data, $where);	
    		
    		$_tierPriceArray = $product->getTierPrice();
    		if ( is_array($_tierPriceArray) && count($_tierPriceArray)>0 ) {
    		
    			//$this->pr($_tierPriceArray);
    			foreach ($_tierPriceArray as $_tierPrice) {
    				//$this->pr($_tierPrice);

					if( is_numeric($_tierPrice['price_id']) && 
						is_numeric($_tierPrice['percentage']) && 
						$_tierPrice['percentage'] > 0 &&
						is_numeric($_tierPrice['price_qty']) && 
						$_tierPrice['price_qty'] > 0 ) {	

							//update tier price table for each product
							$value_id = $_tierPrice['price_id'];
							$price_discount = $_tierPrice['percentage'];
							$final_tier_price = $total_price - $price_discount;
							$data = array("value" => $final_tier_price);
							$where = "value_id = {$value_id}";
							$write->update("catalog_product_entity_tier_price", $data, $where);
					} 
    			}
    		}
    	}

		//reindex price only
		if($reindexPrice){
			$process = Mage::getModel('index/indexer')->getProcessByCode('catalog_product_price');
			$process->reindexAll();
		}


    	//calculate time
    	$time_end = microtime(true);
		$execution_time = ($time_end - $time_start); 
		
		echo '<br/><b>Total Execution Time:</b> '.$execution_time.' Sec. for <b>'.$i.'</b> products.';

    }
}