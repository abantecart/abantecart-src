
# Auction Information

The auction information.

## Structure

`AuctionInformation`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `auctionSite` | `?string` | Optional | The name of the auction site.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `200`, *Pattern*: `^[a-zA-Z0-9_'\-., ":;\!?]*$` | getAuctionSite(): ?string | setAuctionSite(?string auctionSite): void |
| `auctionItemSite` | `?string` | Optional | The auction site URL.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `4000`, *Pattern*: `^[a-zA-Z0-9_'\-., ":;\!?]*$` | getAuctionItemSite(): ?string | setAuctionItemSite(?string auctionItemSite): void |
| `auctionBuyerId` | `?string` | Optional | The ID of the buyer who makes the purchase in the auction. This ID might be different from the payer ID provided for the payment.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `500`, *Pattern*: `^[a-zA-Z0-9_'\-., ":;\!?]*$` | getAuctionBuyerId(): ?string | setAuctionBuyerId(?string auctionBuyerId): void |
| `auctionClosingDate` | `?string` | Optional | The date and time, in [Internet date and time format](https://tools.ietf.org/html/rfc3339#section-5.6). Seconds are required while fractional seconds are optional. Note: The regular expression provides guidance but does not reject all invalid dates.<br><br>**Constraints**: *Minimum Length*: `20`, *Maximum Length*: `64`, *Pattern*: `^[0-9]{4}-(0[1-9]\|1[0-2])-(0[1-9]\|[1-2][0-9]\|3[0-1])[T,t]([0-1][0-9]\|2[0-3]):[0-5][0-9]:([0-5][0-9]\|60)([.][0-9]+)?([Zz]\|[+-][0-9]{2}:[0-9]{2})$` | getAuctionClosingDate(): ?string | setAuctionClosingDate(?string auctionClosingDate): void |

## Example (as JSON)

```json
{
  "auction_site": "auction_site6",
  "auction_item_site": "auction_item_site8",
  "auction_buyer_id": "auction_buyer_id0",
  "auction_closing_date": "auction_closing_date0"
}
```

