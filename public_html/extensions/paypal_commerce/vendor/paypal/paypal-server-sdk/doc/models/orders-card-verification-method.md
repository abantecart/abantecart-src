
# Orders Card Verification Method

The method used for card verification.

## Enumeration

`OrdersCardVerificationMethod`

## Fields

| Name | Description |
|  --- | --- |
| `SCA_ALWAYS` | Selecting this option will attempt to force a strong customer authentication for the authorization/transaction. In countries where SCA has been defined and implemented it will result in a contingency and HATEOAS link being returned.  The API caller should redirect the payer to that link so that they can authenticate themselves against their issuing bank or other entity. As noted, the HATEOAS link is only available in all regions where strong authentication is supported, (e.g. in European countries where 3DS is live). Merchants can use this setting as an additional layer of security if they choose to. In all cases, when an authorization is requested the AVS/CVV results will be returned in the response. |
| `SCA_WHEN_REQUIRED` | This is the default. When an authorization or transaction is attempted this option will return a contingency and HATEOAS link only when local regulations require strong customer authentication, (e.g. 3DS in countries and use cases where it is mandated). The API caller should redirect the payer to the link so that they can authenticate themselves. In all cases, when an authorization is requested the AVS/CVV results will be returned in the response. |
| `ENUM_3D_SECURE` | The contingency surfaced as an additional security layer that helps prevent unauthorized card-not-present transactions and protects the merchant from exposure to fraud. |
| `AVS_CVV` | Places a temporary hold on the card to ensure its validity. This process protects the merchant from exposure to fraud. This verification method will confirm that the address information or CVV included matches what the issuing bank has on file for the associated card, ensuring that only authorized card users are able to make purchases from you. |

