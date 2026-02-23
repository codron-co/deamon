<?php

declare(strict_types=1);

namespace Codron\Deamon;

/**
 * Router: path -> module/action. URL structure: module/submodule/action or module/{param}/{param}.
 * Supports routes.json (path with {param}) or add() with * pattern.
 */
final class Router
{
    /** @var string */
    private $basePath;

    /** @var array<int, array{pattern: string, params: string[], handler?: string, module?: string, action?: string, css?: array, js?: array}> */
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
            'params' => $params,
            'module' => $module,
            'action' => $action,
        ];
    }

    /**
     * Load routes from JSON. path, handler (page file), optional css, js. URL: module/submodule/action or module/{param}.
     */
    public function loadFromJson(string $filePath): void
    {
        if (!is_file($filePath)) {
            return;
        }
        $data = json_decode(file_get_contents($filePath), true);
        if (!isset($data['routes']) || !is_array($data['routes'])) {
            return;
        }
        foreach ($data['routes'] as $r) {
            $path = $r['path'] ?? '';
            $handler = $r['handler'] ?? '';
            if ($path === '' || $handler === '') {
                continue;
            }
            preg_match_all('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', $path, $m);
            $paramNames = $m[1] ?? [];
            $this->routes[] = [
                'pattern' => $path,
                'params' => $paramNames,
                'handler' => $handler,
                'css' => $r['css'] ?? [],
                'js' => $r['js'] ?? [],
            ];
        }
    }

    /**
     * Match current path. Returns ['route' => routeArray, 'params' => params] or null.
     * @return array{route: array, params: array<string, string>}|null
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
                return ['route' => $route, 'params' => $params];
            }
        }
        return null;
    }

    private function patternToRegex(string $pattern): string
    {
        $pattern = preg_quote($pattern, '#');
        $pattern = preg_replace('#\\\\\{[^}]+\\\\\}#', '([^/]+)', $pattern);
        $pattern = str_replace('\*', '([^/]+)', $pattern);
        return '#^' . $pattern . '$#';
    }
}
