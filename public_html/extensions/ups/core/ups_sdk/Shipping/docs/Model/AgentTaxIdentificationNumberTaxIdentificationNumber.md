# AgentTaxIdentificationNumberTaxIdentificationNumber

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**identification_number** | **string** | The code or number that a shipper or consignee has registered with a particular country’s authority for doing business, or for identification purposes. | 
**id_number_customer_role** | **string** | A business or individual identification type description (Future Use).specifies the relationship of the customer/ID Number to the shipment  05 &#x3D;importer Address, 06&#x3D;Exporter Address  , 18&#x3D;DeliverTo/Consignee/Reciever Address,  37&#x3D; Shipper Address. | [optional] 
**id_number_encryption_indicator** | **string** | to determine if decryption is required. 0 &#x3D; Identification number is not  Encrypted 1 &#x3D; Identification number is  Encrypted | 
**id_number_issuing_cntry_cd** | **string** | The ISO-defined country code of the country where the Identification Number was issued, when applicable (as per business requirements). Needed for certain types of Identification Numbers (e.g., Passport Number). Sample Values: &#x27;ID&#x27; &#x3D; Indonesia, &#x27;VN&#x27; &#x3D; Vietnam, &#x27;DE&#x27; &#x3D; Germany | [optional] 
**id_number_purpose_code** | **string** | Code that specifies the purpose of the Identification Number. For all tax ID that are not EORI &#x3D; ‘01’ Valid values: 00/ Spaces &#x3D; Unknown 01&#x3D; Customs/Brokerage (Default) 02&#x3D; Customs/Brokerage EORI 99&#x3D; Other | 
**id_number_requesting_cntry_cd** | **string** | The ISO-defined country code of the country whose regulatory agency is requesting the Identification Number. Typically for Import, the Consignee ID is requested by the Ship To country For export, the Shipper ID is requested by the Ship From country.  Required when a country (e.g., Origin country, Destination country) is requesting an ID Number for a shipment. | [optional] 
**id_number_type_code** | **string** | Valid Values are: 0000 &#x3D; Unknown IDNumberTypeCode equal to ‘0000’ (unknown) is to be used when an ‘ID Number Type’ is not applicable, or when the front-end/client system cannot determine the type of IdentificationNumber (for any reason). 0001 &#x3D; Exporter Tax ID Number 0002 &#x3D; Importer Tax ID Number or EORI Number – When IdentificationNumberPurposeCode &#x3D; 02 0005 &#x3D; Personal Tax ID Number 1001 &#x3D; Other / Free Form 1002 &#x3D; Company/Business Tax ID Number 1003 &#x3D; National ID Number 1004 &#x3D; Passport Number 1005 &#x3D; Personal ID Number 1006 &#x3D; Phone Number | 
**id_number_sub_type_code** | **string** | Combination of IDnumberCode and IDNumberSubTypeCode were used to form the correct regex for processing | 
**include_id_number_on_shipping_brokerage_docs** | **string** | field to determine if the Identification Number should be excluded from Shipping/Brokerage documents (not be passed to Document Services)  ‘00’ -&gt; Do Not include 01-&gt; Include. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

