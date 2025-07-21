# ChargesInfo

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**accessorial_charge** | [****](.md) | Includes sum of all accessorial charges (i.e. including addition handling, intercept). | [optional] 
**intercept_charge** | [****](.md) | The flat rate charge specific for the intercept. | [optional] 
**transportation_charge** | [****](.md) | Transportation Charge only for AlternateAddress(Redirect,deliver to another address). | [optional] 
**total_charge** | [****](.md) | Total charges | [optional] 
**charge_currency** | [****](.md) | Charge Currency | 
**est_delivery_date** | [****](.md) | Estimate Delivery Date (YYYYMMDD). | [optional] 
**residential_indicator** | [****](.md) | Ship to residential indicator. | [optional] 
**additional_handling_indicator** | [****](.md) | Additional handling indicator. May be set by NRF during rating. | [optional] 
**downgrade_service_code** | [****](.md) | Downgraded service code. | [optional] 
**oversize_code** | [****](.md) | Oversize indicator. May be set by NRF during rating LPK. | [optional] 
**service_code** | [****](.md) | Original service code. | [optional] 
**service_desc** | [****](.md) | Original service description. | [optional] 
**package_weight** | [****](.md) | Package weight. May also be changed by NRF during rating to billable weight. | [optional] 
**package_weight_uom** | [****](.md) | Weight unit of measureLBS - pounds, KGS - Kilograms | [optional] 
**packaging_type** | [****](.md) | Packaging Type code. | [optional] 
**tracking_number** | [****](.md) | Tracking Number | [optional] 
**shipper_name** | [****](.md) | Name of the shipper Applicable for  Intercept options FD,AA,UR etc. | [optional] 
**display_shipper_paid_intercept_charges** | [****](.md) | Indicates if a message should be displayed that the shipper has paid for the charges related to the intercept. | [optional] 
**shipper_paid_intercept_charges** | [****](.md) | Indicates if charges related to the intercept are paid by the shipper. | [optional] 
**shipper_paid_transportation_charges** | [****](.md) | Indicates if charges related to transportation are paid by the shipper. Applicable only for Redirect to Another Address. | [optional] 
**display_shipper_paid_transportation_charges** | [****](.md) | Indicates if a message should be displayed that the shipper has paid for the charges related to transportation. Applicable only for Redirect to Another Address and Redirect to UPS Location intercepts. TRUE - display a message that the shipper has paid the transportation charges. | [optional] 
**charges_paid_by_third_party_shipper** | [****](.md) | Indicates if the charges were paid by a third-party shipper and must be supplied by trusted end clients only.  | CODE  | DESCRIPTION                                     | | TRUE  | the charges were paid by a third-party shipper. | | FALSE | the charges were paid by the original shipper.  | | [optional] 
**taxes** | [****](.md) | Taxes for EU/Mexico/Canada  country  movement. This array is unbounded. | [optional] 
**total_tax** | [****](.md) | Taxes for EU/Mexico/Canada country movement . sum of tax amounts present in the taxes field. | [optional] 
**pre_tax_total_charge** | [****](.md) | Pre-tax total Charge. Sum of accessorial charges and transportation charges  excluding taxes. | [optional] 
**tax_disclaimer_indicator** | [****](.md) | Indicates if a tax disclaimer message should be displayed.  | VALUE | DESCRIPTION         | | :--:  | :--                 | | NTC   | NO Tax calculation  |  | NTA   | NO Tax applicable   |  | TA    | Taxes applicable    | | TE    | Taxes exempt        | | TU    | Taxes Undermined    | | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

