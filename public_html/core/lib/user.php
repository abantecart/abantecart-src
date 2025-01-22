<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2024 Belavier Commerce LLC

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
 * Class AUser
 */
final class AUser
{
    /**
     * @var int
     */
    private $user_id;
    private $user_group_id;

    /**
     * @var string
     */
    private $email;
    private $username;
    private $firstname;
    private $lastname;
    private $last_login;
    /**
     * @var ASession
     */
    private $session;
    private $request;
    private $db;
    private $config;

    /**
     * @var array
     */
    private $permission = array();

    /**
     * @param $registry Registry
     */
    public function __construct($registry)
    {
        $this->db = $registry->get('db');
        $this->request = $registry->get('request');
        $this->session = $registry->get('session');
        $this->config = $registry->get('config');

        if (isset($this->session->data['user_id'])) {
            $user_query = $this->db->query("SELECT * 
                FROM ".$this->db->table("users")." 
                WHERE status = 1 AND user_id = '".(int)$this->session->data['user_id']."'");
            if ($user_query->num_rows) {
                $this->user_id = (int)$user_query->row['user_id'];
                $this->user_group_id = (int)$user_query->row['user_group_id'];
                $this->email = $user_query->row['email'];
                $this->username = $user_query->row['username'];
                $this->firstname = $user_query->row['firstname'];
                $this->lastname = $user_query->row['lastname'];
                $this->last_login = $this->session->data['user_last_login'];
                $this->_user_init();
            } else {
                $this->logout();
            }
        } else {
            unset($this->session->data['token']);
        }
    }

    /**
     * @param $username string
     * @param $password string
     *
     * @return bool
     */
    public function login($username, $password)
    {
        //Supports older passwords for upgraded/migrated stores prior to 1.2.8
        $add_pass_sql = '';
        if (defined('SALT')) {
            $add_pass_sql = "OR password = '".$this->db->escape(md5($password.SALT))."'";
        }

        $user_query = $this->db->query("SELECT *
            FROM ".$this->db->table("users")."
            WHERE username = '".$this->db->escape($username)."'
            AND (
                password = 	SHA1(CONCAT(salt,
                            SHA1(CONCAT(salt, SHA1('".$this->db->escape($password)."')))
                        ))
                ".$add_pass_sql."
            )
            AND status = 1
        ");

        if ($user_query->num_rows) {
            $this->user_id = $this->session->data['user_id'] = (int)$user_query->row['user_id'];
            $this->user_group_id = (int)$user_query->row['user_group_id'];
            $this->username = $user_query->row['username'];

            $this->last_login = $this->session->data['user_last_login'] = $user_query->row['last_login'];
            if (!$this->last_login || $this->last_login == 'null' || $this->last_login == '0000-00-00 00:00:00') {
                $this->session->data['user_last_login'] = $this->last_login = '';
            }

            $this->_user_init();
            $this->_update_last_login();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Init user
     *
     * @param void
     *
     * @return void
     */
    private function _user_init()
    {

        $this->db->query("SET @USER_ID = '".$this->user_id."';");
        $this->db->query("UPDATE ".$this->db->table("users")." 
            SET ip = '".$this->db->escape($this->request->getRemoteIP())."'
            WHERE user_id = '".$this->user_id."';");

        $user_group_query = $this->db->query("SELECT permission
              FROM ".$this->db->table("user_groups")."
              WHERE user_group_id = '".$this->user_group_id."'");
        if (unserialize($user_group_query->row['permission'])) {
            foreach (unserialize($user_group_query->row['permission']) as $key => $value) {
                $this->permission[$key] = $value;
            }
        }
    }

    private function _update_last_login()
    {
        $this->db->query("UPDATE ".$this->db->table("users")." 
            SET last_login = NOW()
            WHERE user_id = '".$this->user_id."';");
    }

    public function logout()
    {
        unset($this->session->data['user_id']);
        $this->user_id = '';
        $this->username = '';
        $this->deleteActiveTokens();
    }

    /**
     * @param $key   - route to controller
     * @param $value bool
     *
     * @return bool
     */
    public function hasPermission($key, $value)
    {
        //If top_admin allow all permission. Make sure Top Admin Group is set to ID 1
        if ($this->user_group_id == 1) {
            return true;
        } else {
            if (isset($this->permission[$key])) {
                return $this->permission[$key][$value] == 1 ? true : false;
            } else {
                return false;
            }
        }
    }

    /**
     * @param string $value - route to controller
     *
     * @return bool
     */
    public function canAccess($value)
    {
        return $this->hasPermission('access', $value);
    }

    /**
     * @param string $value route to controller
     *
     * @return bool
     */
    public function canModify($value)
    {
        return $this->hasPermission('modify', $value);
    }

    /**
     * @param string $token
     *
     * @return bool|int
     */
    public function isLoggedWithToken($token)
    {
        if ((isset($this->session->data['token']) && !isset($token))
            || ((isset($token) && (isset($this->session->data['token']) && ($token != $this->session->data['token']))))
        ) {
            return false;
        } else {
            return $this->user_id;
        }
    }

    /**
     * @return bool|int
     */
    public function isLogged()
    {
        if (IS_ADMIN && $this->request->get['token'] != $this->session->data['token']) {
            return false;
        }
        return $this->user_id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->user_id;
    }

    /**
     * @return int
     */
    public function getUserGroupId()
    {
        return $this->user_group_id;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getLastLogin()
    {
        return $this->last_login;
    }

    /**
     * @param string $token
     * @return bool
     */
    public function isActiveToken($token)
    {
        $user_query = $this->db->query("SELECT * 
                FROM ".$this->db->table("user_sessions")." 
                WHERE user_id = '".$this->user_id."'  
                AND token = '".$this->db->escape($token)."'"
        );
        if ($user_query->num_rows) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $token
     * @return string
     */
    public function setActiveToken($token)
    {
        //first clear all expired tokens
        $userSesTbl = $this->db->table("user_sessions");
        $session_ttl = $this->config->get('config_session_ttl');
        $this->db->query("DELETE FROM ".$userSesTbl." 
            WHERE user_id = '".$this->user_id."' 
            AND last_active < DATE_SUB(NOW(), INTERVAL ".$session_ttl." MINUTE)");

        //set or update token
        $this->db->query("INSERT INTO ".$userSesTbl." 
             VALUES (
                '".$this->user_id."',
                '".$this->db->escape($token)."',
                '".$this->db->escape($this->request->getRemoteIP())."',
                NOW(),
                NOW()
            )
            ON DUPLICATE KEY UPDATE last_active = NOW()");
    }

    /**
     * Delete all active sessions for the user
     * @return $userID
     * @throws AException
     */
    public function deleteActiveTokens($userID = null)
    {
        if (!$userID) {
            $userID = $this->user_id;
        }
        $userSesTbl = $this->db->table("user_sessions");
        $this->db->query("DELETE FROM ".$userSesTbl." 
            WHERE user_id = '".$userID."'");
    }

    /**
     * @param string $username
     * @param string $email
     *
     * @return bool
     */
    public function validate($username, $email)
    {
        $user_query = $this->db->query(
            "SELECT * FROM ".$this->db->table("users")."
                    WHERE username = '".$this->db->escape($username)."'
                            AND email = '".$this->db->escape($email)."'");
        if ($user_query->num_rows) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param int $length
     *
     * @return string
     */
    static function generatePassword($length = 8)
    {
        $chars = str_split("1234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ");
        $i = 0;
        $password = "";
        while ($i <= $length) {
            $password .= $chars[mt_rand(0, count($chars))];
            $i++;
        }
        return $password;
    }

    /**
     * @return string
     */
    public function getUserFirstName()
    {
        return $this->firstname;
    }

    /**
     * @return string
     */
    public function getUserLastName()
    {
        return $this->lastname;
    }
}
