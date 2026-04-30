<?php
if (!function_exists('verification_statuses')) {
    function verification_statuses() {
        return ['unverified', 'submitted', 'pending', 'under_review', 'approved', 'verified', 'needs_update', 'rejected', 'expired', 'suspended'];
    }
}
