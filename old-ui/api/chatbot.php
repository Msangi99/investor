<?php
/**
 * UNIDA Gateway Unny Chatbot API
 * Default language: Kiswahili
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function unny_table_exists($table) {
    try {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
            return false;
        }

        $stmt = db()->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);

        return (bool) $stmt->fetch();
    } catch (Throwable $e) {
        return false;
    }
}

function unny_language($lang, $message = '') {
    $lang = strtolower(trim((string) $lang));

    if (in_array($lang, ['sw', 'en'], true)) {
        return $lang;
    }

    $text = mb_strtolower($message);
    $swHints = ['habari', 'msaada', 'naomba', 'tafadhali', 'jinsi', 'kujisajili', 'ingia', 'uhakiki', 'nifanye', 'nataka', 'fursa', 'biashara', 'mwekezaji', 'mdau'];

    foreach ($swHints as $hint) {
        if (str_contains($text, $hint)) {
            return 'sw';
        }
    }

    return $_SESSION['lang'] ?? 'sw';
}

function unny_sw_reply($text) {
    if ($text === '') {
        return 'Tafadhali andika swali lako. Naweza kukusaidia kuhusu kujisajili, kuingia, uhakiki, dashboards, fursa na support.';
    }

    if (str_contains($text, 'habari') || str_contains($text, 'hello') || str_contains($text, 'hi')) {
        return 'Habari, karibu UNIDA Gateway. Mimi ni Unny. Unaweza kuniuliza kuhusu kujisajili, login, uhakiki, dashboards, fursa au support.';
    }

    if (str_contains($text, 'jisajili') || str_contains($text, 'register') || str_contains($text, 'akaunti') || str_contains($text, 'account')) {
        return 'Ili kujisajili, bonyeza Create Account, kisha chagua Business, Investor au Stakeholder. Admin account hutengenezwa ndani na SUPER_ADMIN pekee.';
    }

    if (str_contains($text, 'login') || str_contains($text, 'ingia') || str_contains($text, 'dashboard') || str_contains($text, 'workspace')) {
        return 'Baada ya ku-login, mfumo unasoma role yako na kukupeleka kwenye dashboard sahihi: Business, Investor, Stakeholder au Admin.';
    }

    if (str_contains($text, 'uhakiki') || str_contains($text, 'verification') || str_contains($text, 'verify') || str_contains($text, 'nyaraka') || str_contains($text, 'documents')) {
        return 'Uhakiki unahitaji profile iliyokamilika na documents zinazotakiwa. Fuata Verification Track kuona status kama unverified, submitted, under review, needs update, approved, verified, rejected au expired.';
    }

    if (str_contains($text, 'biashara') || str_contains($text, 'business') || str_contains($text, 'sme') || str_contains($text, 'startup')) {
        return 'Kwa Business account, kamilisha taarifa za biashara, address, industry, stage, support needs, documents na readiness checklist kabla ya ku-submit kwa uhakiki.';
    }

    if (str_contains($text, 'mwekezaji') || str_contains($text, 'investor') || str_contains($text, 'fursa') || str_contains($text, 'funding')) {
        return 'Kwa Investor account, kamilisha investor profile, preferences, ticket size, sectors na verification. Baada ya approval utaweza kuona fursa zinazostahili.';
    }

    if (str_contains($text, 'mdau') || str_contains($text, 'stakeholder') || str_contains($text, 'partner') || str_contains($text, 'serikali') || str_contains($text, 'ngo')) {
        return 'Kwa Stakeholder account, kamilisha organization profile, support services, target groups na authorization documents. Baada ya uhakiki utaweza kuratibu recommendations na support.';
    }

    if (str_contains($text, 'privacy') || str_contains($text, 'terms') || str_contains($text, 'policy') || str_contains($text, 'sheria')) {
        return 'Unaweza kusoma Privacy Policy, Terms of Use, Platform Limitations na Verification Policy kwenye sehemu ya Legal & Trust chini ya ukurasa.';
    }

    return 'Mimi ni Unny, msaidizi wa UNIDA Gateway. Naweza kusaidia kuhusu kujisajili, login, uhakiki, business readiness, investor access, stakeholder coordination, dashboards na policies. Tafadhali uliza kwa maelezo zaidi kidogo.';
}

function unny_en_reply($text) {
    if ($text === '') {
        return 'Please write your question. I can help with registration, login, verification, dashboards, opportunities and support.';
    }

    if (str_contains($text, 'hello') || str_contains($text, 'hi')) {
        return 'Hello, welcome to UNIDA Gateway. I am Unny. You can ask me about registration, login, verification, dashboards, opportunities or support.';
    }

    if (str_contains($text, 'register') || str_contains($text, 'account')) {
        return 'To create an account, click Create Account and choose Business, Investor or Stakeholder. Admin accounts are created internally by SUPER_ADMIN only.';
    }

    if (str_contains($text, 'login') || str_contains($text, 'dashboard') || str_contains($text, 'workspace')) {
        return 'After login, the system reads your role and redirects you to the correct workspace: Business, Investor, Stakeholder or Admin dashboard.';
    }

    if (str_contains($text, 'verification') || str_contains($text, 'verify') || str_contains($text, 'documents')) {
        return 'Verification requires a completed profile and required documents. Track progress in Verification Track. Status may be unverified, submitted, under review, needs update, approved, verified, rejected or expired.';
    }

    if (str_contains($text, 'business') || str_contains($text, 'sme') || str_contains($text, 'startup')) {
        return 'Business users should complete profile, address, industry, stage, support needs, documents and readiness checklist before submitting for verification.';
    }

    if (str_contains($text, 'investor') || str_contains($text, 'opportunity') || str_contains($text, 'funding')) {
        return 'Investors complete their investor profile, preferences, ticket size and sectors, then discover verified opportunities after approval.';
    }

    return 'I am Unny, your UNIDA Gateway assistant. I can help with registration, login, verification, business readiness, investor access, stakeholder coordination, dashboards and policies.';
}

$input = json_decode(file_get_contents('php://input'), true);

if (!is_array($input)) {
    $input = $_POST;
}

$message = trim((string) ($input['message'] ?? ''));
$lang = unny_language($input['lang'] ?? '', $message);

$_SESSION['lang'] = $lang;

$text = mb_strtolower($message);
$reply = $lang === 'en' ? unny_en_reply($text) : unny_sw_reply($text);

$sessionId = session_id() ?: bin2hex(random_bytes(16));
$userId = $_SESSION['user_id'] ?? null;
$conversationId = null;

try {
    if (unny_table_exists('chatbot_conversations') && unny_table_exists('chatbot_messages')) {
        $stmt = db()->prepare("SELECT id FROM chatbot_conversations WHERE session_id = ? AND assistant_key = 'unny' AND language = ? AND status = 'open' ORDER BY id DESC LIMIT 1");
        $stmt->execute([$sessionId, $lang]);
        $conversation = $stmt->fetch();

        if ($conversation) {
            $conversationId = (int) $conversation['id'];
        } else {
            $insert = db()->prepare("INSERT INTO chatbot_conversations (user_id, session_id, assistant_key, language, title, status) VALUES (?, ?, 'unny', ?, 'Unny Chat', 'open')");
            $insert->execute([$userId, $sessionId, $lang]);
            $conversationId = (int) db()->lastInsertId();
        }

        $msg = db()->prepare("INSERT INTO chatbot_messages (conversation_id, sender, assistant_key, language, message, intent) VALUES (?, ?, 'unny', ?, ?, ?)");
        $msg->execute([$conversationId, 'user', $lang, $message, 'user_question']);
        $msg->execute([$conversationId, 'assistant', $lang, $reply, 'assistant_reply']);
    }

    if (unny_table_exists('ai_tool_logs')) {
        $log = db()->prepare("INSERT INTO ai_tool_logs (user_id, tool_key, assistant_key, language, prompt, response, status, ip_address, user_agent) VALUES (?, 'chatbot', 'unny', ?, ?, ?, 'completed', ?, ?)");
        $log->execute([$userId, $lang, $message, $reply, $_SERVER['REMOTE_ADDR'] ?? null, $_SERVER['HTTP_USER_AGENT'] ?? null]);
    }
} catch (Throwable $e) {
    error_log('Unny chatbot log error: ' . $e->getMessage());
}

echo json_encode([
    'success' => true,
    'assistant' => 'unny',
    'assistant_name' => 'Unny',
    'lang' => $lang,
    'reply' => $reply,
    'conversation_id' => $conversationId,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
