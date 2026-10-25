<?php
// Session handling: login state, role protection and flash messages.
class Auth
{
    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?int
    {
        return self::user()['id'] ?? null;
    }

    public static function role(): ?string
    {
        return self::user()['role'] ?? null;
    }

    /** Returns an error message, or null when the login succeeded. */
    public static function login(string $email, string $password): ?string
    {
        $u = (new User())->findByEmail(trim($email));

        if (!$u || !password_verify($password, $u['password_hash'])) {
            return 'Wrong email or password.';
        }
        if (!$u['is_active']) {
            return 'Your account has been deactivated. Contact the gym.';
        }
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => $u['id'], 'name' => $u['full_name'], 'role' => $u['role']];
        return null;
    }

    public static function logout(): void
    {
        session_destroy();
    }

    public static function setName(string $name): void
    {
        $_SESSION['user']['name'] = $name;
    }

    /** Redirect to login unless the visitor has one of the given roles. */
    public static function requireRole(string ...$roles): void
    {
        if (!in_array(self::role(), $roles, true)) {
            go(BASE . '/login.php');
        }
    }

    public static function flash(string $msg, string $type = 'success'): void
    {
        $_SESSION['flash'] = [$msg, $type];
    }

    public static function pullFlash(): ?array
    {
        $f = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $f;
    }
}
