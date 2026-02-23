<?php
$category = $params['category'] ?? '';
$slug = $params['slug'] ?? '';
echo $app->getTwig()->render('blog/detail.html.twig', ['title' => 'Blog', 'category' => $category, 'slug' => $slug]);
