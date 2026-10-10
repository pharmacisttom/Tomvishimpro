<?php

namespace Tomvis\HIMPro\Database\Seeders;

use PDO;

class DatabaseSeeder
{
    public static function run(PDO $pdo): void
    {
        // 1. Roles
        $roles = [
            ['name' => 'super_admin', 'display_name' => 'Super Administrator', 'description' => 'Full access to system'],
            ['name' => 'hospital_admin', 'display_name' => 'Hospital Administrator', 'description' => 'Administrative operations'],
            ['name' => 'it_admin', 'display_name' => 'IT Administrator', 'description' => 'IT System operations'],
            ['name' => 'security_officer', 'display_name' => 'Security Officer', 'description' => 'Security oversight & Pentest'],
            ['name' => 'pharmacist', 'display_name' => 'Pharmacist', 'description' => 'Clinical Pharmacy & MedRec'],
            ['name' => 'physician', 'display_name' => 'Physician', 'description' => 'OPD/IPD Clinical Access'],
            ['name' => 'nurse', 'display_name' => 'Nurse', 'description' => 'Nursing care & Vital signs'],
            ['name' => 'medical_records_officer', 'display_name' => 'Medical Records Officer', 'description' => 'Patient records'],
            ['name' => 'quality_officer', 'display_name' => 'Quality Officer', 'description' => 'HA & QA Audits'],
            ['name' => 'auditor', 'display_name' => 'Auditor', 'description' => 'Read-only Audit Log access'],
            ['name' => 'developer', 'display_name' => 'Developer', 'description' => 'Framework developer & generator'],
            ['name' => 'sandbox_tester', 'display_name' => 'Sandbox Tester', 'description' => 'Sandbox Studio tester'],
        ];

        $stmtRole = $pdo->prepare("INSERT IGNORE INTO roles (name, display_name, description) VALUES (?, ?, ?)");
        foreach ($roles as $r) {
            $stmtRole->execute([$r['name'], $r['display_name'], $r['description']]);
        }

        // 2. Permissions
        $permissions = [
            ['name' => 'patient.view', 'display_name' => 'View Patient Records', 'module' => 'Patient', 'action' => 'view', 'patient_level' => 1],
            ['name' => 'patient.search', 'display_name' => 'Search Patients', 'module' => 'Patient', 'action' => 'search', 'patient_level' => 1],
            ['name' => 'allergy.view', 'display_name' => 'View Drug Allergies', 'module' => 'DrugAllergy', 'action' => 'view', 'patient_level' => 1],
            ['name' => 'allergy.create', 'display_name' => 'Create Drug Allergy', 'module' => 'DrugAllergy', 'action' => 'create', 'patient_level' => 1],
            ['name' => 'allergy.update', 'display_name' => 'Update Drug Allergy', 'module' => 'DrugAllergy', 'action' => 'update', 'patient_level' => 1],
            ['name' => 'medication.view', 'display_name' => 'View Medication History', 'module' => 'Medication', 'action' => 'view', 'patient_level' => 1],
            ['name' => 'lab.view', 'display_name' => 'View Laboratory Results', 'module' => 'Laboratory', 'action' => 'view', 'patient_level' => 1],
            ['name' => 'report.export', 'display_name' => 'Export Reports', 'module' => 'Reporting', 'action' => 'export', 'patient_level' => 0],
            ['name' => 'security.scan', 'display_name' => 'Run Security Scans', 'module' => 'SecurityLab', 'action' => 'scan', 'patient_level' => 0],
            ['name' => 'security.review', 'display_name' => 'Review Vulnerabilities', 'module' => 'SecurityLab', 'action' => 'review', 'patient_level' => 0],
            ['name' => 'security.approve', 'display_name' => 'Approve Release Security', 'module' => 'SecurityLab', 'action' => 'approve', 'patient_level' => 0],
            ['name' => 'sandbox.manage', 'display_name' => 'Manage Sandbox Studio', 'module' => 'Sandbox', 'action' => 'manage', 'patient_level' => 0],
        ];

        $stmtPerm = $pdo->prepare("INSERT IGNORE INTO permissions (name, display_name, module, action, patient_level) VALUES (?, ?, ?, ?, ?)");
        foreach ($permissions as $p) {
            $stmtPerm->execute([$p['name'], $p['display_name'], $p['module'], $p['action'], $p['patient_level']]);
        }

        // 3. Default Admin User (username: admin, password: Admin123456!)
        $adminPassword = password_hash('Admin123456!', PASSWORD_BCRYPT);
        $stmtUser = $pdo->prepare("INSERT IGNORE INTO users (username, password_hash, full_name, email, department, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmtUser->execute(['admin', $adminPassword, 'Super Administrator', 'admin@tomvishimpro.hospital', 'IT', 'active']);

        // Assign super_admin role to admin user
        $userId = $pdo->lastInsertId() ?: 1;
        $stmtRoleGet = $pdo->query("SELECT id FROM roles WHERE name = 'super_admin'");
        $roleId = $stmtRoleGet->fetchColumn();

        if ($userId && $roleId) {
            $stmtUserRole = $pdo->prepare("INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (?, ?)");
            $stmtUserRole->execute([$userId, $roleId]);
        }

        // 4. Default HIS Connection & Profile
        $pdo->exec("INSERT IGNORE INTO his_connections (id, name, driver, host, port, database_name, username, status) VALUES
            (1, 'HIMPro Local Mock Adapter', 'mock', '127.0.0.1', 3306, 'himpro_his', 'himpro_read', 'active')");

        $pdo->exec("INSERT IGNORE INTO his_mapping_profiles (id, name, is_active, description) VALUES
            (1, 'HIMPro Standard v1.0 Mapping Profile', 1, 'Default mapping for HIMPro HIS tables (pt, opd, ipd)')");

        // 5. Default Sandbox Scenarios
        $scenarios = [
            ['code' => 'no_allergy', 'name' => 'Normal Patient (No Allergies)', 'category' => 'General', 'description' => 'Patient with no recorded drug allergies or severe renal impairment.'],
            ['code' => 'penicillin_allergy', 'name' => 'Penicillin Allergy Patient', 'category' => 'Allergy', 'description' => 'Patient with documented Anaphylaxis to Penicillin G.'],
            ['code' => 'adr_history', 'name' => 'Adverse Drug Reaction History', 'category' => 'ADR', 'description' => 'Patient with previous Stevens-Johnson Syndrome from Co-trimoxazole.'],
            ['code' => 'warfarin_therapy', 'name' => 'Warfarin Anticoagulation', 'category' => 'Clinical Pharmacy', 'description' => 'Patient receiving Warfarin with high target INR monitoring requirement.'],
            ['code' => 'ckd_stage4', 'name' => 'Severe CKD (eGFR < 30)', 'category' => 'Renal', 'description' => 'Patient requiring renal dose adjustments for antibiotics.'],
        ];

        $stmtScen = $pdo->prepare("INSERT IGNORE INTO sandbox_scenarios (code, name, category, description) VALUES (?, ?, ?, ?)");
        foreach ($scenarios as $sc) {
            $stmtScen->execute([$sc['code'], $sc['name'], $sc['category'], $sc['description']]);
        }

        // 6. Default Security Scan Target
        $pdo->exec("INSERT IGNORE INTO security_scan_targets (id, target_name, target_url, environment, is_approved) VALUES
            (1, 'Local Sandbox Application Target', 'http://localhost/tomvishimpro/public', 'sandbox', 1)");
    }
}
