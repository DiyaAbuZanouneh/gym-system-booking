<?php
require '../includes/bootstrap.php';
Auth::requireRole('admin');

$model = new Review();
$tab   = $_GET['tab'] ?? 'all';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rid = (int)$_POST['review_id'];
    if (($_POST['do'] ?? '') === 'delete') {
        $model->delete($rid);
        Auth::flash('Review deleted.');
    } else {
        $model->toggleVisible($rid);
        Auth::flash('Review visibility updated.');
    }
    go('reviews.php?tab=' . urlencode($tab));
}

$reviews = $model->list($tab);

$title = 'Reviews';
$active = 'reviews';
$css = ['admin-reviews'];
require '../includes/admin-header.php';
?>

<p class="note">Only visible reviews appear on the Home page.</p>
<div class="tabs">
    <?php foreach (['all' => 'All', 'pending' => 'Pending', 'visible' => 'Visible'] as $k => $label): ?>
        <a href="?tab=<?= $k ?>" class="<?= $tab === $k ? 'active' : '' ?>"><?= $label ?></a>
    <?php endforeach; ?>
</div>

<div class="table-wrap reviews-table reveal">
    <table>
        <thead><tr><th>Author</th><th>Rating</th><th>Comment</th><th>Date</th><th>Status</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($reviews as $r): ?>
            <tr>
                <td><strong><?= e($r['full_name']) ?></strong><br><span class="badge badge-accent"><?= ucfirst($r['role']) ?></span></td>
                <td class="stars"><?= stars($r['rating']) ?></td>
                <td class="comment"><?= e($r['comment']) ?></td>
                <td><?= date('M j, Y', strtotime($r['created_at'])) ?></td>
                <td><span class="badge <?= $r['is_visible'] ? 'badge-success' : 'badge-warning' ?>"><?= $r['is_visible'] ? 'Visible' : 'Hidden' ?></span></td>
                <td>
                    <div class="actions">
                        <form method="post">
                            <input type="hidden" name="review_id" value="<?= $r['id'] ?>">
                            <button class="btn btn-sm <?= $r['is_visible'] ? 'btn-outline' : 'btn-success' ?>"><?= $r['is_visible'] ? 'Hide' : 'Show on Home' ?></button>
                        </form>
                        <details class="confirm">
                            <summary class="btn btn-danger btn-sm">Delete</summary>
                            <form method="post" class="confirm-box">
                                <p>Delete this review permanently?</p>
                                <input type="hidden" name="do" value="delete">
                                <input type="hidden" name="review_id" value="<?= $r['id'] ?>">
                                <button class="btn btn-danger btn-sm">Yes, delete</button>
                            </form>
                        </details>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$reviews): ?><tr><td colspan="6" class="empty">No reviews.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>

<?php require '../includes/admin-footer.php'; ?>
