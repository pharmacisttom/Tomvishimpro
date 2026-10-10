<?php

return new class {
    public function up(PDO $pdo): void
    {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS his_connections (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50) NOT NULL UNIQUE,
                driver VARCHAR(30) NOT NULL,
                host VARCHAR(100) NOT NULL,
                port INT DEFAULT 3306,
                database_name VARCHAR(100) NOT NULL,
                username VARCHAR(100) NOT NULL,
                password_encrypted TEXT NULL,
                status ENUM('active', 'inactive', 'failed') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS his_schema_snapshots (
                id INT AUTO_INCREMENT PRIMARY KEY,
                connection_id INT NOT NULL,
                table_name VARCHAR(100) NOT NULL,
                schema_json LONGTEXT NOT NULL,
                snapshot_hash VARCHAR(64) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (connection_id) REFERENCES his_connections(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS his_mapping_profiles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL UNIQUE,
                is_active TINYINT(1) DEFAULT 1,
                description VARCHAR(255) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS his_field_mappings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                profile_id INT NOT NULL,
                entity_type VARCHAR(50) NOT NULL,
                canonical_field VARCHAR(50) NOT NULL,
                target_table VARCHAR(100) NOT NULL,
                target_column VARCHAR(100) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (profile_id) REFERENCES his_mapping_profiles(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS his_gateway_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                connection_id INT NULL,
                query_type VARCHAR(30) NOT NULL,
                target_table VARCHAR(100) NOT NULL,
                execution_time_ms FLOAT NOT NULL,
                row_count INT DEFAULT 0,
                ip_address VARCHAR(45) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }
};
