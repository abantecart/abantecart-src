<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2013 Belavier Commerce LLC

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

require_once DIR_ROOT.'/admin/model/tool/migration/interface_migration.php';

class Migration_OC15x implements Migration {

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

    public function getName() {
        return 'OpenCart';
    }
    public function getVersion() {
        return '1.5.x';
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
                p.sku,
                p.location,
                p.quantity,
                p.stock_status_id,
                p.image,
                p.manufacturer_id,
                p.shipping,
                p.price,
                pd.name,
                pd.description,
                pd.description,
                pd.meta_keyword,
                pd.meta_description,
                pd.tag,
                p.tax_class_id,
                p.date_available,
                p.weight as weight,
                p.weight_class_id,
                p.length as length,
                p.length_class_id,
                p.height,
                p.status,
                p.viewed,
                p.minimum,
                p.subtract,
                p.sort_order,
                p.date_added,
                p.date_modified
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
	
	public function getProductOptions(){
		$this->error_msg = "";
		$this->db = mysql_connect($this->data[ 'db_host' ], $this->data[ 'db_user' ], $this->data[ 'db_password' ], true);
		mysql_select_db($this->data[ 'db_name' ], $this->db);
		//build opencart options
		$option_types_map = array( 1 => 'R', 2 => 'C', 4 => 'I', 5 => 'S', 6 => 'T', 7 => 'U', 8 => 'D', 9 => 'E', 11 => 'S', 12 => 'D');
		$result = array();
		foreach ($option_types_map as $oc_code => $abc_code) {
			$optons = "
				SELECT 	po.product_id as product_id, 
					po.product_option_id  as product_option_id,
					od.name as product_option_name,
					po.required as required,
					o.sort_order as sort_order,
					0 as products_text_attributes_id,
					'$abc_code' as element_type 
				FROM " . $this->data['db_prefix'] . "product_option po 
				LEFT JOIN `" . $this->data['db_prefix'] . "option` o ON (o.option_id = po.option_id )  
				LEFT JOIN `" . $this->data['db_prefix'] . "option_description` od ON (od.option_id = po.option_id AND od.language_id=1 ) 
				WHERE po.option_id = $oc_code
			";		

			$items = mysql_query($optons, $this->db);
			if (!$items) {
				$this->error_msg = 'Migration Error: ' . mysql_error() . '<br>File :' . __FILE__ . '<br>Line :' . __LINE__ . '<br>';
				return false;
			}
			while ($item = mysql_fetch_assoc($items)) {
				$result['product_options'][] = $item;
			}		
			mysql_free_result($items);		
		}

		//build opencart option values
		$option_vals = "
			SELECT
				pov.price_prefix as price_prefix, 
				pov.price as price, 
				pov.product_id as product_id, 
				pov.product_option_id as product_option_id,
				pov.product_option_value_id as product_option_value_id,
				pov.quantity as quantity,
				pov.weight as weight,
				ovd.name as product_option_value_name,
				0 as products_text_attributes_id,
				ov.sort_order as sort_order
			FROM " . $this->data['db_prefix'] . "product_option_value pov
			LEFT JOIN " . $this->data['db_prefix'] . "option_value ov ON (ov.option_value_id = pov.option_value_id)
			LEFT JOIN " . $this->data['db_prefix'] . "option_value_description ovd ON (ovd.option_value_id = pov.option_value_id AND language_id = 1 )
			ORDER BY pov.product_id, pov.product_option_id	
		";

		$items = mysql_query($option_vals, $this->db);
		if (!$items) {
		    $this->error_msg = 'Migration Error: ' . mysql_error() . '<br>File :' . __FILE__ . '<br>Line :' . __LINE__ . '<br>';
		    return false;
		}
		while ($item = mysql_fetch_assoc($items)) {
		    $result['product_option_values'][] = $item;
		}		
		
		mysql_free_result($items);		

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
                c.store_id,
                c.firstname,
                c.lastname,
                c.email,
                c.telephone,
                c.fax,
                c.password,
                c.newsletter,
                c.ip,
                c.status,
                c.approved,
                c.date_added                
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