# TomvisHIMPro Framework & Healthcare Sandbox v1.0

**Tomvis Hospital Information Management Framework for HIMPro**

Enterprise Hospital Web Application Platform & HIS Integration Sandbox built on PHP 8.4+ / PHP 8.2 Legacy Compatibility & MySQL.

---

## Key Features
- **Modular Monolith Architecture:** Separation of Core framework, HIS Gateway, and Clinical Modules.
- **HIMPro HIS Gateway:** Safe Read-Only Database Adapters, Metadata Schema Explorer, and Canonical DTOs (`PatientDTO`, `VisitDTO`, `AllergyDTO`, `MedicationDTO`, `LabResultDTO`).
- **Healthcare Sandbox Studio:** Controlled Synthetic Patient Generator (50 default records) with 5 Clinical Test Scenarios (Normal, Penicillin Allergy, ADR SJS, Warfarin Therapy, Severe CKD).
- **Clinical Modules:** Patient Search, Drug Allergy & Naranjo ADR Probability Calculator, Medication Reconciliation workspace (BPMH), Hospital Executive Dashboard.
- **CLI Generator (`scripts/tomvis`):** CLI tool for generating modules, controllers, migrations, and running database migrations/seeders.
- **Security Lab (`/security`):** Built-in OWASP ASVS/WSTG Security Dashboard, SAST/DAST scanner runners, OWASP ZAP configuration export, Findings Workflow, and Release Security Gate.

---

## Quick Start (Windows XAMPP)

1. **Clone or Copy Repository:**
   Place the project at `C:\xampp\htdocs\tomvishimpro` or `d:\xampp\htdocs\tomvishimpro`.

2. **Install Dependencies:**
   ```bash
   composer install
   ```

3. **Configure Environment:**
   Copy `.env.example` to `.env`. Ensure MySQL server is running in XAMPP.

4. **Run Database Migrations & Seeders:**
   ```bash
   php scripts/tomvis migrate
   php scripts/tomvis seed
   ```

5. **Run Test Suite:**
   ```bash
   vendor\bin\phpunit
   ```

6. **Access Web Application:**
   Open browser at: [http://localhost/tomvishimpro/public](http://localhost/tomvishimpro/public)
   - **Default Admin Account:** `admin`
   - **Default Password:** `Admin123456!`

---

## Documentation Links
- [Project Status & Test Evidence](file:///d:/xampp/htdocs/tomvishimpro/docs/PROJECT_STATUS.md)
- [Installation Guide](file:///d:/xampp/htdocs/tomvishimpro/docs/INSTALLATION.md)
- [Architecture Guide](file:///d:/xampp/htdocs/tomvishimpro/docs/ARCHITECTURE.md)
- [Database Schema & ERD](file:///d:/xampp/htdocs/tomvishimpro/docs/DATABASE.md)
- [HIMPro HIS Mapping Guide](file:///d:/xampp/htdocs/tomvishimpro/docs/HIMPRO_MAPPING.md)
- [Sandbox Studio Guide](file:///d:/xampp/htdocs/tomvishimpro/docs/SANDBOX_GUIDE.md)
- [Security & Pentest Guide](file:///d:/xampp/htdocs/tomvishimpro/docs/SECURITY_POLICY.md)
