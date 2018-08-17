<?php
/**
 * Message.class.php
 */

/**
 * Message class used in results and exceptions.
 * Contains status detail about call results.
 * Note that the REST API does not make use of all of these properties for all methods.
 *
 * @package   Address
 * @author    Avalara
 * @copyright � 2004 - 2011 Avalara, Inc.  All rights reserved.
 */

namespace AvaTax;

class Message implements JsonSerializable
{
    private $Summary;
    private $Details;
    private $RefersTo;
    private $Severity;
    private $Source;

    public function __construct($summary = null, $details = null, $refersto = null, $severity = null, $source = null)
    {
        $this->Summary = $summary;
        $this->Details = $details;
        $this->RefersTo = $refersto;
        $this->Severity = $severity;
        $this->Source = $source;
    }

    //Helper function to decode result objects from Json responses to specific objects.
    public static function parseMessages($jsonString)
    {
        $object = json_decode($jsonString);
        $messageArray = array();
        $summary = null;
        $details = null;
        $refersto = null;
        $severity = null;
        $source = null;

        foreach ($object->Messages as $message) {
            if (property_exists($message, 'Summary')) {
                $summary = $message->Summary;
            }
            if (property_exists($message, 'Details')) {
                $details = $message->Details;
            }
            if (property_exists($message, 'RefersTo')) {
                $refersto = $message->RefersTo;
            }
            if (property_exists($message, 'Severity')) {
                $severity = $message->Severity;
            }
            if (property_exists($message, 'Source')) {
                $source = $message->Source;
            }

            $messageArray[] = new self(
                $summary,
                $details,
                $refersto,
                $severity,
                $source);
        }

        return $messageArray;

    }

    public function jsonSerialize()
    {
        return array(
            'Summary'  => $this->getSummary(),
            'Details'  => $this->getDetails(),
            'RefersTo' => $this->getRefersTo(),
            'Severity' => $this->getSeverity(),
            'Source'   => $this->getSource(),
        );
    }

    public function getSummary()
    {
        return $this->Summary;
    }

    public function getDetails()
    {
        return $this->Details;
    }

    public function getRefersTo()
    {
        return $this->RefersTo;
    }

    public function getSeverity()
    {
        return $this->Severity;
    }

    public function getSource()
    {
        return $this->Source;
    }

    // mutators
    public function setSummary($value)
    {
        $this->Summary = $value;
        return $this;
    }

    public function setDetails($value)
    {
        $this->Details = $value;
        return $this;
    }

    public function setRefersTo($value)
    {
        $this->RefersTo = $value;
        return $this;
    }

    public function setSeverity($value)
    {
        SeverityLevel::Validate($value);
        $this->Severity = $value;
        return $this;
    }

    public function setSource($value)
    {
        $this->Source = $value;
        return $this;
    }

}

?>