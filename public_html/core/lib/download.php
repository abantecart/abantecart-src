<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
/**
 * Class ADownload - class to manage downloads
 *
 * @property ADB $db
 * @property ALanguageManager $language
 * @property ACustomer $customer
 * @property AConfig $config
 * @property ALoader $load
 */
final class ADownload {

	public $errors = array();

	/**
	 * @var registry - access to application registry
	 */
	protected $registry;

	public function __construct() {
		$this->registry = Registry::getInstance();
		$this->errors = array();
	}

	/**
	 * @param  $key - key to load data from registry
	 * @return mixed  - data from registry
	 */
	public function __get($key) {
		return $this->registry->get($key);
	}

	/**
	 * @param  string $key - key to save data in registry
	 * @param  mixed $value - key to save data in registry
	 * @return mixed  - data from registry
	 */
	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

	public function getDownloadsBeforeOrder($product_id, $language_id=null){
		if(!(int)$product_id){ return array(); }
		if(!$language_id){
			if(IS_ADMIN===true){
				$language_id = $this->language->getContentLanguageID();
			}else{
				$language_id = $this->language->getLanguageID();
			}
		}

		$result = $this->db->query("SELECT d.*, dd.*, ptd.*
									FROM ". $this->db->table('downloads')." d
									RIGHT JOIN ". $this->db->table('products_to_downloads')." ptd ON ptd.download_id = d.download_id
									LEFT JOIN ". $this->db->table('download_descriptions')." dd ON d.download_id = dd.download_id AND dd.language_id = '".$language_id."'
									WHERE ptd.product_id='".(int)$product_id."'
										AND d.activate='before_order'
										AND d.status>0");
		return $result->rows;
	}

	public function getDownloadInfo($download_id){
		if(!(int)$download_id){ return array(); }
		if(!$language_id){
			if(IS_ADMIN===true){
				$language_id = $this->language->getContentLanguageID();
			}else{
				$language_id = $this->language->getLanguageID();
			}
		}

		$result = $this->db->query("SELECT dd.*, d.*
									FROM ". $this->db->table('downloads')." d
									LEFT JOIN ". $this->db->table('download_descriptions')." dd
										ON (d.download_id = dd.download_id AND dd.language_id = '".$language_id."')
									WHERE d.download_id='".(int)$download_id."'");
		return $result->row;
	}

	/**
	 * Method returns order download info selected by order_download_id
	 * @param int $order_download_id
	 * @param int|string $language_id (optional)
	 * @return array
	 */
	public function getOrderDownloadInfo($order_download_id, $language_id = ''){
		if(!(int)$order_download_id){ return array(); }
		if(!$language_id){
			if(IS_ADMIN===true){
				$language_id = $this->language->getContentLanguageID();
			}else{
				$language_id = $this->language->getLanguageID();
			}
		}

		$result = $this->db->query("SELECT dd.*, d.*, od.*
									FROM ". $this->db->table('order_downloads')." od
									LEFT JOIN ". $this->db->table('downloads')." d ON od.download_id = d.download_id
									LEFT JOIN ". $this->db->table('download_descriptions')." dd ON d.download_id = dd.download_id AND dd.language_id = '".$language_id."'
									WHERE od.order_download_id='".(int)$order_download_id."'");
		return $result->row;
	}

	/**
	 * Method returns list of downloads that will be append to order
	 * @param int $product_id
	 * @return array
	 */
	public function getProductOrderDownloads($product_id) {
		if ( !(int)$product_id ) {
			return array();
		}
		$query =  $this->db->query(
					"SELECT dd.*, d.*, p2d.*
					 FROM " . $this->db->table("products_to_downloads") . " p2d
					 INNER JOIN " . $this->db->table("downloads") . " d ON (p2d.download_id = d.download_id)
					 LEFT JOIN " . $this->db->table("download_descriptions") . " dd
						ON (d.download_id = dd.download_id
								AND dd.language_id = '" . (int)$this->config->get('storefront_language_id') . "')
					 WHERE p2d.product_id = '" . (int)$product_id . "'
					 		AND d.status=1 AND d.activate<>'before_order' ");
		return $query->rows;
	}

	/**
	 * Method to add/update download in order
	 * Method returns list of downloads that will be append to order
	 * @param (int) $order_product_id
	 * @param (int) $order_id
	 * @param array $download
	 * @return array (added/updated ids)
	 */
	public function addUpdateOrderDownload($order_product_id, $order_id, $download = array()) {
		if ( !(int)$order_product_id || !(int)$order_id || !(int)$download['download_id'] ) {
			return false;
		}

		if($download['activate']!='order_status'){
			$download['activate_order_status_id'] = 0;
		}
		if($download['activate'] == 'before_order'){
			$download['max_downloads'] = '';
			$download['expire_days'] = '';
		}

		//check if we have download yet
		$check = $this->db->query("SELECT od.order_download_id
									FROM ". $this->db->table('order_downloads')." od
									WHERE 	od.order_id='".(int)$order_id."' 
											AND od.order_product_id='" . (int)$order_product_id . "' 
											AND od.download_id='" . (int)$download['download_id'] . "'");
		if($check->num_rows) {
			//need to update
			$return = array();
			//loop cause we can have multiple records
			foreach($check->rows as $row){
				$return[] = $this->updateProductDownloadToOrder($row['order_download_id'], $download);
			}
			return $return;
		} else {
			//create new
			return array( $this->addProductDownloadToOrder($order_product_id, $order_id, $download) );
		}
	}

	/**
	 * Method to update download to the order
	 * @param (int) $order_download_id
	 * @param array $download
	 * @return int (added id)
	 */
	public function updateProductDownloadToOrder($order_download_id, $download = array()) {
		if ( !(int)$order_download_id || empty($download)) {
			return false;
		}
		if((int)$download['expire_days']){
			$expire = "(NOW() + INTERVAL ".(int)$download['expire_days']." DAY)";
		}else{
			$expire = 'NULL';
		}

		$this->db->query("UPDATE " . $this->db->table("order_downloads") . "
							SET name = '" . $this->db->escape($download['name']) . "',
								filename = '" . $this->db->escape($download['filename']) . "',
								mask = '" . $this->db->escape($download['mask']) . "',
								remaining_count = " . ( (int)$download['max_downloads'] ? "'".(int)$download['max_downloads']."'" : 'NULL'). ",
								status = '" . (int)$download['status'] . "',
								activate_order_status_id = '" . (int)$download['activate_order_status_id'] . "',
								expire_date = ".$expire.",
								attributes_data = '".$this->db->escape($download['attributes_data'])."',
								date_modified = NOW()
							WHERE order_download_id = '" . (int)$order_download_id . "'");

		return $order_download_id;
	}


	/**
	 * Method to add download to the order
	 * @param (int) $order_product_id
	 * @param (int) $order_id
	 * @param array $download
	 * @return int (added id)
	 */
	public function addProductDownloadToOrder($order_product_id, $order_id, $download = array()) {
		if ( !(int)$order_product_id || !(int)$order_id ) {
			return false;
		}
		if((int)$download['expire_days']){
			$expire = "(NOW() + INTERVAL ".(int)$download['expire_days']." DAY)";
		}else{
			$expire = 'NULL';
		}
		$this->db->query("INSERT INTO " . $this->db->table("order_downloads") . "
							SET order_id = '" . (int)$order_id . "',
								order_product_id = '" . (int)$order_product_id . "',
								download_id = '" . (int)$download['download_id'] . "',
								name = '" . $this->db->escape($download['name']) . "',
								filename = '" . $this->db->escape($download['filename']) . "',
								mask = '" . $this->db->escape($download['mask']) . "',
								remaining_count = " . ( (int)$download['max_downloads'] ? "'".(int)$download['max_downloads']."'" : 'NULL'). ",
								status = '" . (int)$download['status'] . "',
								activate_order_status_id = '" . (int)$download['activate_order_status_id'] . "',
								expire_date = ".$expire.",
								attributes_data = '".$this->db->escape($download['attributes_data'])."',
								date_modified = NOW(),
								date_added = NOW()");

		return $this->db->getLastId();
	}

	/**
	 * @param int $download_id
	 * @param string $mode - can be "full" - all download attributes (with empty values too),
	 * "to_customer" - download atributes with values that allowed to display for customers,
	 * "to_display"  - all download atributes with values
	 * @return array
	 */
	public function getDownloadAttributesValues($download_id, $mode='full') {
		if(!(int)$download_id){
			return array();
		}
		$output = array();
		$attr = new AAttribute('download_attribute');
		$attributes = $attr->getAttributes();

		$ids = array();
		foreach($attributes as &$attribute){
			if($mode=='to_customer'){
				$attribute['settings'] = unserialize($attribute['settings']);
				if(!$attribute['settings']['show_to_customer']){ continue; }
			}
			$ids[] = (int)$attribute['attribute_id'];
			$attribute['attribute_values'] = $attr->getAttributeValues($attribute['attribute_id']);
		} unset($attribute);

		if($ids){
			$result = $this->db->query( "SELECT dav.attribute_id, dav.attribute_value_ids as value
										  FROM ".$this->db->table('download_attribute_values')." dav
										  LEFT JOIN ".$this->db->table('global_attributes')." ga
										        ON ga.attribute_id = dav.attribute_id
										  WHERE dav.attribute_id IN (".implode(',',$ids).") AND dav.download_id = '".$download_id."'
										  ORDER BY ga.sort_order ASC");

			$attributes_with_options = HtmlElementFactory::getElementsWithOptions();
			foreach($result->rows as $row){
				if(!in_array($row['attribute_id'], $ids)){continue;}
				$row['value'] = unserialize($row['value']);
				if(in_array($attributes[$row['attribute_id']]['element_type'],$attributes_with_options)){
					foreach($attributes[$row['attribute_id']]['attribute_values'] as $values){
						if( (is_array($row['value']) && in_array($values['attribute_value_id'],$row['value']) )){
							// do not include empty value for display for customer
							if(!$row['value'] && in_array($mode,array('to_customer','to_display'))){ continue 1; }
							$output[$attributes[$row['attribute_id']]['name']][] = $values['value'];
						}elseif(!is_array($row['value']) && $values['attribute_value_id']==$row['value'] ){
							// do not include empty value for display for customer
							if(!$row['value'] && in_array($mode,array('to_customer','to_display'))){ continue 1; }
							$output[$attributes[$row['attribute_id']]['name']] = $values['value'];
						}
					}
				}else{
					 // do not include empty value for display for customer or admin display
					if( !has_value($row['value']) && in_array($mode,array('to_customer','to_display'))){ continue;}
				 	$output[$attributes[$row['attribute_id']]['name']] = $row['value'];
					// for checkbox value show text yes or no
					if($attributes[$row['attribute_id']]['element_type']=='C'){
						$output[$attributes[$row['attribute_id']]['name']] = $row['value'] ? $this->language->get('text_yes') : $this->language->get('text_no');
					}
				}

			}
		}
		return $output;
	}

	public function getDownloadAttributesValuesForCustomer($download_id) {
		return $this->getDownloadAttributesValues($download_id,'to_customer');
	}
	public function getDownloadAttributesValuesForDisplay($download_id) {
		return $this->getDownloadAttributesValues($download_id,'to_display');
	}

	public function sendDownload($download_info=array()){
		// do checks
		if(!$download_info || !$this->isFileAvailable($download_info['filename'])){
			return false;
		}
		if($download_info['remaining_count']!='' && $download_info['remaining_count']<1){
			return false;
		}
		if($download_info['expire_date']!='' && dateISO2Int($download_info['expire_date']) < time()){
			return false;
		}

		if( $this->customer->isLogged() && $download_info['activate']!='before_order'){
			$customer_downloads = $this->getCustomerDownloads();
			if(!in_array($download_info['order_download_id'],array_keys($customer_downloads))){
				return false;
			}
		}

		$file = DIR_RESOURCE . $download_info['filename'];
		$mask = basename($download_info['mask']);
		$mime = 'application/octet-stream';
		$encoding = 'binary';

		if (!headers_sent()) {
			if (file_exists($file)) {
				$file_handler = fopen($file,"rb");
				$filesize = filesize($file);
				header('Pragma: public');
				header('Expires: 0');
				header('Content-Description: File Transfer');
				header('Content-Type: ' . $mime);
				header('Content-Transfer-Encoding: ' . $encoding);
				header('Content-Disposition: attachment; filename=' . ($mask ? $mask : basename($file)));
				header('Content-Length: ' . $filesize);
				ob_end_clean();
				$bytes_sent = 0;
				$order_download_history_id = 'NULL';
				while(!feof($file_handler) ){// if we haven't got to the End Of File
					print(fread($file_handler, 1024*8) );//read 8k from the file and send to the user
					flush();//force the previous line to send its info
					if (connection_status()!=0){//check the connection, if it has ended...
						fclose($file_handler);
						exit();
					}else{
						$bytes_sent += 1024*8;
						$prc = round($bytes_sent*100/$filesize,0);
						$prc = $prc>100 ? 100 : $prc;
						//do insert or update
						$sql = "INSERT INTO ".$this->db->table('order_downloads_history')."
														(order_download_history_id,
														 order_download_id,
														 order_id,
														 order_product_id,
														 filename,
														 mask,
														 download_id,
														 download_percent,
														 `time`)
						VALUES (".$order_download_history_id.",
								'".$download_info['order_download_id']."',
								'".$download_info['order_id']."',
								'".$download_info['order_product_id']."',
								'".$download_info['filename']."',
								'".$download_info['mask']."',
								'".$download_info['download_id']."',
								'".$prc."',
								NOW())
						ON DUPLICATE KEY UPDATE
						 download_percent = '".$prc."'";

						$this->db->query($sql);
						if($order_download_history_id == 'NULL'){
							$order_download_history_id = $this->db->getLastId();
						}
					}
				}
				fclose($file_handler);

			} else {
				throw new AException(AC_ERR_LOAD, 'Error: Could not find file ' . $file . '!');
			}
		} else {
			exit('Error: Headers already sent out!');
		}

		if($download_info['remaining_count']!=''){ //if count is not NULL (unlimited)
			$this->updateRemaining($this->request->get['order_download_id']);
		}

		//init controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
		exit;

	}

	/**
	 * @param $order_download_id
	 */
	public function updateRemaining($order_download_id) {
		$this->db->query("UPDATE " . $this->db->table("order_downloads") . "
						  SET remaining_count = (remaining_count - 1)
						  WHERE order_download_id = '" . (int)$order_download_id . "'");
	}

	/**
	 * @param int $start
	 * @param int $limit
	 * @return array
	 */
	public function getCustomerDownloads($start = 0, $limit = 0) {
		$customer_id = (int)$this->customer->getId();
		if(!$customer_id) return array();
		$start =  $start < 0 ? 0 : (int)$start;

		$sql = "SELECT o.order_id,
					  o.order_status_id,
					  od.download_id,
					  od.status,
					  od.date_added,
					  od.order_download_id,
					  d.activate,
					  od.activate_order_status_id,
					  od.name,
					  od.filename,
					  od.mask,
					  od.remaining_count,
					  od.expire_date,
					  op.product_id
			   FROM " . $this->db->table("order_downloads") . " od
			   LEFT JOIN " . $this->db->table("orders") . " o ON (od.order_id = o.order_id)
			   LEFT JOIN " . $this->db->table("downloads") . " d ON (d.download_id = od.download_id)
			   LEFT JOIN " . $this->db->table("order_products") . " op ON (op.order_product_id = od.order_product_id)
			   WHERE o.customer_id = '" . $customer_id . "'
			   ORDER BY  o.date_added DESC, od.sort_order ASC ";
		if($limit){
		   $sql .= "LIMIT " . (int)$start . "," . (int)$limit;
		}

		$query = $this->db->query($sql);
		$downloads = array();
		foreach ($query->rows as $download_info) {
			$downloads[$download_info['order_download_id']] = $download_info;
		}

		return $downloads;

	}

	/**
	 * @return mixed
	 */
	public function getTotalDownloads() {
		return sizeof($this->getCustomerDownloads());
	}

	/**
	 * @param string $resource_path
	 * @return bool
	 */
	public function isFileAvailable($resource_path=''){
		if(!$resource_path){
			return false;
		}
		if(!is_file(DIR_RESOURCE.$resource_path) || !is_readable(DIR_RESOURCE.$resource_path)){
			return false;
		}

		if(!filesize(DIR_RESOURCE.$resource_path)){
			return false;
		}

		return true;
	}

	public function getTextStatusForOrderDownload($download_info){
		$this->load->language('account/download');
		$text_status = '';

		if($download_info['status']==0){
			$text_status = $this->language->get('text_pending');
		}elseif( dateISO2Int($download_info['expire_date']) < time()){
			$text_status = $this->language->get('text_expired');
		}elseif( $download_info['remaining_count'] == '0' ) {
			$text_status = $this->language->get('text_reached_limit');
		}

		if((int)$download_info['activate_order_status_id']>0){
			if((int)$download_info['activate_order_status_id'] != (int)$download_info['order_status_id']){
				$text_status = $this->language->get('text_pending');
			}
		}

		//2. check is file exists
		$download_info['filename'] = trim($download_info['filename']);
		if(!$this->isFileAvailable($download_info['filename'])){
			$err = new AError('Error: file "'.$download_info['filename'].'" (download_id = '.$download_info['order_id'].') of order #'.$download_info['order_id'].' is unavailable for download!');
			$err->toLog()->toDebug()->toMessages();
			$text_status = $this->language->get('text_unavailable');
		}

		return $text_status;
	}
}
