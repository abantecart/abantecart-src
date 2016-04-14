<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2016 Belavier Commerce LLC

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
 * Abstract cache driver class
 *
 * @since  1.2.7
 */
class ACacheDriver {

	/**
	 * @var string  key
	 * @since  1.2.7
	 */
	protected $key;

	/**
	 * @var datetime Now time
	 * @since  1.2.7
	 */
	public $now;

	/**
	 * @var integer, cache lifetime
	 * @since  1.2.7
	 */
	protected $expire;

	/**
	 * @var int  Lock period (0 - no lock)
	 * @since  1.2.7
	 */
	public $lock_time;

	/**
	 * Base Constructor
	 *
	 * @param int $expiration
	 * @param int $lock_time
	 *
	 * @since   1.2.7
	 */
	public function __construct( $expiration, $lock_time = 0 ) {

		//expiration is default to 1 day
		$this->expire = ( $expiration ) ? $expiration : 86400;
		$this->lock_time = ( $lock_time ) ? $lock_time : 0;
		$this->now = time();

	}

	/**
	 * Set cache expiration time 
	 *
	 * @param   int $expire_time expiration time
	 *
	 * @return  true
	 *
	 * @since   1.2.7
	 */
	public function setExpiration($expire_time) {
		$this->expire = $expire_time;
		return false;
	}

	/**
	 * Get cached data by key and group
	 *
	 * @param   string	$key	The cache data key
	 * @param   string	$group	The cache data group
	 * @param   boolean	$check_expire  True to verify cache time expiration
	 *
	 * @return  mixed  Boolean  false on failure or a cached data object
	 *
	 * @since   1.2.7
	 */
	public function get($key, $group, $check_expire = true) {
		return false;
	}

	/**
	 * Get all cached data
	 *	 *
	 * @return  mixed  Boolean  false on failure or a cached data object
	 *
	 * @since   1.2.7
	 */
	public function getAll() {
		return false;
	}

	/**
	 * Store data to cache by key and group
	 *
	 * @param   string	$key	The cache data key
	 * @param   string	$group	The cache data group
	 * @param   string	$data   The data to store in cache
	 *
	 * @return  boolean 
	 *
	 * @since   1.2.7
	 */
	public function put($key, $group, $data){
		return true;
	}

	/**
	 * Remove cached data entry by key and group
	 *
	 * @param   string	$key	The cache data key
	 * @param   string	$group	The cache data group
	 *
	 * @return  boolean
	 *
	 * @since   1.2.7
	 */
	public function remove($key, $group){
		return true;
	}

	/**
	 * Clean cache for the group
	 *
	 * @param   string	$group	The cache data group
	 *
	 * @return  boolean
	 *
	 * @since   1.2.7
	 */
	public function clean($group){
		return true;
	}

	/**
	 * Garbage collect expired cache data
	 *
	 * @return boolean.
	 *
	 * @since   1.2.7
	 */
	public function gc(){
		return true;
	}

	/**
	 * Test to see if the storage driver is available.
	 *
	 * @return   boolean
	 *
	 * @since    12.1
	 */
	public function isSupported(){
		return true;
	}

	/**
	 * Lock cached item
	 *
	 * @param   string	$key	The cache data key
	 * @param   string	$group	The cache data group
	 * @param   integer	$locktime	Cached item max lock time
	 *
	 * @return  boolean 
	 *
	 * @since   1.2.7
	 */
	public function lock($key, $group, $locktime){
		return false;
	}

	/**
	 * Unlock cached item
	 *
	 * @param   string	$key	The cache data key
	 * @param   string	$group	The cache data group
	 *
	 * @return  boolean 
	 *
	 * @since   1.2.7
	 */
	public function unlock($key, $group = null){
		return false;
	}

	/**
	 * Get unique hashed cache key string from an key/group pair
	 *
	 * @param   string	$key	The cache data key
	 * @param   string	$group	The cache data group
	 *
	 * @return  string
	 *
	 * @since   1.2.7
	 */
	protected function _hashCacheKey($key, $group){
		return AEncryption::getHash($group . '-' . $key);
	}

}