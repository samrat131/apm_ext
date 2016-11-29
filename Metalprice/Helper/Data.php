<?php
/**
* Author : Samrat Azad
* Company : Cybernetikz
*/

class Cybernetikz_Metalprice_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getMetalPrice($precious_material)
	{
		/*
		Cybernetikz_Spotprice_Helper_Data::getMetalPrice();
		*/

		$metalPrice = Mage::helper('spotprice')->getMetalPrice();

		if ($precious_material=='gold') {
			//$current_price = mt_rand(1500,1500);
			$current_price = $metalPrice['gold_ask_usd_toz'];
		}
		
		if ($precious_material=='silver') {
			//$current_price = mt_rand(15,15);
			$current_price = $metalPrice['silver_ask_usd_toz'];
		}

		//$this->log($current_price);
		return $current_price;
	}

}