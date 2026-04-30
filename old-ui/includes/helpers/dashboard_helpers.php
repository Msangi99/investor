<?php
if (!function_exists('status_badge_class')) {
    function status_badge_class($status) {
        $status = strtolower((string) $status);

        if (in_array($status, ['verified', 'approved', 'active', 'completed', 'published'], true)) {
            return 'status-verified';
        }

        if (in_array($status, ['pending', 'submitted', 'under_review', 'needs_update'], true)) {
            return 'status-progress';
        }

        if (in_array($status, ['rejected', 'expired', 'suspended', 'inactive'], true)) {
            return 'status-danger';
        }

        return 'status-open';
    }
}
