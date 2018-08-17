<?php
/**
 * TaxOverride.class.php
 */

/**
 *
 *
 * @author    Avalara
 * @copyright � 2004 - 2011 Avalara, Inc.  All rights reserved.
 * @package   Tax
 */

namespace AvaTax;

class TaxOverride
{
    public $TaxOverrideType;   //TaxOverrideType
    public $TaxAmount;         //decimal
    public $TaxDate;           //date
    public $Reason;            //string

    public function __construct($type = null, $amount = null, $date = null, $reason = null)
    {
        $this->TaxOverrideType = $type;
        $this->TaxAmount = $amount;
        $this->TaxDate = $date;
        $this->Reason = $reason;
    }

    public function setTaxOverrideType($value)
    {
        $this->TaxOverrideType = $value;
    }   //TaxOverrideType

    public function setTaxAmount($value)
    {
        $this->TaxAmount = $value;
    }         //decimal

    public function setTaxDate($value)
    {
        $this->TaxDate = $value;
    }           //date

    public function setReason($value)
    {
        $this->Reason = $value;
    }            //string

    public function getTaxOverrideType()
    {
        return $this->TaxOverrideType;
    }   //TaxOverrideType

    public function getTaxAmount()
    {
        return $this->TaxAmount;
    }         //decimal

    public function getTaxDate()
    {
        return $this->TaxDate;
    }           //date

    public function getReason()
    {
        return $this->Reason;
    }            //string

}

?>