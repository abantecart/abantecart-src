<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ModelToolMessageManager extends Model
{

    /**
     * @param int $messageId
     * @return true
     * @throws AException
     */
    public function deleteMessage($messageId)
    {
        return $this->messages->deleteMessage($messageId);
    }

    /**
     * @param int $messageId
     * @return array
     * @throws AException
     */
    public function getMessage($messageId)
    {
        return $this->messages->getMessage($messageId);
    }

    /**
     * @param array $data
     * @return array
     * @throws AException
     */
    public function getMessages(array $data = [])
    {

        if (!isset($data['sort'])) {
            $data['sort'] = 'viewed';
        }

        if ($data['start'] < 0) {
            $data['start'] = 0;
        }

        if ($data['limit'] < 1) {
            $data['limit'] = 10;
        }

        return $this->messages->getMessages($data['start'], $data['limit'], $data['sort'], $data['order']);
    }

    /**
     * @return int
     * @throws AException
     */
    public function getTotalMessages()
    {
        return $this->messages->getTotalMessages();
    }
}
