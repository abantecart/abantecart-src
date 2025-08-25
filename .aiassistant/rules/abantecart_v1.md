---
apply: always
---

# Language & Tone
- Keep answers concise and practical: show code first, then a short explanation.

# Project Conventions
- Follow PSR-12 coding style (namespaces, braces, imports, indentation).
- Always type-hint parameters, return types, and class properties.
- Use short array syntax `[]`.
- Prefer modern PHP features: null-coalescing, nullsafe operator, constructor property promotion.
- Use single quotes for strings unless interpolation is required.
- Use double quotes for sql-queries.
- Keep controllers skinny; put business logic in models/services.
- Do not echo in controllers; assign data to views and return.
- Save all text's key and value into language "xml" files inside tag <definition><key></key><value></value></definition>. Path of xml file should be the same as controller.
- Do not use inline texts inside "php" and "tpl" files. Use call $this->language->get('language_definition_key','route') instead.
- In case of extension, use the extension's text ID (key) as prefix for all it's language definition keys. 
- Route parameter is not necessary when the route of a language file is equal route of controller
- All html-attributes inside tpl files should be wrapped function echo_html2view() or html2view(). For javascript code inside tpl-file use js_echo() function for php-variables.
    

# Error Handling & Logging
- Never use `@` to suppress errors.
- Do not use `die/exit/var_dump/print_r` in examples.
- Throw specific exceptions (InvalidArgumentException, RuntimeException, etc.) with clear messages.

# Project Structure (paths are illustrative)
- Storefront controllers: storefront/controller/pages/* or extensions/<ext_key>/storefront/controller/*
- Admin controllers: admin/controller/pages/* or extensions/<ext_key>/admin/controller/*
- Models: */model/*/*.php
- Views/templates: */view/<theme>/template/*/*.tpl
- Language files: */language/<language>/*/*.xml
- Assets: */view/<theme>/css|js|image/* or extension assets folder

# Security
- For hashing passwords, use `password_hash()` and `password_verify()`.
- Never invent your own crypto.

# Architecture
- Keep controllers thin. Put business logic into services, persistence in repositories.
- Use DTOs or Value Objects instead of raw arrays in public APIs.
- Favor immutable Value Objects (Email, Money, Uuid).
- Use dependency injection (constructor injection), not service locators.

# Testing

# Documentation
- Add PHPDoc only when it adds value (e.g., generics for collections).
- For utility classes, add a small "Usage" example in comments.
- Add PHPDoc comment before call of method "processTemplate". It must be @see real path which started with public_html

# Performance
- Avoid premature optimization, but do not create obvious N+1 queries.
- Use generators (yield) for large collections when appropriate.

# Prohibited Patterns
- Do not use: `var_dump`, `print_r`, `dd`, `exit`, global singletons, or magic methods `__get/__set` in domain models.
- Do not generate "// TODO: implement". Always provide a minimal working implementation.

# Static Analysis
- Code must be compatible with PHPStan/Psalm (high strictness).
- Support generics in collections via PHPDoc where needed.
