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

/**
 * Class ASession
 */
final class ASession
{
    public $registry = null;
    public $data = [];
    public $ses_name = SESSION_ID;

    /**
     * @param string $ses_name
     *
     * @throws AException
     */
    public function __construct(string $ses_name = '')
    {
        $savePathCheck = check_session_save_path();

        if ($savePathCheck) {
            exit($savePathCheck['title'] . PHP_EOL . $savePathCheck['body']);
        }

        if (class_exists('Registry')) {
            $this->registry = Registry::getInstance();
        }

        if (!session_id() || has_value($ses_name)) {
            $this->ses_name = $ses_name ? : SESSION_ID;
            $this->init($this->ses_name);
        }

        if ($this->registry && $this->registry->get('config')) {
            $sessionTTL = $this->registry->get('config')->get('config_session_ttl') ? : 30;
            if ((isset($_SESSION['user_id']) || isset($_SESSION['customer_id']))
                && isset($_SESSION['LAST_ACTIVITY'])
                && ((time() - $_SESSION['LAST_ACTIVITY']) / 60 > $sessionTTL)
            ) {
                // last request was more than 30 minutes ago
                $this->clear();
                redirect($this->registry->get('html')->currentURL(['token']));
            }
        }
        // update last activity time stamp
        $_SESSION['LAST_ACTIVITY'] = time();
        $this->data =& $_SESSION;
    }

    /**
     * @param string $sessionName
     *
     * @throws AException
     */
    public function init(string $sessionName)
    {
        $sessionMode = '';
        if (defined('IS_API') && IS_API === true) {
            //set up session specific for API based on the token or create new
            $token = $_GET['token'] ?? $_POST['token'] ?? '';
            $token = is_string($token) ? $token : '';
            $finalSessionId = $this->prepareSessionId($token);
            session_id($finalSessionId);
        } else if (!headers_sent()) {
            $this->setCookie();
            session_name($sessionName);
            // for shared ssl domain set session id of non-secure domain
            if ($this->registry && $this->registry->get('config')) {
                $sId = $_GET['session_id'] ?? '';
                if ($this->registry->get('config')->get('config_shared_session')
                    && is_string($sId)
                    && $this->isSessionIdValid($sId)
                ) {
                    header('P3P: CP="CAO COR CURa ADMa DEVa OUR IND ONL COM DEM PRE"');
                    session_id($sId);
                    $this->setCookie($sessionName, $sId);
                }
            }

            if (defined('EMBED_TOKEN_NAME') && isset($_GET[EMBED_TOKEN_NAME]) && !isset($_COOKIE[$sessionName])) {
                //check and reset session if it is not valid
                $token = is_string($_GET[EMBED_TOKEN_NAME]) ? $_GET[EMBED_TOKEN_NAME] : '';
                $finalSessionId = $this->prepareSessionId($token);
                session_id($finalSessionId);
                $this->setCookie($sessionName, $finalSessionId);
                $sessionMode = 'embed_token';
            }
        }

        //check if the session cannot be started. Try one more time with a new generated session ID
        if (!headers_sent()) {
            $isSessionOk = session_start();
            if (!$isSessionOk) {
                //auto-generating session id and try to start session again
                $finalSessionId = $this->prepareSessionId();
                session_id($finalSessionId);
                $this->setCookie($sessionName, $finalSessionId);
                session_start();
            }
        }

        $_SESSION['session_mode'] = $sessionMode;
    }

    public function clear()
    {
        @session_unset();
        @session_destroy();
        $_SESSION = [];
    }

    /**
     * This function to return clean validated session ID
     *
     * @param string $sessionId
     *
     * @return string
     */
    private function prepareSessionId($sessionId = '')
    {
        if (!$sessionId || !$this->isSessionIdValid($sessionId)) {
            //if session ID is invalid, generate new one
            $sessionId = uniqid(substr(UNIQUE_ID, 0, 4), true);
            return preg_replace("/[^-,a-zA-Z0-9]/", '', $sessionId);
        } else {
            return $sessionId;
        }
    }

    /**
     * This function is to validate session id
     *
     * @param string $sessionId
     *
     * @return bool
     */
    private function isSessionIdValid($sessionId)
    {
        if (empty($sessionId)) {
            return false;
        } else {
            return preg_match('/^[-,a-zA-Z0-9]{1,128}$/', $sessionId) > 0;
        }
    }

    private function setCookie($name = null, $value = null)
    {
        setCookieOrParams(
            $name,
            $value,
            [
                'path'     => dirname($_SERVER['PHP_SELF']),
                'domain'   => null,
                'secure'   => (defined('HTTPS') && HTTPS),
                'httponly' => true,
                //NOTE: lax mode blocks transferring of cookie when redirecting from another domain
                'samesite' => (defined('HTTPS') && HTTPS) ? 'None' : 'lax',
                'lifetime' => 0,
            ]
        );
    }
}
