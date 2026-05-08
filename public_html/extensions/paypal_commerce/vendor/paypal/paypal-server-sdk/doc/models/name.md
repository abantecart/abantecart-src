
# Name

The name of the party.

## Structure

`Name`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `givenName` | `?string` | Optional | When the party is a person, the party's given, or first, name.<br><br>**Constraints**: *Maximum Length*: `140` | getGivenName(): ?string | setGivenName(?string givenName): void |
| `surname` | `?string` | Optional | When the party is a person, the party's surname or family name. Also known as the last name. Required when the party is a person. Use also to store multiple surnames including the matronymic, or mother's, surname.<br><br>**Constraints**: *Maximum Length*: `140` | getSurname(): ?string | setSurname(?string surname): void |

## Example (as JSON)

```json
{
  "given_name": "given_name2",
  "surname": "surname8"
}
```

