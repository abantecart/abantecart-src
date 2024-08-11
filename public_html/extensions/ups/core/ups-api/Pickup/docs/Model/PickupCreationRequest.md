# PickupCreationRequest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**request** | [**\UPS\Pickup\Pickup\PickupCreationRequestRequest**](PickupCreationRequestRequest.md) |  | 
**rate_pickup_indicator** | **string** | Indicates whether to rate the on-callpickup or not.  Valid values: Y &#x3D; Rate this pickup N &#x3D; Do not rate this pickup (default) | 
**rate_chart_type** | **string** | Rate Type with which pickup is rated. Possible RateChart values for different regions will be:  US 48 origin: - 1 – Daily Rates - 3 – Standard List Rates - 4 – Retail Rates.   Alaska/Hawaii origin: - 1 – Daily Rates - 3 – Standard List Rates - 4 – Retail Rates.  All Other origins: - 1 – Rates - 5 - Regional Rates - 6 - General List Rates.  3 and 4 do not apply | [optional] 
**tax_information_indicator** | **string** | Indicates whether to return detailed taxes for the on-callpickups.  Valid values: Y &#x3D; Rate this pickup with taxes N &#x3D; Do not rate this pickup with taxes (default) | [optional] 
**user_level_discount_indicator** | **string** | Indicates whether to return user level promo discount for the on-callpickups.  Valid values: Y &#x3D; Rate this pickup with user level promo discount N &#x3D; Do not rate this pickup with user level promo discount(default) | [optional] 
**shipper** | [**\UPS\Pickup\Pickup\PickupCreationRequestShipper**](PickupCreationRequestShipper.md) |  | [optional] 
**pickup_date_info** | [**\UPS\Pickup\Pickup\PickupCreationRequestPickupDateInfo**](PickupCreationRequestPickupDateInfo.md) |  | 
**pickup_address** | [**\UPS\Pickup\Pickup\PickupCreationRequestPickupAddress**](PickupCreationRequestPickupAddress.md) |  | 
**alternate_address_indicator** | **string** | Indicates if pickup address is a different address than that specified in a customer&#x27;s profile.  Valid values: Y &#x3D; Alternate address N &#x3D; Original pickup address (default) | 
**pickup_piece** | [**\UPS\Pickup\Pickup\PickupCreationRequestPickupPiece[]**](PickupCreationRequestPickupPiece.md) |  | 
**total_weight** | [**\UPS\Pickup\Pickup\PickupCreationRequestTotalWeight**](PickupCreationRequestTotalWeight.md) |  | [optional] 
**overweight_indicator** | **string** | Indicates if at least any package is over 70 lbs or 32 kgs.  Valid values:  Y &#x3D; Over weight  N &#x3D; Not over weight (default)  Not required for WWEF service. | [optional] 
**tracking_data** | [**\UPS\Pickup\Pickup\PickupCreationRequestTrackingData[]**](PickupCreationRequestTrackingData.md) |  | [optional] 
**tracking_data_with_reference_number** | [**\UPS\Pickup\Pickup\PickupCreationRequestTrackingDataWithReferenceNumber**](PickupCreationRequestTrackingDataWithReferenceNumber.md) |  | [optional] 
**payment_method** | **string** | The payment method to pay for this on call pickup. 00 &#x3D; No payment needed 01 &#x3D; Pay by shipper account 03 &#x3D; Pay by charge card 04 &#x3D; Pay by 1Z tracking number 05 &#x3D; Pay by check or money order 06 &#x3D; Cash(applicable only for these countries - BE,FR,DE,IT,MX,NL,PL,ES,GB,CZ,HU,FI,NO) 07&#x3D;Pay by PayPal Refer to Appendix # for valid payment methods for CZ, HU, FI and NO   For countries and (or) zip codes where pickup is free of charge, please submit 00, means no payment needed as payment method.  - If 01 is the payment method, then ShipperAccountNumber and ShipperAccount CountryCode must be provided. - If 03 is selected, then CreditCard information should be provided. - If 04 is selected, then the shipper agreed to pay for the pickup packages. - If 05 is selected, then the shipper will pay for the pickup packages with a check or money order. | 
**special_instruction** | **string** | Special handling instruction from the customer | [optional] 
**reference_number** | **string** | Information entered by a customer for Privileged reference | [optional] 
**freight_options** | [**\UPS\Pickup\Pickup\PickupCreationRequestFreightOptions**](PickupCreationRequestFreightOptions.md) |  | [optional] 
**service_category** | **string** | Service Category. Applicable to the following countries: BE, FR, DE, IT, MX, NL, PL, ES, GB  Valid values:  01 - domestic (default) 02 - international 03 - transborder | [optional] 
**cash_type** | **string** | Describes the type of cash funds that the driver will collect. Applicable to the following countries: BE,FR,DE,IT,MX,NL,PL,ES,GB Valid values:  01 - Pickup only (default) 02 - Transportation only 03 - Pickup and Transportation | [optional] 
**shipping_labels_available** | **string** | This element should be set to \&quot;Y\&quot; in the request to indicate that user has pre-printed shipping labels for all the packages, otherwise this will be treated as false. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

