<?php

declare(strict_types=1);

namespace Codron\Deamon\Modules\Home;

use Codron\Deamon\App;
use Codron\Deamon\ModuleInterface;

final class HomeModule implements ModuleInterface
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
        if ($action !== 'index') {
            return;
        }
        echo $this->app->getTwig()->render('home.html.twig', [
            'title' => 'Home',
        ]);
    }
}
