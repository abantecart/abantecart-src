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

use contracts\MailApi;
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
    /** @var MailApi|false|Mailer|null  */
    protected $mailer;
    /** @var Email  */
    protected $email;

    /** @var AMessage */
    protected $messages;
    /** @var ALog */
    protected $log;
    protected $storeId = 0;
    protected $placeholders = [];
    protected $emailTemplate = [];
    public $transporting = 'mail';
    public $error = [];

    protected $extensions;

    /**
     * @param null | AConfig $config
     *
     * @throws AException
     */
    public function __construct(?AConfig $config)
    {
        $dsn = '';
        $registry = Registry::getInstance();
        $config = $config ?? $registry?->get('config');
        $this->email = new Email();

        //set default configuration values
        $this->transporting = $config->get('config_mail_transporting');
        if ($this->transporting == 'smtp') {
            $host = $config->get('config_smtp_host');
            $host = substr($host, 0, 6) == 'ssl://' ? substr($host, 6) : $host;
            $dsn = 'smtp://'
                . urlencode($config->get('config_smtp_username')) . ':' . urlencode($config->get('config_smtp_password'))
                . '@' . urlencode($host) . ':' . $config->get('config_smtp_port');
            //try to set timeout silently
            try {
                ini_set('default_socket_timeout', $config->get('config_smtp_timeout'));
            } catch (Exception) {
            }
        } elseif ($this->transporting == 'mail') {
            $dsn = 'native://default';
        } elseif ($this->transporting == 'dsn' && defined('MAILER') && is_array(MAILER)) {
            if (MAILER['dsn']) {
                $dsn = MAILER['dsn'];
            } else {
                $dsn = MAILER['protocol'] . '://'
                    . urlencode(MAILER['username']) . (MAILER['password'] ? ':' . urlencode(MAILER['password']) : '')
                    . '@' . urlencode(MAILER['host'] ?: 'default') . (MAILER['port'] ? ':' . MAILER['port'] : '');
            }
        }elseif(str_starts_with($this->transporting, 'mailapi_')){
            $this->mailer = MailApiManager::getInstance()->getCurrentMailApiDriver();
            if(is_bool($this->mailer)){
                $this->mailer = null;
            }
        }
        if (!$dsn) {
            $dsn = 'native://default';
        }
        if(!$this->mailer) {
            $transport = Symfony\Component\Mailer\Transport::fromDsn($dsn);
            $registry->set('current_mail_transport', get_class($transport));
            $this->mailer = new Mailer($transport);
        }

        $this->log = $registry->get('log');
        $this->messages = $registry->get('messages');
        $this->storeId = (int)($config->get('current_store_id') ?? $config->get('config_store_id'));
        $this->extensions = $registry->get('extensions');
    }

    /**
     * @param string|array $to - email address
     */
    public function setTo(string|array $to = '')
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
    public function setFrom(string $from = '')
    {
        $this->email->from($from);
    }

    /**
     * @param string $header
     * @param string $value
     */
    public function addHeader(string $header, string $value)
    {
        $this->email->getHeaders()->addTextHeader(trim($header, " "), trim($value));
    }

    /**
     * @param string $name - sender's name
     */
    public function setSender(string $name, ?string $from = '')
    {
        $from = $from ?? current($this->email->getFrom());
        $from = $from instanceof Address ? $from->getAddress() : (string)$from;
        if ($from) {
            $this->email->sender(new Address($from, $name));
        }
    }

    /**
     * @param string $reply_to - email address
     */
    public function setReplyTo(string $reply_to = '')
    {
        $this->email->replyTo(new Address($reply_to));

    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject = '')
    {
        $this->email->subject($subject);
    }

    /**
     * @param string $text
     */
    public function setText(string $text = '')
    {
        $this->email->text($text);
    }

    /**
     * @param string $html
     */
    public function setHtml(string $html = '')
    {
        $this->email->html($html);
    }

    /**
     * @param string $textId
     * @param array $placeholders
     * @param int $languageId
     *
     * @throws AException
     */
    public function setTemplate(string $textId, array $placeholders = [], int $languageId = 0)
    {
        if (preformatTextID($textId) != $textId) {
            $this->log->write('Email text id "' . $textId . '" must be in one word without spaces, underscores are allowed');
            return;
        }
        if (empty($textId)) {
            $this->log->write('Email text id can\'t be empty');
            return;
        }


        $db = Registry::getInstance()->get('db');
        if (!$languageId) {
            /** @var ALanguageManager $language */
            $language = Registry::getInstance()->get('language');
            $languageId = IS_ADMIN ? $language?->getContentLanguageID() : $language?->getLanguageID();
        }
        if (!$languageId) {
            throw new AException(__METHOD__ . ' Language ID is required for email template!');
        }

        $emailTemplate = $db->query(
            "SELECT * 
            FROM " . $db->table('email_templates') . " 
            WHERE `text_id`='" . $textId . "' 
                AND `language_id` = " . (int)$languageId . "
                AND `status` = 1 and `store_id` = " . (int)$this->storeId . " 
            LIMIT 1"
        );
        if (empty($emailTemplate->rows)) {
            $this->log->write('Email Template with text id "' . $textId . '" and language_id = ' . $languageId . ' not found');
            return;
        }

        $this->emailTemplate = $emailTemplate->row;
        $arAllowedPlaceholders = array_map(
            'trim',
            explode(',', $this->emailTemplate['allowed_placeholders'])
        );

        foreach ($placeholders as $key => $val) {
            if (in_array($key, $arAllowedPlaceholders, true)) {
                $this->placeholders[$key] = $val;
            }
        }

        $this->extensions->hk_ProcessData(
            $this,
            'setTemplate',
            [
                'text_id'     => $textId,
                'language_id' => $languageId,
            ]
        );

        $subject = html_entity_decode($this->emailTemplate['subject'], ENT_QUOTES);
        $htmlBody = html_entity_decode($this->emailTemplate['html_body'], ENT_QUOTES);
        $textBody = $this->emailTemplate['text_body'];

        // allow passing HTML as text_variable (needed for logo as resource_html)
        //override default escaping by transparent custom
        $mustache = new Mustache_Engine([
            'escape' => function ($value) {
                return $value;
            }
        ]);
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
     * @param string $path - full path to file
     * @param string $filename
     */
    public function addAttachment(string $path, string $filename = '')
    {
        if (!$filename) {
            $filename = md5(pathinfo($path, PATHINFO_FILENAME)) . '.' . pathinfo($path, PATHINFO_EXTENSION);
        }
        $this->email->attachFromPath($path, $filename);

    }

    /**
     * @return bool
     * @throws TransportExceptionInterface|AException
     */
    public function send(bool $silent = false)
    {

        if (defined('IS_DEMO') && IS_DEMO) {
            return true;
        }

        if (!$this->email->getTo()) {
            $error = 'Error: E-Mail to required!';
            $this->log->write($error);
            $this->error[] = $error;
            $this->messages->saveError('Mailer error!', 'Can\'t send emails. Please see log for details and check your mail settings.');
            return $silent;
        }

        if (!$this->email->getFrom()) {
            $error = 'Error: E-Mail from required!';
            $this->log->write($error);
            $this->error[] = $error;
            $this->messages->saveError('Mailer error!', 'Can\'t send emails. Please see log for details and check your mail settings.');
            return $silent;
        }

        if (!$this->email->getSubject()) {
            $error = 'Error: E-Mail subject required!';
            $this->log->write($error);
            $this->error[] = $error;
            $this->messages->saveError('Mailer error!', 'Can\'t send emails. Please see log for details and check your mail settings.');
            return $silent;
        }

        if (!$this->email->getTextBody() && !$this->email->getHtmlBody()) {
            $error = 'Error: E-Mail message required!';
            $this->log->write($error);
            $this->error[] = $error;
            $this->messages->saveError('Mailer error!', "Can't send emails. Please see log for details and check your mail settings.");
            return $silent;
        }

        try {
            $this->email->ensureValidity();
            if($this->mailer instanceof MailApi){
                $this->mailer->send($this);
            }else {
                $this->mailer->send($this->email);
            }
        } catch (Exception|Error $e) {
            $this->log->write(__CLASS__ . ': ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            $this->error[] = $e->getMessage() . "\n" . $e->getTraceAsString();
        }

        if ($this->error) {
            $this->messages->saveError('Mailer error!', 'Can\'t send emails. Please see log for details and check your mail settings.');
            return $silent;
        }

        return true;
    }

    public function getEmail(): EMail
    {
        return $this->email;
    }

    public function getEmailTemplate(): array
    {
        return $this->emailTemplate;
    }

    public function getPlaceholders(): array
    {
        return $this->placeholders;
    }
}
