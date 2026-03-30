<?php
/*
 *   Shared USPS context helpers for environment/base URL/cache key parts.
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
