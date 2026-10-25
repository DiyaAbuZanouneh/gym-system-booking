<?php
require 'includes/bootstrap.php';
Auth::requireRole('member');

$uid      = Auth::id();
$id       = (int)($_GET['id'] ?? 0);
$classes  = new GymClass();
$bookings = new Booking();

$c = $classes->find($id);
if (!$c) { Auth::flash('Class not found.', 'error'); go('classes.php'); }

$isBooked = $bookings->isBooked($id, $uid);
$isPast   = $c['class_date'] < date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['do'] ?? '') === 'book' && !$isBooked && !$isPast) {
    $error = $bookings->book($id, $uid);
    if ($error) Auth::flash($error, 'error');
    else        Auth::flash("You're booked!");
    go("class-details.php?id=$id");
}

$trainer = $classes->trainerSpecialization((int)$c['trainer_id']);
$full = $c['booked'] >= $c['capacity'];
$pct  = min(100, round($c['booked'] / $c['capacity'] * 100));

$title = $c['title'];
$css = ['class-details'];
require 'includes/header.php';
?>

<div class="container details">
    <a href="classes.php" class="back">← Back to classes</a>

    <div class="details-grid">
        <div class="cover reveal"><img src="<?= e(class_image($c)) ?>" alt="<?= e($c['title']) ?>"></div>

        <div class="card info reveal d1">
            <span class="badge badge-accent"><?= e($c['type_name']) ?></span>
            <h1><?= e($c['title']) ?></h1>

            <ul class="facts">
                <li><span>Trainer</span><?= e($c['trainer_name']) ?><?= $trainer ? ' · ' . e($trainer) : '' ?></li>
                <li><span>Date</span><?= fmt_date($c['class_date']) ?></li>
                <li><span>Time</span><?= fmt_time($c['start_time']) ?> – <?= fmt_time($c['end_time']) ?></li>
                <li><span>Room</span><?= e($c['room']) ?></li>
                <li><span>Capacity</span><?= $c['booked'] ?> / <?= $c['capacity'] ?></li>
            </ul>
            <div class="bar"><i style="width: <?= $pct ?>%"></i></div>

            <form method="post">
                <input type="hidden" name="do" value="book">
                <?php if ($isBooked): ?>
                    <button type="button" class="btn btn-success btn-block" disabled style="opacity:.8">Already Booked ✓</button>
                <?php elseif ($isPast): ?>
                    <button type="button" class="btn btn-block" disabled>Class ended</button>
                <?php elseif ($full): ?>
                    <button type="button" class="btn btn-danger btn-block" disabled style="opacity:.7">Class Full</button>
                <?php else: ?>
                    <button class="btn btn-block">Book Class</button>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="card reveal about-class">
        <h3>About this class</h3>
        <p><?= nl2br(e($c['description'])) ?></p>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
