<?php
$slug = $params['slug'] ?? '';
echo $app->getTwig()->render('services/detail.html.twig', ['title' => 'Services', 'slug' => $slug]);
