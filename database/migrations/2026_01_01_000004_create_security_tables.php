<?php

return new class {
    public function up(PDO $pdo): void
    {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS security_scan_targets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                target_name VARCHAR(100) NOT NULL,
                target_url VARCHAR(255) NOT NULL,
                environment VARCHAR(30) NOT NULL,
                is_approved TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS security_scan_approvals (
                id INT AUTO_INCREMENT PRIMARY KEY,
                target_id INT NOT NULL,
                approved_by INT NOT NULL,
                start_date DATETIME NOT NULL,
                expiry_date DATETIME NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (target_id) REFERENCES security_scan_targets(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS security_scans (
                id INT AUTO_INCREMENT PRIMARY KEY,
                scan_type VARCHAR(50) NOT NULL,
                scanner_name VARCHAR(50) NOT NULL,
                status ENUM('running', 'completed', 'failed') DEFAULT 'running',
                findings_count INT DEFAULT 0,
                started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                finished_at DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS security_findings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                scan_id INT NOT NULL,
                vulnerability_name VARCHAR(255) NOT NULL,
                severity ENUM('Critical', 'High', 'Medium', 'Low', 'Info') NOT NULL,
                affected_component VARCHAR(255) NOT NULL,
                cwe VARCHAR(30) NULL,
                cvss_score FLOAT NULL,
                description TEXT NULL,
                evidence TEXT NULL,
                remediation TEXT NULL,
                status ENUM('Open', 'Triaged', 'Assigned', 'In Progress', 'Ready for Retest', 'Verified', 'Closed', 'Risk Accepted') DEFAULT 'Open',
                owner_id INT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (scan_id) REFERENCES security_scans(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS security_remediation (
                id INT AUTO_INCREMENT PRIMARY KEY,
                finding_id INT NOT NULL,
                status VARCHAR(50) NOT NULL,
                notes TEXT NULL,
                updated_by INT NOT NULL,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (finding_id) REFERENCES security_findings(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS security_retests (
                id INT AUTO_INCREMENT PRIMARY KEY,
                finding_id INT NOT NULL,
                result ENUM('Passed', 'Failed') NOT NULL,
                notes TEXT NULL,
                retested_by INT NOT NULL,
                retested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (finding_id) REFERENCES security_findings(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS security_release_approvals (
                id INT AUTO_INCREMENT PRIMARY KEY,
                release_version VARCHAR(30) NOT NULL,
                status ENUM('Approved', 'Rejected', 'Pending') DEFAULT 'Pending',
                security_score INT NOT NULL,
                approved_by INT NULL,
                approved_at DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }
};
