# Dynamic Portfolio (PHP + MySQL)

Full-stack portfolio with public site, admin dashboard, and user dashboard.
Built with PHP 8.2, MySQL, Bootstrap 5, and PDO prepared statements.

## Requirements
- WAMP (Apache + MySQL) or equivalent
- PHP >= 8.2 with PDO MySQL extension
- MySQL / MariaDB 5.7+

## Install
1. Copy project to `www/portfolio`.
2. Import `database/portfolio_db.sql` via phpMyAdmin.
3. Edit `config/database.php` ($user/$pass if non-default).
4. Edit `config/config.php` — set `BASE_URL`.
5. Visit `http://localhost/portfolio/`.

## Demo credentials
- Admin: `admin@example.com` / `Admin@12345` — **change immediately**.
- Create users via `/register.php`.

## First-time checklist
- [ ] Log in as admin → Settings → change password
- [ ] Content → set owner name, title, bio, contacts
- [ ] Projects → add your first real project (thumbnail + gallery)
- [ ] Services → enable/edit what you offer
- [ ] Categories → verify slugs
- [ ] Delete or replace demo projects & testimonials

## Troubleshooting
| Symptom | Fix |
|---|---|
| "Database connection failed" | Check MySQL is running (green WAMP icon), check creds in `config/database.php`, ensure `portfolio_db` exists |
| CSS/JS 404 | Confirm `BASE_URL` ends with `/` and matches your folder name |
| Upload rejected | File > 5 MB or not jpg/png/webp/gif |
| 403 on admin | You are not logged in as `role=admin` |
| Reset link invalid | Link expired (1h) or already used — request again |
| Blank page | Enable `display_errors` in `php.ini` temporarily or check `apache_error.log` in WAMP |
| "Call to undefined function" | Missing `require` — ensure every file starts with the correct `includes/auth.php` or `_init.php` |
| Password reset link shows nothing | On production, wire `mail()` in `forgot-password.php`; dev mode prints the link on screen |

## Security notes
- Passwords: `password_hash()` / `password_verify()`.
- All mutations: CSRF token verified.
- All queries: prepared statements via PDO.
- Admin pages: `require_admin()` at the top of every file.
- User data: every query filtered by `user_id`.
- Uploads: MIME-checked, size-limited, randomized names, PHP execution disabled.