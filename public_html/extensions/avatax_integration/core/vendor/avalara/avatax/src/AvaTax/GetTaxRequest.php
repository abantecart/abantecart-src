<?php
/**
 * GetTaxRequest.class.php
 */

/**
 * Data to pass to {@link TaxServiceSoap#getTax}.
 *
 * @see       GetTaxResult
 *
 * @author    Avalara
 * @copyright � 2004 - 2011 Avalara, Inc.  All rights reserved.
 * @package   Tax
 */

namespace AvaTax;

class GetTaxRequest
{
    public $CompanyCode; // string
    public $DocCode;    //string
    public $DocType;    //DocumentType
    public $DocDate;                //date
    public $CustomerCode;            //string
    public $CustomerUsageType;        //string   Entity Usage
    public $Discount;                //decimal
    public $PurchaseOrderNo;        //string
    public $ExemptionNo;            //string   if not using ECMS which keys on customer code
    public $Addresses;                //array
    public $Lines;                    //array
    public $DetailLevel;            //Summary or Document or Line or Tax or Diagnostic
    public $ReferenceCode;        // string
    public $Commit = false;            //boolean

    public $TaxOverride;        //TaxOverride
    public $PosLaneCode;        //string
    public $Client = "PHP REST Sample, 1.0"; //string, should uniquely identify the software client making the call to the service.
    public $BusinessIdentificationNo; //string, VAT ID for VAT calculations.
    public $CurrencyCode;        //string

    public function __construct()
    {
        date_default_timezone_set('UTC');
        $this->DocDate = date("Y-m-d");
        $this->Commit = false;
        $this->DocType = DocumentType::$SalesOrder;
        $this->DetailLevel = DetailLevel::$Tax;
        $this->DocCode = date("Y-m-d-H-i-s.u");
        $this->CustomerCode = 'CustomerCodeString';
        $this->Lines = array(new Line());
    }

    public function setTaxOverride($value)
    {
        $this->TaxOverride = $value;
    }        //TaxOverride

    public function setAddresses($value)
    {
        $this->Addresses = $value;
    }                //array

    public function setLines($value)
    {
        $this->Lines = $value;
    }                    //array

    public function setCompanyCode($value)
    {
        $this->CompanyCode = $value;
    }

    public function setDocCode($value)
    {
        $this->DocCode = $value;
    }

    public function setDocType($value)
    {
        $this->DocType = $value;
    }

    public function setDocDate($value)
    {
        $this->DocDate = $value;
    }

    public function setCustomerCode($value)
    {
        $this->CustomerCode = $value;
    }

    public function setCustomerUsagetype($value)
    {
        $this->CustomerUsagetype = $value;
    }

    public function setDiscount($value)
    {
        $this->Discount = $value;
    }

    public function setPurchaseOrderNo($value)
    {
        $this->PurchaseOrderNo = $value;
    }

    public function setExemptionNo($value)
    {
        $this->ExemptionNo = $value;
    }

    public function setDetailLevel($value)
    {
        $this->DetailLevel = $value;
    }

    public function setReferenceCode($value)
    {
        $this->ReferenceCode = $value;
    }

    public function setCommit($value)
    {
        $this->Commit = $value;
    }

    public function setPosLaneCode($value)
    {
        $this->PosLaneCode = $value;
    }

    public function setClient($value)
    {
        $this->Client = $value;
    }

    public function setBusinessIdentificationNo($value)
    {
        $this->BusinessIdentificationNo = $value;
    }

    public function setCurrencyCode($value)
    {
        $this->CurrencyCode = $value;
    }

    public function getCompanyCode()
    {
        return $this->CompanyCode;
    }

    public function getDocCode()
    {
        return $this->DocCode;
    }

    public function getDocType()
    {
        return $this->DocType;
    }

    public function getDocDate()
    {
        return $this->DocDate;
    }

    public function getCustomerCode()
    {
        return $this->CustomerCode;
    }

    public function getCustomerUsageType()
    {
        return $this->CustomerUsageType;
    }

    public function getDiscount()
    {
        return $this->Discount;
    }

    public function getPurchaseOrderNo()
    {
        return $this->PurchaseOrderNo;
    }

    public function getExemptionNo()
    {
        return $this->ExemptionNo;
    }

    public function getDetailLevel()
    {
        return $this->DetailLevel;
    }

    public function getReferenceCode()
    {
        return $this->ReferenceCode;
    }

    public function getCommit()
    {
        return $this->Commit;
    }

    public function getPosLaneCode()
    {
        return $this->PosLaneCode;
    }

    public function getClient()
    {
        return $this->Client;
    }

    public function getBusinessIdentificationNo()
    {
        return $this->BusinessIdentificationNo;
    }

    public function getTaxOverride()
    {
        return $this->TaxOverride;
    }

    public function getCurrencyCode()
    {
        return $this->CurrencyCode;
    }

    public function getAddresses()
    {
        return is_array($this->Addresses) ? $this->Addresses : AvaFunctions::EnsureIsArray($this->Addresses->BaseAddress);
    }

    public function getLines()
    {
        return is_array($this->Lines) ? $this->Lines : AvaFunctions::EnsureIsArray($this->Lines->Line);
    }

    //Adding getLine function which returns line based on line number
    public function getLine($lineNo)
    {
        if ($this->Lines != null) {
            foreach ($this->getLines() as $line) {
                if ($lineNo == $line->getNo()) {
                    return $line;
                }

            }
        }
    }

}

?>