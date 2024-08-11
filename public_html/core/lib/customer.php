<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2024 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 */

/** @noinspection PhpUndefinedClassInspection */

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * Class ACustomer
 */
class ACustomer
{
    /** @var int */
    protected $customer_id;
    /** @var string */
    protected $loginname;
    /** @var string */
    protected $firstname;
    /** @var string */
    protected $lastname;
    /** @var string */
    protected $email;
    /** @var string */
    protected $telephone;
    /** @var string */
    protected $fax;
    /** @var int */
    protected $newsletter;
    /** @var int */
    protected $customer_group_id;
    /** @var string */
    protected $customer_group_name;
    /** @var bool */
    protected $customer_tax_exempt;
    /** @var int */
    protected $address_id;
    /** @var AConfig */
    protected $config;
    /** @var ACache */
    protected $cache;
    /** @var ADB */
    protected $db;
    /** @var ARequest */
    protected $request;
    /** @var ASession */
    protected $session;
    /** @var ADataEncryption */
    protected $dcrypt;
    /** @var ExtensionsApi */
    protected $extensions;
    /** @var ALoader */
    protected $load;

    /** @var array (unauthenticated customer details) */
    protected $unauth_customer = [];

    /**
     * @param Registry $registry
     *
     * @throws AException
     */
    public function __construct($registry)
    {
        $this->cache = $registry->get('cache');
        $this->config = $registry->get('config');
        $this->db = $registry->get('db');
        $this->request = $registry->get('request');
        $this->session = $registry->get('session');
        $this->dcrypt = $registry->get('dcrypt');
        $this->load = $registry->get('load');
        $this->extensions = $registry->get('extensions');

        if (isset($this->session->data['customer_id'])) {
            $customer_data = $this->db->query(
                "SELECT c.*, cg.* 
                FROM " . $this->db->table("customers") . " c
                LEFT JOIN " . $this->db->table("customer_groups") . " cg 
                    ON c.customer_group_id = cg.customer_group_id
                WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "' 
                    AND status = '1'"
            );

            if ($customer_data->num_rows) {
                $this->_customer_init($customer_data->row);
            } else {
                $this->logout();
            }
        } elseif (isset($this->request->cookie['customer'])) {
            //we have unauthenticated customer
            $encryption = new AEncryption($this->config->get('encryption_key'));
            $this->unauth_customer = unserialize($encryption->decrypt($this->request->cookie['customer']));
            //customer is not valid or not from the same store (under the same domain)
            if (
                $this->unauth_customer['script_name'] != $this->request->server['SCRIPT_NAME']
                || !$this->isValidEnabledCustomer()
            ) {
                //clean up
                $this->unauth_customer = [];
                //expire unauth cookie
                unset($_COOKIE['customer']);
                setCookieOrParams(
                    'customer',
                    '',
                    [
                        'lifetime' => time() - 3600,
                        'path'     => dirname($this->request->server['PHP_SELF']),
                    ]
                );
            }
            //check if unauthenticated customer cart content was found and merge with session
            $saved_cart = $this->getCustomerCart();
            if (!empty($saved_cart) && count($saved_cart)) {
                $this->mergeCustomerCart($saved_cart);
            }
        }

        //Update online customers' activity
        $ip = $this->request->getRemoteIP();
        $url = '';
        if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
            $url = 'http://' . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
        }
        $referer = '';
        if (isset($this->request->server['HTTP_REFERER'])) {
            $referer = $this->request->server['HTTP_REFERER'];
        }
        $customer_id = '';
        if ($this->isLogged()) {
            $customer_id = $this->getId();
        } else {
            if ($this->isUnauthCustomer()) {
                $customer_id = $this->isUnauthCustomer();
            }
        }
        /** @var ModelToolOnlineNow $mdl */
        $mdl = $this->load->model('tool/online_now');
        $mdl->setOnline($ip, $customer_id, $url, $referer);
        //call hooks
        $this->extensions->hk_ProcessData($this, 'constructor', $customer_id);
    }

    /**
     * @param string $loginname
     * @param string $password
     *
     * @return bool
     * @throws AException
     */
    public function login($loginname, $password)
    {
        $approved_only = '';
        if ($this->config->get('config_customer_approval')) {
            $approved_only = " AND approved = '1'";
        }

        //Supports older passwords for upgraded/migrated stores prior to 1.2.8
        $add_pass_sql = '';
        if (defined('SALT')) {
            $add_pass_sql = "OR password = '" . $this->db->escape(md5($password . SALT)) . "'";
        }
        $customer_data = $this->db->query(
            "SELECT *
            FROM " . $this->db->table("customers") . "
            WHERE LOWER(loginname)  = LOWER('" . $this->db->escape($loginname) . "')
                AND (
                    password = 	SHA1(CONCAT(salt, 
                                SHA1(CONCAT(salt, SHA1('" . $this->db->escape($password) . "')))
                            ))
                    " . $add_pass_sql . "
                )
                AND status = '1' " . $approved_only
        );
        if ($customer_data->num_rows) {
            $this->_customer_init($customer_data->row);
            $this->session->data['customer_id'] = $this->customer_id;
            //load customer saved cart and merge with session cart before login
            $cart = $this->getCustomerCart();
            $this->mergeCustomerCart($cart);
            //save merged cart
            $this->saveCustomerCart();

            //set cookie for unauthenticated user (expire in 1 year)
            $encryption = new AEncryption($this->config->get('encryption_key'));
            $customer_data = $encryption->encrypt(
                serialize(
                    [
                        'first_name'  => $this->firstname,
                        'customer_id' => $this->customer_id,
                        'script_name' => $this->request->server['SCRIPT_NAME'],
                    ]
                )
            );
            //Set cookie for this customer to track unauthenticated activity, expire in 1 year
            setCookieOrParams(
                'customer',
                $customer_data,
                [
                    'lifetime' => time() + 60 * 60 * 24 * 365,
                    'path'     => dirname($this->request->server['PHP_SELF']),
                    'secure'   => (defined('HTTPS') && HTTPS),
                    'httponly' => true,
                    'samesite' => ((defined('HTTPS') && HTTPS) ? 'None' : 'lax'),
                ]
            );
            //set date of login
            $this->setLastLogin($this->customer_id);
            $this->extensions->hk_ProcessData($this, 'login_success', $customer_data);
            return true;
        } else {
            $this->extensions->hk_ProcessData($this, 'login_failed');
            return false;
        }
    }

    /**
     * Init customer
     *
     * @param $data array
     *
     * @return void
     * @throws AException
     */
    private function _customer_init($data)
    {
        $this->customer_id = (int)$data['customer_id'];
        $this->loginname = $data['loginname'];
        $this->firstname = $data['firstname'];
        $this->lastname = $data['lastname'];
        if ($this->dcrypt->active) {
            $this->email = $this->dcrypt->decrypt_field($data['email'], $data['key_id']);
            $this->telephone = $this->dcrypt->decrypt_field($data['telephone'], $data['key_id']);
            $this->fax = $this->dcrypt->decrypt_field($data['fax'], $data['key_id']);
        } else {
            $this->email = $data['email'];
            $this->telephone = $data['telephone'];
            $this->fax = $data['fax'];
        }
        $this->newsletter = (int)$data['newsletter'];

        $this->customer_group_id = (int)$data['customer_group_id'];
        //save it to use in APromotion class
        $this->session->data['customer_group_id'] = (int)$data['customer_group_id'];

        $this->customer_group_name = $data['name'];

        $this->customer_tax_exempt = $data['tax_exempt'];
        //save this sign to use in ATax lib
        $this->session->data['customer_tax_exempt'] = $data['tax_exempt'];

        $this->address_id = (int)$data['address_id'];

        $this->db->query("SET @CUSTOMER_ID = '" . (int)$this->customer_id . "'");
    }

    public function setLastLogin($customer_id)
    {
        $customer_id = (int)$customer_id;
        if (!$customer_id) {
            return false;
        }

        //insert new record
        $this->db->query(
            "UPDATE `" . $this->db->table("customers") . "`
            SET `last_login` = NOW()
            WHERE customer_id = " . $customer_id
        );
        return true;
    }

    /**
     * @void
     */
    public function logout()
    {
        unset($this->session->data['customer_id']);
        unset($this->session->data['customer_group_id']);

        $this->customer_id = '';
        $this->loginname = '';
        $this->firstname = '';
        $this->lastname = '';
        $this->email = '';
        $this->telephone = '';
        $this->fax = '';
        $this->newsletter = '';
        $this->customer_group_id = '';
        $this->customer_group_name = '';
        $this->customer_tax_exempt = '';
        $this->address_id = '';

        //expire unauth cookie
        unset($_COOKIE['customer']);
        setCookieOrParams(
            'customer',
            '',
            [
                'lifetime' => time() - 3600,
                'path'     => dirname($this->request->server['PHP_SELF']),
            ]
        );
        $this->extensions->hk_ProcessData($this, 'logout');
    }

    /**
     * @param string $token
     *
     * @return bool|int
     */
    public function isLoggedWithToken($token)
    {
        if (isset($this->session->data['token']) && !isset($token)
            || (isset($token) && isset($this->session->data['token']) && $token != $this->session->data['token'])
        ) {
            return false;
        } else {
            return $this->customer_id;
        }
    }

    /**
     * @return int
     */
    public function isUnauthCustomer()
    {
        return $this->unauth_customer['customer_id'] ?? null;
    }

    /**
     * @return string
     */
    public function getUnauthName()
    {
        return $this->unauth_customer['first_name'] ?? '';
    }

    /**
     * @return int
     */
    public function isLogged()
    {
        return $this->customer_id;
    }

    /**
     * @return bool
     */
    public function isTaxExempt()
    {
        if ($this->customer_tax_exempt) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->customer_id;
    }

    /**
     * Validate if loginname is the same as email.
     *
     * @return bool
     */
    public function isLoginnameAsEmail()
    {
        if ($this->loginname == $this->email) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstname;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastname;
    }

    /**
     * @return string
     */
    public function getLoginName()
    {
        return $this->loginname;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * @return int
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }

    /**
     * @return int
     */
    public function getCustomerGroupId()
    {
        return $this->customer_group_id;
    }

    /**
     * @return int
     */
    public function getAddressId()
    {
        return $this->address_id;
    }

    /**
     * @param array $data_array
     * @param string $format
     * @param array $locate
     *
     * @return string
     * @since 1.2.7
     *
     */
    public function getFormattedAddress($data_array, $format = '', $locate = [])
    {
        $data_array = (array)$data_array;
        // Set default format
        if ($format == '') {
            $format = '{firstname} {lastname}'
                . "\n" . '{company}'
                . "\n" . '{address_1}'
                . "\n" . '{address_2}'
                . "\n" . '{city} {postcode}'
                . "\n" . '{zone}'
                . "\n" . '{country}';
        }
        //when some data missing - remove it from address format
        preg_match_all('/\{(.*?)}/', $format, $matches);
        if ($matches[1]) {
            $matches = $matches[1];
            foreach ($matches as $key) {
                if (!isset($data_array[$key])) {
                    $format = str_replace('{' . $key . '}', '', $format);
                }
            }
            $format = trim($format);
        }

        //Set default variable to be set for address based on the data
        if (count($locate) <= 0) {
            $locate = [];
            foreach ($data_array as $key => $value) {
                $locate[] = "{" . $key . "}";
            }
        }
        return nl2br(
            preg_replace(
                ["/\s\s+/", "/\r\r+/", "/\n\n+/"], '<br />',
                trim(str_replace($locate, $data_array, $format))
            )
        );
    }

    /**
     * Customer Transactions Section. Track account balance transactions.
     * Return customer account balance in customer currency based on debit/credit calculation
     *
     * @return float|bool
     * @throws AException
     */
    public function getBalance()
    {
        if (!$this->isLogged()) {
            return false;
        }

        $query = $this->db->query(
            "SELECT sum(credit) - sum(debit) as balance
            FROM " . $this->db->table("customer_transactions") . "
            WHERE customer_id = '" . (int)$this->getId() . "'"
        );
        return (float)$query->row['balance'];
    }

    /**
     * Record debit transaction
     *
     * @param array $tr_details - amount, order_id, transaction_type, description, comments, creator
     *
     * @return bool
     * @throws AException
     */
    public function debitTransaction($tr_details)
    {
        return $this->_record_transaction('debit', $tr_details);
    }

    /**
     * Record credit transaction
     *
     * @param array $tr_details - amount, order_id, transaction_type, description, comments, creator
     *
     * @return bool
     * @throws AException
     */
    public function creditTransaction($tr_details)
    {
        return $this->_record_transaction('credit', $tr_details);
    }

    /**
     * Record cart content
     */
    public function saveCustomerCart()
    {
        $customer_id = $this->customer_id;
        $store_id = (int)$this->config->get('config_store_id');
        if (!$customer_id) {
            $customer_id = $this->unauth_customer['customer_id'];
        }
        if (!$customer_id) {
            return null;
        }

        //before write get cart-info from db to non-override cart for other stores of multistore
        $result = $this->db->query(
            "SELECT cart
            FROM " . $this->db->table("customers") . "
            WHERE customer_id = '" . (int)$customer_id . "' 
                AND status = '1'"
        );
        $cart = unserialize($result->row['cart']);
        $cart = $cart ?: [];
        $cart['store_' . $store_id] = $this->session->data['cart'];
        $this->db->query(
            "UPDATE " . $this->db->table("customers") . "
            SET
                cart = '" . $this->db->escape(serialize($cart)) . "',
                ip = '" . $this->db->escape($this->request->getRemoteIP()) . "'
            WHERE customer_id = '" . (int)$customer_id . "'"
        );
    }

    /**
     * Confirm that current customer is valid
     *
     *
     * @return bool
     * @throws AException
     */
    public function isValidEnabledCustomer()
    {
        $customer_id = $this->customer_id;
        if (!$customer_id) {
            $customer_id = $this->unauth_customer['customer_id'];
        }
        if (!$customer_id) {
            return false;
        }

        $sql = "SELECT cart
                FROM " . $this->db->table("customers") . "
                WHERE customer_id = '" . (int)$customer_id . "' 
                    AND status = '1'";
        $result = $this->db->query($sql);
        if ($result->num_rows) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get cart content
     *
     * @return array()
     * @throws AException
     */
    public function getCustomerCart()
    {
        $store_id = (int)$this->config->get('config_store_id');
        $customer_id = $this->customer_id;
        if (!$customer_id) {
            $customer_id = $this->unauth_customer['customer_id'];
        }
        if (!$customer_id) {
            return [];
        }

        $cart = [];
        $sql = "SELECT cart
                FROM " . $this->db->table("customers") . "
                WHERE customer_id = '" . (int)$customer_id . "' 
                    AND status = '1'";

        $result = $this->db->query($sql);
        if ($result->num_rows) {
            //load customer saved cart
            if (($result->row['cart']) && (is_string($result->row['cart']))) {
                $cart = unserialize($result->row['cart']);
                $cart = $cart ? (array)$cart['store_' . $store_id] : [];
                //clean products
                if ($cart) {
                    $cart_products = [];
                    foreach ($cart as $key => $val) {
                        $k = explode(':', $key);
                        $cart_products[] = (int)$k[0]; // <-product_id
                    }
                    $sql = "SELECT product_id
                            FROM " . $this->db->table('products_to_stores') . " pts
                            WHERE store_id = '" . $store_id . "' 
                                AND product_id IN (" . implode(', ', $cart_products) . ")";

                    $result = $this->db->query($sql);
                    $products = [];
                    foreach ($result->rows as $row) {
                        $products[] = $row['product_id'];
                    }

                    $diff = array_diff($cart_products, $products);
                    foreach ($diff as $p) {
                        unset($cart[$p]);
                    }
                }
            }
        }
        return $cart;
    }

    /**
     * Merge cart from session and cart from database content
     *
     * @param array $cart - data from database, table "customers"
     *
     * @void
     */
    public function mergeCustomerCart($cart)
    {
        $cart = !is_array($cart) ? [] : $cart;

        if ($cart && !is_array($this->session->data['cart'])) {
            $this->session->data['cart'] = [];
        }
        foreach ($cart as $key => $value) {
            if (!array_key_exists($key, $this->session->data['cart'])) {
                $this->session->data['cart'][$key] = $value;
            }
        }
        if (!$this->session->data['fc']['cart']) {
            $this->session->data['fc']['cart'] = $this->session->data['cart'];
        }
    }

    /**
     * Clear cart from database content
     *
     * @return bool
     * @throws AException
     */
    public function clearCustomerCart()
    {
        $cart = [];
        $customer_id = $this->customer_id;
        if (!$customer_id) {
            $customer_id = $this->unauth_customer['customer_id'];
        }
        if (!$customer_id) {
            return false;
        }
        $this->db->query(
            "UPDATE " . $this->db->table("customers") . "
            SET
                cart = '" . $this->db->escape(serialize($cart)) . "'
            WHERE customer_id = '" . (int)$customer_id . "'"
        );
        return true;
    }

    /**
     * Add item to wishlist
     *
     * @param int $product_id
     *
     * @throws AException
     */
    public function addToWishList($product_id)
    {
        if (!has_value($product_id) || !is_numeric($product_id)) {
            return;
        }
        $wishList = $this->getWishList();
        $wishList[$product_id] = time();
        $this->saveWishList($wishList);
    }

    /**
     * Remove item from wish list
     *
     * @param int $product_id
     *
     * @throws AException
     */
    public function removeFromWishList($product_id)
    {
        if (!has_value($product_id) || !is_numeric($product_id)) {
            return;
        }
        $wishList = $this->getWishList();
        unset($wishList[$product_id]);
        $this->saveWishList($wishList);
    }

    /**
     * Record wish list content
     *
     * @param array $wishList
     *
     * @return null
     * @throws AException
     */
    public function saveWishList($wishList = [])
    {
        $customer_id = $this->customer_id;
        if (!$customer_id) {
            $customer_id = $this->unauth_customer['customer_id'];
        }
        if (!$customer_id) {
            return false;
        }
        $this->db->query(
            "UPDATE " . $this->db->table("customers") . "
            SET
                wishlist = '" . $this->db->escape(serialize($wishList)) . "',
                ip = '" . $this->db->escape($this->request->getRemoteIP()) . "'
            WHERE customer_id = '" . (int)$customer_id . "'"
        );
        return true;
    }

    /**
     * Get cart content
     *
     * @return array()
     * @throws AException
     */
    public function getWishList()
    {
        $customerId = $this->customer_id ?: $this->unauth_customer['customer_id'];
        if (!$customerId) {
            return [];
        }
        $customer_data = $this->db->query(
            "SELECT wishlist
            FROM " . $this->db->table("customers") . "
            WHERE customer_id = '" . (int)$customerId . "' 
                AND status = '1'"
        );
        if ($customer_data->num_rows) {
            //load customer saved cart
            if (($customer_data->row['wishlist']) && (is_string($customer_data->row['wishlist']))) {
                return unserialize($customer_data->row['wishlist']);
            }
        }
        return [];
    }

    /**
     * @param string $type
     * @param array $tr_details - amount, order_id, transaction_type, description, comments, creator
     *
     * @return bool
     * @throws AException
     */
    protected function _record_transaction($type, $tr_details)
    {
        if (!$this->isLogged()) {
            return false;
        }
        if (!has_value($tr_details['transaction_type']) || !has_value($tr_details['created_by'])) {
            return false;
        }

        if ($type == 'debit') {
            $amount = 'debit = ' . (float)$tr_details['amount'];
        } else {
            if ($type == 'credit') {
                $amount = 'credit = ' . (float)$tr_details['amount'];
            } else {
                return false;
            }
        }

        $this->db->query(
            "INSERT INTO " . $this->db->table("customer_transactions") . "
            SET customer_id = '" . (int)$this->getId() . "',
                order_id = '" . (int)$tr_details['order_id'] . "',
                transaction_type = '" . $this->db->escape($tr_details['transaction_type']) . "',
                description = '" . $this->db->escape($tr_details['description']) . "',
                comment = '" . $this->db->escape($tr_details['comment']) . "',
                " . $amount . ",
                section = '" . ((int)$tr_details['section'] ?: 0) . "',
                created_by = '" . (int)$tr_details['created_by'] . "',
                date_added = NOW()"
        );

        if ($this->db->getLastId()) {
            return true;
        }
        return false;
    }

}