<?php
/**
 * Line.class.php
 */

/**
 * A single line within a document containing data used for calculating tax.
 *
 * @see       GetTaxRequest
 *
 * @author    Avalara
 * @copyright � 2004 - 2011 Avalara, Inc.  All rights reserved.
 * @package   Tax
 */

namespace AvaTax;

class Line
{
    public $LineNo;                  //string  // line Number of invoice
    public $OriginCode;          //string  Line#getOriginAddress.
    public $DestinationCode;     //string  Line#getDestinationAddress.
    public $ItemCode;            //string
    public $Description;         //string
    public $TaxCode;             //string
    public $Qty;                 //decimal
    public $Amount;              //decimal // TotalAmmount
    public $Discounted;          //boolean  is discount applied to this item
    public $Ref1;                //string
    public $Ref2;                //string
    public $TaxIncluded;        //boolean
    public $TaxOverride;        //TaxOverride

    public function __construct($no = 1, $qty = 1, $amount = 100.00)
    {
        $this->LineNo = $no;
        $this->Qty = $qty;
        $this->Amount = $amount;
        $this->Discounted = false;
    }

    public function getLineNo()
    {
        return $this->LineNo;
    }

    public function getOriginCode()
    {
        return $this->OriginCode;
    }

    public function getDestinationCode()
    {
        return $this->DestinationCode;
    }

    public function getItemCode()
    {
        return $this->ItemCode;
    }

    public function getDescription()
    {
        return $this->Description;
    }

    public function getTaxCode()
    {
        return $this->TaxCode;
    }

    public function getQty()
    {
        return $this->Qty;
    }

    public function getAmount()
    {
        return $this->Amount;
    }

    public function getDiscounted()
    {
        return $this->Discounted;
    }

    public function getRef1()
    {
        return $this->Ref1;
    }

    public function getRef2()
    {
        return $this->Ref2;
    }

    public function getTaxIncluded()
    {
        return $this->TaxIncluded;
    }

    public function getTaxOverride()
    {
        return $this->TaxOverride;
    }

    public function setLineNo($value)
    {
        $this->LineNo = $value;
    }

    public function setOriginCode($value)
    {
        $this->OriginCode = $value;
    }

    public function setDestinationCode($value)
    {
        $this->DestinationCode = $value;
    }

    public function setItemCode($value)
    {
        $this->ItemCode = $value;
    }

    public function setDescription($value)
    {
        $this->Description = $value;
    }

    public function setTaxCode($value)
    {
        $this->TaxCode = $value;
    }

    public function setQty($value)
    {
        $this->Qty = $value;
    }

    public function setAmount($value)
    {
        $this->Amount = $value;
    }

    public function setDiscounted($value)
    {
        $this->Discounted = $value;
    }

    public function setRef1($value)
    {
        $this->Ref1 = $value;
    }

    public function setRef2($value)
    {
        $this->Ref2 = $value;
    }

    public function setTaxIncluded($value)
    {
        $this->TaxIncluded = $value;
    }

    public function setTaxOverride($value)
    {
        $this->TaxOverride = $value;
    }        //TaxOverride

    //Helper functions for a transition from SOAP
    public function getNo()
    {
        return $this->LineNo;
    }

    public function setNo($value)
    {
        $this->LineNo = $value;
    }

}

?>