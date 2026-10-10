<?php

return new class {
    public function up(PDO $pdo): void
    {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS sandbox_scenarios (
                id INT AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(50) NOT NULL UNIQUE,
                name VARCHAR(100) NOT NULL,
                description TEXT NULL,
                category VARCHAR(50) NOT NULL,
                patient_seed_data_json LONGTEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS sandbox_datasets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                record_count INT NOT NULL,
                seed INT NOT NULL,
                dataset_json LONGTEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS sandbox_test_runs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                scenario_id INT NOT NULL,
                status ENUM('pending', 'running', 'passed', 'failed') DEFAULT 'pending',
                executed_by INT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (scenario_id) REFERENCES sandbox_scenarios(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS sandbox_test_results (
                id INT AUTO_INCREMENT PRIMARY KEY,
                test_run_id INT NOT NULL,
                test_name VARCHAR(150) NOT NULL,
                status ENUM('passed', 'failed', 'skipped') NOT NULL,
                expected_output TEXT NULL,
                actual_output TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (test_run_id) REFERENCES sandbox_test_runs(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }
};
