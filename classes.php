<?php
require 'includes/bootstrap.php';
Auth::requireRole('member');

$type = (int)($_GET['type'] ?? 0);
$date = $_GET['date'] ?? '';
$tod  = $_GET['tod'] ?? '';

$types   = (new Catalog())->classTypes();
$classes = (new GymClass())->search($type, $date, $tod);
$booked  = (new Booking())->classIdsFor(Auth::id());

$title = 'Classes';
$css = ['classes'];
require 'includes/header.php';
?>

<div class="container">
    <div class="page-head">
        <h1>Our <span class="accent">Classes</span></h1>
        <p>Pick a class, book your seat and show up ready.</p>
    </div>

    <form method="get" class="filter card">
        <div class="chips">
            <input type="radio" name="type" id="t0" value="0" <?= !$type ? 'checked' : '' ?>><label for="t0">All</label>
            <?php foreach ($types as $t): ?>
                <input type="radio" name="type" id="t<?= $t['id'] ?>" value="<?= $t['id'] ?>" <?= $type == $t['id'] ? 'checked' : '' ?>>
                <label for="t<?= $t['id'] ?>"><?= e($t['name']) ?></label>
            <?php endforeach; ?>
        </div>
        <div class="filter-row">
            <input type="date" name="date" value="<?= e($date) ?>" min="<?= date('Y-m-d') ?>">
            <select name="tod">
                <option value="">Any time</option>
                <option value="morning"   <?= $tod === 'morning' ? 'selected' : '' ?>>Morning</option>
                <option value="afternoon" <?= $tod === 'afternoon' ? 'selected' : '' ?>>Afternoon</option>
                <option value="evening"   <?= $tod === 'evening' ? 'selected' : '' ?>>Evening</option>
            </select>
            <button class="btn">Filter</button>
            <a href="classes.php" class="btn btn-outline">Reset</a>
        </div>
    </form>

    <?php if (!$classes): ?>
        <div class="empty card">No classes match your filters.</div>
    <?php endif; ?>

    <div class="classes-grid">
        <?php foreach ($classes as $c):
            $full = $c['booked'] >= $c['capacity'];
            $isBooked = in_array($c['id'], $booked);
        ?>
            <article class="card hover class-card reveal <?= $full && !$isBooked ? 'is-full' : '' ?>">
                <div class="class-img"><img src="<?= e(class_image($c)) ?>" alt="<?= e($c['title']) ?>" loading="lazy"></div>
                <div class="class-body">
                    <div class="class-tags">
                        <span class="badge badge-accent"><?= e($c['type_name']) ?></span>
                        <?= $isBooked ? '<span class="badge badge-success">Booked</span>' : seats_badge($c) ?>
                    </div>
                    <h3><?= e($c['title']) ?></h3>
                    <p>with <?= e($c['trainer_name']) ?></p>
                    <p><?= fmt_date($c['class_date']) ?> · <?= fmt_time($c['start_time']) ?> – <?= fmt_time($c['end_time']) ?></p>
                    <p><?= $c['booked'] ?> / <?= $c['capacity'] ?> booked
                       <?php if ($c['avg_rating']): ?> · <span class="stars">★</span> <?= $c['avg_rating'] ?><?php endif; ?></p>
                    <a href="class-details.php?id=<?= $c['id'] ?>" class="btn btn-sm <?= $full && !$isBooked ? 'btn-outline' : '' ?>">
                        <?= $full && !$isBooked ? 'View only' : 'View Details' ?>
                    </a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
