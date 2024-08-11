# SubscriptionFileManifest

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**shipper** | [**\UPS\QuantumView\QuantumView\ManifestShipper**](ManifestShipper.md) |  | 
**ship_to** | [**\UPS\QuantumView\QuantumView\ManifestShipTo**](ManifestShipTo.md) |  | 
**reference_number** | [**\UPS\QuantumView\QuantumView\ManifestReferenceNumber[]**](ManifestReferenceNumber.md) | Shipment-level reference numbers.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**service** | [**\UPS\QuantumView\QuantumView\ManifestService**](ManifestService.md) |  | [optional] 
**pickup_date** | **string** | Should be set equal to the date on while the packages were picked up (may be prior days date if the transmission occurs after midnight). Formatted as YYYYMMDD. | [optional] 
**scheduled_delivery_date** | **string** | The date the shipment originally was scheduled for delivery. Formatted as YYYYMMDD. | [optional] 
**scheduled_delivery_time** | **string** | Schedule delivery time. Time format is HHMMSS | [optional] 
**documents_only** | **string** | If the tag is present then the shipment is a document, otherwise the shipment is a non-document. Valid values: - 1 &#x3D; Letter - 2 &#x3D; Document (Non-Letter Document) - 3 &#x3D; Non-Document - 4 &#x3D; Pallet | [optional] 
**package** | [**\UPS\QuantumView\QuantumView\ManifestPackage[]**](ManifestPackage.md) | Defines a package.  **NOTE:** For versions &gt;&#x3D; v2, this element will always be returned as an array. For requests using version &#x3D; v1, this element will be returned as an array if there is more than one object and a single object if there is only 1. | [optional] 
**shipment_service_options** | [**\UPS\QuantumView\QuantumView\ManifestShipmentServiceOptions**](ManifestShipmentServiceOptions.md) |  | [optional] 
**manufacture_country** | **string** | Country or Territory  of Manufacture of the contents of the package. | [optional] 
**harmonized_code** | **string** | Harmonized code of the package. | [optional] 
**customs_value** | [**\UPS\QuantumView\QuantumView\ManifestCustomsValue**](ManifestCustomsValue.md) |  | [optional] 
**special_instructions** | **string** | User-defined special instructions for delivery. | [optional] 
**shipment_charge_type** | **string** | Shipment charge type. Valid values: C/F - Cost and Freight C/B - Consignee Billed Package F/C - Freight Collect DDP - Delivered Duty Paid VAT Unpaid FOB - Free On Board P/P - Prepaid F/D - Free Domicile T/P - Third Party Billing | [optional] 
**bill_to_account** | [**\UPS\QuantumView\QuantumView\ManifestBillToAccount**](ManifestBillToAccount.md) |  | [optional] 
**consignee_bill_indicator** | **string** | Indicates if consignee will be billed the shipment. | 
**collect_bill_indicator** | **string** | Indicates whether or not to collect bill at time of delivery. | 
**location_assured** | **string** | Indicates Location Assured Values: Y - Location Assured accessorial requested | [optional] 
**import_control** | **string** | Import Control Indication is used to designate that the shipment is an Import Control shipment. If the shipment is an import control shipment then this element will have value. For no import shipment this will not be appear | [optional] 
**label_delivery_method** | **string** | Indicates Label Delivery Method, Values are: LDE Electronic Label. LDO One Attempt. LDP Print Label. LDT Three Attempt. LPM Print and Mail Label. | [optional] 
**commercial_invoice_removal** | **string** | Commercial Invoice Removal (CIR) is an accessorial or indication that will allow a shipper to dictate that UPS remove the Commercial Invoice from the user&#x27;s shipment before the shipment is delivered to the ultimate consignee. If shipment is CIR then this element will have value. For no CIR this element will not be appear | [optional] 
**postal_service_tracking_id** | **string** | Postal Service Tracking ID transport company tracking number. | [optional] 
**returns_flexible_access** | **string** | (RFA) UPS returns flexible access. This element will appear with value only when returns flexible access uploaded. For no returns flexible access this element will not be appear | [optional] 
**up_scarbonneutral** | **string** | UPS carbon neutral is a term used to reflect a generic term for the tagging to be included on any document, label, e-mail, etc. used to identify that the UPS carbon neutral fee is applied. This element will appear only when shipment is UPS carbon neutral with value. For non UPS carbon neutral shipping this element appear. | [optional] 
**product** | **string** | This element will have value \&quot;PAC\&quot; for CAR shipments. For no CAR shipments this element will not be appeared. | [optional] 
**ups_returns_exchange** | **string** | UPS Return and Exchange – This element will appear with value Y only when UPS Return and Exchange was requested. For no UPS Returns and Exchange then this element will not appear | [optional] 
**lift_gate_on_delivery** | **string** | Lift Gate On Delivery - This element will appear only when Lift Gate For Delivery was requested for UPS World Wide Express Freight Shipments. If no Lift Gate for Delivery was requested, this element will not appear. | [optional] 
**lift_gate_on_pick_up** | **string** | Lift Gate On PickUp - This element will appear only when Lift Gate For PickUp was requested for UPS World Wide Express Freight Shipments. If no Lift Gate for PickUp was requested, this element will not appear. | [optional] 
**pickup_preference** | **string** | Pickup Preference -This element will appear only when Dropoff At UPS Facility was requested for UPS World Wide Express Freight Shipments. If no Dropoff At UPS Facility was requested, this element will not appear. | [optional] 
**delivery_preference** | **string** | Delivery Preference - This element will appear only when Hold for pick up was requested for UPS World Wide Express Freight Shipments. If no Hold for pick up was requested, this element will not appear. | [optional] 
**hold_for_pickup_at_ups_access_point** | **string** | \&quot;Y\&quot; Indicates Shipment is Direct to Retail. | [optional] 
**uap_address** | [**\UPS\QuantumView\QuantumView\ManifestUAPAddress**](ManifestUAPAddress.md) |  | [optional] 
**deliver_to_addressee_only_indicator** | **string** | \&quot;Y\&quot; Indicates Shipment is Deliver to Addressee. | [optional] 
**ups_access_point_cod_indicator** | **string** | \&quot;Y\&quot; Indicates Shipment is Cash on Delivery in Direct to Retail | [optional] 
**clinical_trial_indicator** | **string** | An accessorial Indicator flag: Y &#x3D; Clinical Trial accessorial provided in Manifest. Spaces &#x3D; Clinical Trial accessorial not provided in Manifest. | [optional] 
**clinical_trial_indication_number** | **string** | An unique Clinical Trial associated with the shipment provided in Manifest. | [optional] 
**category_a_hazardous_indicator** | **string** | An accessorial Indicator flag: Y &#x3D; Category A Hazardous materials accessorial provided in Manifest. Spaces &#x3D; Category A Hazardous materials accessorial not provided in Manifest. | [optional] 
**direct_delivery_indicator** | **string** | An accessorial Indicator flag: Y &#x3D; Direct Delivery accessorisal provided in Manifest. Spaces &#x3D; Direct Delivery accessorial not provided in Manifest. | [optional] 
**package_release_code_indicator** | **string** | \&quot;Y\&quot; indicates Shipment has PackageReleaseCode Accessorial. | [optional] 
**proactive_response_indicator** | **string** | \&quot;Y\&quot; indicates that a UPS Proactive Response Accessorial is provided. | [optional] 
**white_glove_delivery_indicator** | **string** | \&quot;Y\&quot; indicates that a Heavy Goods White Glove Delivery Accessorial is provided. | [optional] 
**room_of_choice_indicator** | **string** | \&quot;Y\&quot; indicates that a Heavy Goods Room of Choice Accessorial is provided. | [optional] 
**installation_delivery_indicator** | **string** | \&quot;Y\&quot; indicates that a Heavy Goods Installation Delivery Accessorial is provided. | [optional] 
**item_disposal_indicator** | **string** | \&quot;Y\&quot; indicates that a Heavy Goods Item Disposal Accessorial is provided. | [optional] 
**lead_shipment_tracking_number** | **string** | Lead Tracking Number in shipment | [optional] 
**saturday_non_premium_commercial_delivery_indicator** | **string** | \&quot;Y\&quot;  indicates that a SaturdayNonPremiumCommercialDeliveryIndicator is provided. | [optional] 
**sunday_non_premium_commercial_delivery_indicator** | **string** | \&quot;Y\&quot;  indicates that a SundayNonPremiumCommercialDeliveryIndicator is provided. | [optional] 
**ups_premier_accessorial_indicator** | **string** | \&quot;Y\&quot; indicates that the UPS Premier accessorial is provided. | [optional] 
**ups_premier_category_code** | **string** | Indicates the UPS Premier category applied to the package Valid values: - &#x27;PRS&#x27; – UPS Premier Silver - &#x27;PRG&#x27; – UPS Premier Gold - &#x27;PRP&#x27; - UPS Premier Platinum | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

