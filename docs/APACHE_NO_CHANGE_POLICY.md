# Apache Zero-Change Policy & Front Controller Fallback

## Front Controller Fallback Routing Specification

When URL rewriting (`mod_rewrite`) is disabled or restricted in shared Apache environments:

1. **Standard Rewrite Route:**
   `http://192.168.111.240/tomvishimpro/public/dashboard`

2. **Front Controller Fallback Route (No Apache Rewrite Required):**
   `http://192.168.111.240/tomvishimpro/public/index.php?route=/dashboard`
   OR
   `http://192.168.111.240/tomvishimpro/index.php?route=/dashboard`

## Subdirectory Base Path Resolution
`Tomvis\HIMPro\Core\Request\Request` resolves URI paths dynamically by stripping configurable subdirectories:
- Config: `APP_URL=http://192.168.111.240/tomvishimpro/public`
- Request URI: `/tomvishimpro/public/dashboard` -> Resolved Internal Route: `/dashboard`
- Request Query Parameter: `?route=/dashboard` -> Resolved Internal Route: `/dashboard`
