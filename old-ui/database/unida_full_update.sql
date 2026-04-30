-- =========================================================
-- UNIDA Gateway Full Policy, Permissions, Dashboards,
-- Limitations, FAQ and Verification Tracking Update
-- =========================================================

CREATE TABLE IF NOT EXISTS roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_key VARCHAR(80) NOT NULL UNIQUE,
    role_name VARCHAR(160) NOT NULL,
    role_type ENUM('public_user','admin') NOT NULL DEFAULT 'public_user',
    description TEXT NULL,
    is_system TINYINT(1) NOT NULL DEFAULT 1,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS permissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    permission_key VARCHAR(120) NOT NULL UNIQUE,
    permission_group VARCHAR(120) NOT NULL,
    permission_label VARCHAR(180) NOT NULL,
    description TEXT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS role_permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id INT UNSIGNED NOT NULL,
    permission_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_role_permission (role_id, permission_id),
    CONSTRAINT fk_role_permissions_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    CONSTRAINT fk_role_permissions_permission FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user_permission_overrides (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    permission_id INT UNSIGNED NOT NULL,
    effect ENUM('allow','deny') NOT NULL DEFAULT 'allow',
    reason VARCHAR(255) NULL,
    created_by INT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_permission_override (user_id, permission_id),
    CONSTRAINT fk_user_permission_overrides_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_user_permission_overrides_permission FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    CONSTRAINT fk_user_permission_overrides_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS dashboard_registry (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    dashboard_key VARCHAR(120) NOT NULL UNIQUE,
    dashboard_name VARCHAR(180) NOT NULL,
    role_key VARCHAR(80) NOT NULL,
    dashboard_url VARCHAR(255) NOT NULL,
    description TEXT NULL,
    status ENUM('active','planned','disabled') NOT NULL DEFAULT 'active',
    display_order INT UNSIGNED NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_dashboard_role (role_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS legal_documents (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    document_key VARCHAR(80) NOT NULL,
    title VARCHAR(180) NOT NULL,
    version VARCHAR(50) NOT NULL DEFAULT '1.0',
    content LONGTEXT NOT NULL,
    effective_date DATE NULL,
    status ENUM('draft','active','archived') NOT NULL DEFAULT 'active',
    reviewed_by VARCHAR(180) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_legal_document_version (document_key, version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS platform_limitations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    limitation_key VARCHAR(120) NOT NULL UNIQUE,
    title VARCHAR(180) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(120) NULL,
    severity ENUM('info','important','restricted') NOT NULL DEFAULT 'info',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    display_order INT UNSIGNED NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS faq_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(255) NOT NULL,
    answer TEXT NOT NULL,
    category VARCHAR(120) NOT NULL DEFAULT 'General',
    audience ENUM('all','business','investor','stakeholder','admin') NOT NULL DEFAULT 'all',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    display_order INT UNSIGNED NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS verification_process_steps (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    step_key VARCHAR(120) NOT NULL UNIQUE,
    step_name VARCHAR(180) NOT NULL,
    description TEXT NULL,
    applies_to ENUM('business','investor','stakeholder','group','all') NOT NULL DEFAULT 'all',
    required_status VARCHAR(80) NULL,
    display_order INT UNSIGNED NOT NULL DEFAULT 1,
    is_required TINYINT(1) NOT NULL DEFAULT 1,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS verification_tracks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    user_role ENUM('business','investor','stakeholder','admin') NOT NULL,
    current_status ENUM('unverified','submitted','pending','under_review','needs_update','approved','verified','rejected','expired','suspended') NOT NULL DEFAULT 'unverified',
    completion_percent TINYINT UNSIGNED NOT NULL DEFAULT 0,
    due_date DATETIME NULL,
    submitted_at DATETIME NULL,
    reviewed_at DATETIME NULL,
    verified_at DATETIME NULL,
    rejected_at DATETIME NULL,
    expired_at DATETIME NULL,
    assigned_admin_id INT UNSIGNED NULL,
    admin_notes TEXT NULL,
    user_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_verification_track_user (user_id),
    INDEX idx_verification_tracks_status (current_status),
    INDEX idx_verification_tracks_role (user_role),
    CONSTRAINT fk_verification_tracks_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_verification_tracks_admin FOREIGN KEY (assigned_admin_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS verification_track_events (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    track_id BIGINT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    event_key VARCHAR(120) NOT NULL,
    event_title VARCHAR(180) NOT NULL,
    event_description TEXT NULL,
    old_status VARCHAR(80) NULL,
    new_status VARCHAR(80) NULL,
    created_by INT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_verification_events_track (track_id),
    CONSTRAINT fk_verification_events_track FOREIGN KEY (track_id) REFERENCES verification_tracks(id) ON DELETE CASCADE,
    CONSTRAINT fk_verification_events_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_verification_events_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO roles (role_key, role_name, role_type, description, is_system, is_active)
VALUES
('business', 'Business / SME / Startup', 'public_user', 'Businesses, SMEs and startups seeking readiness, funding, partnership or support.', 1, 1),
('investor', 'Investor', 'public_user', 'Individual, angel, VC, bank, corporate, foundation or development partner seeking verified opportunities.', 1, 1),
('stakeholder', 'Stakeholder', 'public_user', 'Government, NGO, hub, university, bank, corporate, agency or institution supporting the ecosystem.', 1, 1),
('SUPER_ADMIN', 'Super Admin', 'admin', 'Full system access and protected platform ownership role.', 1, 1),
('ADMIN', 'Admin', 'admin', 'General platform administration role.', 1, 1),
('VERIFICATION_ADMIN', 'Verification Admin', 'admin', 'Manages verification requests, documents, approvals, rejections and update requests.', 1, 1),
('SUPPORT_ADMIN', 'Support Admin', 'admin', 'Manages user support, messages and account issues.', 1, 1),
('FINANCE_ADMIN', 'Finance Admin', 'admin', 'Manages finance, funding and payment-related records.', 1, 1),
('CONTENT_ADMIN', 'Content Admin', 'admin', 'Manages insights, updates, newsletters and public content.', 1, 1),
('PARTNERSHIP_ADMIN', 'Partnership Admin', 'admin', 'Manages stakeholders, partners, recommendations and follow-ups.', 1, 1),
('ANALYTICS_ADMIN', 'Analytics Admin', 'admin', 'Views analytics, metrics, reports and ecosystem insights.', 1, 1)
ON DUPLICATE KEY UPDATE
role_name = VALUES(role_name),
role_type = VALUES(role_type),
description = VALUES(description),
is_active = VALUES(is_active);

INSERT INTO permissions (permission_key, permission_group, permission_label, description)
VALUES
('dashboard.view', 'Dashboard', 'View own dashboard', 'Allows user to view the dashboard assigned to their role.'),
('profile.manage', 'Profile', 'Manage own profile', 'Allows user to create and update their own profile.'),
('documents.upload', 'Documents', 'Upload documents', 'Allows user to upload verification and profile documents.'),
('verification.submit', 'Verification', 'Submit verification', 'Allows user to submit profile for verification review.'),
('verification.track', 'Verification', 'Track verification progress', 'Allows user to view real-time verification progress.'),
('opportunities.view_public', 'Opportunities', 'View public opportunities', 'Allows user to view non-sensitive opportunity summaries.'),
('opportunities.view_restricted', 'Opportunities', 'View restricted opportunities', 'Allows verified users to view restricted opportunity details.'),
('opportunities.manage_own', 'Opportunities', 'Manage own opportunities', 'Allows business users to manage their own opportunities.'),
('investor.shortlist', 'Investor', 'Manage shortlist', 'Allows investors to shortlist verified opportunities.'),
('investor.request_meeting', 'Investor', 'Request meeting', 'Allows investors to request meetings or more information.'),
('stakeholder.review_businesses', 'Stakeholder', 'Review businesses', 'Allows stakeholders to review businesses for support or referral.'),
('stakeholder.recommend', 'Stakeholder', 'Send recommendation', 'Allows stakeholders to send recommendations or referrals.'),
('admin.users.manage', 'Admin', 'Manage users', 'Allows admin to manage users.'),
('admin.roles.manage', 'Admin', 'Manage roles and permissions', 'Allows admin to manage roles and permissions.'),
('admin.verification.manage', 'Admin', 'Manage verification', 'Allows admin to approve, reject or request updates.'),
('admin.finance.manage', 'Admin', 'Manage finance records', 'Allows admin to manage finance records.'),
('admin.content.manage', 'Admin', 'Manage content', 'Allows admin to publish content and insights.'),
('admin.partnership.manage', 'Admin', 'Manage partnerships', 'Allows admin to manage stakeholder relationships.'),
('admin.analytics.view', 'Admin', 'View analytics', 'Allows admin to view analytics and reports.'),
('admin.settings.manage', 'Admin', 'Manage settings', 'Allows admin to manage platform settings.')
ON DUPLICATE KEY UPDATE
permission_group = VALUES(permission_group),
permission_label = VALUES(permission_label),
description = VALUES(description),
is_active = 1;

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.permission_key IN (
    'dashboard.view','profile.manage','documents.upload','verification.submit','verification.track','opportunities.view_public','opportunities.manage_own'
)
WHERE r.role_key = 'business'
ON DUPLICATE KEY UPDATE role_id = role_id;

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.permission_key IN (
    'dashboard.view','profile.manage','documents.upload','verification.submit','verification.track','opportunities.view_public','opportunities.view_restricted','investor.shortlist','investor.request_meeting'
)
WHERE r.role_key = 'investor'
ON DUPLICATE KEY UPDATE role_id = role_id;

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.permission_key IN (
    'dashboard.view','profile.manage','documents.upload','verification.submit','verification.track','opportunities.view_public','stakeholder.review_businesses','stakeholder.recommend'
)
WHERE r.role_key = 'stakeholder'
ON DUPLICATE KEY UPDATE role_id = role_id;

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p
WHERE r.role_key = 'SUPER_ADMIN'
ON DUPLICATE KEY UPDATE role_id = role_id;

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.permission_key IN (
    'dashboard.view','admin.users.manage','admin.verification.manage','admin.content.manage','admin.analytics.view'
)
WHERE r.role_key = 'ADMIN'
ON DUPLICATE KEY UPDATE role_id = role_id;

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.permission_key IN ('dashboard.view','admin.verification.manage')
WHERE r.role_key = 'VERIFICATION_ADMIN'
ON DUPLICATE KEY UPDATE role_id = role_id;

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.permission_key IN ('dashboard.view','admin.users.manage')
WHERE r.role_key = 'SUPPORT_ADMIN'
ON DUPLICATE KEY UPDATE role_id = role_id;

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.permission_key IN ('dashboard.view','admin.finance.manage','admin.analytics.view')
WHERE r.role_key = 'FINANCE_ADMIN'
ON DUPLICATE KEY UPDATE role_id = role_id;

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.permission_key IN ('dashboard.view','admin.content.manage')
WHERE r.role_key = 'CONTENT_ADMIN'
ON DUPLICATE KEY UPDATE role_id = role_id;

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.permission_key IN ('dashboard.view','admin.partnership.manage','admin.analytics.view')
WHERE r.role_key = 'PARTNERSHIP_ADMIN'
ON DUPLICATE KEY UPDATE role_id = role_id;

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.permission_key IN ('dashboard.view','admin.analytics.view')
WHERE r.role_key = 'ANALYTICS_ADMIN'
ON DUPLICATE KEY UPDATE role_id = role_id;

INSERT INTO dashboard_registry (dashboard_key, dashboard_name, role_key, dashboard_url, description, status, display_order)
VALUES
('business_dashboard', 'Business Dashboard', 'business', 'business/dashboard.php', 'Business readiness, documents, verification and opportunities workspace.', 'active', 1),
('investor_dashboard', 'Investor Dashboard', 'investor', 'investor/dashboard.php', 'Investor preferences, verified opportunities, shortlist and meeting workspace.', 'active', 2),
('stakeholder_dashboard', 'Stakeholder Dashboard', 'stakeholder', 'stakeholder/dashboard.php', 'Stakeholder support coverage, recommendations, connections and reports workspace.', 'active', 3),
('admin_dashboard', 'Admin Dashboard', 'ADMIN', 'admin/dashboards/admin.php', 'General administration workspace.', 'active', 4),
('super_admin_dashboard', 'Super Admin Dashboard', 'SUPER_ADMIN', 'admin/dashboards/super-admin.php', 'Full platform owner and system control workspace.', 'active', 5),
('verification_admin_dashboard', 'Verification Admin Dashboard', 'VERIFICATION_ADMIN', 'admin/dashboards/verification-admin.php', 'Verification workflow and document review workspace.', 'active', 6),
('support_admin_dashboard', 'Support Admin Dashboard', 'SUPPORT_ADMIN', 'admin/dashboards/support-admin.php', 'User support and messages workspace.', 'active', 7),
('finance_admin_dashboard', 'Finance Admin Dashboard', 'FINANCE_ADMIN', 'admin/dashboards/finance-admin.php', 'Finance, funding and future payments workspace.', 'active', 8),
('content_admin_dashboard', 'Content Admin Dashboard', 'CONTENT_ADMIN', 'admin/dashboards/content-admin.php', 'Insights, newsletters and content management workspace.', 'active', 9),
('partnership_admin_dashboard', 'Partnership Admin Dashboard', 'PARTNERSHIP_ADMIN', 'admin/dashboards/partnership-admin.php', 'Stakeholder coordination and partnership management workspace.', 'active', 10),
('analytics_admin_dashboard', 'Analytics Admin Dashboard', 'ANALYTICS_ADMIN', 'admin/dashboards/analytics-admin.php', 'Data, analytics and reports workspace.', 'active', 11)
ON DUPLICATE KEY UPDATE
dashboard_name = VALUES(dashboard_name),
role_key = VALUES(role_key),
dashboard_url = VALUES(dashboard_url),
description = VALUES(description),
status = VALUES(status),
display_order = VALUES(display_order);

INSERT INTO verification_process_steps (step_key, step_name, description, applies_to, display_order, is_required)
VALUES
('account_created', 'Account Created', 'User account has been created and role selected.', 'all', 1, 1),
('profile_started', 'Profile Started', 'User has started completing role-specific profile information.', 'all', 2, 1),
('profile_completed', 'Profile Completed', 'Required profile sections are completed.', 'all', 3, 1),
('documents_uploaded', 'Documents Uploaded', 'Required identity, mandate, registration or authorization documents are uploaded.', 'all', 4, 1),
('submitted_for_review', 'Submitted for Review', 'User has submitted profile and documents for verification review.', 'all', 5, 1),
('admin_review', 'Admin Review', 'Authorized admin is reviewing the profile and documents.', 'all', 6, 1),
('decision_issued', 'Decision Issued', 'Admin decision issued: approved, needs update, rejected or verified.', 'all', 7, 1),
('workspace_access', 'Workspace Access', 'Verified or approved user receives access to eligible restricted features.', 'all', 8, 1)
ON DUPLICATE KEY UPDATE
step_name = VALUES(step_name),
description = VALUES(description),
applies_to = VALUES(applies_to),
display_order = VALUES(display_order),
is_required = VALUES(is_required);

INSERT INTO platform_limitations (limitation_key, title, description, category, severity, display_order)
VALUES
('no_investment_guarantee', 'No Investment Guarantee', 'UNIDA Gateway supports verification, discovery and coordination, but does not guarantee funding, investment, partnership, contract or profit.', 'Investment', 'important', 1),
('verification_required', 'Verification Required', 'Restricted features may require completed profile, required documents and approval by authorized administrators.', 'Access', 'restricted', 2),
('accurate_information', 'Accurate Information Required', 'Users are responsible for providing true, authorized and updated information.', 'User Responsibility', 'important', 3),
('document_review_time', 'Review Time May Vary', 'Verification review time may depend on completeness of documents, user category and admin workload.', 'Verification', 'info', 4),
('seven_day_rule', 'Seven-Day Submission Rule', 'Users who start verification should complete required submissions within seven days. Expired submissions may require restart or resubmission.', 'Verification', 'important', 5),
('third_party_decisions', 'Third-Party Decisions', 'Investors, partners, banks, government agencies and stakeholders make their own independent decisions.', 'Partnership', 'important', 6)
ON DUPLICATE KEY UPDATE
title = VALUES(title),
description = VALUES(description),
category = VALUES(category),
severity = VALUES(severity),
display_order = VALUES(display_order),
is_active = 1;

INSERT INTO faq_items (question, answer, category, audience, display_order)
VALUES
('What is UNIDA Gateway?', 'UNIDA Gateway is an investment ecosystem platform for verification, business readiness, stakeholder coordination and data-driven opportunity access.', 'General', 'all', 1),
('Who can create an account?', 'Businesses, SMEs, startups, investors and stakeholders can create accounts. Admin accounts are created internally by authorized administrators.', 'Accounts', 'all', 2),
('Does UNIDA Gateway guarantee investment?', 'No. The platform supports discovery, verification and coordination, but does not guarantee funding, partnership or investment approval.', 'Investment', 'all', 3),
('Why do I need verification?', 'Verification helps protect users, improve trust and ensure sensitive opportunity information is accessed by eligible users.', 'Verification', 'all', 4),
('What documents may be required?', 'Depending on account type, documents may include representative ID, business registration, license, TIN, investor mandate, organization registration or authorization letter.', 'Documents', 'all', 5),
('How long do I have to complete verification?', 'Users should complete required verification submissions within seven days after starting the process.', 'Verification', 'all', 6),
('Can groups or special categories join?', 'Yes. Groups may be asked for a WEO, VEO, institutional or legal confirmation letter depending on their category.', 'Groups', 'business', 7),
('Can investors view all opportunities?', 'Investors may view restricted opportunity details only after completing investor profile and verification requirements.', 'Investor', 'investor', 8),
('Can stakeholders recommend businesses?', 'Verified stakeholders can review eligible businesses and send recommendations, referrals or support connections.', 'Stakeholder', 'stakeholder', 9),
('Where do I track verification progress?', 'Logged-in users can open Verification Track to see their current verification stage, status and next action.', 'Verification', 'all', 10)
ON DUPLICATE KEY UPDATE
answer = VALUES(answer),
category = VALUES(category),
audience = VALUES(audience),
display_order = VALUES(display_order),
is_active = 1;

INSERT INTO legal_documents (document_key, title, version, content, effective_date, status)
VALUES
('privacy', 'Privacy Policy', '1.0', 'UNIDA Gateway collects account, profile, verification, document, role and activity information to provide secure platform access, verification, readiness, stakeholder coordination and ecosystem insights. Sensitive information should be accessed only by authorized users according to role and verification status.', CURDATE(), 'active'),
('terms', 'Terms of Use', '1.0', 'Users must be 18 years or older, provide accurate information, use authorized documents, respect platform rules and understand that UNIDA Gateway does not guarantee investment, funding, partnership, contract or profit.', CURDATE(), 'active'),
('limitations', 'Platform Limitations', '1.0', 'UNIDA Gateway supports verification, discovery and coordination, but all investment, partnership and institutional decisions are made independently by relevant third parties.', CURDATE(), 'active'),
('verification_policy', 'Verification Policy', '1.0', 'Users may be required to complete profile information, upload documents and submit verification within seven days. Statuses may include unverified, submitted, under review, needs update, approved, verified, rejected, expired or suspended.', CURDATE(), 'active')
ON DUPLICATE KEY UPDATE
content = VALUES(content),
effective_date = VALUES(effective_date),
status = VALUES(status);
