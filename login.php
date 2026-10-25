<?php
require 'includes/bootstrap.php';

if (Auth::user()) go(Auth::role() === 'admin' ? 'admin/index.php' : 'index.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = Auth::login($_POST['email'] ?? '', $_POST['password'] ?? '');
    if (!$error) go(Auth::role() === 'admin' ? 'admin/index.php' : 'index.php');
}

$title = 'Login';
$css = ['login'];
require 'includes/header.php';
?>

<section class="auth">
    <div class="auth-photo" style="background-image: linear-gradient(rgba(14,15,18,.55), rgba(14,15,18,.85)), url('https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=1200&q=70')">
        <h2>Welcome <span>back</span></h2>
        <p>Every rep counts. Log in and keep going.</p>
    </div>
    <div class="auth-form">
        <form method="post" class="card auth-card">
            <h2>Login</h2>
            <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?= e($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button class="btn btn-block">Login</button>
            <p class="switch">New here? <a href="register.php">Create an account</a></p>
        </form>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
