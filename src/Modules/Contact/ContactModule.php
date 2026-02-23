<?php

declare(strict_types=1);

namespace Codron\Deamon\Modules\Contact;

use Codron\Deamon\App;
use Codron\Deamon\ModuleInterface;

final class ContactModule implements ModuleInterface
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
        echo $this->app->getTwig()->render('contact.html.twig', [
            'title' => 'Contact',
        ]);
    }
}
