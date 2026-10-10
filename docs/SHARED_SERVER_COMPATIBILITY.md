# TomvisHIMPro 3.0 — Shared Server Compatibility Specification

## Hospital Shared Server Environment (`192.168.111.240`)
The target server at `192.168.111.240` hosts multiple active clinical and administrative hospital applications, including **PDHTelemed**, **PDHTawan**, and other hospital services.

```
TomvisHIMPro must adapt to the existing hospital server — the hospital server must not be modified to accommodate TomvisHIMPro.
```

---

## Technical Adaptation Checklist

| Domain | Hospital Shared Server Constraint | TomvisHIMPro 3.0 Adaptation |
| :--- | :--- | :--- |
| **Apache Service** | Cannot reload/restart Apache or touch VirtualHost | **Zero Service Restart** deployment scripts |
| **DocumentRoot** | DocumentRoot points to Shared Web Root (`/var/www/html` or `/tomvishimpro/`) | **Subdirectory & Base Path Support** (`/tomvishimpro/` or `/tomvishimpro/public/`) |
| **URL Rewriting** | Mod_rewrite may be disabled or limited | **Front Controller Fallback Routing** (`index.php?route=/dashboard`) |
| **PHP Runtime** | Shared PHP version cannot be upgraded system-wide | **PHP 8.2+ Compatibility Profile** with feature polyfills |
| **File Security** | Application co-exists in web-accessible folder | **Non-Invasive Root Entry Point & Storage Protection** |
| **Other Systems** | Must not touch files of PDHTelemed, PDHTawan | **Strict Path Scoping** within `/tomvishimpro/` only |
