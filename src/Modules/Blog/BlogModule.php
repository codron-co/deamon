<?php

declare(strict_types=1);

namespace Codron\Deamon\Modules\Blog;

use Codron\Deamon\App;
use Codron\Deamon\ModuleInterface;

final class BlogModule implements ModuleInterface
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
        if ($action === 'index') {
            echo $this->app->getTwig()->render('blog/index.html.twig', ['title' => 'Blog']);
            return;
        }
        if ($action === 'category') {
            echo $this->app->getTwig()->render('blog/category.html.twig', [
                'title' => 'Blog',
                'category' => $params['category'] ?? '',
            ]);
            return;
        }
        if ($action === 'detail') {
            echo $this->app->getTwig()->render('blog/detail.html.twig', [
                'title' => 'Blog',
                'category' => $params['category'] ?? '',
                'slug' => $params['slug'] ?? '',
            ]);
            return;
        }
    }
}
