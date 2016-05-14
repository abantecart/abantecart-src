<?php
/*
------------------------------------------------------------------------------
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
------------------------------------------------------------------------------  
*/

class ModelInstall extends Model {
	public function RunSQL($data) {
		$db = new ADB($data['db_driver'],$data['db_host'], $data['db_user'], $data['db_password'], $data['db_name']);

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

                        $db->query($query); //no silence mode! if error - will throw to exception
						$query = '';
					}
				}
			}

			$db->query("SET CHARACTER SET utf8;");
			$db->query("SET @@session.sql_mode = 'MYSQL40';");
			$db->query(
				"INSERT INTO `" . $data['db_prefix'] . "users`
				SET user_id = '1',
					user_group_id = '1',
					email = '".$db->escape($data['email'])."',
				    username = '" . $db->escape($data['username']) . "',
				    password = '" . $db->escape(AEncryption::getHash($data['password'])) . "',
				    status = '1',
				    date_added = NOW();");

			$db->query("UPDATE `" . $data['db_prefix'] . "settings` SET value = '" . $db->escape($data['email']) . "' WHERE `key` = 'store_main_email'; ");
			$db->query("UPDATE `" . $data['db_prefix'] . "settings` SET value = '" . $db->escape(HTTP_ABANTECART) . "' WHERE `key` = 'config_url'; ");
			$db->query("INSERT INTO `" . $data['db_prefix'] . "settings` SET `group` = 'config', `key` = 'install_date', value = NOW(); ");

			$db->query("UPDATE `" . $data['db_prefix'] . "products` SET `viewed` = '0';");

			//process triggers
			//$this->create_triggers($db, $data['db_name']);

			//run descructor and close db-connection
			unset($db);
		}
		
        //clear cache dir in case of reinstall
        $cache = new ACache();
        $cache->remove('*');

	}

	/**
	 * @param ADB $db
	 * @param string $database_name
	 */
	private function create_triggers($db, $database_name) {
		$tables_sql = "
			SELECT DISTINCT TABLE_NAME 
		    FROM INFORMATION_SCHEMA.COLUMNS
		    WHERE COLUMN_NAME IN ('date_added')
		    AND TABLE_SCHEMA='" . $database_name . "'";
		
		$query = $db->query( $tables_sql);
		foreach ($query->rows as $t) {
			$table_name = $t['TABLE_NAME'];
			$triger_name = $table_name . "_date_add_trg";
		
			$triger_checker = $db->query("SELECT TRIGGER_NAME
								FROM information_schema.triggers
								WHERE TRIGGER_SCHEMA = '" . $database_name . "' AND TRIGGER_NAME = '$triger_name'");
			if (!$query->row[0]) {
				//create trigger
				$sql = "
				CREATE TRIGGER `$triger_name` BEFORE INSERT ON `$table_name` FOR EACH ROW
				BEGIN
		    		SET NEW.date_added = NOW();
				END;
				";
				$db->query($sql);
			}
		}	
	}

    public function getLanguages() {
        $query = $this->db->query( "SELECT *
                                    FROM " . DB_PREFIX . "languages
                                    ORDER BY sort_order, name");
	    $language_data= array();

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
