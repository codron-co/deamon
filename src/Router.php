<?php

declare(strict_types=1);

namespace Codron\Deamon;

/**
 * Simple router: path -> handler (module + action).
 * All routes and code in English.
 */
final class Router
{
    /** @var string */
    private $basePath;

    /** @var array<int, array{pattern: string, module: string, action: string, params?: string[]}> */
    private $routes = [];

    public function __construct(string $basePath = '')
    {
        $this->basePath = rtrim($basePath, '/');
    }

    public function getPath(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);
        $path = $path ?: '/';
        $path = '/' . trim($path, '/');
        if ($this->basePath !== '' && strpos($path, $this->basePath) === 0) {
            $path = substr($path, strlen($this->basePath)) ?: '/';
        }
        return $path;
    }

    public function add(string $pattern, string $module, string $action, array $params = []): void
    {
        $this->routes[] = [
            'pattern' => $pattern,
            'module' => $module,
            'action' => $action,
            'params' => $params,
        ];
    }

    /**
     * Match current path. Returns [module, action, params] or null.
     * @return array{0: string, 1: string, 2: array<string, string>}|null
     */
    public function match(): ?array
    {
        $path = $this->getPath();
        foreach ($this->routes as $route) {
            $re = $this->patternToRegex($route['pattern']);
            if (preg_match($re, $path, $m)) {
                $params = [];
                foreach ($route['params'] ?? [] as $i => $name) {
                    $params[$name] = $m[$i + 1] ?? '';
                }
                return [$route['module'], $route['action'], $params];
            }
        }
        return null;
    }

    private function patternToRegex(string $pattern): string
    {
        $pattern = preg_quote($pattern, '#');
        $pattern = str_replace('\*', '([^/]+)', $pattern);
        return '#^' . $pattern . '$#';
    }
}
