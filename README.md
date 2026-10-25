# PulseFit (PHP + MySQL, no JavaScript)

1. Start MySQL (XAMPP) and import `database/database.sql` in phpMyAdmin.
2. Run the site from the project folder:
   `C:\xampp\php\php.exe -S localhost:8000`  → open http://localhost:8000
   (or copy the folder into `C:\xampp\htdocs` and use Apache)
3. DB credentials: `includes/db.php` (default root / empty password).

Sample accounts (password for all: `123456`):
- admin@gym.com (admin) · omar@gym.com (trainer) · ali@gym.com (member)
- hadi@gym.com is a deactivated member.

Structure: root = public/member/trainer pages · `admin/` = admin pages · `includes/` = db, helpers, header/footer · `css/` = `base.css` (shared theme) + one CSS file per page.
