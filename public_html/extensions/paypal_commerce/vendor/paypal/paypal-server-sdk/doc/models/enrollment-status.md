
# Enrollment Status

Status of Authentication eligibility.

## Enumeration

`EnrollmentStatus`

## Fields

| Name | Description |
|  --- | --- |
| `ENROLLED` | Yes. The bank is participating in 3-D Secure protocol and will return the ACSUrl. |
| `NOTENROLLED` | No. The bank is not participating in 3-D Secure protocol. |
| `UNAVAILABLE` | Unavailable. The DS or ACS is not available for authentication at the time of the request. |
| `BYPASS` | Bypass. The merchant authentication rule is triggered to bypass authentication. |

