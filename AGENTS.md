# AGENTS.md — Developer & AI Assistant Guidelines for TomvisHIMPro

## Project Mission
TomvisHIMPro is a modular PHP 8.4+ / MySQL Healthcare Application Platform and HIS Integration Sandbox designed for secure deployment in hospital environments.

## System Guidelines
1. **Security First:** Never allow direct raw SQL execution from user input. Always use `QueryBuilder` or PDO prepared statements.
2. **Data Privacy (PHI Protection):** Never log Patient Health Information (PHI) or credentials in plain text. Always sanitize logs via `Logger::audit()` or `Logger::info()`.
3. **Hard Environment Isolation:** Always use `MockHISAdapter` and `SyntheticPatientGenerator` during local development and testing. Do NOT connect live HIS production databases to local sandbox or GitHub CI workflows.
4. **Strict Control Flow & Authorization:** Enforce Default Deny authorization (`RbacMiddleware`) on all new modules and clinical endpoints.
5. **Testing & Verification:** Run `vendor\bin\phpunit` before committing code. Ensure test suite passes 100%.

## Common CLI Commands
- Run Migrations: `php scripts/tomvis migrate`
- Seed Database: `php scripts/tomvis seed`
- Make Module: `php tomvis make module <ModuleName>`
- Execute Tests: `vendor\bin\phpunit`
