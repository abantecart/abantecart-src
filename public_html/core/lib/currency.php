<?php

/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

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
 * Class ACurrency
 */
final class ACurrency
{
    private $code;
    private $currencies = [];
    private $config;
    private $db;
    private $language;
    private $request;
    private $session;
    private $log;
    private $message;
    /**
     * @var bool - sign that currency was switched
     */
    private $is_switched = false;

    /**
     * @param $registry Registry
     *
     * @throws AException
     */
    public function __construct($registry)
    {
        $this->config = $registry->get('config');
        $this->db = $registry->get('db');
        $this->language = $registry->get('language');
        $this->request = $registry->get('request');
        $this->session = $registry->get('session');
        $this->log = $registry->get('log');
        /** @var AMessage */
        $this->message = $registry->get('messages');

        $cache = $registry->get('cache');
        $cache_key = 'localization.currencies';
        $cache_data = $cache->pull($cache_key);
        if ($cache_data !== false) {
            $this->currencies = $cache_data;
        } else {
            $query = $this->db->query("SELECT * FROM ".$this->db->table("currencies"));
            foreach ($query->rows as $result) {
                $this->currencies[$result['code']] = [
                    'code'          => $result['code'],
                    'currency_id'   => $result['currency_id'],
                    'title'         => $result['title'],
                    'symbol_left'   => $result['symbol_left'],
                    'symbol_right'  => $result['symbol_right'],
                    'decimal_place' => (int)$result['decimal_place'],
                    'value'         => $result['value'],
                    'status'        => $result['status'],
                ];
            }
            $cache->push($cache_key, $this->currencies);
        }

        $currencyCode =
            isset($this->request->get['currency']) && $this->isValidCodeFormat($this->request->get['currency'])
                ? $this->request->get['currency']
                : '';
        if ($currencyCode && array_key_exists($currencyCode, $this->currencies)) {
            $this->set($currencyCode);
            // Currency is switched, set sign for external use via isSwitched method
            $this->is_switched = true;
            unset(
                $this->request->get['currency'],
                $this->session->data['shipping_methods'],
                $this->session->data['shipping_method']
            );
        } elseif (isset($this->session->data['currency'])
            && array_key_exists($this->session->data['currency'], $this->currencies)
        ) {
            $this->set($this->session->data['currency']);
        } elseif (isset($this->request->cookie['currency'])
            && array_key_exists($this->request->cookie['currency'], $this->currencies)
        ) {
            if (IS_ADMIN === true) {
                $this->set($this->config->get('config_currency'));
            } else {
                $this->set($this->request->cookie['currency']);
            }
        } else {
            // need to know about currency switch. Check if currency was set but not in list of available currencies
            if (isset($currencyCode) || isset($this->session->data['currency'])
                || isset($this->request->cookie['currency'])
            ) {
                $this->is_switched = true;
            }
            $this->set($this->config->get('config_currency'));
        }
    }

    /**
     * @return bool
     */
    public function isSwitched()
    {
        return $this->is_switched;
    }

    /**
     * @param string $currency
     *
     * @return false
     */
    public function set($currency)
    {
        if (!isset($this->currencies[$currency])) {
            return false;
        }
        // if currency disabled - set first enabled from list
        if (!$this->currencies[$currency]['status']) {
            foreach ($this->currencies as $curr) {
                if ($curr['status']) {
                    $currency = $curr['code'];
                    break;
                }
            }
        }

        $this->code = $currency;

        if ((!isset($this->session->data['currency'])) || ($this->session->data['currency'] != $currency)) {
            $this->session->data['currency'] = $currency;
        }

        if ((!isset($this->request->cookie['currency'])) || ($this->request->cookie['currency'] != $currency)) {
            //Set cookie for the currency code
            setCookieOrParams(
                'currency',
                $currency,
                [
                    'path'     => dirname($this->request->server['PHP_SELF']),
                    'domain'   => null,
                    'secure'   => (defined('HTTPS') && HTTPS),
                    'httponly' => true,
                    'samesite' => ((defined('HTTPS') && HTTPS) ? 'None' : 'lax'),
                    'lifetime' => time() + 60 * 60 * 24 * 30,
                ]
            );
        }
        return true;
    }

    /**
     * Format only number part (digit based)
     *
     * @param float $number
     * @param string $currency
     * @param string $crr_value
     *
     * @return string
     * @throws AException
     */
    public function format_number($number, $currency = '', $crr_value = '')
    {
        return $this->format($number, $currency, $crr_value, false);
    }

    /**
     * Format total number part and/or currency symbol based on original price and quantity
     *
     * @param float $price
     * @param float $qty
     * @param string $currency
     * @param string $crr_value
     *
     * @return string
     * @throws AException
     */
    public function format_total($price, $qty, $currency = '', $crr_value = '')
    {
        if (!is_numeric($price) || !is_numeric($qty)) {
            return '';
        }

        $total = $this->format_number($price, $currency, $crr_value) * $qty;
        return $this->wrap_display_format($total, $currency);
    }

    /**
     * Format number part and/or currency symbol
     *
     * @param float $number
     * @param string $currency
     * @param float $crr_value
     * @param bool $format
     *
     * @return string|float
     * @throws AException
     */
    public function format($number, $currency = '', $crr_value = 0.0, $format = true)
    {
        if (empty ($currency)) {
            $currency = $this->code;
        }

        if (!$crr_value) {
            $crr_value = $this->currencies[$currency]['value'];
        }

        if ($crr_value) {
            $value = (float)$number * (float)$crr_value;
        } else {
            $value = (float)$number;
        }

        $decimal_place = (int) $this->currencies[$currency]['decimal_place'];

        if ($format) {
            $formatted_number = $this->wrap_display_format(round(abs($value), $decimal_place), $currency);
        } else {
            $formatted_number = number_format(round(abs($value), $decimal_place), $decimal_place, '.', '');
        }
        //check if number is negative
        $sign = '';
        if (round($value, $decimal_place) < 0) {
            $sign = '-';
        }
        return $sign.$formatted_number;
    }

    /**
     * Format number part and/or currency symbol
     *
     * @param float $number
     * @param string $currency
     *
     * @return string
     * @throws AException
     * @internal param bool $format
     */
    public function wrap_display_format($number, $currency = '')
    {
        if (empty ($currency)) {
            $currency = $this->code;
        }
        $symbol_left = $this->currencies[$currency]['symbol_left'];
        $symbol_right = $this->currencies[$currency]['symbol_right'];
        $decimal_place = (int) $this->currencies[$currency]['decimal_place'];
        $decimal_point = $this->language->get('decimal_point');
        $thousand_point = $this->language->get('thousand_point');

        $formatted_number = number_format($number, $decimal_place, $decimal_point, $thousand_point);
        return $symbol_left.$formatted_number.$symbol_right;
    }

    /**
     * @param float $value
     * @param string $code_from
     * @param string $code_to
     *
     * @return float|bool
     */
    public function convert($value, $code_from, $code_to)
    {
        $value = (float)$value;
        $from = isset($this->currencies[$code_from]['value']) ? (float)$this->currencies[$code_from]['value'] : 0;
        $to = isset($this->currencies[$code_to]['value']) ? (float)$this->currencies[$code_to]['value'] : 0;
        $to_decimal = isset($this->currencies[$code_to]['decimal_place'])
            ? (int) $this->currencies[$code_to]['decimal_place']
            : 2;

        $error = false;
        if (!$to) {
            $msg = 'Error: tried to convert into inaccessible currency! Currency code is '.$code_to;
            $this->log->write('ACurrency '.$msg);
            $this->message->saveError('Currency conversion error', $msg);
            $error = true;
        }
        if (!$from) {
            $msg = 'Error: tried to convert from inaccessible currency! Currency code is '.$code_from;
            $this->log->write('ACurrency '.$msg);
            $this->message->saveError('Currency conversion error .', $msg);
            $error = true;
        }

        if ($error) {
            return false;
        }
        return round($value * ($to / $from), $to_decimal);
    }

    public function getCurrencies()
    {
        return $this->currencies;
    }

    /**
     * @param string $code
     *
     * @return array
     */
    public function getCurrency($code = '')
    {
        if ($code == '') {
            $code = $this->code;
        }
        return $this->currencies[$code];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->currencies[$this->code]['currency_id'];
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $currency
     *
     * @return float
     */
    public function getValue($currency)
    {
        if (isset($this->currencies[$currency])) {
            return $this->currencies[$currency]['value'];
        } else {
            return 0.00;
        }
    }

    /**
     * @param string $code
     *
     * @return bool
     */
    public function has($code)
    {
        return isset($this->currencies[$code]);
    }

    /**
     * @param string $code
     *
     * @return bool
     */
    public function isValidCodeFormat($code)
    {
        if (preg_match('/^[a-zA-Z0-9]{3}$/', $code)) {
            return true;
        } else {
            return false;
        }
    }
}