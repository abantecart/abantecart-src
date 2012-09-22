<?php
/*
------------------------------------------------------------------------------
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
------------------------------------------------------------------------------  
*/

class ModelInstall extends Model {
	public function mysql($data) {
		$connection = mysql_connect($data['db_host'], $data['db_user'], $data['db_password']);
		
		mysql_select_db($data['db_name'], $connection);
		
		mysql_query("SET NAMES 'utf8'", $connection);
		mysql_query("SET CHARACTER SET utf8", $connection);
		
		$file = DIR_APP_SECTION . 'abantecart_database.sql';
	
		if ($sql = file($file)) {
			$query = '';

			foreach($sql as $line) {
				$tsl = trim($line);

				if (($sql != '') && (substr($tsl, 0, 2) != "--") && (substr($tsl, 0, 1) != '#')) {
					$query .= $line;
  
					if (preg_match('/;\s*$/', $line)) {
						$query = str_replace("DROP TABLE IF EXISTS `ac_", "DROP TABLE IF EXISTS `" . $data['db_prefix'], $query);
						$query = str_replace("CREATE TABLE `ac_", "CREATE TABLE `" . $data['db_prefix'], $query);
						$query = str_replace("INSERT INTO `ac_", "INSERT INTO `" . $data['db_prefix'], $query);
						$query = str_replace("ON `ac_", "ON `" . $data['db_prefix'], $query);

                        $result = mysql_query($query, $connection);
  
						if (!$result) {
							throw new AException(AC_ERR_MYSQL, mysql_errno($connection) . ' Error: ' . mysql_error($connection) . ' in query:  ' . $query);
						}
	
						$query = '';
					}
				}
			}
			
			mysql_query("SET CHARACTER SET utf8", $connection);
	
			mysql_query("SET @@session.sql_mode = 'MYSQL40'", $connection);
		
			mysql_query("DELETE FROM from `" . $data['db_prefix'] . "users` WHERE user_id = '1'");
		
			mysql_query(
				"INSERT INTO `" . $data['db_prefix'] . "users`
				SET user_id = '1',
					user_group_id = '1',
					email = '".mysql_real_escape_string($data['email'])."',
				    username = '" . mysql_real_escape_string($data['username']) . "',
				    password = '" . mysql_real_escape_string(AEncryption::getHash($data['password'])) . "',
				    status = '1',
				    date_added = NOW()",
				$connection
			);

			mysql_query("UPDATE `" . $data['db_prefix'] . "settings` SET value = '" . mysql_real_escape_string($data['email']) . "' WHERE `key` = 'store_main_email' ", $connection);
			mysql_query("UPDATE `" . $data['db_prefix'] . "settings` SET value = '" . mysql_real_escape_string(HTTP_ABANTECART) . "' WHERE `key` = 'config_url' ", $connection);
			mysql_query("INSERT INTO `" . $data['db_prefix'] . "settings` SET `group` = 'config', `key` = 'install_date', value = NOW() ", $connection);

			mysql_query("UPDATE `" . $data['db_prefix'] . "products` SET `viewed` = '0'", $connection);
			
			mysql_close($connection);	
		}

        //clear cache dir in case of reinstall
        $cache = new ACache();
        $cache->delete('*');

	}
    public function getLanguages() {

        $query = $this->db->query( "SELECT *
                                    FROM " . DB_PREFIX . "languages
                                    ORDER BY sort_order, name");

        foreach ($query->rows as $result) {
            $language_data[$result['code']] = array(
                'language_id' => $result['language_id'],
                'name'        => $result['name'],
                'code'        => $result['code'],
                'locale'      => $result['locale'],
                'directory'   => $result['directory'],
                'filename'    => $result['filename'],
                'sort_order'  => $result['sort_order'],
                'status'      => $result['status']
            );
        }

    return $language_data;
    }
}
?>