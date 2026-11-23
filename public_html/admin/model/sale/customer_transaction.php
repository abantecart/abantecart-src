<?php 
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2026 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */
/** @noinspection PhpMultipleClassDeclarationsInspection */

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ModelSaleCustomerTransaction extends Model
{
    /**
     * @param int $customerTransactionId
     *
     * @throws AException
     */
    public function deleteCustomerTransaction(int $customerTransactionId)
    {
        $this->db->query(
            "DELETE FROM " . $this->db->table('customer_transactions') . "
             WHERE customer_transaction_id = '" . $customerTransactionId . "'"
        );
    }

    /**
     * @param int $customerTransactionId
     *
     * @return array
     * @throws AException
     */
    public function getCustomerTransaction(int $customerTransactionId = 0)
    {
        if (!$customerTransactionId) {
            return [];
        }

        $sql = "SELECT *, t.date_added, t.date_modified,
				CASE
					WHEN t.section=1
						THEN CONCAT(u.firstname,' ',u.lastname, ' (',u.username,')')
					ELSE
						''
					 END as user
				FROM " . $this->db->table("customer_transactions") . " t
				LEFT JOIN " . $this->db->table("users") . " u 
				    ON u.user_id = t.created_by
				WHERE t.customer_transaction_id = '" . $customerTransactionId . "'";
        $result = $this->db->query($sql);
        $row = $result->row;
        if (empty($row['user']) && $row['section'] != 1) {
            /** @var ModelSaleCustomer $cMdl */
            $cMdl = $this->load->model('sale/customer');
            $customer_info = $cMdl->getCustomer($row['customer_id']);
            $row['user'] = $customer_info['firstname'] . ' ' . $customer_info['lastname'];
        }
        return $row;
    }

    /**
     * @param array $data
     * @param string|null $mode
     *
     * @return array|int
     * @throws AException
     */
    public function getCustomerTransactions(array $data = [], ?string $mode = '')
    {
        // get decrypted customer name first
        /** @var ModelSaleCustomer $cMdl */
        $cMdl = $this->load->model('sale/customer');
        $customer_info = $cMdl->getCustomer((int) $data['customer_id']);
        $sql = "SELECT *, t.date_added, t.date_modified,
				CASE
					WHEN t.section=1
						THEN CONCAT(u.firstname,' ',u.lastname, ' (',u.username,')')
					ELSE
						'" . $this->db->escape($customer_info['firstname'] . ' ' . $customer_info['lastname']) . "'
					 END as user
				FROM " . $this->db->table("customer_transactions") . " t
				LEFT JOIN " . $this->db->table("users") . " u ON u.user_id = t.created_by
				WHERE t.customer_id = '" . (int) $data['customer_id'] . "'";

        $filter = $data['filter'] ?? [];
        $implode = [];
        if (has_value($filter['date_start']) && has_value($filter['date_end'])) {
            $implode[] = "DATE(t.date_added) BETWEEN 
                DATE('" . $this->db->escape($filter['date_start']) . "') 
                    AND DATE('". $this->db->escape($filter['date_end']) . "')";
        }

        if (has_value($filter['debit'])) {
            $implode[] = "ROUND(t.debit,2) = '" . round((float) $filter['debit'], 2) . "'";
        }

        if (has_value($filter['credit'])) {
            $implode[] = "ROUND(t.credit,2) = '" . round((float) $filter['credit'], 2) . "'";
        }
        if (has_value($filter['transaction_type'])) {
            $implode[] = "t.transaction_type like '%" . $this->db->escape($filter['transaction_type']) . "%'";
        }
        if (has_value($filter['user'])) {
            $implode[] = "LOWER(
                CASE
                    WHEN t.section=1
                        THEN CONCAT(u.firstname,' ',u.lastname, ' (',u.username,')')
                    ELSE
                        '" . $this->db->escape($customer_info['firstname'] . ' ' . $customer_info['lastname']) . "'
                     END) like '%" . mb_strtolower($this->db->escape($filter['user'])) . "%'";
        }

        if ($implode) {
            $sql .= " AND " . implode(" AND ", $implode);
        }

        //If for total, we're done building the query
        if ($mode == 'total_only') {
            $query = $this->db->query($sql);
            return $query->num_rows;
        }

        $sort_data = [
            't.date_added',
            'user',
            'debit',
            'credit',
            'transaction_type',
        ];

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

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }

        $query = $this->db->query($sql);
        return $query->rows;
    }

    /**
     * @param array $data
     *
     * @return int
     * @throws AException
     */
    public function getTotalCustomerTransactions($data)
    {
        return $this->getCustomerTransactions($data, 'total_only');
    }

    /**
     * @param int $customerId
     *
     * @return float
     * @throws AException
     */
    public function getBalance($customerId)
    {
        $customerId = (int) $customerId;
        $sql = "SELECT SUM(credit) - SUM(debit) as balance
                FROM " . $this->db->table("customer_transactions") . "
                WHERE customer_id=" . (int) $customerId;
        $query = $this->db->query($sql);
        return (float) $query->row['balance'];
    }

    /**
     * @param array $data
     *
     * @return bool|int
     * @throws AException|TransportExceptionInterface
     */
    public function addCustomerTransaction($data = [])
    {
        if ((!(float) $data['credit'] && !(float) $data['debit']) || !(int) $data['customer_id']) {
            return false;
        }
        $sql = "INSERT INTO " . $this->db->table("customer_transactions") . "
                    (`customer_id`,`order_id`,`created_by`,`credit`,`debit`,`section`, `transaction_type`,`comment`,`description`,`date_added`)
                VALUES (
                        '" . (int) $data['customer_id'] . "',
                        '" . (int) $data['order_id'] . "',
                        '" . $this->user->getId() . "',
                        '" . (float) $data['credit'] . "',
                        '" . (float) $data['debit'] . "',
                        '1',
                        '" . $this->db->escape($data['transaction_type']) . "',
                        '" . $this->db->escape($data['comment']) . "',
                        '" . $this->db->escape($data['description']) . "',
                        NOW()
                        )";
        $this->db->query($sql);
        $transaction_id = $this->db->getLastId();

        if ($data['notify']) {
            /** @var ModelSaleCustomer $cMdl */
            $cMdl = $this->load->model('sale/customer');
            $customer_info = $cMdl->getCustomer($data['customer_id']);

            if ($customer_info) {
                //detect customer's language
                $sql = "SELECT language_id
                        FROM " . $this->db->table('orders') . "
                        WHERE customer_id = '" . (int) $data['customer_id'] . "'
                        ORDER BY date_added DESC";
                $result = $this->db->query($sql);
                $language_code = '';
                if ($result->row['language_id']) {
                    $lang = $this->language->getLanguageDetailsByID($result->row['language_id']);
                    $language_code = $lang['code'];
                }

                if (!$language_code) {
                    $language_code = $this->language->getDefaultLanguageCode();
                }

                //load language specific for the order in admin section
                $language = new ALanguage(Registry::getInstance(), $language_code, 1);
                $language->load('sale/customer');

                /** @var ModelSettingStore $storeMdl */
                $storeMdl = $this->load->model('setting/store');
                $storeInfo = $storeMdl->getStore((int) $this->session->data['current_store_id']);

                $url = html_entity_decode(
                    $storeInfo['config_url'] . 'index.php?rt=account/transactions',
                    ENT_QUOTES,
                    'UTF-8'
                );

                $amount = $this->currency->format($data['credit'] - $data['debit']);

                $this->data['mail_template_data'] = [
                    'store_name'       => $storeInfo['store_name'],
                    'amount'           => $amount,
                    'transactions_url' => $url,
                ];
                $this->extensions->hk_ProcessData($this, 'admin_new_transaction_notify', [$data]);
                $mail = new AMail($this->config);
                $mail->setTo($customer_info['email']);
                $mail->setFrom($storeInfo['store_main_email']);
                $mail->setSender($storeInfo['store_name']);
                $mail->setTemplate('admin_new_transaction_notify', $this->data['mail_template_data']);
                $mail->send();

                //notify customer
                $language->load('common/im');
                $message_arr = [
                    0 => [
                        'message' => $language->getAndReplace(
                                      'im_customer_account_update_text_to_customer',
                            replaces: [$storeInfo['store_name'], $amount, $storeInfo['store_name']]
                        ),
                    ],
                ];
                $this->im->sendToCustomer($data['customer_id'], 'customer_account_update', $message_arr);
            }
        }

        return $transaction_id;
    }

    /**
     * @return array
     * @throws AException
     */
    public function getTransactionTypes()
    {
        $cache_key = 'transaction_types';
        $output = $this->cache->pull($cache_key);
        if ($output === false) {
            $sql = "SELECT DISTINCT `transaction_type`
                    FROM " . $this->db->table("customer_transactions") . "
                    ORDER BY `transaction_type`";
            $result = $this->db->query($sql);
            $output = array_column($result->rows, 'transaction_type', 'transaction_type');
            $this->cache->push($cache_key, $output);
        }
        return $output;
    }
}
