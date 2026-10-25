<?php
require 'includes/bootstrap.php';
Auth::requireRole('trainer');

$id = (int)($_GET['id'] ?? 0);
$c  = (new GymClass())->findForTrainer($id, Auth::id());
if (!$c) { Auth::flash('Class not found.', 'error'); go('my-classes.php'); }

$q       = trim($_GET['q'] ?? '');
$members = (new Booking())->members($id, $q);

$title = 'Class Bookings';
$css = ['class-bookings'];
require 'includes/header.php';
?>

<div class="container">
    <div class="page-head">
        <p class="crumb"><a href="my-classes.php">My Classes</a> › <?= e($c['title']) ?></p>
        <h1><?= e($c['title']) ?></h1>
    </div>

    <div class="card summary reveal">
        <div><span>Date</span><?= fmt_date($c['class_date']) ?></div>
        <div><span>Time</span><?= fmt_time($c['start_time']) ?> – <?= fmt_time($c['end_time']) ?></div>
        <div><span>Booked</span><?= $c['booked'] ?> / <?= $c['capacity'] ?></div>
    </div>

    <form method="get" class="search">
        <input type="hidden" name="id" value="<?= $id ?>">
        <input type="search" name="q" placeholder="Search by name or email" value="<?= e($q) ?>">
        <button class="btn">Search</button>
    </form>

    <?php if (!$members): ?>
        <div class="card empty">No bookings found.</div>
    <?php else: ?>
        <div class="table-wrap reveal">
            <table>
                <thead><tr><th>#</th><th>Member</th><th>Email</th><th>Phone</th><th>Booked at</th></tr></thead>
                <tbody>
                <?php foreach ($members as $i => $m): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><div class="who"><span class="avatar sm"><?= e(initials($m['full_name'])) ?></span><?= e($m['full_name']) ?></div></td>
                        <td><?= e($m['email']) ?></td>
                        <td><?= e($m['phone']) ?></td>
                        <td><?= date('M j, g:i A', strtotime($m['booked_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require 'includes/footer.php'; ?>
