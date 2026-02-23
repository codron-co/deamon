<?php

declare(strict_types=1);

namespace Codron\Deamon\Modules\About;

use Codron\Deamon\App;
use Codron\Deamon\ModuleInterface;

final class AboutModule implements ModuleInterface
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
        echo $this->app->getTwig()->render('about.html.twig', [
            'title' => 'About us',
        ]);
    }
}
