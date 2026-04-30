UNIDA Safe Reset Super Admin Login

This fixes:
FAILED installer error SQLSTATE[42S01]: Base table or view already exists: 1050 Table 'users' already exists

Upload:
reset_superadmin_login_safe.php

Open:
https://investoraccess.unidatechs.com/reset_superadmin_login_safe.php

Expected OK:
- users table done/already exists
- password_verify test hash matches pass123456Mama
- admin profile set to SUPER_ADMIN

Delete after success:
reset_superadmin_login_safe.php
