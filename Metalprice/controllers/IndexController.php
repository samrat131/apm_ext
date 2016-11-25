<?php

class Cybernetikz_Metalprice_IndexController extends Mage_Core_Controller_Front_Action
{

    public function indexAction(){ 
		
		//$read = Mage::getSingleton('core/resource')->getConnection('core_read');
		//$table = Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_decimal');

		$reindexPrice = true;
		$time_start = microtime(true);
		
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		$enitity_id = 1;

		$tierPrice = array(
			//'1'=>0,
			'5'=>50,
			'10'=>100,
			'20'=>200,
			);
						
		for ($i=1; $i <= 5; $i++) { 
			$enitity_id = $i;
			
			if($enitity_id != ''){
				//Update product price
				
				$price_random = mt_rand(1200,1300);
				$data = array("value" => $price_random);
				$where = "entity_id = {$enitity_id} AND attribute_id in (75)";
				$write->update("catalog_product_entity_decimal", $data, $where);	

				

				//Check tier exist or not 
				if(count($tierPrice)>0){	
					//delete all tier price 
					$write->delete("catalog_product_entity_tier_price", "entity_id=$enitity_id");
							
					//Reset tier price 		
					foreach($tierPrice as $qty => $price_discount){								
						//insert tier price 		
						$write->insert(
							"catalog_product_entity_tier_price",
							array("value_id" => NULL,"entity_id" => $enitity_id,"all_groups" => '1',"customer_group_id" => '0',
							"qty" => (float)$qty,"value" => (float)($price_random - $price_discount),"website_id" => '0')
						);
					}
				}  

				echo $price_random.'<br>';
			}
		}	


		if($reindexPrice){
			$process = Mage::getModel('index/indexer')->getProcessByCode('catalog_product_price');
			$process->reindexAll();
		}
		
		$time_end = microtime(true);
		$execution_time = ($time_end - $time_start); 
		
		echo '<br/><b>Total Execution Time:</b> '.$execution_time.' Sec. for <b>'.$i.'</b> products.';
    }
}