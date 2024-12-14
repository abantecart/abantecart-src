CHANGELOG
=========

7.1
---

* Add support for `X-SES-LIST-MANAGEMENT-OPTIONS`

6.1
---

 * Add support for `X-SES-MESSAGE-TAGS`
 * Add support for custom ses+smtp hosts

6.0
---

 * Remove the `SesApiTransport` class, use `SesApiAsyncAwsTransport` instead
 * Remove the `SesHttpTransport` class, use `SesHttpAsyncAwsTransport` instead

5.3
---

 * Add support for `X-SES-SOURCE-ARN`

5.1.0
-----

 * Added `async-aws/ses` to communicate with AWS API.

4.4.0
-----

 * [BC BREAK] Renamed and moved `Symfony\Component\Mailer\Bridge\Amazon\Http\Api\SesTransport`
   to `Symfony\Component\Mailer\Bridge\Amazon\Transpor\SesApiTransport`, `Symfony\Component\Mailer\Bridge\Amazon\Http\SesTransport`
   to `Symfony\Component\Mailer\Bridge\Amazon\Transport\SesHttpTransport`, `Symfony\Component\Mailer\Bridge\Amazon\Smtp\SesTransport`
   to `Symfony\Component\Mailer\Bridge\Amazon\Transport\SesSmtpTransport`.

4.3.0
-----

 * Added the bridge
