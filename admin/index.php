<?php
require '../includes/bootstrap.php';
Auth::requireRole('admin');

$stats   = (new Stats())->summary();
$latest  = (new User())->latest(5);
$top     = (new GymClass())->topBooked(5);
$reviews = (new Review())->latest(4);

$title = 'Dashboard';
$active = 'dashboard';
$css = ['admin-dashboard'];
require '../includes/admin-header.php';

$cards = [
    ['Members', $stats['members_active'] + $stats['members_inactive'], $stats['members_active'] . ' active · ' . $stats['members_inactive'] . ' inactive'],
    ['Trainers', $stats['trainers'], 'Coaches on the team'],
    ['Upcoming classes', $stats['upcoming'], 'Scheduled from today'],
    ['Bookings', $stats['bookings'], 'All time'],
    ['Pending reviews', $stats['pending'], 'Waiting for approval'],
];
?>

<div class="stat-grid">
    <?php foreach ($cards as $i => [$label, $num, $sub]): ?>
        <div class="card stat reveal" style="animation-delay: <?= $i * .08 ?>s">
            <span><?= $label ?></span>
            <strong><?= $num ?></strong>
            <small><?= $sub ?></small>
        </div>
    <?php endforeach; ?>
</div>

<div class="widgets">
    <section class="card reveal">
        <h3>Latest members</h3>
        <ul class="list">
            <?php foreach ($latest as $u): ?>
                <li><span class="avatar sm"><?= e(initials($u['full_name'])) ?></span>
                    <div><strong><?= e($u['full_name']) ?></strong><br><small><?= ucfirst($u['role']) ?> · <?= date('M j', strtotime($u['created_at'])) ?></small></div>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section class="card reveal d1">
        <h3>Most booked classes</h3>
        <ul class="list">
            <?php foreach ($top as $t): $pct = min(100, round($t['n'] / $t['capacity'] * 100)); ?>
                <li class="bar-item">
                    <div class="bar-label"><span><?= e($t['title']) ?></span><small><?= $t['n'] ?>/<?= $t['capacity'] ?></small></div>
                    <div class="bar"><i style="width: <?= $pct ?>%"></i></div>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section class="card reveal d2">
        <h3>Latest reviews</h3>
        <ul class="list">
            <?php foreach ($reviews as $r): ?>
                <li class="review-item">
                    <div><strong><?= e($r['full_name']) ?></strong> <span class="stars"><?= stars($r['rating']) ?></span>
                        <span class="badge <?= $r['is_visible'] ? 'badge-success' : 'badge-warning' ?>"><?= $r['is_visible'] ? 'Visible' : 'Pending' ?></span>
                        <p><?= e(mb_strimwidth($r['comment'], 0, 70, '…')) ?></p></div>
                </li>
            <?php endforeach; ?>
        </ul>
        <a href="reviews.php" class="btn btn-outline btn-sm">Manage reviews</a>
    </section>
</div>

<?php require '../includes/admin-footer.php'; ?>
