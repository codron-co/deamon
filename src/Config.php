<?php

declare(strict_types=1);

namespace Codron\Deamon;

/**
 * Site config: DB (per-site), CDN base URL, site id, theme path.
 * All code in English.
 */
final class Config
{
    /** @var array<string, mixed> */
    private $values;

    /** @param array<string, mixed> $values */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function get(string $key, $default = null)
    {
        return $this->values[$key] ?? $default;
    }

    public function getDbHost(): string
    {
        return (string) $this->get('db_host', 'localhost');
    }

    public function getDbName(): string
    {
        return (string) $this->get('db_name', '');
    }

    public function getDbUser(): string
    {
        return (string) $this->get('db_user', '');
    }

    public function getDbPassword(): string
    {
        return (string) $this->get('db_password', '');
    }

    public function getSiteId(): int
    {
        return (int) $this->get('site_id', 1);
    }

    public function getCdnUrl(): string
    {
        return rtrim((string) $this->get('cdn_url', ''), '/');
    }

    public function getThemePath(): string
    {
        return rtrim((string) $this->get('theme_path', ''), DIRECTORY_SEPARATOR);
    }

    public function getDeamonPath(): string
    {
        return rtrim((string) $this->get('deamon_path', ''), DIRECTORY_SEPARATOR);
    }
}
