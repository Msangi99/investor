@extends('layouts.dashboard')

@section('content')
" href="admin/users.php"><i class="fa-solid fa-users"></i> Users</a>' . "\n";

            if (strpos($sidebar, '<a') !== false) {
                $sidebar = preg_replace('/(<a[^>]+>.*?Dashboard.*?<\/a>)/s', '$1' . "\n" . $insert, $sidebar, 1);
                if (strpos($sidebar, 'admin/users.php') === false) {
                    $sidebar .= "\n" . $insert;
                }
            } else {
                $sidebar .= "\n" . $insert;
            }

            @copy($sidebarPath, $sidebarPath . '.backup-' . date('Ymd-His'));
            $ok = file_put_contents($sidebarPath, $sidebar) !== false;
            add_result('includes/sidebar.php', $ok, $ok ? 'Users link added' : 'failed');
        } else {
            add_result('includes/sidebar.php', true, 'Users link already exists');
        }
    } else {
        add_result('includes/sidebar.php', false, 'sidebar.php not found; admin/users.php still works');
    }
} catch (Throwable $e) {
    add_result('includes/sidebar.php', false, $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>UNIDA Admin Users Installer</title>
<style>
body{font-family:Arial,sans-serif;background:#f2f6f9;color:#1d2939;padding:30px}
.box{max-width:1120px;margin:auto;background:#fff;border-radius:18px;padding:24px;box-shadow:0 18px 44px rgba(8,59,122,.12)}
h1{color:#083B7A}.ok{color:#0E7C6B;font-weight:800}.fail{color:#991b1b;font-weight:800}
li{padding:8px 0;border-bottom:1px solid #eef2f6}code{background:#eef7fb;padding:3px 6px;border-radius:6px}
.note{background:#f8fcff;border-left:5px solid #0A5DB7;padding:14px;border-radius:12px;margin-top:18px}
.warn{background:#fff5f5;border-left:5px solid #dc2626;padding:14px;border-radius:12px;margin-top:18px}
</style>
</head>
<body>
<div class="box">
<h1>UNIDA Admin Users Installer</h1>
<ul>
<?php foreach ($results as $row): ?>
<li><span class="<?= $row[1] ? 'ok' : 'fail'; ?>"><?= $row[1] ? 'OK' : 'FAILED'; ?></span> <code><?= htmlspecialchars($row[0], ENT_QUOTES, 'UTF-8'); ?></code> <?= htmlspecialchars($row[2], ENT_QUOTES, 'UTF-8'); ?></li>
<?php endforeach; ?>
</ul>
<div class="note">
<strong>Created page:</strong> <code>admin/users.php</code><br>
<strong>Also fixed:</strong> missing <code>admin_profiles.last_activity_at</code> column.
</div>
<div class="warn"><strong>Important:</strong> Delete <code>install_admin_users.php</code> after success.</div>
</div>
</body>
</html>
@endsection
