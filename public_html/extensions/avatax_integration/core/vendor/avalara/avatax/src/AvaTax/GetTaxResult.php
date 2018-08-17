<?php
/**
 * GetTaxResult.class.php
 */

/**
 * Result data returned from {@link TaxServiceSoap#getTax}.
 *
 * @see       GetTaxRequest
 *
 * @author    Avalara
 * @copyright � 2004 - 2011 Avalara, Inc.  All rights reserved.
 * @package   Tax
 */

namespace AvaTax;

class GetTaxResult implements JsonSerializable// extends BaseResult
{
    private $DocCode;    //string  
    private $DocDate;            //date  		 	
    private $Timestamp;        //dateTime  	
    private $TotalAmount;        //decimal  
    private $TotalDiscount;    //decimal  
    private $TotalExemption;    //decimal  
    private $TotalTaxable;    //decimal  
    private $TotalTax;        //decimal  	
    private $TotalTaxCalculated;        //decimal  	 
    private $TaxDate;        //date 		
    private $TaxLines;    //ArrayOfTaxLine
    private $TaxSummary;        //ArrayOfTaxDetail	
    private $TaxAddresses;        //ArrayOfAddress

    public function __construct(
        $resultCode,
        $messages,
        $docCode,
        $docDate,
        $timestamp,
        $totalAmount,
        $totalDiscount,
        $totalExemption,
        $totalTaxable,
        $totalTax,
        $totalTaxCalculated,
        $taxDate,
        $taxLines,
        $taxSummary,
        $taxAddresses
    ) {
        $this->ResultCode = $resultCode;
        $this->Messages = $messages;
        $this->DocCode = $docCode;
        $this->DocDate = $docDate;
        $this->Timestamp = $timestamp;
        $this->TotalAmount = $totalAmount;
        $this->TotalDiscount = $totalDiscount;
        $this->TotalExemption = $totalExemption;
        $this->TotalTaxable = $totalTaxable;
        $this->TotalTax = $totalTax;
        $this->TotalTaxCalculated = $totalTaxCalculated;
        $this->TaxDate = $taxDate;
        $this->TaxLines = $taxLines;
        $this->TaxSummary = $taxSummary;
        $this->TaxAddresses = $taxAddresses;

    }

    //Helper function to decode result objects from Json responses to specific objects.	
    public static function parseResult($jsonString)
    {
        $object = json_decode($jsonString);
        $taxlines = array();
        $taxsummary = array();
        $taxaddresses = array();
        $messages = array();
        $resultcode = null;
        $doccode = null;
        $docdate = null;
        $timestamp = null;
        $totalamount = null;
        $totaldiscount = null;
        $totalexemption = null;
        $totaltaxable = null;
        $totaltax = null;
        $totaltaxcalculated = null;
        $taxdate = null;

        if (property_exists($object, "ResultCode")) {
            $resultcode = $object->ResultCode;
        }
        if (property_exists($object, "DocCode")) {
            $doccode = $object->DocCode;
        }
        if (property_exists($object, "DocDate")) {
            $docdate = $object->DocDate;
        }
        if (property_exists($object, "Timestamp")) {
            $timestamp = $object->Timestamp;
        }
        if (property_exists($object, "TotalAmount")) {
            $totalamount = $object->TotalAmount;
        }
        if (property_exists($object, "TotalDiscount")) {
            $totaldiscount = $object->TotalDiscount;
        }
        if (property_exists($object, "TotalExemption")) {
            $totalexemption = $object->TotalExemption;
        }
        if (property_exists($object, "TotalTaxable")) {
            $totaltaxable = $object->TotalTaxable;
        }
        if (property_exists($object, "TotalTax")) {
            $totaltax = $object->TotalTax;
        }
        if (property_exists($object, "TotalTaxCalculated")) {
            $totaltaxcalculated = $object->TotalTaxCalculated;
        }
        if (property_exists($object, "TaxDate")) {
            $taxdate = $object->TaxDate;
        }

        if (property_exists($object, "TaxLines")) {
            $taxlines = TaxLine::parseTaxLines("{\"TaxLines\": ".json_encode($object->TaxLines)."}");
        }
        if (property_exists($object, "TaxSummary")) {
            $taxsummary = TaxDetail::parseTaxDetails("{\"TaxSummary\": ".json_encode($object->TaxSummary)."}");
        }
        if (property_exists($object, "TaxAddresses")) {
            $taxaddresses = Address::parseAddress("{\"TaxAddresses\": ".json_encode($object->TaxAddresses)."}");
        }
        if (property_exists($object, "Messages")) {
            $messages = Message::parseMessages("{\"Messages\": ".json_encode($object->Messages)."}");
        }

        return new self($resultcode, $messages, $doccode, $docdate,
            $timestamp, $totalamount, $totaldiscount,
            $totalexemption, $totaltaxable, $totaltax, $totaltaxcalculated,
            $taxdate, $taxlines, $taxsummary, $taxaddresses);
    }

    public function jsonSerialize()
    {
        return array(
            'DocCode'            => $this->getDocCode(),
            'DocDate'            => $this->getDocDate(),
            'Timestamp'          => $this->getTimestamp(),
            'TotalAmount'        => $this->getTotalAmount(),
            'TotalDiscount'      => $this->getTotalDiscount(),
            'TotalExemption'     => $this->getTotalExemption(),
            'TotalTaxable'       => $this->getTotalTaxable(),
            'TotalTax'           => $this->getTotalTax(),
            'TotalTaxCalculated' => $this->getTotalTaxCalculated(),
            'TaxDate'            => $this->getTaxDate(),
            'TaxLines'           => $this->getTaxLines(),
            'TaxSummary'         => $this->getTaxSummary(),
            'TaxAddresses'       => $this->getTaxAddresses(),
            'ResultCode'         => $this->getResultCode(),
            'Messages'           => $this->getMessages(),
        );
    }

    public function getDocCode()
    {
        return $this->DocCode;
    }

    public function getDocDate()
    {
        return $this->DocDate;
    }

    public function getTimestamp()
    {
        return $this->Timestamp;
    }

    public function getTotalAmount()
    {
        return $this->TotalAmount;
    }

    public function getTotalDiscount()
    {
        return $this->TotalDiscount;
    }

    public function getTotalExemption()
    {
        return $this->TotalExemption;
    }

    public function getTotalTaxable()
    {
        return $this->TotalTaxable;
    }

    public function getTotalTax()
    {
        return $this->TotalTax;
    }

    public function getTotalTaxCalculated()
    {
        return $this->TotalTaxCalculated;
    }

    public function getTaxDate()
    {
        return $this->TaxDate;
    }

    public function getTaxLines()
    {
        return $this->TaxLines;
    }

    public function getTaxSummary()
    {
        return $this->TaxSummary;
    }

    public function getTaxAddresses()
    {
        return $this->TaxAddresses;
    }

    public function setDocCode($value)
    {
        $this->DocCode = $value;
    }

    public function setDocDate($value)
    {
        $this->DocDate = $value;
    }

    public function setTimestamp($value)
    {
        $this->Timestamp = $value;
    }

    public function setTotalAmount($value)
    {
        $this->TotalAmount = $value;
    }

    public function setTotalDiscount($value)
    {
        $this->TotalDiscount = $value;
    }

    public function setTotalExemption($value)
    {
        $this->TotalExemption = $value;
    }

    public function setTotalTaxable($value)
    {
        $this->TotalTaxable = $value;
    }

    public function setTotalTax($value)
    {
        $this->TotalTax = $value;
    }

    public function setTotalTaxCalculated($value)
    {
        $this->TotalTaxCalculated = $value;
    }

    public function setTaxDate($value)
    {
        $this->TaxDate = $value;
    }

    public function setTaxLines($value)
    {
        $this->TaxLines = $value;
    }

    public function setTaxSummary($value)
    {
        $this->TaxSummary = $value;
    }

    public function setTaxAddresses($value)
    {
        $this->TaxAddresses = $value;
    }

    //Allows for direct reference to and lookup of lines by line number.
    public function getTaxLine($lineNo)
    {
        if ($this->getTaxLines() != null) {
            foreach ($this->getTaxLines() as $taxLine) {
                if ($lineNo == $taxLine->getLineNo()) {
                    return $taxLine;
                }

            }
        }
    }



    /////////////////////////////////////////////PHP bug requires this copy from BaseResult ///////////
    /**
     * @var string must be one of the values defined in {@link SeverityLevel}.
     */
    private $ResultCode = 'Success';
    /**
     * @var array of Message.
     */
    private $Messages = array();

    /**
     * Accessor
     *
     * @return string
     */
    public function getResultCode()
    {
        return $this->ResultCode;
    }

    /**
     * Accessor
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->Messages;
    }

}

?>