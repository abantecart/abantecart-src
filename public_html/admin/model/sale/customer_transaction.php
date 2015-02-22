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
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
/**
 * Class ModelSaleCustomerTransaction
 * @property ModelSaleCustomer $model_sale_customer
 */
class ModelSaleCustomerTransaction extends Model {
		
	public function deleteCustomerTransaction($customer_transaction_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_transactions
		                 WHERE customer_transaction_id = '" . (int)$customer_transaction_id . "'");
	}

    public function getCustomerTransaction($customer_transaction_id=0){
        if(!(int)$customer_transaction_id) return array();

        $sql = "SELECT *, t.date_added, t.date_modified,
				CASE
					WHEN t.section=1
						THEN CONCAT(u.firstname,' ',u.lastname, ' (',u.username,')')
					ELSE
						''
					 END as user
				FROM " . $this->db->table("customer_transactions") . " t
				LEFT JOIN " . $this->db->table("users") . " u ON u.user_id = t.created_by
				WHERE t.customer_transaction_id = '" . (int)$customer_transaction_id . "'";
        $result = $this->db->query($sql);
        $row = $result->row;
        if(empty($row['user']) && $row['section']!=1){
            $this->load->model('sale/customer');
            $customer_info = $this->model_sale_customer->getCustomer($row['customer_id']);
            $row['user'] = $customer_info['firstname'].' '.$customer_info['lastname'];
        }
        return $row;
    }

    public function getCustomerTransactions($data=array(), $mode=''){
        // get decrypted customer name first
        $this->load->model('sale/customer');
        $customer_info = $this->model_sale_customer->getCustomer((int)$data['customer_id']);
        $sql = "SELECT *, t.date_added, t.date_modified,
				CASE
					WHEN t.section=1
						THEN CONCAT(u.firstname,' ',u.lastname, ' (',u.username,')')
					ELSE
						'".$customer_info['firstname'].' '.$customer_info['lastname']."'
					 END as user
				FROM " . $this->db->table("customer_transactions") . " t
				LEFT JOIN " . $this->db->table("users") . " u ON u.user_id = t.created_by
				WHERE t.customer_id = '" . (int)$data['customer_id'] . "'";

        $filter = (isset($data['filter']) ? $data['filter'] : array());
        $implode = array();
        if (has_value($filter['date_start']) && has_value($filter['date_end'])) {
            $implode[] = "DATE(t.date_added) BETWEEN DATE('" . $this->db->escape($filter['date_start']) . "') AND DATE('" . $this->db->escape($filter['date_end']) . "')";
        }

        if (has_value($filter['debit'])) {
            $implode[] = "ROUND(t.debit,2) = '" . round((float)$filter['debit'],2) . "'";
        }

        if (has_value($filter['credit'])) {
            $implode[] = "ROUND(t.credit,2) = '" . round((float)$filter['credit'],2) . "'";
        }
        if (has_value($filter['transaction_type'])) {
            $implode[] = "t.transaction_type like '%" . $this->db->escape($filter['transaction_type']) . "%'";
        }
        if (has_value($filter['user'])) {
            $implode[] = "LOWER(CASE
								WHEN t.section=1
									THEN CONCAT(u.firstname,' ',u.lastname, ' (',u.username,')')
								ELSE
									'".$customer_info['firstname'].' '.$customer_info['lastname']."'
								 END) like '%" . mb_strtolower($this->db->escape($filter['user'])) . "%'";
        }

        if ($implode) {
            $sql .= " AND " . implode(" AND ", $implode);
        }

        //If for total, we done bulding the query
        if ($mode == 'total_only') {
            $query = $this->db->query($sql);
            return $query->num_rows;
        }

        $sort_data = array(
            't.date_added',
            'user',
            'debit',
            'credit',
            'transaction_type'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY t.date_added";
        }

        if (isset($data['order']) && (strtoupper($data['order']) == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query( $sql );
        return $query->rows;
    }

    public function getTotalCustomerTransactions($data){
        return $this->getCustomerTransactions($data,'total_only');
    }

    public function getBalance($customer_id){
        $cache_name = 'balance.'.$customer_id;
        $balance = $this->cache->get($cache_name);
        if(is_null($balance)){
            $sql = "SELECT SUM(credit) - SUM(debit) as balance
					FROM " . $this->db->table("customer_transactions") . "
					WHERE customer_id=".(int)$customer_id;
            $query = $this->db->query($sql);
            $balance = (float)$query->row['balance'];
			$this->cache->set($cache_name,$balance);
        }
        return $balance;
    }

    public function addCustomerTransaction($data=array()){
        if(((float)$data['credit'] || (float)$data['debit']) && (int)$data['customer_id']){
            $sql = "INSERT INTO " . $this->db->table("customer_transactions") . " (`customer_id`,`order_id`,`created_by`,`credit`,`debit`,`section`, `transaction_type`,`comment`,`description`,`date_added`)
					VALUES (
							'".(int)$data['customer_id']."',
							'".(int)$data['order_id']."',
							'".$this->user->getId()."',
							'".(float)$data['credit']."',
							'".(float)$data['debit']."',
							'1',
							'".$this->db->escape($data['transaction_type'])."',
							'".$this->db->escape($data['comment'])."',
							'".$this->db->escape($data['description'])."',
							NOW()
							)";
            $this->db->query($sql);
            $this->cache->delete('balance.'.(int)$data['customer_id']);
        }

        return $this->db->getLastId();
    }


    public function getTransactionTypes(){
        $cache_name = 'transaction_types';
        $output = $this->cache->get($cache_name);
        if(is_null($output)){
            $sql = "SELECT DISTINCT `transaction_type` FROM " . $this->db->table("customer_transactions") . " ORDER BY `transaction_type` ASC";
            $result = $this->db->query($sql);
            foreach($result->rows as $row){
                $output[$row['transaction_type']] = $row['transaction_type'];
            }
            $this->cache->set($cache_name,$output);
        }
        return $output;
    }


}