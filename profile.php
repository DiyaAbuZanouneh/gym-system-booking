<?php
require 'includes/bootstrap.php';
Auth::requireRole('member', 'trainer');

$uid   = Auth::id();
$role  = Auth::role();
$users = new User();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['do'] ?? 'save') === 'cancel' && $role === 'member') {
        (new Booking())->cancel((int)$_POST['class_id'], $uid);
        Auth::flash('Booking cancelled.');
        go('profile.php');
    }

    $email   = trim($_POST['email'] ?? '');
    $newPass = $_POST['password'] ?? '';

    if (trim($_POST['full_name'] ?? '') === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        Auth::flash('Please enter a valid name and email.', 'error');
    } elseif ($users->emailTaken($email, $uid)) {
        Auth::flash('That email is used by another account.', 'error');
    } elseif ($newPass !== '' && strlen($newPass) < 6) {
        Auth::flash('New password must be at least 6 characters.', 'error');
    } else {
        $users->updateProfile($uid, $_POST);
        if ($newPass !== '') $users->updatePassword($uid, $newPass);
        if ($role === 'trainer') {
            $users->saveTrainerDetails($uid, trim($_POST['specialization'] ?? ''), trim($_POST['bio'] ?? ''), (int)($_POST['experience_years'] ?? 0));
        }
        Auth::setName(trim($_POST['full_name']));
        Auth::flash('Profile updated.');
    }
    go('profile.php');
}

$u = $users->find($uid);
$t = $role === 'trainer' ? $users->trainerDetails($uid) : [];
$bookings = $role === 'member' ? (new GymClass())->forMember($uid) : [];

$title = 'Profile';
$css = ['profile'];
require 'includes/header.php';
?>

<div class="container profile">
    <div class="profile-grid">
        <aside class="card side reveal">
            <span class="avatar big"><?= e(initials($u['full_name'])) ?></span>
            <h3><?= e($u['full_name']) ?></h3>
            <p><?= e($u['email']) ?></p>
            <span class="badge badge-accent"><?= ucfirst($role) ?></span>
            <p class="since">Member since <?= date('M Y', strtotime($u['created_at'])) ?></p>
        </aside>

        <form method="post" class="card reveal d1">
            <h3>Edit info</h3>
            <input type="hidden" name="do" value="save">
            <div class="form-row">
                <div class="form-group"><label>Full name</label><input name="full_name" value="<?= e($u['full_name']) ?>" required></div>
                <div class="form-group"><label>Email</label><input type="email" name="email" value="<?= e($u['email']) ?>" required></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Phone</label><input name="phone" value="<?= e($u['phone']) ?>"></div>
                <div class="form-group"><label>Gender</label>
                    <select name="gender">
                        <option value="">-</option>
                        <option value="male" <?= $u['gender'] === 'male' ? 'selected' : '' ?>>Male</option>
                        <option value="female" <?= $u['gender'] === 'female' ? 'selected' : '' ?>>Female</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Date of birth</label><input type="date" name="date_of_birth" value="<?= e($u['date_of_birth']) ?>"></div>
                <div class="form-group"><label>New password (optional)</label><input type="password" name="password" placeholder="Leave empty to keep"></div>
            </div>
            <?php if ($role === 'trainer'): ?>
                <div class="form-row">
                    <div class="form-group"><label>Specialization</label><input name="specialization" value="<?= e($t['specialization'] ?? '') ?>"></div>
                    <div class="form-group"><label>Years of experience</label><input type="number" min="0" max="60" name="experience_years" value="<?= e($t['experience_years'] ?? '') ?>"></div>
                </div>
                <div class="form-group"><label>Bio</label><textarea name="bio"><?= e($t['bio'] ?? '') ?></textarea></div>
            <?php endif; ?>
            <button class="btn">Save changes</button>
        </form>
    </div>

    <?php if ($role === 'member'): ?>
        <section class="booked">
            <h2>My booked classes</h2>
            <?php if (!$bookings): ?>
                <div class="card empty">You haven't booked any classes yet.<br><br><a href="classes.php" class="btn btn-sm">Browse classes</a></div>
            <?php else: ?>
                <div class="table-wrap reveal">
                    <table>
                        <thead><tr><th>Class</th><th>Type</th><th>Trainer</th><th>When</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                        <?php foreach ($bookings as $b): $past = $b['class_date'] < date('Y-m-d'); ?>
                            <tr>
                                <td><a href="class-details.php?id=<?= $b['id'] ?>"><strong><?= e($b['title']) ?></strong></a></td>
                                <td><?= e($b['type_name']) ?></td>
                                <td><?= e($b['trainer_name']) ?></td>
                                <td><?= fmt_date($b['class_date']) ?> · <?= fmt_time($b['start_time']) ?></td>
                                <td><span class="badge <?= $past ? '' : 'badge-success' ?>"><?= $past ? 'Completed' : 'Upcoming' ?></span></td>
                                <td>
                                    <?php if (!$past): ?>
                                        <details class="confirm">
                                            <summary class="btn btn-danger btn-sm">Cancel</summary>
                                            <form method="post" class="confirm-box">
                                                <p>Cancel your booking for “<?= e($b['title']) ?>”?</p>
                                                <input type="hidden" name="do" value="cancel">
                                                <input type="hidden" name="class_id" value="<?= $b['id'] ?>">
                                                <button class="btn btn-danger btn-sm">Yes, cancel</button>
                                            </form>
                                        </details>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</div>

<?php require 'includes/footer.php'; ?>
