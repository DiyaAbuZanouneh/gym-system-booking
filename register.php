?php
require 'includes/bootstrap.php';

if (Auth::user()) go('index.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$users = new User();
$error = $users->validateRegistration($_POST);
if (!$error) {
$users->register($_POST);
Auth::flash('Account created! You can log in now.');
go('login.php');
}
}

$selected = ($_POST['role'] ?? 'member');
$title = 'Register';
$css = ['login', 'register'];
require 'includes/header.php';
?>

<section class="auth">
    <div class="auth-photo"
        style="background-image: linear-gradient(rgba(14,15,18,.55), rgba(14,15,18,.85)), url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=1200&q=70')">
        <h2>Start your <span>journey</span></h2>
        <p>Join PulseFit as a member or as a trainer.</p>
    </div>
    <div class="auth-form">
        <form method="post" class="card auth-card">
            <h2>Register</h2>
            <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

            <!-- Account type toggle (CSS-only: radios placed before the trainer fields) -->
            <div class="role-toggle">
                <input type="radio" name="role" id="r-member" value="member"
                    <?= $selected !== 'trainer' ? 'checked' : '' ?>>
                <label for="r-member">Member</label>
                <input type="radio" name="role" id="r-trainer" value="trainer"
                    <?= $selected === 'trainer' ? 'checked' : '' ?>>
                <label for="r-trainer">Trainer</label>

                <div class="trainer-fields form-row">
                    <div class="form-group">
                        <label for="spec">Specialization</label>
                        <input type="text" name="specialization" id="spec"
                            value="<?= e($_POST['specialization'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="exp">Years of experience</label>
                        <input type="number" name="experience_years" id="exp" min="0" max="60"
                            value="<?= e($_POST['experience_years'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="name">Full name</label>
                <input type="text" name="full_name" id="name" value="<?= e($_POST['full_name'] ?? '') ?>" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="<?= e($_POST['email'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" name="phone" id="phone" value="<?= e($_POST['phone'] ?? '') ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm">Confirm password</label>
                    <input type="password" name="confirm" id="confirm" required>
                </div>
            </div>
            <button class="btn btn-block">Create account</button>
            <p class="switch">Already have an account? <a href="login.php">Login</a></p>
        </form>
    </div>
</section>

<?php require 'includes/footer.php'; ?>