<?php

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}
class ModelExtensionBackToStock extends Model
{
    /**
     * @param $user_id
     * @param $product_id
     * @throws AException
     */
    public function addToQueue($user_id, $product_id)
    {
        $this->db->query(
            "INSERT INTO ".$this->db->table('back_to_stock')."
            SET `user_id` = '".(int) $user_id."', 
                `product_id` = '".(int) $product_id."',  
                `date_modified` = NOW(), 
                `date_added` = NOW()"
        );
    }
    public function getQueueByProdId($product_id){
        $result = $this->db->query(
            "SELECT * 
             FROM `".$this->db->table("back_to_stock")."` 
             WHERE `product_id` = '".intval($product_id)."'"
        );
        return $result;
    }

    public function deleteQueueByUserId($user_id,$product_id){
        $this->db->query(
            "DELETE FROM " . $this->db->table('back_to_stock') . "
        WHERE `user_id` = '" . (int) $user_id . "'
        AND `product_id` = '" . (int) $product_id . "'"
        );
    }
    /**
     * Send mail to customer
     * @param string $email
     * @param string $data
     * @param string $product_id
     */
    public function _send_email($email, $product_id, $data)
    {
        if (!$email || !$product_id) {
            $error = new AError('Error: Cannot send email. Unknown address or empty message.');
            $error->toLog();
            return false;
        }

        // HTML Mail
        $this->data['mail_template_data']['lang_direction'] = $this->language->get('direction');
        $this->data['mail_template_data']['lang_code'] = $this->language->get('code');
        $this->data['mail_template_data']['subject'] = $data;


        $text = $this->language->get('back_to_stock_notification_email_send');
        $message_body = [];
        if ($product_id) {
                $message_body = "\n\n<br><br>".sprintf($text,
                        $email,
                        $this->html->getCatalogURL('product/product',
                            '&product_id='.$product_id));
        }

        $this->data['mail_template_data']['body'] = html_entity_decode($message_body, ENT_QUOTES, 'UTF-8');
        $this->data['mail_template'] = 'mail/contact.tpl';

        //allow to change email data from extensions
        $view = new AView($this->registry, 0);
        $view->batchAssign($this->data['mail_template_data']);
        $html_body = $view->fetch($this->data['mail_template']);

        $mail = new AMail($this->config);
        $mail->setTo($email);
        $mail->setFrom($this->config->get('store_main_email'));
        $mail->setSender($this->config->get('store_name'));
        $mail->setHtml($html_body);
        $mail->setSubject($this->data['mail_template_data']['subject']);
        $mail->send();

        if ($mail->error) {
            $error = new AError('AMail Errors: '.implode("\n", $mail->error));
            $error->toLog()->toDebug();
            return false;
        }

        return true;
    }
}