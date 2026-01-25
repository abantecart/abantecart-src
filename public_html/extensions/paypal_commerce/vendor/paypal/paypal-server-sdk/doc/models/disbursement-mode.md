
# Disbursement Mode

The funds that are held on behalf of the merchant.

## Enumeration

`DisbursementMode`

## Fields

| Name | Description |
|  --- | --- |
| `INSTANT` | The funds are released to the merchant immediately. |
| `DELAYED` | The funds are held for a finite number of days. The actual duration depends on the region and type of integration. You can release the funds through a referenced payout. Otherwise, the funds disbursed automatically after the specified duration. |

