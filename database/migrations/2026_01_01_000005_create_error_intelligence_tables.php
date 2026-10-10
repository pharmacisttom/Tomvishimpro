<?php

return new class {
    public function up(PDO $pdo): void
    {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS error_groups (
                id INT AUTO_INCREMENT PRIMARY KEY,
                fingerprint VARCHAR(64) NOT NULL UNIQUE,
                exception_class VARCHAR(150) NOT NULL,
                normalized_code VARCHAR(50) NULL,
                source_component VARCHAR(150) NOT NULL,
                error_type ENUM('Exception', 'Error', 'FatalError', 'PDOException', 'ApiException', 'AuthException', 'HISGatewayException', 'JobException') NOT NULL,
                severity ENUM('Critical', 'High', 'Medium', 'Low') DEFAULT 'Medium',
                category ENUM('New Issue', 'Recurring Issue', 'Regression', 'Resolved Issue', 'Infrastructure Incident', 'Module-Specific Defect', 'Framework Core Defect') DEFAULT 'New Issue',
                status ENUM('Detected', 'Triaged', 'Assigned', 'Investigating', 'Fix Proposed', 'Testing', 'Ready for Review', 'Approved', 'Released', 'Monitoring', 'Closed') DEFAULT 'Detected',
                first_seen_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                last_seen_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                occurrence_count INT DEFAULT 1,
                affected_apps_count INT DEFAULT 1
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS error_events (
                id INT AUTO_INCREMENT PRIMARY KEY,
                group_id INT NOT NULL,
                trace_id VARCHAR(64) NOT NULL,
                app_id VARCHAR(50) NOT NULL,
                module_id VARCHAR(50) NOT NULL,
                framework_version VARCHAR(20) NOT NULL,
                module_version VARCHAR(20) NOT NULL,
                environment VARCHAR(20) NOT NULL,
                sanitized_message TEXT NOT NULL,
                sanitized_trace LONGTEXT NOT NULL,
                route_template VARCHAR(255) NULL,
                ip_address VARCHAR(45) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (group_id) REFERENCES error_groups(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS error_occurrences (
                id INT AUTO_INCREMENT PRIMARY KEY,
                group_id INT NOT NULL,
                app_id VARCHAR(50) NOT NULL,
                occurrence_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (group_id) REFERENCES error_groups(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS error_affected_applications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                group_id INT NOT NULL,
                app_id VARCHAR(50) NOT NULL,
                environment VARCHAR(20) NOT NULL,
                first_detected DATETIME NOT NULL,
                last_detected DATETIME NOT NULL,
                FOREIGN KEY (group_id) REFERENCES error_groups(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS error_root_causes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                group_id INT NOT NULL,
                layer VARCHAR(50) NOT NULL,
                suspected_component VARCHAR(150) NOT NULL,
                confidence_score FLOAT NOT NULL,
                evidence_summary TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (group_id) REFERENCES error_groups(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS framework_components (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL UNIQUE,
                namespace VARCHAR(150) NOT NULL,
                description VARCHAR(255) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS framework_fix_proposals (
                id INT AUTO_INCREMENT PRIMARY KEY,
                group_id INT NOT NULL,
                title VARCHAR(255) NOT NULL,
                description TEXT NULL,
                diff_patch LONGTEXT NULL,
                unit_test_code LONGTEXT NULL,
                status ENUM('Proposed', 'Testing', 'Approved', 'Rejected') DEFAULT 'Proposed',
                proposed_by INT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (group_id) REFERENCES error_groups(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS framework_patch_releases (
                id INT AUTO_INCREMENT PRIMARY KEY,
                patch_version VARCHAR(30) NOT NULL UNIQUE,
                release_notes TEXT NULL,
                status ENUM('Draft', 'Testing', 'Approved', 'Released') DEFAULT 'Draft',
                released_at DATETIME NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS error_notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                group_id INT NOT NULL,
                title VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                is_read TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (group_id) REFERENCES error_groups(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }
};
