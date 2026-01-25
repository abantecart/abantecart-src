
# Network Transaction Reference Entity

Previous network transaction reference including id and network.

## Structure

`NetworkTransactionReferenceEntity`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `id` | `string` | Required | Transaction reference id returned by the scheme. For Visa and Amex, this is the "Tran id" field in response. For MasterCard, this is the "BankNet reference id" field in response. For Discover, this is the "NRID" field in response. The pattern we expect for this field from Visa/Amex/CB/Discover is numeric, Mastercard/BNPP is alphanumeric and Paysecure is alphanumeric with special character -.<br><br>**Constraints**: *Minimum Length*: `9`, *Maximum Length*: `36`, *Pattern*: `^[a-zA-Z0-9-_@.:&+=*^'~#!$%()]+$` | getId(): string | setId(string id): void |
| `date` | `?string` | Optional | The date that the transaction was authorized by the scheme. This field may not be returned for all networks. MasterCard refers to this field as "BankNet reference date.<br><br>**Constraints**: *Minimum Length*: `4`, *Maximum Length*: `4`, *Pattern*: `^[0-9]+$` | getDate(): ?string | setDate(?string date): void |
| `network` | [`?string(CardBrand)`](../../doc/models/card-brand.md) | Optional | The card network or brand. Applies to credit, debit, gift, and payment cards.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[A-Z_]+$` | getNetwork(): ?string | setNetwork(?string network): void |
| `time` | `?string` | Optional | The date and time, in [Internet date and time format](https://tools.ietf.org/html/rfc3339#section-5.6). Seconds are required while fractional seconds are optional. Note: The regular expression provides guidance but does not reject all invalid dates.<br><br>**Constraints**: *Minimum Length*: `20`, *Maximum Length*: `64`, *Pattern*: `^[0-9]{4}-(0[1-9]\|1[0-2])-(0[1-9]\|[1-2][0-9]\|3[0-1])[T,t]([0-1][0-9]\|2[0-3]):[0-5][0-9]:([0-5][0-9]\|60)([.][0-9]+)?([Zz]\|[+-][0-9]{2}:[0-9]{2})$` | getTime(): ?string | setTime(?string time): void |

## Example (as JSON)

```json
{
  "id": "id6",
  "date": "date2",
  "network": "CONFIDIS",
  "time": "time6"
}
```

