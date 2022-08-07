<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2022 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/


use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * Class AMail
 *
 * @property ExtensionsApi $extensions
 */
class AMail
{
    protected $mailer;
    protected $email;


    /** @var AMessage */
    protected $messages;
    /** @var ALog */
    protected $log;
    protected $storeId = 0;
    protected $placeholders = [];
    protected $emailTemplate;
    public $transporting = 'mail';
    public $error = [];

    protected $extensions;

    /**
     * @param null | AConfig $config
     *
     * @throws AException
     */
    public function __construct($config = null)
    {
        $dsn = '';
        $registry = Registry::getInstance();
        $config = is_object($config) ? $registry->get('config') : $config;
        $this->email = new Email();

        //set default configuration values
        $this->transporting = $config->get('config_mail_transporting');
        if ($this->transporting == 'smtp') {
            $host = $config->get('config_smtp_host');
            $host = substr($host,0, 6) == 'ssl://' ? substr($host,6) : $host;
            $dsn = 'smtp://'
                . urlencode($config->get('config_smtp_username')) . ':' . urlencode($config->get('config_smtp_password'))
                . '@' . urlencode($host) . ':' . $config->get('config_smtp_port');
            //try to set timeout silently
            try {
                ini_set('default_socket_timeout', $config->get('config_smtp_timeout'));
            } catch (Exception $e) {}
        } elseif ($this->transporting == 'mail') {
            $dsn = 'native://default';
        } elseif ($this->transporting == 'dsn') {
            if (MAILER['dsn']) {
                $dsn = MAILER['dsn'];
            } else {
                $dsn = MAILER['protocol'] . '://'
                    . urlencode(MAILER['username']) . (MAILER['password'] ? ':' . urlencode(MAILER['password']) : '')
                    . '@' . urlencode(MAILER['host'] ?: 'default') . (MAILER['port'] ? ':' . MAILER['port'] : '');
            }
        }
        if(!$dsn){
            $dsn = 'native://default';
        }

        $this->log = $registry->get('log');
        $this->messages = $registry->get('messages');
        $this->storeId = $config->get('current_store_id') ?? $config->get('config_store_id') ?? 0;
        $this->extensions = $registry->get('extensions');

        $transport = Symfony\Component\Mailer\Transport::fromDsn($dsn);
        $registry->set('current_mail_transport', get_class($transport));
        $this->mailer = new Mailer($transport);
    }

    /**
     * @param string|array $to - email address
     */
    public function setTo($to)
    {
        if (is_array($to)) {
            foreach ($to as $t) {
                $this->email->addTo(new Address($t));
            }
        } else {
            $this->email->to($to);
        }
    }

    /**
     * @param string $from - email address
     */
    public function setFrom($from)
    {
        $this->email->from($from);
    }

    /**
     * @param string $header
     * @param string $value
     */
    public function addHeader($header, $value)
    {
        $this->email->getHeaders()->addTextHeader(trim($header, " "), trim($value));
    }

    /**
     * @param string $name - sender's name
     */
    public function setSender($name, $from = null)
    {
        $from = $from ?? current($this->email->getFrom());
        $from = $from instanceof Address ? $from->getAddress() : (string)$from;
        if($from) {
            $this->email->sender(new Address($from, $name));
        }
    }

    /**
     * @param string $reply_to - email address
     */
    public function setReplyTo($reply_to)
    {
        $this->email->replyTo(new Address($reply_to));

    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->email->subject($subject);
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->email->text($text);
    }

    /**
     * @param string $html
     */
    public function setHtml($html)
    {
        $this->email->html($html);
    }

    /**
     * @param string $text_id
     * @param array $placeholders
     * @param int $languageId
     *
     * @throws AException
     */
    public function setTemplate($text_id, array $placeholders = [], $languageId = 0)
    {

        $text_id = trim($text_id);
        if (empty($text_id)) {
            $this->log->write('Email text id can\'t be empty');
            return;
        }

        if (!preg_match("/(^[\w]+)$/i", $text_id)) {
            $this->log->write('Email text id "' . $text_id . '" must be in one word without spaces, underscores are allowed');
            return;
        }

        $db = Registry::getInstance()->get('db');
        if (!$languageId) {
            /** @var ALanguageManager */
            $language = Registry::getInstance()->get('language');
            $languageId = IS_ADMIN ? $language->getContentLanguageID() : $language->getLanguageID();
        }

        $emailTemplate = $db->query(
            "SELECT * 
            FROM " . $db->table('email_templates') . " 
            WHERE `text_id`='" . $text_id . "' 
                AND `language_id` = " . (int)$languageId . "
                AND `status` = 1 and `store_id` = " . (int)$this->storeId . " LIMIT 1"
        );
        if (empty($emailTemplate->rows)) {
            $this->log->write('Email Template with text id "' . $text_id . '" and language_id = ' . $languageId . ' not found');
            return;
        }

        $this->emailTemplate = $emailTemplate->row;
        $arAllowedPlaceholders = explode(',', $this->emailTemplate['allowed_placeholders']);

        foreach ($arAllowedPlaceholders as &$placeholder) {
            $placeholder = trim($placeholder);
        }

        foreach ($placeholders as $key => $val) {
            if (in_array($key, $arAllowedPlaceholders, true)) {
                $this->placeholders[$key] = $val;
            }
        }

        $this->extensions->hk_ProcessData($this, 'setTemplate', [
            'text_id' => $text_id,
            'language_id' => $languageId,
        ]);

        $subject = html_entity_decode($this->emailTemplate['subject'], ENT_QUOTES);
        $htmlBody = html_entity_decode($this->emailTemplate['html_body'], ENT_QUOTES);
        $textBody = $this->emailTemplate['text_body'];

        // allow to pass html as text_variable (needed for logo as resource_html)
        //override default escaping by transparent custom
        $mustache = new Mustache_Engine(['escape' => function ($value) {
            return $value;
        }]);
        $subject = $mustache->render($subject, $this->placeholders);
        $htmlBody = $mustache->render($htmlBody, $this->placeholders);
        $textBody = $mustache->render($textBody, $this->placeholders);

        $this->setSubject($subject);
        $this->setHtml($htmlBody);
        $this->setText($textBody);

        if ($this->emailTemplate['headers']) {
            $headers = explode(',', $this->emailTemplate['headers']);
            foreach ($headers as $header) {
                $parts = explode(':', $header);
                if (count((array)$parts) !== 2) {
                    continue;
                }
                $this->addHeader($parts[0], $parts[1]);
            }
        }
    }

    /**
     * @param $key
     * @param $value
     */
    public function setPlaceholder($key, $value)
    {
        $this->placeholders[$key] = $value;
    }

    /**
     * @param string $file - full path to file
     * @param string $filename
     */
    public function addAttachment($file, $filename = '')
    {
        if (!$filename) {
            $filename = md5(pathinfo($file, PATHINFO_FILENAME)) . '.' . pathinfo($file, PATHINFO_EXTENSION);
        }
        $this->email->attachFromPath($file, $filename);

    }

    /**
     * @return bool
     * @throws TransportExceptionInterface
     */
    public function send()
    {

        if (defined('IS_DEMO') && IS_DEMO) {
            return null;
        }

        if (!$this->email->getTo()) {
            $error = 'Error: E-Mail to required!';
            $this->log->write($error);
            $this->error[] = $error;
            $this->messages->saveError('Mailer error!', 'Can\'t send emails. Please see log for details and check your mail settings.');
            return false;
        }

        if (!$this->email->getFrom()) {
            $error = 'Error: E-Mail from required!';
            $this->log->write($error);
            $this->error[] = $error;
            $this->messages->saveError('Mailer error!', 'Can\'t send emails. Please see log for details and check your mail settings.');
            return false;
        }

        if (!$this->email->getSubject()) {
            $error = 'Error: E-Mail subject required!';
            $this->log->write($error);
            $this->error[] = $error;
            $this->messages->saveError('Mailer error!', 'Can\'t send emails. Please see log for details and check your mail settings.');
            return false;
        }

        if (!$this->email->getTextBody() && !$this->email->getHtmlBody()) {
            $error = 'Error: E-Mail message required!';
            $this->log->write($error);
            $this->error[] = $error;
            $this->messages->saveError('Mailer error!', 'Can\'t send emails. Please see log for details and check your mail settings.');
            return false;
        }

        try {
            $this->email->ensureValidity();
            $this->mailer->send($this->email);
        }catch(Exception $e){
            $this->log->write(__CLASS__.'. transport: '.Registry::getInstance()->get('current_mail_transport').': '.$e->getMessage());
            $this->error[] = $e->getMessage();
        }

        if ($this->error) {
            $this->messages->saveError('Mailer error!', 'Can\'t send emails. Please see log for details and check your mail settings.');
            return false;
        }

        return true;
    }
}
