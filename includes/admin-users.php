<?php
// Shared by admin/members.php and admin/trainers.php. Expects $role ('member' | 'trainer').
$self = basename($_SERVER['SCRIPT_NAME']);
$users = new User();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $users->toggleActive((int)$_POST['user_id'], $role);
    Auth::flash('Account status updated.');
    go($self . (!empty($_GET['q']) ? '?q=' . urlencode($_GET['q']) : ''));
}

$q = trim($_GET['q'] ?? '');
$rows = $users->listByRole($role, $q);
?>

<form method="get" class="search">
    <input type="search" name="q" placeholder="Search by name or email" value="<?= e($q) ?>">
    <button class="btn">Search</button>
</form>

<div class="table-wrap reveal">
    <table>
        <thead>
            <tr>
                <th>Name</th><th>Email</th><th>Phone</th>
                <?= $role === 'trainer' ? '<th>Specialization</th>' : '' ?>
                <th><?= $role === 'member' ? 'Bookings' : 'Classes' ?></th><th>Joined</th><th>Status</th><th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $u): ?>
            <tr>
                <td><div class="who"><span class="avatar sm"><?= e(initials($u['full_name'])) ?></span><?= e($u['full_name']) ?></div></td>
                <td><?= e($u['email']) ?></td>
                <td><?= e($u['phone']) ?></td>
                <?= $role === 'trainer' ? '<td>' . e($u['spec']) . '</td>' : '' ?>
                <td><?= $u['total'] ?></td>
                <td><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
                <td><span class="badge <?= $u['is_active'] ? 'badge-success' : 'badge-danger' ?>"><?= $u['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                        <button class="btn btn-sm <?= $u['is_active'] ? 'btn-outline' : 'btn-success' ?>"><?= $u['is_active'] ? 'Deactivate' : 'Activate' ?></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="8" class="empty">Nobody found.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
