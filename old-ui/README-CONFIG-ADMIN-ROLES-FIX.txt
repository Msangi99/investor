UNIDA Config Admin Role Fix Pack

This fixes the issue where config.php only understands users.role = admin/business/investor/stakeholder
but does not understand admin sub-roles such as:
SUPER_ADMIN, VERIFICATION_ADMIN, SUPPORT_ADMIN, FINANCE_ADMIN, CONTENT_ADMIN, PARTNERSHIP_ADMIN, ANALYTICS_ADMIN.

Install:
1. Upload fix_config_admin_roles.php to project root.
2. Open:
   https://investoraccess.unidatechs.com/fix_config_admin_roles.php
3. Delete after success:
   fix_config_admin_roles.php

It updates:
- includes/config.php
- login.php
- admin/dashboard.php

Then:
- login again as admin@unidatechs.com
- open /admin/dashboard.php
