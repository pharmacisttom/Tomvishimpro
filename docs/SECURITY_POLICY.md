# TomvisHIMPro Security Baseline Policy & Guidelines

## Security Standards Compliance
TomvisHIMPro adheres to:
- OWASP ASVS 5.0.0 (Application Security Verification Standard)
- OWASP Web Security Testing Guide (WSTG)
- OWASP API Security Top 10
- Thailand Personal Data Protection Act (PDPA) & Healthcare Data Governance Regulations

## Key Security Controls
1. **Parameterized Queries:** All SQL statements MUST use `QueryBuilder` or PDO prepared statements. Dynamic table or column names require explicit allowlist verification via `HIMProSchemaExplorer::getAllowlist()`.
2. **Output Encoding:** HTML output is escaped using `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.
3. **Anti-CSRF Protection:** Non-GET HTTP requests require a valid CSRF token verified by `CsrfMiddleware`.
4. **Session Security:** Cookies configured with `HttpOnly`, `SameSite=Lax`, and `Secure` flags. Session ID regenerated on login.
5. **Rate Limiting:** Login endpoints enforced at 5 max attempts per 15 minutes; API endpoints capped at 60 requests/minute.
6. **Audit Trail & Data Minimization:** Every access to patient health data records an entry in `data_access_logs`. Sensitive fields (passwords, tokens, PHI) are redacted from logs via `Logger::sanitizeContext()`.
