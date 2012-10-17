<?php
/*------------------------------------------------------------------------------
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
------------------------------------------------------------------------------*/

class ControllerPagesActivation extends AController {

    private $connection;

	public function main() {

        if ( !defined('DB_HOSTNAME') ) {
            header('Location: index.php?rt=license');
	        exit;
        }

        $_GET['admin_path'] = ADMIN_PATH;

        $this->connection = mysql_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
        mysql_select_db(DB_DATABASE, $this->connection);
        $r = mysql_query("SELECT product_id FROM ".DB_PREFIX."products", $this->connection);
        $data_exist = mysql_num_rows($r);

        // redirect to storefront in case more than day has passed from date of installation
        $r = mysql_query("SELECT value FROM ".DB_PREFIX."settings WHERE `key` = 'install_date' ", $this->connection);
        $install_date = mysql_fetch_assoc($r);
        if ( $data_exist || strtotime($install_date['value']) + 60*60*24 < time() ) {
            header('Location: ../');
        }
	
		if(isset($_GET['install_demo']) && !$data_exist){
            $data = array();
            $data['db_host']     = DB_HOSTNAME;
            $data['db_name']     = DB_DATABASE;
            $data['db_user']     = DB_USERNAME;
            $data['db_password'] = DB_PASSWORD;
            $data['db_prefix']   = DB_PREFIX;

            $this->install_demo($data);
            $this->session->data['finish'] = 1;
            header('Location: index.php?rt=finish');
        }


		$this->view->assign('data_exist', $data_exist);
		$this->view->assign('salt', SALT);
		
		$this->view->assign('admin_path', 'index.php?s=' . $_GET['admin_path']);

		$this->addChild('common/header', 'header', 'common/header.tpl');
		$this->addChild('common/footer', 'footer', 'common/footer.tpl');

		$this->processTemplate('pages/activation.tpl');
	}

	public function install_demo($data) {
		$connection = $this->connection;
		
		mysql_query("SET NAMES 'utf8'", $connection);
		mysql_query("SET CHARACTER SET utf8", $connection);
		
		$file = DIR_APP_SECTION . 'abantecart_sample_data.sql';
	
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
						
						$result = mysql_query($query, $connection);
  
						if (!$result) {
							die(mysql_error().'<br>'.$query);
						}
	
						$query = '';
					}
				}
			}
			
			mysql_query("SET CHARACTER SET utf8", $connection);
			mysql_query("SET @@session.sql_mode = 'MYSQL40'", $connection);
			mysql_close($connection);	
		}		
	}	
}
?>