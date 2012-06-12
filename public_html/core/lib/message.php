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
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
final class AMessage {

    private $db;
    private  $session;
    private $html;
	private $registry;

	public function __construct() {
        $this->registry = Registry::getInstance();
        $this->db = $this->registry->get('db');
        $this->html = $this->registry->get('html');
        $this->session = $this->registry->get('session');

	}

    /**
     * save notice
     *
     * @param  $title - string - message title
     * @param  $message - string - message body
     * @return void
     */
    public function saveNotice($title, $message) {    
        $this->_saveMessage($title, $message, 'N');
    }

    /**
     * save warning
     *
     * @param  $title - string - message title
     * @param  $message - string - message body
     * @return void
     */
    public function saveWarning($title, $message) {
        $this->_saveMessage($title, $message, 'W');
    }

    /**
     * save Error
     *
     * @param  $title - string - message title
     * @param  $message - string - message body
     * @return void
     */
    public function saveError($title, $message) {
        $this->_saveMessage($title, $message, 'E');
    }

    /**
     * save notice
     *
     * @param  $title - string - message title
     * @param  $message - string - message body
     * @param  $status - message status ( N - notice, W - warning, E - error )
     * @return void
     */
    private function _saveMessage($title, $message, $status) {
    	$last_message = $this->getLikeMessage($title);
    	// if last message equal new - update it's repeated field
    	if($last_message['title']== $title){
    		$this->db->query("UPDATE " . DB_PREFIX . "messages SET `repeated` = `repeated` + 1, viewed='0' WHERE msg_id = '".$last_message['msg_id']."'"); 		    
    	}else{
		   
		$this->db->query("INSERT INTO " . DB_PREFIX . "messages
						    SET `title` = '" . $this->db->escape($title) . "',
						    `message` = '" . $this->db->escape($message) . "',
						    `status` = '" . $this->db->escape($status) . "',						    
						    `create_date` = NOW()");
    	}
	    // update message indicator
		$this->setMessageIndicator();
	}

    public function deleteMessage($msg_id) { 	
    	
		$this->db->query("DELETE FROM " . DB_PREFIX . "messages WHERE `msg_id` = " . (int)$msg_id );
	    $this->setMessageIndicator();
	}

    public function getMessage($msg_id) {
    	$this->markAsRead($msg_id);
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "messages WHERE msg_id = " . (int)$msg_id );
		$row = $query->row;
		if($row){
			// replace html-links in message
			$row['message'] =  $this->html->convertLinks($row['message'], 'message');
        }         
		return $row;
	}

	public function getMessages($start = 0, $limit = 0, $sort='', $order = 'DESC') {		
		$sort = !$sort ? 'viewed' : $this->db->escape($sort);		
		$limit_str = '';
        if ( $limit > 0 ) {
            $limit_str = "LIMIT ".(int)$start.", ". (int)$limit;
        }
        $sql = "SELECT * FROM " . DB_PREFIX . "messages ORDER BY ".$sort." " .$order. ", update_date DESC, msg_id DESC ".$limit_str;
        $query = $this->db->query($sql);       
        return $query->rows;
	}
    public function getLikeMessage($title) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "messages WHERE title='".$this->db->escape($title)."'");
		return $query->row;
	}
	
	public function getTotalMessages() {		
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "messages");
        return $query->row['total'];
	}

    public function markAsRead($msg_id) {    	
		$this->db->query("UPDATE " . DB_PREFIX . "messages SET viewed = viewed + 1 WHERE `msg_id` = '" . $this->db->escape($msg_id) . "'");
	    $this->setMessageIndicator();
		return true;    		
	}

    public function markAsUnRead($msg_id) {
    	$msg_info = $this->getMessage($msg_id);    	
    	if($msg_info['viewed']){
			$this->db->query("UPDATE " . DB_PREFIX . "messages SET viewed = 0 WHERE `msg_id` = '" . $this->db->escape($msg_id) . "'");
		    $this->setMessageIndicator();
			return true;
    	}else{
    		return false;
    	}	
	}
	
    public function saveForDisplay($title, $message, $status) {
        $this->session->data['ac_messages'][] = array($title, $message, $status);
    }

    public function getForDisplay($title, $message, $status) {
        $messages = array();
        if ( !empty($this->session->data['ac_messages']) ) {
            $messages = $this->session->data['ac_messages'];
            unset($this->session->data['ac_messages']);
        }
        return $messages;
    }

	public function purgeANTMessages($no_delete=array()){
		if(!$no_delete || !is_array($no_delete)) return false;
		$ids=array();
		foreach($no_delete as $id){
			$ids[] = $this->db->escape($id);
		}
		$ids = "'".implode("', '",$ids)."'";
		$sql = "DELETE FROM " . DB_PREFIX . "ant_messages WHERE id NOT IN (".$ids.")";
		$this->db->query( $sql);
		return true;
	}
	public function saveANTMessage($data=array()) {
		 if(!$data || !$data['message_id']){
			 return;
		 }

		 // need to find message with same id and language. If language not set - find for all
		 // if lanuguage_code is empty it mean that banner shows for all insterface languages
		 $sql = "SELECT *
		         FROM " . DB_PREFIX . "ant_messages
		         WHERE id = '".$this->db->escape($data['message_id'])."'
		         ".($data['language_code'] ? "AND language_code = '".$this->db->escape($data['language_code'])."'" : "")."
		         ORDER BY viewed_date ASC";
		 $result = $this->db->query($sql);

		 $exists =array();
		 $viewed = 0;
		 if( $result->num_rows){
			foreach($result->rows as $row){
				$exists[] = "'".$row['id']."'";
				$viewed += $row['viewed'];
				$last_view = $row['viewed_date'];
			}
			$this->db->query("DELETE FROM " . DB_PREFIX . "ant_messages WHERE id IN (". implode(",",$exists) .")");
		 }
		 $data['end_date'] = !$data['end_date'] || $data['end_date']=='0000-00-00 00:00:00' ? '2030-01-01' : $data['end_date'];
		 $data['priority'] = !(int)$data['priority'] ? 1 : (int)$data['priority'];
		 $sql = "INSERT INTO " . DB_PREFIX . "ant_messages (`id`,
															`priority`,
															`start_date`,
															`end_date`,
															`viewed_date`,
															`viewed`,
															`title`,
															`description`,
															`html`,
															`url`,
															`language_code`)
				VALUES ('" . $this->db->escape($data['message_id']) . "',
						'" . $this->db->escape($data['priority']) . "',
						'" . $this->db->escape($data['start_date']) . "',
						'" . $this->db->escape($data['end_date']) . "',
						'" . $last_view . "',
						'" . $viewed . "',
						'" . $this->db->escape($data['title']) . "',
						'" . $this->db->escape($data['description']) . "',
						'" . $this->db->escape($data['html']) . "',
						'" . $this->db->escape($data['url']) . "',
						'" . $this->db->escape($data['language_code']) . "')"; 
		 $this->db->query( $sql );
	}

	public function getANTMessage( $rt = '' ){

		// delete expired banners first
		 $this->db->query("DELETE FROM " . DB_PREFIX . "ant_messages
		                   WHERE end_date < CURRENT_TIMESTAMP");

		$sql = "SELECT *
		         FROM " . DB_PREFIX . "ant_messages
		         WHERE start_date< CURRENT_TIMESTAMP and end_date > CURRENT_TIMESTAMP
		            AND ( language_code = '".$this->registry->get('config')->get('admin_language')."'
		                      OR COALESCE(language_code,'*') = '*' OR language_code = '*' )
		         ORDER BY viewed_date ASC, priority DESC, COALESCE(language_code,'') DESC, COALESCE(url,'') DESC";

		$result = $this->db->query($sql);
		if($result->num_rows){
			$output = $result->row['html'] ? $result->row['html'] : $result->row['description'];
		 	$sql = "UPDATE  " . DB_PREFIX . "ant_messages SET viewed = viewed+1 , viewed_date = NOW() WHERE id = '".$result->row['id']."'
					AND language_code = '".$result->row['language_code']."'";
			$this->db->query( $sql );
		}
		return $output;
	}

	public function setMessageIndicator(){
		if(in_array($this->registry->get('request')->get['rt'],array('index/login','index/logout')) || !IS_ADMIN){
			return;
		}
		$sql = $this->db->query ( "SELECT status, COUNT(msg_id) as count
									FROM " . DB_PREFIX . "messages
									WHERE viewed<'1'
									GROUP BY status" );
		if($sql->num_rows){
			foreach($sql->rows as $row){
				$this->registry->get('session')->data['new_messages'][$row['status']] = ( int ) $row['count'];
			}
		}else{
			$this->registry->get('session')->data['new_messages']['N'] = 0;
			$this->registry->get('session')->data['new_messages']['W'] = 0;
			$this->registry->get('session')->data['new_messages']['E'] = 0;
		}
		return true;
	}

}