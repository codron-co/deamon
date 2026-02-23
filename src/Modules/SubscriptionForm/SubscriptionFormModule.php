<?php

declare(strict_types=1);

namespace Codron\Deamon\Modules\SubscriptionForm;

use Codron\Deamon\App;
use Codron\Deamon\ModuleInterface;

/**
 * Newsletter / subscription form. Usually used as a block (e.g. footer).
 * POST to e.g. /api/subscribe handled separately in router or API layer.
 */
final class SubscriptionFormModule implements ModuleInterface
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
        // Renders only a fragment; full page not used. Included by layout/footer.
        echo $this->app->getTwig()->render('subscription_form/form.html.twig', [
            'title' => 'Subscribe',
        ]);
    }
}
