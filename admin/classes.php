<?php
require '../includes/bootstrap.php';
Auth::requireRole('admin');

$model = new GymClass();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $model->delete((int)$_POST['class_id']);
    Auth::flash('Class deleted.');
    go('classes.php');
}

$classes = $model->all();

$title = 'Classes';
$active = 'classes';
$css = ['admin-classes'];
require '../includes/admin-header.php';
?>

<div class="table-wrap classes-table reveal">
    <table>
        <thead><tr><th>Title</th><th>Type</th><th>Trainer</th><th>Date</th><th>Time</th><th>Bookings</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($classes as $c): ?>
            <tr>
                <td><strong><?= e($c['title']) ?></strong></td>
                <td><?= e($c['type_name']) ?></td>
                <td><?= e($c['trainer_name']) ?></td>
                <td><?= date('M j, Y', strtotime($c['class_date'])) ?></td>
                <td><?= fmt_time($c['start_time']) ?> – <?= fmt_time($c['end_time']) ?></td>
                <td><?= $c['booked'] ?>/<?= $c['capacity'] ?></td>
                <td>
                    <details class="confirm">
                        <summary class="btn btn-danger btn-sm">Delete</summary>
                        <form method="post" class="confirm-box">
                            <p>This will remove the class from the trainer and from all booked members.</p>
                            <input type="hidden" name="class_id" value="<?= $c['id'] ?>">
                            <button class="btn btn-danger btn-sm">Yes, delete</button>
                        </form>
                    </details>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require '../includes/admin-footer.php'; ?>
