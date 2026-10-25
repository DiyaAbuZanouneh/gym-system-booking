<?php
require 'includes/bootstrap.php';
Auth::requireRole('trainer');

$uid     = Auth::id();
$classes = new GymClass();
$errors  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['do'] ?? '') === 'delete') {
        $classes->delete((int)$_POST['class_id'], $uid);
        Auth::flash('Class deleted.');
        go('my-classes.php');
    }

    $data = [
        'title'         => trim($_POST['title'] ?? ''),
        'class_type_id' => (int)($_POST['class_type_id'] ?? 0),
        'description'   => trim($_POST['description'] ?? ''),
        'image'         => trim($_POST['image'] ?? ''),
        'class_date'    => $_POST['class_date'] ?? '',
        'start_time'    => $_POST['start_time'] ?? '',
        'end_time'      => $_POST['end_time'] ?? '',
        'capacity'      => (int)($_POST['capacity'] ?? 0),
        'room'          => trim($_POST['room'] ?? ''),
    ];
    $errors = $classes->validate($data, $uid);
    if (!$errors) {
        $classes->create($uid, $data);
        Auth::flash('Class created.');
        go('my-classes.php');
    }
}

$tab     = ($_GET['tab'] ?? 'upcoming') === 'past' ? 'past' : 'upcoming';
$list    = $classes->forTrainer($uid, $tab === 'upcoming');
$types   = (new Catalog())->classTypes();

$title = 'My Classes';
$css = ['my-classes'];
require 'includes/header.php';
?>

<div class="container">
    <div class="page-head head-row">
        <h1>My <span class="accent">Classes</span></h1>
    </div>

    <details class="card create" <?= $errors ? 'open' : '' ?>>
        <summary class="btn">+ Create Class</summary>
        <form method="post" class="create-form">
            <?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>
            <div class="form-row">
                <div class="form-group"><label>Title *</label><input name="title" value="<?= e($_POST['title'] ?? '') ?>" required></div>
                <div class="form-group"><label>Class type *</label>
                    <select name="class_type_id" required>
                        <?php foreach ($types as $t): ?>
                            <option value="<?= $t['id'] ?>" <?= ($_POST['class_type_id'] ?? '') == $t['id'] ? 'selected' : '' ?>><?= e($t['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group"><label>Description</label><textarea name="description"><?= e($_POST['description'] ?? '') ?></textarea></div>
            <div class="form-group"><label>Image URL (optional)</label><input type="url" name="image" value="<?= e($_POST['image'] ?? '') ?>" placeholder="https://..."></div>
            <div class="form-row">
                <div class="form-group"><label>Date *</label><input type="date" name="class_date" min="<?= date('Y-m-d') ?>" value="<?= e($_POST['class_date'] ?? '') ?>" required></div>
                <div class="form-group"><label>Room</label><input name="room" value="<?= e($_POST['room'] ?? '') ?>"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Start time *</label><input type="time" name="start_time" value="<?= e($_POST['start_time'] ?? '') ?>" required></div>
                <div class="form-group"><label>End time *</label><input type="time" name="end_time" value="<?= e($_POST['end_time'] ?? '') ?>" required></div>
            </div>
            <div class="form-group"><label>Capacity *</label><input type="number" min="1" name="capacity" value="<?= e($_POST['capacity'] ?? '') ?>" required></div>
            <button class="btn">Create</button>
        </form>
    </details>

    <div class="tabs">
        <a href="?tab=upcoming" class="<?= $tab === 'upcoming' ? 'active' : '' ?>">Upcoming</a>
        <a href="?tab=past" class="<?= $tab === 'past' ? 'active' : '' ?>">Past</a>
    </div>

    <?php if (!$list): ?>
        <div class="card empty">No <?= $tab ?> classes.</div>
    <?php else: ?>
        <div class="table-wrap my-table">
            <table>
                <thead><tr><th>Title</th><th>Type</th><th>Date</th><th>Time</th><th>Room</th><th>Bookings</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($list as $c): $pct = min(100, round($c['booked'] / $c['capacity'] * 100)); ?>
                    <tr>
                        <td><strong><?= e($c['title']) ?></strong></td>
                        <td><?= e($c['type_name']) ?></td>
                        <td><?= fmt_date($c['class_date']) ?></td>
                        <td><?= fmt_time($c['start_time']) ?> – <?= fmt_time($c['end_time']) ?></td>
                        <td><?= e($c['room']) ?></td>
                        <td><?= $c['booked'] ?>/<?= $c['capacity'] ?><div class="mini"><i style="width: <?= $pct ?>%"></i></div></td>
                        <td>
                            <div class="actions">
                                <a href="class-bookings.php?id=<?= $c['id'] ?>" class="btn btn-outline btn-sm">View Bookings</a>
                                <details class="confirm">
                                    <summary class="btn btn-danger btn-sm">Delete</summary>
                                    <form method="post" class="confirm-box">
                                        <p>Deleting this class will also remove all <?= $c['booked'] ?> bookings. This cannot be undone.</p>
                                        <input type="hidden" name="do" value="delete">
                                        <input type="hidden" name="class_id" value="<?= $c['id'] ?>">
                                        <button class="btn btn-danger btn-sm">Yes, delete</button>
                                    </form>
                                </details>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require 'includes/footer.php'; ?>
