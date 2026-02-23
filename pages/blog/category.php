<?php
$category = $params['category'] ?? '';
echo $app->getTwig()->render('blog/category.html.twig', ['title' => 'Blog', 'category' => $category]);
