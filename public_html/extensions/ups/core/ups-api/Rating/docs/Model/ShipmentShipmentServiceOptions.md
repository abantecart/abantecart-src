# ShipmentShipmentServiceOptions

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**saturday_pickup_indicator** | **string** | A flag indicating if the shipment requires a Saturday pickup. True if SaturdayPickupIndicator tag exists; false otherwise. Not available for GFP rating requests.  Empty Tag. | [optional] 
**saturday_delivery_indicator** | **string** | A flag indicating if a shipment must be delivered on a Saturday. True if SaturdayDeliveryIndicator tag exists; false otherwise  Empty Tag. | [optional] 
**sunday_delivery_indicator** | **string** | A flag indicating if a shipment must be delivered on a Sunday. True if SundayDeliveryIndicator tag exists; false otherwise  Empty Tag. | [optional] 
**available_services_option** | **string** | If we need diferent available services in response, this option is used for shop request option. SaturdayDeliveryIndicator/ SundayDeliveryIndicator will be ignored in that case.  Valid Values:1- Weekday+Saturday services2- Weekday+Sunday services3- Weekday+Sat services+Sun services | [optional] 
**access_point_cod** | [**\UPS\Rating\Rating\ShipmentServiceOptionsAccessPointCOD**](ShipmentServiceOptionsAccessPointCOD.md) |  | [optional] 
**deliver_to_addressee_only_indicator** | **string** | Presence/Absence Indicator. Any value inside is ignored.  DeliverToAddresseeOnlyIndicator is shipper specified restriction that requires the addressee to be the one who takes final delivery of the \&quot;Hold For PickUp at UPS Access Point\&quot; package.  Presence of indicator means shipper restriction will apply to the shipment.  Only valid for Shipment Indication type \&quot;01 - Hold For PickUp at UPS Access Point\&quot;. | [optional] 
**direct_delivery_only_indicator** | **string** | Presence/Absence Indicator. Any value inside is ignored. Direct Delivery Only (DDO) accessorial in a request would ensure that delivery is made only to the Ship To address on the shipping label.  This accessorial is not valid with Shipment Indication Types: - 01 - Hold For Pickup At UPS Access Point - 02 - UPS Access Point™ Delivery | [optional] 
**cod** | [**\UPS\Rating\Rating\ShipmentServiceOptionsCOD**](ShipmentServiceOptionsCOD.md) |  | [optional] 
**delivery_confirmation** | [**\UPS\Rating\Rating\ShipmentServiceOptionsDeliveryConfirmation**](ShipmentServiceOptionsDeliveryConfirmation.md) |  | [optional] 
**return_of_document_indicator** | **string** | Return of Documents Indicator - If the flag is present, the shipper has requested the ReturnOfDocument accessorial be added to the shipment  Valid for Poland to Poland shipment. | [optional] 
**up_scarbonneutral_indicator** | **string** | UPS carbon neutral indicator. Indicates the shipment will be rated as carbon neutral. | [optional] 
**certificate_of_origin_indicator** | **string** | The empty tag in request indicates that customer would be using UPS prepared SED form.  Valid for UPS World Wide Express Freight shipments. | [optional] 
**pickup_options** | [**\UPS\Rating\Rating\ShipmentServiceOptionsPickupOptions**](ShipmentServiceOptionsPickupOptions.md) |  | [optional] 
**delivery_options** | [**\UPS\Rating\Rating\ShipmentServiceOptionsDeliveryOptions**](ShipmentServiceOptionsDeliveryOptions.md) |  | [optional] 
**restricted_articles** | [**\UPS\Rating\Rating\ShipmentServiceOptionsRestrictedArticles**](ShipmentServiceOptionsRestrictedArticles.md) |  | [optional] 
**shipper_export_declaration_indicator** | **string** | The empty tag in request indicates that customer would be using UPS prepared SED form.  Valid for UPS World Wide Express Freight shipments. | [optional] 
**commercial_invoice_removal_indicator** | **string** | Presence/Absence Indicator. Any value inside is ignored. CommercialInvoiceRemovalIndicator - empty tag means indicator is present. CommercialInvoiceRemovalIndicator allows a shipper to dictate that UPS remove the Commercial Invoice from the user&#x27;s shipment before the shipment is delivered to the ultimate consignee. | [optional] 
**import_control** | [**\UPS\Rating\Rating\ShipmentServiceOptionsImportControl**](ShipmentServiceOptionsImportControl.md) |  | [optional] 
**return_service** | [**\UPS\Rating\Rating\ShipmentServiceOptionsReturnService**](ShipmentServiceOptionsReturnService.md) |  | [optional] 
**sdl_shipment_indicator** | **string** | Empty Tag means the indicator is present. This field is a flag to indicate if the receiver needs SDL rates in response. True if SDLShipmentIndicator tag exists; false otherwise.  If present, the State Department License (SDL) rates will be returned in the response.This service requires that the account number is enabled for SDL. | [optional] 
**epra_indicator** | **string** | For valid values, refer to Rating and Shipping COD Supported Countries or Territories in the Appendix.Presence/Absence Indicator. Any value inside is ignored. This field is a flag to indicate Package Release Code is requested for shipment.  This accessorial is only valid with ShipmentIndicationType &#x27;01&#x27; - Hold for Pickup at UPS Access Point™. | [optional] 
**inside_delivery** | **string** | Inside Delivery accessory. Valid values: - 01 - White Glove - 02 - Room of Choice - 03 - Installation  Shippers account needs to have a valid contract for Heavy Goods Service. | [optional] 
**item_disposal_indicator** | **string** | Presence/Absence Indicator. Any value inside is ignored. If present, indicates that the customer would like items disposed.   Shippers account needs to have a valid contract for Heavy Goods Service. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

