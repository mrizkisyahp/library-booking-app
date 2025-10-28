<?php

namespace App\Core;

use PDO;

class Database
{
    public PDO $pdo;

    public function __construct(array $config)
    {
        $host = $config['host'] ?? 'localhost';
        $port = $config['port'] ?? '3306';
        $dbname = $config['name'] ?? '';
        $charset = $config['charset'] ?? 'utf8mb4';
        $user = $config['user'] ?? '';
        $pass = $config['pass'] ?? '';

        $this->pdo = new PDO(
            "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}",
            $user,
            $pass
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function applyMigrations(): void
    {
        $this->createMigrationTable();
        $appliedMigration = $this->getAppliedMigration();

        $newMigrations = [];
        $migrationFolder = App::$ROOT_DIR . '/migrations';
        $this->log($migrationFolder);
        $files = scandir(App::$ROOT_DIR . '/migrations');
        $toApplyMigrations = array_diff($files, $appliedMigration);

        foreach ($toApplyMigrations as $migration) {
            if ($migration === '.' || $migration === '..') {
                continue;
            }

            require_once App::$ROOT_DIR . '/migrations/' . $migration;
            $className = pathinfo($migration, PATHINFO_FILENAME);
            $instance = new $className();

            $this->log("Applying Migration $migration");
            $instance->up();
            $this->log("Applied Migration $migration");

            $newMigrations[] = $migration;
        }

        if (!empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
        } else {
            $this->log("All migrations are applied");
        }
    }

    public function createMigrationTable(): void
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=INNODB;
        ");
    }

    public function getAppliedMigration(): array
    {
        $statement = $this->pdo->prepare("SELECT migration FROM migrations");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    public function saveMigrations(array $migrations): void
    {
        $str = implode(",", array_map(fn($m) => "('$m')", $migrations));
        $statement = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES $str");
        $statement->execute();
    }

    public function rollbackLastMigration(): void {
    $applied = $this->getAppliedMigration();
    if (empty($applied)) {
        $this->log("No migrations to rollback.");
        return;
    }

    $lastMigration = end($applied);
    require_once App::$ROOT_DIR . '/migrations/' . $lastMigration;
    $className = pathinfo($lastMigration, PATHINFO_FILENAME);
    $instance = new $className();

    $this->log("Rolling back $lastMigration...");
    $instance->down();
    $this->pdo->prepare("DELETE FROM migrations WHERE migration = ?")->execute([$lastMigration]);
    $this->log("Rolled back $lastMigration successfully.");
    }

    public function prepare($sql)
    {
        return $this->pdo->prepare($sql);
    }

    protected function log($message): void
    {
        echo '[' . date('Y-m-d H:i:s') . '] - ' . $message . PHP_EOL;
    }
}
