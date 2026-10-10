# TomvisHIMPro Database Schema & ERD Specification

## Database Name: `tomvishimpro`

### 1. Core Framework Tables
- `users`: User identity accounts, password hashes, departments, status, last login timestamp.
- `roles`: Role definitions (`super_admin`, `pharmacist`, `physician`, `nurse`, `security_officer`, etc.).
- `permissions`: Fine-grained permission rules (`patient.view`, `allergy.create`, `security.scan`, etc.).
- `role_permissions`: Association table between roles and permissions.
- `user_roles`: Association table between users and roles.
- `user_sessions`: Active web session digests, IP addresses, user agents.
- `audit_logs`: Audit trail records for system actions.
- `data_access_logs`: Explicit tracking for all patient health record views (HN, access type, department, purpose).
- `modules`: Dynamic module registry and status.
- `module_settings` & `system_settings`: Key-value configuration parameters.
- `rate_limit_events`: Request rate limiting tracking.

### 2. HIS Gateway Tables
- `his_connections`: Managed HIS database connection parameters.
- `his_schema_snapshots`: Saved JSON table structure snapshots for version tracking.
- `his_mapping_profiles`: Active mapping profiles for field translation.
- `his_field_mappings`: Mapping rules connecting canonical fields to HIS table columns.
- `his_gateway_logs`: Execution timing and row count metadata logs.

### 3. Sandbox Tables
- `sandbox_scenarios`: Pre-configured clinical test scenarios.
- `sandbox_datasets`: Generated synthetic patient datasets.
- `sandbox_test_runs` & `sandbox_test_results`: Automated clinical test execution outcomes.

### 4. Security Tables
- `security_scan_targets` & `security_scan_approvals`: Pentest scope approvals.
- `security_scans`: Scan runs (SAST, DAST, Dependency Audit).
- `security_findings`: Vulnerability findings tracker (Severity, CWE, CVSS, Remediation).
- `security_remediation` & `security_retests`: Workflow remediation logs.
- `security_release_approvals`: Production release security sign-off log.
