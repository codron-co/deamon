<?php
$category = $params['category'] ?? '';
$slug = $params['slug'] ?? '';
echo $app->getTwig()->render('products/detail.html.twig', ['title' => 'Products', 'category' => $category, 'slug' => $slug]);
