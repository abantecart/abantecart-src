<?php
/**
 * $Id$
 *
 * AbanteCart, Ideal OpenSource Ecommerce Solution
 * http://www.AbanteCart.com
 *
 * Copyright © 2011-2026 Belavier Commerce LLC
 *
 * This source file is subject to Open Software License (OSL 3.0)
 * License details are bundled with this package in the file LICENSE.txt.
 * It is also available at this URL:
 * <http://www.opensource.org/licenses/OSL-3.0>
 *
 * UPGRADE NOTE:
 *   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *   versions in the future. If you wish to customize AbanteCart for your
 *   needs, please refer to http://www.AbanteCart.com for more information.
 */
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class UspsApiContext
{
    public static function isDeveloperEnvironment($value)
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_numeric($value)) {
            return (int)$value === 1;
        }
        $normalized = strtolower(trim((string)$value));
        return in_array($normalized, ['1', 'true', 'yes', 'on', 'tem', 'developer', 'test'], true);
    }

    public static function getEnvironmentCode($value)
    {
        return self::isDeveloperEnvironment($value) ? 'developer' : 'production';
    }

    public static function getApiBaseUrl($value)
    {
        return self::isDeveloperEnvironment($value)
            ? 'https://apis-tem.usps.com'
            : 'https://apis.usps.com';
    }

    public static function buildHashKey($prefix, array $parts)
    {
        $normalized = [];
        foreach ($parts as $part) {
            $normalized[] = self::normalizeKeyPart($part);
        }
        return $prefix . md5(implode('|', $normalized));
    }

    private static function normalizeKeyPart($value)
    {
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }
        if ($value === null) {
            return '';
        }
        return trim((string)$value);
    }
}
