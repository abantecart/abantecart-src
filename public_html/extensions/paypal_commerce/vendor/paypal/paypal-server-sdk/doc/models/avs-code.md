
# Avs Code

The address verification code for Visa, Discover, Mastercard, or American Express transactions.

## Enumeration

`AvsCode`

## Fields

| Name | Description |
|  --- | --- |
| `AVS_A` | For Visa, Mastercard, or Discover transactions, the address matches but the zip code does not match. For American Express transactions, the card holder address is correct. |
| `AVS_B` | For Visa, Mastercard, or Discover transactions, the address matches. International A. |
| `AVS_C` | For Visa, Mastercard, or Discover transactions, no values match. International N. |
| `AVS_D` | For Visa, Mastercard, or Discover transactions, the address and postal code match. International X. |
| `AVS_E` | For Visa, Mastercard, or Discover transactions, not allowed for Internet or phone transactions. For American Express card holder, the name is incorrect but the address and postal code match. |
| `AVS_F` | For Visa, Mastercard, or Discover transactions, the address and postal code match. UK-specific X. For American Express card holder, the name is incorrect but the address matches. |
| `AVS_G` | For Visa, Mastercard, or Discover transactions, global is unavailable. Nothing matches. |
| `AVS_I` | For Visa, Mastercard, or Discover transactions, international is unavailable. Not applicable. |
| `AVS_M` | For Visa, Mastercard, or Discover transactions, the address and postal code match. For American Express card holder, the name, address, and postal code match. |
| `AVS_N` | For Visa, Mastercard, or Discover transactions, nothing matches. For American Express card holder, the address and postal code are both incorrect. |
| `AVS_P` | For Visa, Mastercard, or Discover transactions, postal international Z. Postal code only. |
| `AVS_R` | For Visa, Mastercard, or Discover transactions, re-try the request. For American Express, the system is unavailable. |
| `AVS_S` | For Visa, Mastercard, Discover, or American Express, the service is not supported. |
| `AVS_U` | For Visa, Mastercard, or Discover transactions, the service is unavailable. For American Express, information is not available. For Maestro, the address is not checked or the acquirer had no response. The service is not available. |
| `AVS_W` | For Visa, Mastercard, or Discover transactions, whole ZIP code. For American Express, the card holder name, address, and postal code are all incorrect. |
| `AVS_X` | For Visa, Mastercard, or Discover transactions, exact match of the address and the nine-digit ZIP code. For American Express, the card holder name, address, and postal code are all incorrect. |
| `AVS_Y` | For Visa, Mastercard, or Discover transactions, the address and five-digit ZIP code match. For American Express, the card holder address and postal code are both correct. |
| `AVS_Z` | For Visa, Mastercard, or Discover transactions, the five-digit ZIP code matches but no address. For American Express, only the card holder postal code is correct. |
| `AVS_NULL` | For Maestro, no AVS response was obtained. |
| `AVS_0` | For Maestro, all address information matches. |
| `AVS_1` | For Maestro, none of the address information matches. |
| `AVS_2` | For Maestro, part of the address information matches. |
| `AVS_3` | For Maestro, the merchant did not provide AVS information. It was not processed. |
| `AVS_4` | For Maestro, the address was not checked or the acquirer had no response. The service is not available. |

