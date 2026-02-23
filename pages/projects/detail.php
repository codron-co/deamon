<?php
$slug = $params['slug'] ?? '';
echo $app->getTwig()->render('projects/detail.html.twig', ['title' => 'Projects', 'slug' => $slug]);
