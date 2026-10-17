# Security Policy

## Supported Versions

The following versions of AbanteCart are currently being evaluated for security support.

| Version | Supported |
| ------- | --------- |
| 1.4.4   | :white_check_mark: |
| < 1.4.4 | :x: |

## Reporting a Vulnerability

If you discover a security vulnerability in AbanteCart, please report it responsibly to the project maintainers.

When reporting a vulnerability, please include:

- A clear description of the vulnerability.
- The affected version and relevant endpoints.
- Step-by-step reproduction instructions.
- The potential security impact.
- Proof-of-concept details, screenshots, or logs where applicable.

### Reported Vulnerability

**Title:** Potential Stored Cross-Site Scripting (XSS) in Product Description

**Affected Version:** AbanteCart 1.4.4

**Vulnerability Type:** Stored Cross-Site Scripting (CWE-79)

**Affected Functionality:** Product description management and product detail rendering.

**Description:**

A potential stored XSS vulnerability was identified in AbanteCart 1.4.4. JavaScript can reportedly be executed in a visitor's browser through content entered in the Product Description fields in the administration panel.

The affected parameters are `product_description[blurb]` and `product_description[description]`. The supplied content is stored and subsequently rendered on the storefront. If confirmed, this behavior may allow JavaScript execution in the context of the affected store's origin.

**Affected Endpoints:**

- **Administration endpoint:** `index.php?rt=catalog/product/update`
- **Storefront endpoint:** `index.php?rt=product/product&product_id=69`

![alt text](https://i.8upload.com/image/22cae5b74db54f1f/screenshot-2026-10-17-at-14-07-13.png)

**Affected Parameters:**

- `product_description[blurb]`
- `product_description[description]`

**Test Payload:**

The following payload was used in a controlled test environment to verify JavaScript execution:

 `';alert(String.fromCharCode(88,83,83))//\';alert(String.fromCharCode(88,83,83))//";alert(String.fromCharCode(88,83,83))//\";alert(String.fromCharCode<script>alert('XSS-Vulnerabilities')</script>`

**Reproduction Overview:**

1. Log in to the AbanteCart administration panel with the required privileges.
2. Navigate to the product editing page.
3. Submit a controlled XSS proof of concept through either `product_description[blurb]` or `product_description[description]`.
4. Save the product.
5. Open the corresponding storefront page and observe whether JavaScript executes.

**Security Impact:**

Successful exploitation may result in JavaScript execution in the context of the affected storefront. The practical impact depends on the application's security controls, the privileges required to modify product descriptions, and the affected users.
