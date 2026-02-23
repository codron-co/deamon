<?php

declare(strict_types=1);

namespace Codron\Deamon;

use PDO;

/**
 * Per-site database connection. One DB per site; credentials from panel (super admin).
 */
final class Database
{
    /** @var PDO|null */
    private static $pdo;

    /** @var Config */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function getPdo(): PDO
    {
        if (self::$pdo === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=utf8mb4',
                $this->config->getDbHost(),
                $this->config->getDbName()
            );
            self::$pdo = new PDO($dsn, $this->config->getDbUser(), $this->config->getDbPassword(), [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }
        return self::$pdo;
    }

    /** Reset connection (e.g. for testing or site switch). */
    public static function reset(): void
    {
        self::$pdo = null;
    }
}
