
# Cvv Code

The card verification value code for for Visa, Discover, Mastercard, or American Express.

## Enumeration

`CvvCode`

## Fields

| Name | Description |
|  --- | --- |
| `CVV_E` | For Visa, Mastercard, Discover, or American Express, error - unrecognized or unknown response. |
| `CVV_I` | For Visa, Mastercard, Discover, or American Express, invalid or null. |
| `CVV_M` | For Visa, Mastercard, Discover, or American Express, the CVV2/CSC matches. |
| `CVV_N` | For Visa, Mastercard, Discover, or American Express, the CVV2/CSC does not match. |
| `CVV_P` | For Visa, Mastercard, Discover, or American Express, it was not processed. |
| `CVV_S` | For Visa, Mastercard, Discover, or American Express, the service is not supported. |
| `CVV_U` | For Visa, Mastercard, Discover, or American Express, unknown - the issuer is not certified. |
| `CVV_X` | For Visa, Mastercard, Discover, or American Express, no response. For Maestro, the service is not available. |
| `ENUM_ALL_OTHERS` | For Visa, Mastercard, Discover, or American Express, error. |
| `CVV_0` | For Maestro, the CVV2 matched. |
| `CVV_1` | For Maestro, the CVV2 did not match. |
| `CVV_2` | For Maestro, the merchant has not implemented CVV2 code handling. |
| `CVV_3` | For Maestro, the merchant has indicated that CVV2 is not present on card. |
| `CVV_4` | For Maestro, the service is not available. |

