<?php

declare(strict_types=1);

namespace Codron\Deamon\Modules\Projects;

use Codron\Deamon\App;
use Codron\Deamon\ModuleInterface;

final class ProjectsModule implements ModuleInterface
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
            echo $this->app->getTwig()->render('projects/index.html.twig', ['title' => 'Projects']);
            return;
        }
        if ($action === 'detail') {
            echo $this->app->getTwig()->render('projects/detail.html.twig', [
                'title' => 'Projects',
                'slug' => $params['slug'] ?? '',
            ]);
            return;
        }
    }
}
