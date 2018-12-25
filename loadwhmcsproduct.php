<?php
/**
 * @version		$Id: example.php 10714 2008-08-21 10:10:14Z eddieajau $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Example Content Plugin
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 		3.8
 */
class PlgContentLoadWhmcsProduct extends JPlugin { 
  /**
   * prepare
   */
	public function onContentPrepare($context, &$item, &$params, $page = 0) {
    // global $mainframe;

    // All api data
    $apiData = json_decode($this->rebuildApiData());
    // All products;
    $products = $apiData->products->product;
    if($item->text) {
			$item->text = substr_replace($item->text, '<textarea type="hidden">'. json_encode($products) .'</textarea>', 0);
		}

		if($item->introtext) {
			$item->introtext = substr_replace($item->introtext, '<textarea type="hidden">'. json_encode($products) .'</textarea>', 0);
    }
    return true;
  }

  /**
   * rebuild data
   */
  public function rebuildApiData() {
    $resData = array();
    $apiData = $this->getApiData($this->param('url'));
    foreach ($apiData as $key => $value) {
      $a = $apiData->gid;
    }
    return $apiData;
  }

  /**
   * get plugin param
   */
  public function param($name) {
		if (!isset($plugin)) {			
			$plugin =& JPluginHelper::getPlugin('content', 'loadwhmcsproduct');
			$pluginParams= new JRegistry($plugin->params);
		}
		return $pluginParams->get($name);
  }

  /**
   * get api data
   */
  private function getApiData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS,
      http_build_query(
        array(
          'action' => $this->param('action'),
          // See https://developers.whmcs.com/api/authentication
          'identifier' => $this->param('identifier'),
          'secret' => $this->param('secret'),
          // 'pid' => '1',
          'responsetype' => 'json',
        )
      )
    );	
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    $result =  curl_exec($ch);
    curl_close ($ch);
    return $result; 
  }
}