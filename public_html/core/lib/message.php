<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2021 Belavier Commerce LLC

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
 * Class AMessage
 */
class AMessage
{
    /**
     * @var ADB
     */
    protected $db;
    /**
     * @var ASession
     */
    protected $session;
    /**
     * @var AHtml
     */
    protected $html;
    /**
     * @var Registry
     */
    protected $registry;

    /**
     *
     */
    public function __construct()
    {
        $this->registry = Registry::getInstance();
        $this->db = $this->registry->get('db');
        $this->html = $this->registry->get('html');
        $this->session = $this->registry->get('session');
    }

    /**
     * save notice
     *
     * @param      $title - string - message title
     * @param      $message - string - message body
     * @param bool $repetition_group - sign to group repetitions of message based on same title of message
     *
     * @void
     */
    public function saveNotice($title, $message, $repetition_group = true)
    {
        $this->_saveMessage($title, $message, 'N', $repetition_group);
    }

    /**
     * save warning
     *
     * @param      $title - string - message title
     * @param      $message - string - message body
     * @param bool $repetition_group - sign to group repetitions of message based on same title of message
     *
     * @void
     */
    public function saveWarning($title, $message, $repetition_group = true)
    {
        $this->_saveMessage($title, $message, 'W', $repetition_group);
    }

    /**
     * save Error
     *
     * @param      $title - string - message title
     * @param      $message - string - message body
     * @param bool $repetition_group - sign to group repetitions of message based on same title of message
     *
     * @void
     */
    public function saveError($title, $message, $repetition_group = true)
    {
        $this->_saveMessage($title, $message, 'E', $repetition_group);
    }

    /**
     * save notice
     *
     * @param      $title - string - message title
     * @param      $message - string - message body
     * @param      $status - message status ( N - notice, W - warning, E - error )
     * @param bool $repetition_group - sign to group repetitions of message based on same title of message
     *
     * @void
     * @return int
     */
    protected function _saveMessage($title, $message, $status, $repetition_group = true)
    {
        $last_message = $this->getLikeMessage($title);
        // if last message equal new - update it's repeated. 
        // update counter and update message body as last one can be different	
        if ($last_message['title'] == $title && $repetition_group) {
            $this->db->query(
                "UPDATE ".$this->db->table("messages")." 
                SET `repeated` = `repeated` + 1, 
                    `viewed`='0', 
                    `message` = '".$this->db->escape($message)."'
                WHERE msg_id = '".$last_message['msg_id']."'"
            );
            $msg_id = (int) $last_message['msg_id'];
        } else {
            $this->db->query(
                "INSERT INTO ".$this->db->table("messages")." 
                SET `title` = '".$this->db->escape($title)."',
                    `message` = '".$this->db->escape($message)."',
                    `status` = '".$this->db->escape($status)."',
                    `date_added` = NOW()"
            );
            $msg_id = (int) $this->db->getLastId();
        }
        return $msg_id;
    }

    /**
     * @param int $msg_id
     */
    public function deleteMessage($msg_id)
    {
        $this->db->query(
            "DELETE FROM ".$this->db->table("messages")." 
            WHERE `msg_id` = ".(int) $msg_id
        );
    }

    /**
     * @param int $msg_id
     *
     * @return array
     */
    public function getMessage($msg_id)
    {
        $this->markAsRead($msg_id);
        $query = $this->db->query(
            "SELECT * 
            FROM ".$this->db->table("messages")." 
            WHERE msg_id = ".(int) $msg_id
        );
        $row = $query->row;
        if ($row) {
            // replace html-links in message
            $row['message'] = $this->html->convertLinks($row['message'], 'message');
        }
        return $row;
    }

    /**
     * @param int $start
     * @param int $limit
     * @param string $sort
     * @param string $order
     *
     * @return array
     */
    public function getMessages($start = 0, $limit = 0, $sort = '', $order = 'DESC')
    {
        $sort = !$sort ? 'viewed' : $this->db->escape($sort);
        $limit_str = '';
        if ($limit > 0) {
            $limit_str = "LIMIT ".(int) $start.", ".(int) $limit;
        }
        $sql = "SELECT * 
                FROM ".$this->db->table("messages")." 
                ORDER BY ".$sort." ".$order.", date_modified DESC, msg_id 
                DESC ".$limit_str;
        $query = $this->db->query($sql);
        return $query->rows;
    }

    /**
     * @param string $title
     *
     * @return array
     */
    public function getLikeMessage($title)
    {
        $query = $this->db->query(
            "SELECT * 
            FROM ".$this->db->table("messages")." 
            WHERE title='".$this->db->escape($title)."'"
        );
        return $query->row;
    }

    /**
     * @return int
     */
    public function getTotalMessages()
    {
        $query = $this->db->query(
            "SELECT COUNT(*) AS total 
            FROM ".$this->db->table("messages")." "
        );
        return (int) $query->row['total'];
    }

    /**
     * @param int $msg_id
     *
     * @return bool
     */
    public function markAsRead($msg_id)
    {
        $this->db->query(
            "UPDATE ".$this->db->table("messages")." 
            SET viewed = viewed + 1, date_modified = date_modified   
            WHERE `msg_id` = '".$this->db->escape($msg_id)."'"
        );
        return true;
    }

    /**
     * @param int $msg_id
     *
     * @return bool
     */
    public function markAsUnRead($msg_id)
    {
        $msg_info = $this->getMessage($msg_id);
        if ($msg_info['viewed']) {
            $this->db->query(
                "UPDATE ".$this->db->table("messages")." 
                SET viewed = 0, date_modified = date_modified   
                WHERE `msg_id` = '".$this->db->escape($msg_id)."'"
            );
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $title
     * @param string $message
     * @param string $status
     */
    public function saveForDisplay($title, $message, $status)
    {
        $this->session->data['ac_messages'][] = [$title, $message, $status];
    }

    /**
     * @return array
     */
    public function getForDisplay()
    {
        $messages = [];
        if (!empty($this->session->data['ac_messages'])) {
            $messages = $this->session->data['ac_messages'];
            unset($this->session->data['ac_messages']);
        }
        return $messages;
    }

    /**
     * @param array $no_delete
     *
     * @return bool
     */
    public function purgeANTMessages($no_delete = [])
    {
        if (!$no_delete || !is_array($no_delete)) {
            return false;
        }
        $ids = [];
        foreach ($no_delete as $id) {
            $ids[] = $this->db->escape($id);
        }
        $ids = "'".implode("', '", $ids)."'";
        $sql = "DELETE FROM ".$this->db->table("ant_messages")." WHERE id NOT IN (".$ids.")";
        $this->db->query($sql);
        return true;
    }

    /**
     * @param array $data
     *
     * @return null
     */
    public function saveANTMessage($data = [])
    {
        if (!$data || !$data['message_id']) {
            return false;
        }

        // need to find message with same id and language. If language not set - find for all
        // if language_code is empty it mean that banner shows for all interface languages
        $sql = "SELECT *
                 FROM ".$this->db->table("ant_messages")." 
                 WHERE id = '".$this->db->escape($data['message_id'])."'
                 ".($data['language_code']
                    ? "AND language_code = '".$this->db->escape($data['language_code'])."'"
                    : "")
                ." ORDER BY viewed_date ASC";
        $result = $this->db->query($sql);

        $exists = [];
        $viewed = 0;
        $last_view = null;
        if ($result->num_rows) {
            foreach ($result->rows as $row) {
                $exists[] = "'".$row['id']."'";
                $viewed += $row['viewed'];
                $last_view = $row['viewed_date'];
            }
            $this->db->query(
                "DELETE FROM ".$this->db->table("ant_messages")." WHERE id IN (".implode(",", $exists).")"
            );
        }
        $data['end_date'] = !$data['end_date'] || $data['end_date'] == '0000-00-00 00:00:00'
                            ? '2030-01-01'
                            : $data['end_date'];
        $data['priority'] = !(int) $data['priority'] ? 1 : (int) $data['priority'];
        $sql = "INSERT INTO ".$this->db->table("ant_messages")." 
                (`id`,
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
                VALUES ('".$this->db->escape($data['message_id'])."',
                        '".$this->db->escape($data['priority'])."',
                        '".$this->db->escape($data['start_date'])."',
                        '".$this->db->escape($data['end_date'])."',
                        '".$this->db->escape($last_view)."',
                        '".(int) $viewed."',
                        '".$this->db->escape($data['title'])."',
                        '".$this->db->escape($data['description'])."',
                        '".$this->db->escape($data['html'])."',
                        '".$this->db->escape($data['url'])."',
                        '".$this->db->escape($data['language_code'])."')";
        $this->db->query($sql, true);
        return true;
    }

    /**
     * @return array
     * @throws AException
     */
    public function getANTMessage()
    {
        $output = '';
        // delete expired banners first
        $this->db->query("DELETE FROM ".$this->db->table("ant_messages")." WHERE end_date < CURRENT_TIMESTAMP");
        $sql = "SELECT *
                 FROM ".$this->db->table("ant_messages")." 
                 WHERE viewed < 1 
                    AND start_date< CURRENT_TIMESTAMP and end_date > CURRENT_TIMESTAMP
                    AND ( language_code = '".$this->registry->get('config')->get('admin_language')."' 
                        OR COALESCE(language_code,'*') = '*')
                 ORDER BY viewed ASC, priority DESC, COALESCE(language_code,'') DESC, COALESCE(url,'') DESC
                 LIMIT 1";
        $result = $this->db->query($sql);
        if ($result->num_rows) {
            $output = $result->row['html'] ?: $result->row['description'];
        }
        return [
            'id' => $result->row['id'] ?? null,
            'viewed' => $result->row['viewed'] ?? 0,
            'html' => $output ?? '',
        ];
    }

    /**
     * @param string $message_id
     * @param string $language_code
     *
     * @return string
     */
    public function markViewedANT($message_id, $language_code)
    {
        if (!has_value($message_id) || !has_value($language_code)) {
            return null;
        }
        $sql = "UPDATE  ".$this->db->table("ant_messages")." 
                SET viewed = viewed+1 , viewed_date = NOW(), date_modified = date_modified 
                WHERE id = '".$this->db->escape($message_id)."'
                    AND language_code = '".$this->db->escape($language_code)."'";
        $this->db->query($sql);
        return $message_id;
    }

    /**
     * @return array
     */
    public function getShortList()
    {
        $output = [];
        $result = $this->db->query(
            "SELECT UPPER(status) as status, COUNT(msg_id) as count
            FROM ".$this->db->table("messages")." 
            WHERE viewed<'1'
            GROUP BY status"
        );
        $total = 0;
        foreach ($result->rows as $row) {
            $output['count'][$row['status']] = ( int ) $row['count'];
            $total += ( int ) $row['count'];
        }

        $output['total'] = $total;
        //get last 9 messages
        $result = $this->db->query(
            "(SELECT msg_id, title, message, status, viewed, date_modified
            FROM ".$this->db->table('messages')."
            WHERE viewed<'1'
            ORDER BY date_modified DESC
            LIMIT 0,9)"
        );
        $output['shortlist'] = $result->rows;
        return $output;
    }
}