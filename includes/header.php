<?php
// Expects: $title (string), $css (array of css file names, without .css)
$user = Auth::user();
$css = $css ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'PulseFit') ?> | PulseFit</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Oswald:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE ?>/css/base.css">
    <?php foreach ($css as $file): ?>
    <link rel="stylesheet" href="<?= BASE ?>/css/<?= e($file) ?>.css">
    <?php endforeach; ?>
</head>
<body>

<header class="navbar">
    <div class="container nav-inner">
        <a href="<?= BASE ?>/index.php" class="logo">PULSE<span>FIT</span></a>

        <!-- CSS-only mobile menu: checkbox + label -->
        <input type="checkbox" id="nav-toggle" class="nav-toggle">
        <label for="nav-toggle" class="burger" aria-label="Menu"><i></i><i></i><i></i></label>

        <nav class="nav-links">
            <a href="<?= BASE ?>/index.php">Home</a>
            <a href="<?= BASE ?>/index.php#about">About</a>
            <a href="<?= BASE ?>/index.php#features">Features</a>
            <a href="<?= BASE ?>/index.php#reviews">Reviews</a>
            <?php if (!$user): ?>
                <a href="<?= BASE ?>/index.php#contact">Contact</a>
                <a href="<?= BASE ?>/login.php" class="btn btn-outline btn-sm">Login</a>
                <a href="<?= BASE ?>/register.php" class="btn btn-sm">Register</a>
            <?php else: ?>
                <?php if ($user['role'] === 'member'): ?>
                    <a href="<?= BASE ?>/classes.php">Classes</a>
                <?php else: ?>
                    <a href="<?= BASE ?>/my-classes.php">My Classes</a>
                <?php endif; ?>
                <a href="<?= BASE ?>/profile.php">Profile</a>
                <a href="<?= BASE ?>/logout.php" class="btn btn-outline btn-sm">Logout</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<?php if ($flash = Auth::pullFlash()): ?>
    <div class="toast toast-<?= e($flash[1]) ?>"><?= e($flash[0]) ?></div>
<?php endif; ?>
