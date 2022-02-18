<?php
/**
 * AvaFunctions.class.php
 */

/**
 * Functions used for this library
 *
 * @author    Shawn Welch <shrimpwagon@yahoo.com>
 */

namespace AvaTax;

class AvaFunctions
{
    public static function EnsureIsArray($obj)
    {
        if (is_object($obj)) {
            $item[0] = $obj;
        } else {
            $item = (array)$obj;
        }
        return $item;
    }

    public static function getDefaultDate()
    {
        $dateTime = new DateTime();
        $dateTime->setDate(1900, 01, 01);
        return $dateTime->format("Y-m-d");
    }

    public static function getCurrentDate()
    {
        $dateTime = new DateTime();
        return $dateTime->format("Y-m-d");
    }
}