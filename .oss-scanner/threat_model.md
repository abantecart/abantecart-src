# Threat model — AbanteCart

## What this project does and where untrusted input enters
AbanteCart is a PHP/MySQL ecommerce platform. A production store exposes:

- **Storefront (fully untrusted input):** product browsing, search, cart, checkout,
  customer registration/login, contact and review forms, coupon codes, downloads.
  Entry point is `public_html/index.php` routing (`?rt=...`) to
  `public_html/storefront/` controllers and `public_html/core/`.
- **Storefront/customer API (untrusted):** `?rt=a/...` JSON endpoints used by mobile
  apps and integrations.
- **Payment gateway callbacks / IPN endpoints (untrusted, unauthenticated):**
  extension callback controllers under `public_html/extensions/*` — these receive
  attacker-controllable POSTs claiming to be from payment providers.
- **Admin panel (authenticated, lower trust in input, high value target):**
  `?s=<admin_path>`; CSRF, privilege escalation, stored XSS from storefront data
  rendered in admin, and auth bypass are all in scope. In the scan image the admin
  path is `admin`, credentials `admin` / `admin`.
- **Installer:** `public_html/install/` — in scope for pre-install exposure issues,
  though production guidance is to remove it after installation.

## Components that matter most / least
- **Most:** core request routing, session/authentication and customer/admin login
  (`public_html/core/lib/`, `public_html/core/engine/`), the database abstraction
  layer (SQL injection), checkout and payment extension code, file upload and media
  handling, output encoding in templates, the storefront API.
- **Less (still in scope):** admin-only display/reporting code, seed/sample data.
- **Third-party bundled libraries** under `public_html/core/` and extension vendor
  dirs: report findings, but upstream bugs are lower priority than AbanteCart's own code.

## How to exercise it
The container is a fully installed store with sample data:
- Web root `http://localhost/` (nginx + php-fpm), MariaDB on `127.0.0.1`
  (user `admin`, password `pass`, database `abantecart`, table prefix `abc_`).
- Admin at `http://localhost/index.php?s=admin` (login `admin` / `admin`).
- PHPUnit suite: `php /usr/local/bin/phpunit -c /var/www/abantecart/tests/phpunit/phpunit_mysql.xml`
  (start MariaDB first: `service mariadb start`).

## How we rate severity
- **Critical:** unauthenticated SQL injection, RCE, authentication bypass,
  unauthenticated arbitrary file read/write, order/payment manipulation
  (e.g., forged IPN marking orders paid).
- **High:** stored XSS (especially storefront → admin), IDOR exposing customer PII
  or orders, CSRF on admin state-changing actions, SQLi requiring a customer account.
- **Medium:** issues requiring authenticated admin access, reflected XSS,
  information disclosure without PII, application-level DoS.
- **Low:** issues requiring unusual configuration, self-XSS, verbose errors.

## Anything to leave alone
- The demo credentials (`admin`/`admin`, DB `admin`/`pass`) and `--with-sample-data`
  exist only in this scan image; do not report them.
- Actions an authenticated admin is entitled to perform by design (admins can edit
  templates/extensions, which implies code execution) are not vulnerabilities on
  their own — unless reachable without admin authentication or via CSRF.
