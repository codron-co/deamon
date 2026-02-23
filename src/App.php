<?php

declare(strict_types=1);

namespace Codron\Deamon;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Main application bootstrap. Loads config, DB, Twig, registers modules, runs router.
 * All code in English.
 */
final class App
{
    /** @var Config */
    private $config;

    /** @var Database */
    private $database;

    /** @var Router */
    private $router;

    /** @var Environment|null */
    private $twig;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->database = new Database($config);
        $this->router = new Router();
        $routesJson = $this->config->getDeamonPath() . '/routes.json';
        if (is_file($routesJson)) {
            $this->router->loadFromJson($routesJson);
        } else {
            $this->registerCoreRoutes();
        }
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function getDatabase(): Database
    {
        return $this->database;
    }

    public function getRouter(): Router
    {
        return $this->router;
    }

    public function getTwig(): Environment
    {
        if ($this->twig === null) {
            $deamonPath = $this->config->getDeamonPath();
            $themePath = $this->config->getThemePath();
            $loader = new FilesystemLoader([
                $themePath ?: $deamonPath . '/templates',
                $deamonPath . '/templates',
            ]);
            $this->twig = new Environment($loader, [
                'cache' => false,
                'autoescape' => 'html',
            ]);
            $this->twig->addGlobal('cdn_url', $this->config->getCdnUrl());
            $this->twig->addGlobal('site_id', $this->config->getSiteId());
        }
        return $this->twig;
    }

    private function registerCoreRoutes(): void
    {
        $this->router->add('/', 'Home', 'index');
        $this->router->add('/about', 'About', 'index');
        $this->router->add('/contact', 'Contact', 'index');
        $this->router->add('/blog', 'Blog', 'index');
        $this->router->add('/blog/*', 'Blog', 'category', ['category']);
        $this->router->add('/blog/*/*', 'Blog', 'detail', ['category', 'slug']);
        $this->router->add('/products', 'Products', 'index');
        $this->router->add('/products/*', 'Products', 'category', ['category']);
        $this->router->add('/products/*/*', 'Products', 'detail', ['category', 'slug']);
        $this->router->add('/services', 'Services', 'index');
        $this->router->add('/services/*', 'Services', 'detail', ['slug']);
        $this->router->add('/projects', 'Projects', 'index');
        $this->router->add('/projects/*', 'Projects', 'detail', ['slug']);
        $this->router->add('/page/*', 'Page', 'show', ['slug']);
    }

    /**
     * Run the app: match route, then run that route's handler (one file per route, no clutter).
     * If route has handler -> include pages/{handler}.php (theme override then deamon). Else dispatch to module.
     */
    public function run(): void
    {
        $match = $this->router->match();
        if ($match === null) {
            $this->render404();
            return;
        }
        $route = $match['route'];
        $params = $match['params'];

        if (!empty($route['handler'])) {
            $handler = str_replace(['.', '::'], '/', $route['handler']) . '.php';
            $themePath = $this->config->getThemePath();
            $deamonPath = $this->config->getDeamonPath();
            $paths = $themePath ? [$themePath . '/pages/' . $handler, $deamonPath . '/pages/' . $handler] : [$deamonPath . '/pages/' . $handler];
            $found = null;
            foreach ($paths as $p) {
                if (is_file($p)) {
                    $found = $p;
                    break;
                }
            }
            if ($found) {
                $app = $this;
                require $found;
                return;
            }
        }

        if (!empty($route['module']) && !empty($route['action'])) {
            $class = 'Codron\\Deamon\\Modules\\' . $route['module'] . '\\' . $route['module'] . 'Module';
            if (class_exists($class)) {
                /** @var ModuleInterface $handler */
                $handler = new $class($this);
                $handler->handle($route['action'], $params);
                return;
            }
        }

        $this->render404();
    }

    private function render404(): void
    {
        http_response_code(404);
        echo $this->getTwig()->render('404.html.twig', ['title' => 'Not found']);
    }
}
