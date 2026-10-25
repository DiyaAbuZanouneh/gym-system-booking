<?php
// Members, trainers and admins (table: users + trainer_details).
class User extends Model
{
    public function find(int $id): ?array
    {
        return $this->db->fetch("SELECT * FROM users WHERE id = ?", [$id]);
    }

    public function findByEmail(string $email): ?array
    {
        return $this->db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
    }

    public function emailTaken(string $email, int $exceptId = 0): bool
    {
        return $this->db->value("SELECT COUNT(*) FROM users WHERE email = ? AND id <> ?", [$email, $exceptId]) > 0;
    }

    /** Returns an error message or null. Used by register.php. */
    public function validateRegistration(array $in): ?string
    {
        if (trim($in['full_name'] ?? '') === '' || !filter_var(trim($in['email'] ?? ''), FILTER_VALIDATE_EMAIL)) {
            return 'Please enter your name and a valid email.';
        }
        if (strlen($in['password'] ?? '') < 6)             return 'Password must be at least 6 characters.';
        if (($in['password'] ?? '') !== ($in['confirm'] ?? '')) return 'Passwords do not match.';
        if ($this->emailTaken(trim($in['email'])))         return 'This email is already registered.';
        return null;
    }

    /** Creates a member or trainer account and returns the new id. */
    public function register(array $in): int
    {
        $role = ($in['role'] ?? '') === 'trainer' ? 'trainer' : 'member';
        $this->db->query(
            "INSERT INTO users (full_name, email, password_hash, phone, role) VALUES (?,?,?,?,?)",
            [trim($in['full_name']), trim($in['email']), password_hash($in['password'], PASSWORD_BCRYPT), trim($in['phone'] ?? ''), $role]
        );
        $id = $this->db->lastId();
        if ($role === 'trainer') {
            $this->saveTrainerDetails($id, trim($in['specialization'] ?? ''), '', (int)($in['experience_years'] ?? 0));
        }
        return $id;
    }

    public function updateProfile(int $id, array $in): void
    {
        $gender = in_array($in['gender'] ?? '', ['male', 'female']) ? $in['gender'] : null;
        $this->db->query(
            "UPDATE users SET full_name=?, email=?, phone=?, gender=?, date_of_birth=? WHERE id=?",
            [trim($in['full_name']), trim($in['email']), trim($in['phone'] ?? ''), $gender, ($in['date_of_birth'] ?? '') ?: null, $id]
        );
    }

    public function updatePassword(int $id, string $password): void
    {
        $this->db->query("UPDATE users SET password_hash = ? WHERE id = ?", [password_hash($password, PASSWORD_BCRYPT), $id]);
    }

    public function trainerDetails(int $id): array
    {
        return $this->db->fetch("SELECT * FROM trainer_details WHERE user_id = ?", [$id]) ?: [];
    }

    public function saveTrainerDetails(int $id, string $spec, string $bio, int $years): void
    {
        $this->db->query(
            "INSERT INTO trainer_details (user_id, specialization, bio, experience_years) VALUES (?,?,?,?)
             ON DUPLICATE KEY UPDATE specialization = VALUES(specialization), bio = VALUES(bio), experience_years = VALUES(experience_years)",
            [$id, $spec, $bio, $years]
        );
    }

    /** Admin lists. 'total' = bookings (member) or classes (trainer). */
    public function listByRole(string $role, string $search = ''): array
    {
        $total = $role === 'member'
            ? "(SELECT COUNT(*) FROM bookings b WHERE b.member_id = u.id) AS total, '' AS spec"
            : "(SELECT COUNT(*) FROM classes c WHERE c.trainer_id = u.id) AS total, COALESCE(td.specialization,'') AS spec";
        return $this->db->fetchAll(
            "SELECT u.*, $total FROM users u LEFT JOIN trainer_details td ON td.user_id = u.id
             WHERE u.role = ? AND (u.full_name LIKE ? OR u.email LIKE ?) ORDER BY u.created_at DESC",
            [$role, "%$search%", "%$search%"]
        );
    }

    public function toggleActive(int $id, string $role): void
    {
        $this->db->query("UPDATE users SET is_active = 1 - is_active WHERE id = ? AND role = ?", [$id, $role]);
    }

    public function latest(int $limit = 5): array
    {
        return $this->db->fetchAll("SELECT full_name, role, created_at FROM users WHERE role <> 'admin' ORDER BY created_at DESC, id DESC LIMIT " . (int)$limit);
    }
}
