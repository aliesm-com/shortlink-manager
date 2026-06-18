<?php

namespace App\Services;

use PDO;
use PDOException;

class Database
{
    /** @var PDO|null */
    private static $instance = null;

    /** @var string|null */
    private static $driver = null;

    /** @var array */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function driver()
    {
        if (self::$driver !== null) {
            return self::$driver;
        }

        $driver = isset($this->config['db_driver']) ? strtolower($this->config['db_driver']) : 'sqlite';
        self::$driver = $driver === 'mysql' ? 'mysql' : 'sqlite';

        return self::$driver;
    }

    /**
     * @return SqlDialect
     */
    public function dialect()
    {
        return new SqlDialect($this->driver());
    }

    /**
     * @return PDO
     */
    public function connection()
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        try {
            if ($this->driver() === 'mysql') {
                self::$instance = $this->connectMysql();
            } else {
                self::$instance = $this->connectSqlite();
            }

            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            if ($this->driver() === 'sqlite') {
                self::$instance->exec('PRAGMA foreign_keys = ON');
            }
        } catch (PDOException $e) {
            throw new PDOException('Database connection failed: ' . $e->getMessage());
        }

        return self::$instance;
    }

    /**
     * @return PDO
     */
    private function connectSqlite()
    {
        $dbPath = $this->config['db_path'];
        $dir = dirname($dbPath);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return new PDO('sqlite:' . $dbPath);
    }

    /**
     * @return PDO
     */
    private function connectMysql()
    {
        $host = isset($this->config['db_host']) ? $this->config['db_host'] : '127.0.0.1';
        $port = isset($this->config['db_port']) ? (int) $this->config['db_port'] : 3306;
        $name = isset($this->config['db_name']) ? $this->config['db_name'] : '';
        $user = isset($this->config['db_user']) ? $this->config['db_user'] : '';
        $pass = isset($this->config['db_pass']) ? $this->config['db_pass'] : '';
        $charset = isset($this->config['db_charset']) ? $this->config['db_charset'] : 'utf8mb4';

        if ($name === '' || $user === '') {
            throw new PDOException('MySQL requires db_name and db_user in config.');
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $host,
            $port,
            $name,
            $charset
        );

        return new PDO($dsn, $user, $pass, [
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    public function migrate()
    {
        if ($this->driver() === 'mysql') {
            $this->migrateMysql();
        } else {
            $this->migrateSqlite();
        }
    }

    private function migrateSqlite()
    {
        $pdo = $this->connection();

        $pdo->exec('
            CREATE TABLE IF NOT EXISTS links (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                slug TEXT NOT NULL UNIQUE,
                original_url TEXT NOT NULL,
                title TEXT DEFAULT NULL,
                is_active INTEGER NOT NULL DEFAULT 1,
                created_at TEXT NOT NULL
            )
        ');

        $pdo->exec('
            CREATE TABLE IF NOT EXISTS clicks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                link_id INTEGER NOT NULL,
                clicked_at TEXT NOT NULL,
                ip_hash TEXT DEFAULT NULL,
                user_agent TEXT DEFAULT NULL,
                referer TEXT DEFAULT NULL,
                FOREIGN KEY (link_id) REFERENCES links(id) ON DELETE CASCADE
            )
        ');

        $pdo->exec('CREATE INDEX IF NOT EXISTS idx_links_slug ON links(slug)');
        $pdo->exec('CREATE INDEX IF NOT EXISTS idx_clicks_link_date ON clicks(link_id, clicked_at)');
    }

    private function migrateMysql()
    {
        $pdo = $this->connection();

        $pdo->exec('
            CREATE TABLE IF NOT EXISTS links (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                slug VARCHAR(64) NOT NULL,
                original_url TEXT NOT NULL,
                title VARCHAR(255) DEFAULT NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                created_at DATETIME NOT NULL,
                UNIQUE KEY uk_links_slug (slug)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');

        $pdo->exec('
            CREATE TABLE IF NOT EXISTS clicks (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                link_id INT UNSIGNED NOT NULL,
                clicked_at DATETIME NOT NULL,
                ip_hash VARCHAR(64) DEFAULT NULL,
                user_agent VARCHAR(512) DEFAULT NULL,
                referer VARCHAR(512) DEFAULT NULL,
                KEY idx_clicks_link_date (link_id, clicked_at),
                CONSTRAINT fk_clicks_link FOREIGN KEY (link_id) REFERENCES links(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
    }
}
