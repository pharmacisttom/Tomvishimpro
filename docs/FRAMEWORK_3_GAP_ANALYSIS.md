# TomvisHIMPro 3.0 — Framework Assessment & Gap Analysis

## Executive Summary
This document provides a thorough audit of the existing **TomvisHIMPro v2.1** framework against the requirements for **v3.0 (Hospital RAD Platform)**.

---

## Component Audit & Categorization

| Component | Current State (v2.1) | Target State (v3.0 RAD Platform) | Action |
| :--- | :--- | :--- | :--- |
| **Framework Core** | Router, DI Container, Config, Logging, PDO Manager | Application Kernel, Feature Flags, Event Dispatcher, Cache, Background Jobs | **REFACTOR & EXPAND** |
| **CLI Tool** | `php tomvis db/migrate/seed/make/deploy` | Add `about`, `env`, `route`, `module`, `health` commands | **EXPAND** |
| **Central Database** | `192.168.111.240` + Environment Guards | Database Health Checker, Permission Inspector, Backup Verifier | **KEEP & EXPAND** |
| **HIS Gateway** | `MockHISAdapter`, `HIMProReadOnlyAdapter`, Schema Explorer | Canonical Models (Patient, Encounter, Visit, Diagnosis, Medication, Allergy, Lab, Admission) + FHIR Foundation | **REFACTOR & EXPAND** |
| **Developer Studio** | Simple `/admin/developer` page | Web-Based Developer Studio `/developer` with 10-step Project Wizard | **NEW FEATURE** |
| **Module Generator** | Basic CLI template generation | Smart Module Generator Engine (`module.json`, Dry Run, Diff Preview, Full Scaffold) | **REFACTOR & EXPAND** |
| **CRUD Generator** | Not present | Smart Web & CLI CRUD Builder with AJAX, DataTables, Backend RBAC | **NEW FEATURE** |
| **Clinical Form Builder**| Static HTML views | Dynamic Schema-Driven Form Builder with Clinical Templates & Versioning | **NEW FEATURE** |
| **API Generator** | Manual route definition | REST API Generator + OpenAPI Exporter + Interactive Explorer (`/developer/api-explorer`) | **NEW FEATURE** |
| **Report Builder** | Basic `ReportEngine.php` | Visual Report Builder (`/developer/reports`) with Thai Fiscal Year & Export | **NEW FEATURE** |
| **Sandbox Studio** | `/sandbox` with 5 scenarios | 12 Scenarios, Synthetic Generator Engine, Deterministic Seed, API Emulator | **EXPAND** |
| **Test Engine** | Basic PHPUnit tests | Test Generator Engine & Web Test Runner (`/developer/tests`) | **NEW FEATURE** |
| **Security Lab** | `/security` dashboard & ZAP config | OWASP ASVS 5.0 Scanner Suite, Workflow Management, Risk Acceptance | **EXPAND** |
| **Deployment Manager**| CLI Deployment Pipeline | Web Deployment Studio (`/developer/deployment`) + Preflight & Rollback | **EXPAND** |
| **Monitoring Center** | Basic metrics | Monitoring Center (`/admin/monitoring`) with Slow Query, Latency, Worker status | **NEW FEATURE** |
| **Module Registry** | Basic module table | Plugin Registry & Version Compatibility Matrix (`/developer/modules`) | **NEW FEATURE** |
| **Interoperability** | HIMPro Gateway | FHIR R4 DTO Foundation (`Patient`, `Encounter`, `Observation`, etc.) | **NEW FEATURE** |
| **Reference Module** | Patient Search, MedRec, Allergy | **PDH Pharmacy Queue System** (`app/Modules/PharmacyQueue/`) | **NEW FEATURE** |
| **Benchmark Tool** | Not present | Time-Saving Benchmark Suite measuring RAD productivity vs manual PHP | **NEW FEATURE** |
