<?php
/**
 * CancelTaxRequest.class.php
 */

/**
 * Data to pass to CancelTax indicating
 * the document that should be cancelled and the reason for the operation.
 * <p>
 * A document can be indicated solely by the DocId if it is known.
 * Otherwise the request must specify all of CompanyCode,
 * DocCode, and
 * DocType in order to uniquely identify the document.
 * </p>
 *
 * @see       CancelTaxResult, DocumentType
 *
 * @author    Avalara
 * @copyright � 2004 - 2011 Avalara, Inc.  All rights reserved.
 * @package   Tax
 */

namespace AvaTax;

class CancelTaxRequest
{
    public $CancelCode;   //CancelCode::$Unspecified or CancelCode::$PostFailed or CancelCode::$DocDeleted or CancelCode::$DocVoided
    public $DocCode;    //string
    public $DocType;    //DocumentType::$SalesInvoice or DocumentType::$ReturnInvoice or DocumentType::$PurchaseInvoice
    public $CompanyCode; //string
    public $DocId;    //integer, deprecated.

    public function __construct()
    {
        $this->DocType = DocumentType::$SalesInvoice;
        $this->CancelCode = CancelCode::$Unspecified;
    }

    public function getCancelCode()
    {
        return $this->CancelCode;
    }

    public function getDocCode()
    {
        return $this->DocCode;
    }

    public function getDocType()
    {
        return $this->DocType;
    }

    public function getCompanyCode()
    {
        return $this->CompanyCode;
    }

    public function getDocId()
    {
        return $this->DocId;
    }

    public function setCancelCode($value)
    {
        CancelCode::Validate($value);
        $this->CancelCode = $value;
        return $this;
    }

    public function setDocCode($value)
    {
        $this->DocCode = $value;
    }

    public function setDocType($value)
    {
        $this->DocType = $value;
    }

    public function setCompanyCode($value)
    {
        $this->CompanyCode = $value;
    }

    public function setDocId($value)
    {
        $this->DocId = $value;
    }
}

?>