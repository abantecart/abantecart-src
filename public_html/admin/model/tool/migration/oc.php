<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}

require "interface_migration.php";

class Migration_OC implements Migration {

    private $data;
    private $config;
    private $db;
    private $error_msg;
    

    function __construct( $migrate_data, $oc_config )
	{
        $this->config = $oc_config;
        $this->data = $migrate_data;
        $this->error_msg = "";
	}

    function __destruct()
	{
	}

    public function getVersion() {
        return '1.4.9';
    }

	public function getCategories()
	{
        $this->db = mysql_connect($this->data['db_host'], $this->data['db_user'], $this->data['db_password'], true);
		mysql_select_db($this->data['db_name'], $this->db);

        // for now use default language
        $languages_id = 1;

        $categories_query = "SELECT c.category_id,
									cd.name,
									cd.description,
									c.image,
									c.parent_id,
									c.sort_order
                            FROM " . $this->data['db_prefix'] . "category c, " . $this->data['db_prefix'] . "category_description cd
                            WHERE c.category_id = cd.category_id AND cd.language_id = '" . (int)$languages_id . "'
                            ORDER BY c.sort_order, cd.name";
        $categories = mysql_query($categories_query, $this->db);
        if (!$categories){
        	$this->error_msg = 'Migration Error: ' . mysql_error().'<br>File :'.__FILE__.'<br>Line :'.__LINE__.'<br>';
        	return false;
        }

        $result = array();
        while ( $item = mysql_fetch_assoc($categories)  ){
            $result[$item['category_id']] = $item;
        }
        
		mysql_free_result($categories);
		mysql_close($this->db);
		
        return $result;
	}

    public function getManufacturers()
    {
        $this->db = mysql_connect($this->data['db_host'], $this->data['db_user'], $this->data['db_password'], true);
		mysql_select_db($this->data['db_name'], $this->db);

        $sql_query = "
            select manufacturer_id, name, image
            from " . $this->data['db_prefix'] . "manufacturer
            order by name";
        $items = mysql_query($sql_query, $this->db);
        if (!$items){
        	$this->error_msg = 'Migration Error: ' . mysql_error().'<br>File :'.__FILE__.'<br>Line :'.__LINE__.'<br>';
        	return false;
        }

        $result = array();
        while ( $item = mysql_fetch_assoc($items)  ){
            $result[$item['manufacturer_id']] = $item;
        }

		mysql_free_result($items);
		mysql_close($this->db);

        return $result;
    }
	
	public function getProducts()
	{
    	$this->error_msg = "";
        $this->db = mysql_connect($this->data['db_host'], $this->data['db_user'], $this->data['db_password'], true);
		mysql_select_db($this->data['db_name'], $this->db);

        // for now use default language
        $languages_id = 1;

        $products_query = "
            select
                p.product_id,
                p.model,
                p.quantity,
                p.stock_status_id,
                p.image,
                p.manufacturer_id,
                p.shipping,
                p.price,
                pd.name,
                pd.description,
                p.tax_class_id,
                p.date_available,
                p.weight as weight,
                p.weight_class_id,
                p.status,
                p.date_added
            from
                " . $this->data['db_prefix'] . "product p,
                " . $this->data['db_prefix'] . "product_description pd
            where
                pd.product_id = p.product_id
                and pd.language_id = '" . (int)$languages_id . "'";
        $items = mysql_query($products_query, $this->db);
        if (!$items){
        	$this->error_msg = 'Migration Error: ' . mysql_error().'<br>File :'.__FILE__.'<br>Line :'.__LINE__.'<br>';
        	return false;
        }

        $result = array();
        while ( $item = mysql_fetch_assoc($items)  ){
            $result[$item['product_id']] = $item;
        }

        //add categories id
        $sql_query = "
            select category_id, product_id
            from " . $this->data['db_prefix'] . "product_to_category";
        $items = mysql_query($sql_query, $this->db);
        if (!$items){
        	$this->error_msg = 'Migration Error: ' . mysql_error().'<br>File :'.__FILE__.'<br>Line :'.__LINE__.'<br>';
        	return false;
        }

        while ( $item = mysql_fetch_assoc($items)  ){
            if ( !empty($result[$item['product_id']]) )
            $result[$item['product_id']]['product_category'][] = $item['category_id'];
        }

		mysql_close($this->db);

        return $result;
	}
	
	public function getCustomers()
	{
        $this->db = mysql_connect($this->data['db_host'], $this->data['db_user'], $this->data['db_password'], true);
		mysql_select_db($this->data['db_name'], $this->db);

        $customers_query = "
            select
                c.customer_id,
                c.firstname,
                c.lastname,
                c.email,
                c.telephone,
                c.fax,
                c.password,
                c.newsletter                
            from
                " . $this->data['db_prefix'] . "customer c ";

        $customers = mysql_query($customers_query, $this->db);
        if (!$customers){
        	$this->error_msg = 'Migration Error: ' . mysql_error().'<br>File :'.__FILE__.'<br>Line :'.__LINE__.'<br>';
        	return false;
        }
        $result = array();
        while ( $customer = mysql_fetch_assoc($customers)  ){
            $result[$customer['customer_id']] = $customer;
        }

        // add customers addresses
        $address_query = "
            select a.customer_id,
                a.company,
                a.firstname,
                a.lastname,
                a.address_1,
                a.address_2,
                a.postcode,
                a.city,
                a.zone_id,
                a.country_id
            from
                " . $this->data['db_prefix'] . "address a ";
        $addresses = mysql_query($address_query, $this->db);
        if (!$addresses){
        	$this->error_msg = 'Migration Error: ' . mysql_error().'<br>File :'.__FILE__.'<br>Line :'.__LINE__.'<br>';
        	return false;
        }

        while ( $address = mysql_fetch_assoc($addresses)  ){
            $result[$address['customer_id']]['address'][] = $address;
        }

		mysql_close($this->db);
        return $result;

	}
	
	public function getOrders()
	{
	}
	
	public function getErrors()
	{
		return $this->error_msg;
	}
}