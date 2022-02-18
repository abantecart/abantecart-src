<?php

final class DefaultTextMarketer
{
    public $errors = array();
    private $registry;
    private $config;
    private $sender;

    public function __construct()
    {
        $this->registry = Registry::getInstance();
        $this->registry->get('language')->load('default_textmarketer/default_textmarketer');
        $this->config = $this->registry->get('config');
        try {
            include_once('textmarketer.php');
            $this->sender = new TextMarketer($this->config->get('default_textmarketer_username'),
                $this->config->get('default_textmarketer_password'),
                $this->config->get('default_textmarketer_test'));
        } catch (Exception $e) {
            if ($this->config->get('default_textmarketer_logging')) {
                $this->registry->get('log')->write('TextMarketer error: '.$e->getMessage().'. Error Code:'.$e->getCode());
            }
        }
    }

    public function getProtocol()
    {
        return 'sms';
    }

    public function getProtocolTitle()
    {
        return $this->registry->get('language')->get('default_textmarketer_protocol_title');
    }

    public function getName()
    {
        return 'TextMarketer';
    }

    public function send($to, $text)
    {
        if (!$to || !$text) {
            return null;
        }
        $to = '+'.ltrim($to, '+');
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        try {
            $originator = $this->config->get('default_textmarketer_originator');
            $originator = preg_replace('/[^a-zA-z]/', '', $originator);
            $this->sender->send($text, $to, $originator);
            $result = true;
        } catch (Exception $e) {
            if ($this->config->get('default_textmarketer_logging')) {
                $this->registry->get('log')->write('TextMarketer error: '.$e->getMessage().'. Error Code:'.$e->getCode());
            }
            $result = false;
        }

        return $result;
    }

    public function sendFew($to, $text)
    {
        foreach ($to as $uri) {
            $this->send($uri, $text);
        }
    }

    public function validateURI($uri)
    {
        $this->errors = array();
        $uri = trim($uri);
        $uri = trim($uri, ',');

        $uris = explode(',', $uri);
        foreach ($uris as $u) {
            $u = trim($u);
            if (!$u) {
                continue;
            }
            $u = preg_replace('/[^0-9\+]/', '', $u);
            if ($u[0] != '+') {
                $u = '+'.$u;
            }
            if (!preg_match('/^\+[1-9]{1}[0-9]{3,14}$/', $u)) {
                $this->errors[] = 'Mobile number '.$u.' is not valid!';
            }
        }

        if ($this->errors) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Function builds form element for storefront side (customer account page)
     *
     * @param AForm  $form
     * @param string $value
     *
     * @return object
     */
    public function getURIField($form, $value = '')
    {
        $this->registry->get('language')->load('default_textmarketer/default_textmarketer');
        return $form->getFieldHtml(
            array(
                'type'       => 'phone',
                'name'       => 'sms',
                'value'      => $value,
                'label_text' => $this->registry->get('language')->get('entry_sms'),
            ));
    }
}