<?php
$category = $params['category'] ?? '';
echo $app->getTwig()->render('products/category.html.twig', ['title' => 'Products', 'category' => $category]);
