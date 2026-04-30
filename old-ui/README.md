# UNIDA Gateway
## Investment Ecosystem Platform

UNIDA Gateway is a role-based product platform for:

- UNIDA Invest
- UNIDA Verify
- UNIDA Readiness
- UNIDA Partners
- UNIDA Insights

Public navbar does not expose dashboards. Users create an account or login first, then the system redirects them to the correct workspace:

- business/dashboard.php
- investor/dashboard.php
- stakeholder/dashboard.php
- admin/dashboard.php

Important:
- Set the real database password directly in includes/config.php on the server.
- Do not commit or share the database password publicly.
- Admin accounts should be created manually in the database, not through public registration.
