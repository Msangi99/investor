UNIDA Gateway Auth + Roles Pack

Files:
- login.php
- register.php
- database/auth_roles_patch.sql
- tools/create_super_admin.php

What changed:
1. Login redirects by role and uses admin role router where available.
2. Register allows only:
   - business
   - investor
   - stakeholder
   Admin accounts are created internally only.
3. Register records Terms/Privacy/Data consent when columns/tables exist.
4. Business profile starts as unverified.
5. Investor and stakeholder profiles start as incomplete.
6. Password fields support password toggle if main.js exists.
7. CSRF protection is used if helpers are installed.
8. Super admin creator generates a real password_hash and assigns SUPER_ADMIN permissions.

How to install:
1. Upload login.php and register.php to project root.
2. Upload database/auth_roles_patch.sql and run it in phpMyAdmin if needed.
3. Upload tools/create_super_admin.php.
4. Edit tools/create_super_admin.php:
   - SETUP_ENABLED = true
   - set full_name, email, phone, password
5. Open:
   https://yourdomain.com/tools/create_super_admin.php
6. Delete tools/create_super_admin.php immediately after success.
