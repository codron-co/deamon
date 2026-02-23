<?php

declare(strict_types=1);

namespace Codron\Deamon\Modules\Page;

use Codron\Deamon\App;
use Codron\Deamon\ModuleInterface;

/**
 * Dynamic pages from DB (created in panel). No repo access needed.
 */
final class PageModule implements ModuleInterface
{
    /** @var App */
    private $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /** @param array<string, string> $params */
    public function handle(string $action, array $params): void
    {
        $slug = $params['slug'] ?? '';
        if ($slug === '') {
            return;
        }
        $pdo = $this->app->getDatabase()->getPdo();
        $stmt = $pdo->prepare('SELECT id, slug, title, content, meta_title, meta_description FROM site_pages WHERE site_id = ? AND slug = ? AND active = 1 LIMIT 1');
        $stmt->execute([$this->app->getConfig()->getSiteId(), $slug]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$row) {
            http_response_code(404);
            echo $this->app->getTwig()->render('404.html.twig', ['title' => 'Not found']);
            return;
        }
        echo $this->app->getTwig()->render('page/show.html.twig', [
            'title' => $row['meta_title'] ?: $row['title'],
            'page' => $row,
        ]);
    }
}
