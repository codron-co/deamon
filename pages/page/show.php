<?php
$slug = $params['slug'] ?? '';
if ($slug === '') {
    http_response_code(404);
    echo $app->getTwig()->render('404.html.twig', ['title' => 'Not found']);
    return;
}
$pdo = $app->getDatabase()->getPdo();
$stmt = $pdo->prepare('SELECT id, slug, title, content, meta_title, meta_description FROM site_pages WHERE site_id = ? AND slug = ? AND active = 1 LIMIT 1');
$stmt->execute([$app->getConfig()->getSiteId(), $slug]);
$row = $stmt->fetch(\PDO::FETCH_ASSOC);
if (!$row) {
    http_response_code(404);
    echo $app->getTwig()->render('404.html.twig', ['title' => 'Not found']);
    return;
}
echo $app->getTwig()->render('page/show.html.twig', [
    'title' => $row['meta_title'] ?: $row['title'],
    'page' => $row,
]);
