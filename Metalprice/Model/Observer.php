<?php

/**
* observer class
*/
class Cybernetikz_Metalprice_Model_Observer
{

	static protected $_singletonFlag = false;

	protected function _getRequest()
    {
        return Mage::app()->getRequest();
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

	public function log($value='')
	{
		if ($value == '') {
			return false;
		}
		file_put_contents( dirname(__FILE__).'/log.txt', $value."\n", FILE_APPEND );
	}


	public function updatePriceNow($observer)
	{
		//die('updatePriceNow');
		if (!self::$_singletonFlag) {
			self::$_singletonFlag = true;
			
			$product = $observer->getProduct();
			$postdata = $this->_getRequest()->getPost();
			//print_r($postdata); die();
			
			try {

				/**
				 * calculating metal price
				 *
				 */

				$base_sell_price = $postdata['product']['base_sell_price'];
				$precious_material = $postdata['product']['precious_material'];

				$market_price = Mage::helper('metalprice')->getMetalPrice($precious_material);
				
				// need to update
				$purity = $postdata['product']['purity'];
				$precious_materials_weight = $postdata['product']['precious_materials_weight'];
				$markup = $postdata['product']['markup'];
				$markup = ($markup=='' || $markup==0) ? 1 : $markup;
				
				$precious_materials_total = $market_price * $purity * $precious_materials_weight * $markup;
				$total_price = $precious_materials_total + $base_sell_price;

				/**
				 * updating product price data
				 *
				 */
				$product->setBaseSellPrice($base_sell_price);
				$product->setPreciousMaterial($precious_material);
				$product->setPurity($purity);
				$product->setMarkup($markup);
				$product->setMarketPrice($market_price);
				$product->setPreciousMaterialsWeight($precious_materials_weight);
				$product->setPreciousMaterialsTotal($precious_materials_total);
				$product->setTotalPrice($total_price);
				$product->setPrice($total_price);
				//$product->save();

				/*$indexCollection = Mage::getModel('index/process')->getCollection();
				foreach ($indexCollection as $index) {
				    $index->reindexAll();
				}*/
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}

		return $this;
	}

	public function updatePriceFrontend($observer)
	{

		//return $this; exit;
		//die('updatePriceFrontend');

		/*$storeId = 0;
		Mage::getSingleton('catalog/product_action')->updateAttributes(
	        array($productId),  //ids to update
	        array('price'=>$total_price), //attributes to update
	        $storeId //store view to update the attribtues
	    );*/

		//if (true) {
		if (!self::$_singletonFlag) {
			self::$_singletonFlag = true;

			$time_start = microtime(true);

			// Gets the current store id
			//$storeId = Mage::app()->getStore()->getStoreId();
			//$storeId = 0;
			//$entity_type_id = 4;

			$product = $observer->getProduct();
			$productId = $product->getId();

			$market_price = Mage::helper('metalprice')->getMetalPrice();
			
			$base_sell_price = $product->getBaseSellPrice();
			$purity = $product->getPurity();

			$markup = $product->getMarkup();
			$markup = $markup == '' || $markup == 0 ? 1 : $markup;
			
			$precious_materials_weight = $product->getPreciousMaterialsWeight();

			$precious_materials_total = $market_price * $purity * $precious_materials_weight * $markup;
			$total_price = $precious_materials_total + $base_sell_price;
			
			//$product->setPrice($total_price);
			//$product->save();


			$product->setData('market_price', $market_price);
			$product->getResource()->saveAttribute($product, 'market_price');

			$product->setData('precious_materials_total', $precious_materials_total);
			$product->getResource()->saveAttribute($product, 'precious_materials_total');

			$product->setData('total_price', $total_price);
			$product->getResource()->saveAttribute($product, 'total_price');

			$product->setData('price', $total_price);
			$product->getResource()->saveAttribute($product, 'price');

			$product->setData('minimal_price', $total_price);
			$product->getResource()->saveAttribute($product, 'minimal_price');

			/*
			// get attribute id : price
			$attributeCode = 'price';
			$attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $attributeCode);
			$attributeId = $attribute->getId();

			$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		    $connection->beginTransaction();

			//$condition = "attribute_id={$attributeId} AND entity_type_id={$entity_type_id} AND entity_id={$productId}";
		    //$connection->delete('catalog_product_entity_decimal', $condition);

		    $fields = array();
		    $fields['entity_type_id']=(int)$entity_type_id;
		    $fields['attribute_id']=(int)$attributeId;
		    $fields['store_id']=(int)$storeId;
		    $fields['entity_id']=(int)$productId;
		    $fields['value']=$total_price;
		    $connection->insertOnDuplicate('catalog_product_entity_decimal', $fields, array('value'));
		    $connection->commit();*/
		    

		    $process = Mage::getModel('index/process')->load(2);
			$process->reindexAll();

			/* @var $indexCollection Mage_Index_Model_Resource_Process_Collection */
			/*$indexCollection = Mage::getModel('index/process')->getCollection();
			foreach ($indexCollection as $index) {
			    $index->reindexAll();
			}*/			
			
			$time_end = microtime(true);
			$execution_time = ($time_end - $time_start); 
			$this->log($execution_time);

			return $this;

		}
		
		//die('updatePriceFrontend');
	}

	public function updatePriceOnListingFrontend($observer)
	{
		//die('updatePriceOnListingFrontend');
		return $this; exit;

		$collection = $observer['collection'];
		$price = 1250;

        foreach ($collection as &$product) {

            $product->setData("price", $price);
            
        }

        
		
		$_collections = $observer->getCollection();
		//print_r($_collections);
		

		if (!self::$_singletonFlag) {
			self::$_singletonFlag = true;

			$time_start = microtime(true);

			$_collections = $observer->getCollection();
			foreach ($_collections as $_product) {
			//foreach ($_collections as $product) {

				//print_r($product->getData()); die();

				// Gets the current store id
				//$storeId = Mage::app()->getStore()->getStoreId();
				//$storeId = 0;
				//$entity_type_id = 4;

				
				$productId = $_product->getId();
				$product = Mage::getModel('catalog/product')->load($productId);

				$market_price = Mage::helper('metalprice')->getMetalPrice();

				$base_sell_price = $product->getBaseSellPrice();
				$purity = $product->getPurity();
				$markup = $product->getMarkup();
				$precious_materials_weight = $product->getPreciousMaterialsWeight();

				$precious_materials_total = $market_price * $purity * $precious_materials_weight * $markup;
				$total_price = $precious_materials_total + $base_sell_price;

				//$total_price =  mt_rand(1100,1200);
				
				//$product->setPrice($total_price);
				//$product->save();

				$product->setData('precious_materials_total', $precious_materials_total);
				$product->getResource()->saveAttribute($product, 'precious_materials_total');

				$product->setData('total_price', $total_price);
				$product->getResource()->saveAttribute($product, 'total_price');

				$product->setData('price', $total_price);
				$product->getResource()->saveAttribute($product, 'price');

				//$product->setData('minimal_price', $total_price);
				//$product->getResource()->saveAttribute($product, 'minimal_price');


				/*
				// get attribute id : price
				$attributeId = $this->_getAttributeIdByCode('price');
				

				$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
			    $connection->beginTransaction();

				//$condition = "attribute_id={$attributeId} AND entity_type_id={$entity_type_id} AND entity_id={$productId}";
			    //$connection->delete('catalog_product_entity_decimal', $condition);

			    $fields = array();
			    $fields['entity_type_id']=(int)$entity_type_id;
			    $fields['attribute_id']=(int)$attributeId;
			    $fields['store_id']=(int)$storeId;
			    $fields['entity_id']=(int)$productId;
			    $fields['value']=$total_price;
			    $connection->insertOnDuplicate('catalog_product_entity_decimal', $fields, array('value'));
			    $connection->commit();
			    */
			}
			
			$time_end = microtime(true);
			$execution_time = ($time_end - $time_start); 
			$this->log($execution_time);

			return $this;
		}
	}

    public function updatePriceCron() { 

		$i = 0;
		$j = 0;
		$time_start = microtime(true);
    	$reindexPrice = true;
    	$_debugLog = false;
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
    		//$_product = Mage::getModel('catalog/product')->load($product->getId());

    		$precious_material = $product['precious_material'];
    		//if metal is not gold or silver then skip
    		if (!in_array($precious_material, $allow_metarial)) {
    			continue;
    		}

    		//get current precious metal market price
    		$market_price = Mage::helper('metalprice')->getMetalPrice($precious_material);
    		//echo $market_price; die();

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
							$j++;
					} 
    			}
    		}
    		$i++;
    	}

		//reindex price only
		if($reindexPrice){
			$process = Mage::getModel('index/indexer')->getProcessByCode('catalog_product_price');
			$process->reindexAll();
		}

		if ($_debugLog) {
			//calculate time
    		$time_end = microtime(true);
			$execution_time = ($time_end - $time_start); 

			$this->log('Execution Time:'.$execution_time.' Sec. for '.$i.' products, '.$j.'tier price.');
		}
		
    }

}