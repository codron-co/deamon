<?php

declare(strict_types=1);

namespace Codron\Deamon\Modules\Services;

use Codron\Deamon\App;
use Codron\Deamon\ModuleInterface;

final class ServicesModule implements ModuleInterface
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
            echo $this->app->getTwig()->render('services/index.html.twig', ['title' => 'Services']);
            return;
        }
        if ($action === 'detail') {
            echo $this->app->getTwig()->render('services/detail.html.twig', [
                'title' => 'Services',
                'slug' => $params['slug'] ?? '',
            ]);
            return;
        }
    }
}
