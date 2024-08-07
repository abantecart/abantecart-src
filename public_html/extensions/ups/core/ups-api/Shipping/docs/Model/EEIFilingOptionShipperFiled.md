# EEIFilingOptionShipperFiled

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**code** | **string** | Indicates the EEI Shipper sub option.  Applicable for EEI form and is required. Valid value is: A- requires the ITN; B- requires the Exemption Legend; C- requires the post departure filing citation. | 
**description** | **string** | Description of ShipperFiled Code.  Applicable for EEI form. | [optional] 
**pre_departure_itn_number** | **string** | Input for Shipper Filed option A and AES Direct. The format is available from AESDirect website.  Valid and Required for Shipper Filed option A. EEI form only. | [optional] 
**exemption_legend** | **string** | Input for Shipper Filed option B. 30.2(d)(2), 30.26(a), 30.36, 30.37(a), 30.37(b), 30.37(c), 30.37(d), 30.37(e), 30.37(f), 30.37(h), 30.37(i), 30.30(j), 30.37(k), 30.37(i), 30.37(j), 30.37(k), 30.37(l), 30.37(m), 30.37(n), 30.37(o), 30.37(p), 30.37(q), 30.37(r), 30.37(s), 30.37(t), 30.37(u), 30.37(x), 30.37(y)(1), 30.37(y)(2), 30.37(y)(3), 30.37(y)(4), 30.37(y)(5), 30.37(y)(6), 30.39, 30.40(a), 30.40(b), 30.40(c), 30.40(d), 30.8(b)  Valid and Required for Shipper Filed option B. EEI form only. | [optional] 
**eei_shipment_reference_number** | **string** | Shipment Reference Number for use during interaction with AES. Valid for EEI form for Shipper Filed option &#x27;C&#x27; and AES Direct Filed. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

