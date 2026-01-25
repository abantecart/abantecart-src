
# Three D Secure Card Authentication Response

Results of 3D Secure Authentication.

## Structure

`ThreeDSecureCardAuthenticationResponse`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `authenticationStatus` | [`?string(PaResStatus)`](../../doc/models/pa-res-status.md) | Optional | Transactions status result identifier. The outcome of the issuer's authentication.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[0-9A-Z_]+$` | getAuthenticationStatus(): ?string | setAuthenticationStatus(?string authenticationStatus): void |
| `enrollmentStatus` | [`?string(EnrollmentStatus)`](../../doc/models/enrollment-status.md) | Optional | Status of Authentication eligibility.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[0-9A-Z_]+$` | getEnrollmentStatus(): ?string | setEnrollmentStatus(?string enrollmentStatus): void |
| `authenticationId` | `?string` | Optional | The externally received 3ds authentication id, to be returned in card detokenization response.<br><br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[0-9a-zA-Z_-]+$` | getAuthenticationId(): ?string | setAuthenticationId(?string authenticationId): void |

## Example (as JSON)

```json
{
  "authentication_status": "D",
  "enrollment_status": "U",
  "authentication_id": "authentication_id2"
}
```

