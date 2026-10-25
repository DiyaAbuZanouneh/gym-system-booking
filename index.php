<?php
require 'includes/bootstrap.php';

$user = Auth::user();

// Submit a gym review (members & trainers only). It stays hidden until admin approves.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user && in_array($user['role'], ['member', 'trainer'])) {
    $saved = (new Review())->create($user['id'], (int)($_POST['rating'] ?? 0), trim($_POST['comment'] ?? ''));
    if ($saved) Auth::flash('Thanks! Your review will appear after admin approval.');
    else        Auth::flash('Please choose a rating and write a comment.', 'error');
    go('index.php#reviews');
}

$features = (new Catalog())->features();
$reviews  = (new Review())->visible(6);

$title = 'Home';
$css = ['home'];
require 'includes/header.php';
?>

<section class="hero">
    <div class="container hero-content">
        <p class="hero-tag">Welcome to PulseFit</p>
        <h1>Train harder.<br><span>Live stronger.</span></h1>
        <p class="hero-text">Modern equipment, expert trainers and classes for every level. Your best shape starts today.</p>
        <?php if (!$user): ?>
            <a href="register.php" class="btn">Join Now</a>
            <a href="#features" class="btn btn-outline">Explore</a>
        <?php else: ?>
            <a href="<?= $user['role'] === 'member' ? 'classes.php' : 'my-classes.php' ?>" class="btn">
                <?= $user['role'] === 'member' ? 'Browse Classes' : 'My Classes' ?>
            </a>
        <?php endif; ?>
    </div>
</section>

<section class="section" id="about">
    <div class="container about-grid">
        <img class="about-img reveal" src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=900&q=70" alt="Inside the gym">
        <div class="reveal d1">
            <h2>About <span>Us</span></h2>
            <p>PulseFit is more than a gym. We are a community built around energy, discipline and results.
               Whether you are lifting for the first time or training for a competition, our space, coaches
               and classes are designed to keep you moving forward.</p>
            <div class="stats">
                <div><strong>1200+</strong><span>Members</span></div>
                <div><strong>25</strong><span>Coaches</span></div>
                <div><strong>60+</strong><span>Weekly classes</span></div>
            </div>
        </div>
    </div>
</section>

<section class="section alt" id="features">
    <div class="container">
        <h2 class="section-title reveal">Why <span>PulseFit</span></h2>
        <div class="features-grid">
            <?php foreach ($features as $f): ?>
                <article class="card hover feature reveal">
                    <div class="feature-img"><img src="<?= e($f['image']) ?>" alt="<?= e($f['title']) ?>" loading="lazy"></div>
                    <h3><?= e($f['title']) ?></h3>
                    <p><?= e($f['description']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section" id="reviews">
    <div class="container">
        <h2 class="section-title reveal">What people <span>say</span></h2>
        <?php if (!$reviews): ?>
            <p class="empty">No reviews yet.</p>
        <?php endif; ?>
        <div class="reviews-grid">
            <?php foreach ($reviews as $r): ?>
                <article class="card hover review reveal">
                    <div class="stars"><?= stars($r['rating']) ?></div>
                    <p>“<?= e($r['comment']) ?>”</p>
                    <div class="review-author">
                        <span class="avatar"><?= e(initials($r['full_name'])) ?></span>
                        <div><strong><?= e($r['full_name']) ?></strong><br><span class="badge badge-accent"><?= ucfirst($r['role']) ?></span></div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if ($user && in_array($user['role'], ['member', 'trainer'])): ?>
            <form method="post" class="card review-form reveal">
                <h3>Share your experience</h3>
                <div class="form-group">
                    <label for="rating">Rating</label>
                    <select name="rating" id="rating" required>
                        <option value="">Choose...</option>
                        <?php for ($i = 5; $i >= 1; $i--): ?><option value="<?= $i ?>"><?= stars($i) ?></option><?php endfor; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="comment">Your review</label>
                    <textarea name="comment" id="comment" maxlength="1000" required></textarea>
                </div>
                <p class="hint">Your review will appear after admin approval.</p>
                <button class="btn">Submit</button>
            </form>
        <?php elseif (!$user): ?>
            <p class="login-hint reveal"><a href="login.php">Log in</a> to share your experience.</p>
        <?php endif; ?>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
