# TomvisHIMPro v2.1 — Centralized Database & Zero-Reconfiguration Gap Analysis

## Executive Overview
This document evaluates the existing **TomvisHIMPro v1.0** framework against the requirements for **v2.1 Centralized Database & Zero-Reconfiguration Deployment**.

---

## 1. Existing System Assessment

| System Component | Current State (v1.0) | Target Requirement (v2.1) | Gap Identified |
| :--- | :--- | :--- | :--- |
| **Database Host** | Defaults to `127.0.0.1` | Centralized MySQL Server `192.168.111.240` | Need central host support with fallback/retry and TLS support |
| **Logical Databases** | Single `tomvishimpro` DB | `tomvishimpro_dev`, `tomvishimpro_staging`, `tomvishimpro` | Must separate dev, staging, and production databases |
| **Database Users** | Single `root` user | `tomvis_dev_app`, `tomvis_staging_app`, `tomvis_prod_app`, `tomvis_his_readonly` | Least privilege isolation per environment |
| **Environment Guard** | Basic config lookup | Hard Runtime Isolation Guard blocking cross-env connections | Require explicit runtime validation preventing `local` from connecting to `tomvishimpro` production DB |
| **Deployment Automation** | Manual `scripts/tomvis` CLI | Zero-Reconfiguration pipeline (`scripts/preflight.php`, `deploy.ps1`, `deploy.sh`, `rollback.sh`) | Need preflight checks, lock verification, and persistent `.env` preservation |
| **Developer Dashboard** | Not implemented | `/admin/developer` Dashboard | Need health checks, latency tests, DB/HIS connection diagnosis, deployment logs |
| **Diagnostic Tool** | Basic PHP script | `php tomvis db check`, `php tomvis db ping`, `php tomvis db info` | Needs network, TCP, TLS, charset, and permission diagnostic commands |

---

## 2. Technical Modifications Plan

1. **Configuration Manager & Environment Files:**
   - Create environment template files: `.env.example`, `config/environments/.env.local.example`, `config/environments/.env.staging.example`, `config/environments/.env.production.example`.
   - Update `config/database.php` to define named connections (`app`, `his_readonly`, `sandbox`) with centralized host `192.168.111.240`.

2. **Connection Factory & Runtime Isolation Guard:**
   - Implement `Tomvis\HIMPro\Core\Database\ConnectionFactory` with connection timeout, retry limits, and PDO prepared statement enforcement.
   - Implement `EnvironmentGuard` preventing cross-environment data contamination:
     - `local` + `tomvishimpro` (Production DB) -> **FORBIDDEN (Throws Exception)**.
     - `production` + `tomvishimpro_dev` -> **FORBIDDEN (Throws Exception)**.
     - `HIS_MODE=mock` + Live HIS connection -> **FORBIDDEN (Throws Exception)**.

3. **CLI Diagnostics Tool (`php tomvis db`):**
   - Add subcommands `check`, `ping`, `info` to `scripts/tomvis` to test socket/TCP connectivity to `192.168.111.240:3306`, check PDO connection, query permissions, character sets, and TLS status.

4. **Zero-Reconfiguration Deployment Pipeline:**
   - Create `scripts/preflight.php`, `scripts/post-deploy-check.php`, `scripts/deploy.ps1`, `scripts/deploy.sh`, and `scripts/rollback.sh`.
   - Register CLI commands: `php tomvis deploy`, `php tomvis rollback`, `php tomvis preflight`.

5. **Developer Dashboard (`/admin/developer`):**
   - Build `DeveloperDashboardController.php` rendering `/admin/developer` view.
   - Features: Environment status, Central DB Host, DB Name, Connection Latency, HIS Adapter Status, Migration Version, Health Check Actions.

6. **Automated Test Suite (PHPUnit):**
   - `CentralDatabaseConnectionTest`
   - `EnvironmentIsolationTest`
   - `DatabasePermissionTest`
   - `HISReadOnlyConnectionTest`
   - `ConfigurationValidationTest`
   - `MigrationSafetyTest`
   - `DeploymentPreflightTest`
   - `GitIgnoreSecretsTest`
   - `ProductionConfigProtectionTest`

---

## 3. Rollback & Disaster Recovery Strategy
- Source code changes are strictly backward compatible.
- Local fallback database configuration support is preserved if central host `192.168.111.240` is unreachable in offline development mode.
