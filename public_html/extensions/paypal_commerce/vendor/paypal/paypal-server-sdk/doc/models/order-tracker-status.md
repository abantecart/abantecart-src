
# Order Tracker Status

The status of the item shipment.

## Enumeration

`OrderTrackerStatus`

## Fields

| Name | Description |
|  --- | --- |
| `CANCELLED` | The shipment was cancelled and the tracking number no longer applies. |
| `SHIPPED` | The merchant has assigned a tracking number to the items being shipped from the Order. This does not correspond to the carrier's actual status for the shipment. The latest status of the parcel must be retrieved from the carrier. |

