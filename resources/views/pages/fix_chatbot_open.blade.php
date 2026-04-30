@extends('layouts.guest')

@section('content')
<link rel="stylesheet" href="<?= e(asset_url('css/rieta-chatbot.css')); ?>">

<script>
    window.UNIDA_BASE_URL = "";
    window.UNIDA_LANG = "<?= function_exists('current_language') ? e(current_language()) : 'en'; ?>";
</script>

<script src="<?= e(asset_url('js/rieta-chatbot.js')); ?>"></script>
<!-- UNIDA_CHATBOT_LANGUAGE_END -->
PHPBLOCK;

        $mainScript = '<script src="<?= e(asset_url(\'js/main.js\')); ?>"></script>';

        if (strpos($footer, $mainScript) !== false) {
            $footer = str_replace($mainScript, $block . "\n\n" . $mainScript, $footer);
        } elseif (strpos($footer, '</body>') !== false) {
            $footer = str_replace('</body>', $block . "\n</body>", $footer);
        } else {
            $footer .= "\n" . $block . "\n";
        }

        @copy($footerPath, $footerPath . '.backup-' . date('Ymd-His'));
        $ok = file_put_contents($footerPath, $footer) !== false;
        add_result('includes/footer.php', $ok, $ok ? 'old duplicate blocks removed and clean chatbot block added' : 'failed');
    } else {
        add_result('includes/footer.php', false, 'footer.php not found');
    }
} catch (Throwable $e) {
    add_result('includes/footer.php', false, $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Chatbot Open Fix</title>
<style>
body{font-family:Arial,sans-serif;background:#f2f6f9;color:#1d2939;padding:30px}
.box{max-width:980px;margin:auto;background:#fff;border-radius:18px;padding:24px;box-shadow:0 18px 44px rgba(8,59,122,.12)}
.ok{color:#0E7C6B;font-weight:800}.fail{color:#991b1b;font-weight:800}
li{padding:8px 0;border-bottom:1px solid #eef2f6}code{background:#eef7fb;padding:3px 6px;border-radius:6px}
.warn{background:#fff5f5;border-left:5px solid #dc2626;padding:14px;border-radius:12px;margin-top:18px}
</style>
</head>
<body>
<div class="box">
<h1>Chatbot Open Fix Result</h1>
<ul>
<?php foreach ($results as $row): ?>
<li><span class="<?= $row[1] ? 'ok' : 'fail'; ?>"><?= $row[1] ? 'OK' : 'FAILED'; ?></span> <code><?= htmlspecialchars($row[0], ENT_QUOTES, 'UTF-8'); ?></code> <?= htmlspecialchars($row[2], ENT_QUOTES, 'UTF-8'); ?></li>
<?php endforeach; ?>
</ul>
<div class="warn"><strong>Important:</strong> Delete <code>fix_chatbot_open.php</code> after success.</div>
</div>
</body>
</html>
@endsection
