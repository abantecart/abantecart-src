<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * Class to handle CSRF Token
 *
 * @property ASession $session
 * @property ARequest $request
 */
class CSRFToken
{

    /**
     * @var registry - access to application registry
     */
    protected $registry;
    /**
     * @var string CSRF-token
     */
    private $token;
    /**
     * @var int CSRF-token instance
     */
    private $instance;

    public function __construct()
    {
        $this->registry = Registry::getInstance();
        $this->errors = array();
        $this->token = $this->session->data['csrftoken'];
    }

    /**
     * @param  $key - key to load data from registry
     *
     * @return mixed  - data from registry
     */
    public function __get($key)
    {
        return $this->registry->get($key);
    }

    /**
     * @param  string $key   - key to save data in registry
     * @param  mixed  $value - key to save data in registry
     *
     * @void
     */
    public function __set($key, $value)
    {
        $this->registry->set($key, $value);
    }

    /**
     * set CSRF Token Instance
     *
     * @param void
     *
     * @return string
     */
    public function setInstance()
    {
        $this->instance = 0;
        if (is_array($this->session->data['csrftoken']) && count($this->session->data['csrftoken'])) {
            end($this->session->data['csrftoken']);
            $this->instance = key($this->session->data['csrftoken']) + 1;
        }
        return $this->instance;
    }

    /**
     * set CSRF Token
     *
     * @param void
     *
     * @return string
     */
    public function setToken()
    {
        if (!$this->instance) {
            //create new token instance
            $this->instance = $this->setInstance();
        }
        $this->token = genToken();

        $this->session->data['csrftoken'][$this->instance] = $this->token;
        return $this->token;
    }

    /**
     * Validate CSRF Token. Can be validated only one time
     *
     * @param int
     * @param string
     *
     * @return bool
     */
    public function isTokenValid($instance = 0, $token = '')
    {
        if (!$instance && !$token) {
            $instance = $this->request->get_or_post('csrfinstance');
            $token = $this->request->get_or_post('csrftoken');
        }
        //note: $instance can be zero!
        if (!empty($token) && has_value($instance) && $this->session->data['csrftoken'][$instance] === $token) {
            $this->instance = $instance;
            $this->token = $this->session->data['csrftoken'][$instance];
            unset($this->session->data['csrftoken'][$instance]);
            return true;
        } else {
            return false;
        }
    }
}