<?php
// Expects: $title, $css (array), $active (menu key). Admin only.
Auth::requireRole('admin');
$css = array_merge(['admin'], $css ?? []);
$menu = [
    'dashboard' => ['Dashboard', 'index.php'],
    'members'   => ['Members',   'members.php'],
    'trainers'  => ['Trainers',  'trainers.php'],
    'classes'   => ['Classes',   'classes.php'],
    'reviews'   => ['Reviews',   'reviews.php'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> | PulseFit Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Oswald:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE ?>/css/base.css">
    <?php foreach ($css as $file): ?>
    <link rel="stylesheet" href="<?= BASE ?>/css/<?= e($file) ?>.css">
    <?php endforeach; ?>
</head>
<body class="admin-body">

<aside class="sidebar">
    <a href="<?= BASE ?>/admin/index.php" class="logo">PULSE<span>FIT</span></a>
    <nav>
        <?php foreach ($menu as $key => [$label, $file]): ?>
            <a href="<?= BASE ?>/admin/<?= $file ?>" class="<?= $active === $key ? 'active' : '' ?>"><?= $label ?></a>
        <?php endforeach; ?>
        <a href="<?= BASE ?>/logout.php" class="logout">Logout</a>
    </nav>
</aside>

<main class="admin-main">
    <div class="admin-top">
        <h1><?= e($title) ?></h1>
        <span class="admin-user"><?= e(Auth::user()['name']) ?></span>
    </div>

    <?php if ($flash = Auth::pullFlash()): ?>
        <div class="toast toast-<?= e($flash[1]) ?>"><?= e($flash[0]) ?></div>
    <?php endif; ?>
