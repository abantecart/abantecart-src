<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2026 Belavier Commerce LLC
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
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * Finds the redirect rule (table url_redirects) matched to the request.
 * Has no side effects: it does not send headers and does not count hits.
 */
class AUrlRedirect
{
    /** actions which send the visitor to the target url */
    public const REDIRECT_ACTIONS = [301, 302];

    /** @var array */
    protected $rules;
    /** @var string */
    protected $storeUrl;

    /**
     * @param array $rules enabled rules in the order of checking
     * @param string $storeUrl store url including the store alias path, e.g. "https://example.com/shop/"
     */
    public function __construct(array $rules, string $storeUrl)
    {
        $this->rules = $rules;
        $this->storeUrl = $storeUrl;
    }

    /**
     * Wraps the regex source into delimiters. Used by admin validation and by matching.
     *
     * @param string $source
     *
     * @return string
     */
    public static function regexPattern(string $source): string
    {
        return '~' . str_replace('~', '\~', $source) . '~i';
    }

    /**
     * Parses headers written in the http format: "Name: value", one header per line.
     * The value can be empty, it means that the header must be present in the request.
     *
     * @param string $headers
     *
     * @return array|null list of [name, value] pairs, null when the text is not valid
     */
    public static function parseHeaders(string $headers): ?array
    {
        $result = [];
        foreach (preg_split('~\r\n|\r|\n~', $headers) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            // token (RFC 7230) followed by colon and printable value
            if (!preg_match('~^([!#$%&\'*+.^_`|0-9a-z-]+)[ \t]*:[ \t]*([^\x00-\x08\x0A-\x1F\x7F]*)$~i', $line, $m)) {
                return null;
            }
            $result[] = [$m[1], rtrim($m[2])];
        }
        return $result;
    }

    /**
     * @param string $host request host, port is allowed. Used to skip rules leading to the same page
     * @param string $requestUri e.g. "/shop/old-product?a=1"
     * @param array $get raw query parameters without "_route_"
     * @param string $referer value of HTTP_REFERER
     * @param string $ip visitor ip address
     * @param array $headers request headers, name => value. Names are case-insensitive
     *
     * @return array|null ['rule' => array, 'action' => int, 'url' => string], url is empty for non-redirect actions
     */
    public function resolve(
        string $host,
        string $requestUri,
        array $get,
        string $referer = '',
        string $ip = '',
        array $headers = []
    ): ?array {
        if (!$this->rules) {
            return null;
        }
        $host = $this->normalizeHost($host);
        $path = $this->getRequestPath($requestUri);
        $refererHost = $this->normalizeHost((string)parse_url($referer, PHP_URL_HOST));
        $ip = mb_strtolower(trim($ip));
        $headers = array_change_key_case($headers, CASE_LOWER);
        foreach ($this->rules as $rule) {
            // source_host is the host of HTTP_REFERER or the ip of the visitor
            if ($rule['source_host'] !== ''
                && $rule['source_host'] !== $refererHost
                && $rule['source_host'] !== $ip
            ) {
                continue;
            }
            if (!$this->matchHeaders((string)($rule['source_http_headers'] ?? ''), $headers)) {
                continue;
            }
            $match = $this->matchRule($rule, $path, $get);
            if ($match === null) {
                continue;
            }
            $action = (int)$rule['action'];
            $url = '';
            if (in_array($action, self::REDIRECT_ACTIONS, true)) {
                $url = $this->buildUrl($match['target'], $rule['keep_query'] ? $match['query'] : []);
                // rule leads to the same page
                if ($this->isSameUrl($url, '//' . $host . $requestUri)) {
                    continue;
                }
            }
            return ['rule' => $rule, 'action' => $action, 'url' => $url];
        }
        return null;
    }

    /**
     * @param string $ruleHeaders headers of the rule in the http format
     * @param array $headers request headers with lower case names
     *
     * @return bool true when every header of the rule is found in the request
     */
    protected function matchHeaders(string $ruleHeaders, array $headers): bool
    {
        $pairs = self::parseHeaders($ruleHeaders);
        if ($pairs === null) {
            return false;
        }
        foreach ($pairs as [$name, $value]) {
            $name = strtolower($name);
            if (!isset($headers[$name]) || !is_scalar($headers[$name])) {
                return false;
            }
            // the value of the rule is a case-insensitive part of the request header value
            if ($value !== '' && mb_stripos((string)$headers[$name], $value) === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param array $rule
     * @param string $path
     * @param array $query
     *
     * @return array|null target and query parameters to keep, null when the rule does not match
     */
    protected function matchRule(array $rule, string $path, array $query): ?array
    {
        $source = $rule['source'];
        $target = $rule['target'];
        // rule restricted by source_host only
        if ($source === '') {
            return ['target' => $target, 'query' => $query];
        }
        switch ($rule['match_type']) {
            case 'exact':
                if (mb_strtolower($path) !== mb_strtolower($source)) {
                    return null;
                }
                break;
            case 'prefix':
                $lowerPath = mb_strtolower($path);
                $lowerSource = mb_strtolower($source);
                if ($lowerPath !== $lowerSource && !str_starts_with($lowerPath, $lowerSource . '/')) {
                    return null;
                }
                // "/old-cat/sub/item" with source "/old-cat" and target "/new-cat" -> "/new-cat/sub/item"
                $tail = (string)mb_substr($path, mb_strlen($source));
                if ($tail !== '') {
                    $tail = implode('/', array_map('rawurlencode', explode('/', $tail)));
                    $splitPos = strcspn($target, '?#');
                    $target = rtrim(substr($target, 0, $splitPos), '/') . $tail . substr($target, $splitPos);
                }
                break;
            case 'regex':
                if (!@preg_match(self::regexPattern($source), $path, $matches)) {
                    return null;
                }
                $target = preg_replace_callback(
                    '~\$(\d+)~',
                    fn($group) => $matches[(int)$group[1]] ?? '',
                    $target
                );
                break;
            case 'query':
                parse_str($source, $params);
                if (!$params) {
                    return null;
                }
                foreach ($params as $name => $value) {
                    if (!isset($query[$name])
                        || !is_scalar($value)
                        || !is_scalar($query[$name])
                        || (string)$query[$name] !== (string)$value
                    ) {
                        return null;
                    }
                    // matched parameters are replaced by the target
                    unset($query[$name]);
                }
                break;
            default:
                return null;
        }
        return ['target' => $target, 'query' => $query];
    }

    /**
     * @param string $target relative or absolute url
     * @param array $query parameters to append
     *
     * @return string
     */
    protected function buildUrl(string $target, array $query): string
    {
        if (!preg_match('~^([a-z][a-z0-9+.-]*:)?//~i', $target)) {
            $target = rtrim($this->storeUrl, '/') . '/' . ltrim($target, '/');
        }
        if ($query) {
            $fragmentPos = strpos($target, '#');
            $fragment = $fragmentPos === false ? '' : substr($target, $fragmentPos);
            $target = $fragmentPos === false ? $target : substr($target, 0, $fragmentPos);
            $target .= (str_contains($target, '?') ? '&' : '?') . http_build_query($query) . $fragment;
        }
        return $target;
    }

    /**
     * @param string $url
     * @param string $currentUrl
     *
     * @return bool
     */
    protected function isSameUrl(string $url, string $currentUrl): bool
    {
        $normalize = fn(string $value) => rtrim(preg_replace('~^[a-z][a-z0-9+.-]*:~i', '', $value), '/');
        return strcasecmp($normalize($url), $normalize($currentUrl)) === 0;
    }

    /**
     * @param string $host
     *
     * @return string host without port in lower case
     */
    protected function normalizeHost(string $host): string
    {
        // forwarded host can contain a list of proxies
        $host = trim(explode(',', $host)[0]);
        return mb_strtolower(preg_replace('~:\d+$~', '', $host));
    }

    /**
     * @param string $requestUri
     *
     * @return string decoded path relative to the store url, e.g. "/old-product"
     */
    protected function getRequestPath(string $requestUri): string
    {
        $path = rawurldecode((string)parse_url($requestUri, PHP_URL_PATH));
        $base = '/' . trim((string)parse_url($this->storeUrl, PHP_URL_PATH), '/');
        if ($base !== '/' && stripos($path . '/', $base . '/') === 0) {
            $path = substr($path, strlen($base));
        }
        $path = '/' . ltrim($path, '/');
        // "/index.php" is the store root
        $indexFile = defined('INDEX_FILE') ? INDEX_FILE : 'index.php';
        if (stripos($path . '/', '/' . $indexFile . '/') === 0) {
            $path = substr($path, strlen($indexFile) + 1);
        }
        return '/' . trim($path, '/');
    }
}
