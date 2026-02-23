<?php

declare(strict_types=1);

namespace Codron\Deamon\Modules\Products;

use Codron\Deamon\App;
use Codron\Deamon\ModuleInterface;

final class ProductsModule implements ModuleInterface
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
            echo $this->app->getTwig()->render('products/index.html.twig', ['title' => 'Products']);
            return;
        }
        if ($action === 'category') {
            echo $this->app->getTwig()->render('products/category.html.twig', [
                'title' => 'Products',
                'category' => $params['category'] ?? '',
            ]);
            return;
        }
        if ($action === 'detail') {
            echo $this->app->getTwig()->render('products/detail.html.twig', [
                'title' => 'Products',
                'category' => $params['category'] ?? '',
                'slug' => $params['slug'] ?? '',
            ]);
            return;
        }
    }
}
