<?php
/**
 * UNIDA Gateway Chatbot API
 * English assistant: Unny
 * Kiswahili assistant: Rieta
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function unida_chat_support_email() {
    return 'support@unidagateway.co.tz';
}

function unida_chat_table_exists($table) {
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

function unida_chat_lang($lang, $message = '') {
    $lang = strtolower(trim((string) $lang));

    if (in_array($lang, ['en', 'sw'], true)) {
        return $lang;
    }

    $text = mb_strtolower($message);
    $swHints = ['habari', 'msaada', 'naomba', 'tafadhali', 'jinsi', 'kujisajili', 'ingia', 'uhakiki', 'nifanye', 'nataka', 'fursa', 'biashara', 'mwekezaji', 'mdau', 'namba'];

    foreach ($swHints as $hint) {
        if (str_contains($text, $hint)) {
            return 'sw';
        }
    }

    return $_SESSION['lang'] ?? 'en';
}

function unida_chat_assistant($lang) {
    return $lang === 'sw'
        ? ['key' => 'rieta', 'name' => 'Rieta']
        : ['key' => 'unny', 'name' => 'Unny'];
}

function unida_chat_answer($intent, $lang) {
    $answers = [
        'en' => [
            'greeting' => 'Hello, I am Unny. Choose an area below or type your question.',
            'registration' => 'To create an account, click Create Account, choose Business, Investor or Stakeholder, fill your information and accept the Terms and Privacy Policy.',
            'login_dashboard' => 'After login, the system reads your role and redirects you to the correct dashboard: Business, Investor, Stakeholder or Admin.',
            'verification' => 'Verification requires a completed profile and required documents. After submission, you can track status through Verification Track.',
            'business_readiness' => 'Business readiness means preparing your business for investors or partners: profile, documents, business stage, support needs, pitch deck, license, tax document and representative ID.',
            'investor_access' => 'A verified investor can view verified opportunities based on sector, stage, region and ticket preference.',
            'stakeholder' => 'A stakeholder can support review, recommendations, connections, follow-ups, reports and coordination for businesses or groups that need support.',
            'human_agent' => 'Okay, I need your details before forwarding this to a human agent: full name, email, phone number and your concern.',
            'unknown' => 'Your question looks like it needs a human agent. Please provide your name, email, phone number and concern so I can forward it to support.',
            'support_success' => 'Thank you. Your message has been forwarded to a human agent through support@unidagateway.co.tz. The support team will contact you.',
        ],
        'sw' => [
            'greeting' => 'Habari, mimi ni Rieta. Chagua eneo unalohitaji msaada hapa chini au andika swali lako.',
            'registration' => 'Ili kujisajili, bonyeza Create Account, chagua Business, Investor au Stakeholder, kisha jaza taarifa zako na ukubali Terms na Privacy Policy.',
            'login_dashboard' => 'Baada ya ku-login, mfumo unasoma role yako na kukupeleka kwenye dashboard sahihi: Business, Investor, Stakeholder au Admin.',
            'verification' => 'Uhakiki unahitaji profile iliyokamilika na documents zinazotakiwa. Baada ya ku-submit unaweza kufuatilia status kupitia Verification Track.',
            'business_readiness' => 'Business readiness ni maandalizi ya biashara yako kwa investor au partner: profile, documents, business stage, support needs, pitch deck, license, tax document na ID ya representative.',
            'investor_access' => 'Investor aliyekamilisha profile na verification anaweza kuona fursa zilizo verified kulingana na sector, stage, region na ticket preference.',
            'stakeholder' => 'Stakeholder anaweza kusaidia review, recommendation, connection, follow-up, reports na coordination kwa businesses au groups zinazohitaji support.',
            'human_agent' => 'Sawa, nitahitaji taarifa zako kabla sijatuma ujumbe kwa human agent: jina kamili, email, namba ya simu na concern yako.',
            'unknown' => 'Swali lako linaonekana linahitaji human agent. Tafadhali jaza jina, email, namba ya simu na concern yako ili nitume kwa support.',
            'support_success' => 'Asante. Ujumbe wako umetumwa kwa human agent kupitia support@unidagateway.co.tz. Timu ya support itawasiliana nawe.',
        ],
    ];

    return $answers[$lang][$intent] ?? $answers[$lang]['unknown'];
}

function unida_chat_detect_intent($message) {
    $text = mb_strtolower(trim((string) $message));

    if ($text === '' || str_contains($text, 'habari') || str_contains($text, 'hello') || str_contains($text, 'hi')) {
        return 'greeting';
    }

    $rules = [
        'registration' => ['register', 'jisajili', 'akaunti', 'account', 'create account'],
        'login_dashboard' => ['login', 'ingia', 'dashboard', 'workspace', 'dashibodi'],
        'verification' => ['verification', 'verify', 'uhakiki', 'thibit', 'documents', 'nyaraka'],
        'business_readiness' => ['business', 'biashara', 'readiness', 'sme', 'startup', 'pitch', 'license'],
        'investor_access' => ['investor', 'mwekezaji', 'investment', 'fursa', 'funding', 'opportunity'],
        'stakeholder' => ['stakeholder', 'mdau', 'partner', 'ngo', 'serikali', 'bank', 'university', 'hub'],
        'human_agent' => ['human', 'support', 'agent', 'mtu', 'mteja', 'helpdesk', 'call me', 'nipigie'],
    ];

    foreach ($rules as $intent => $keywords) {
        foreach ($keywords as $keyword) {
            if (str_contains($text, $keyword)) {
                return $intent;
            }
        }
    }

    return 'unknown';
}

function unida_chat_get_conversation($sessionId, $userId, $lang, $assistantKey) {
    try {
        if (!unida_chat_table_exists('rieta_conversations')) {
            return null;
        }

        $stmt = db()->prepare("SELECT id FROM rieta_conversations WHERE session_id = ? AND status = 'open' ORDER BY id DESC LIMIT 1");
        $stmt->execute([$sessionId]);
        $conversation = $stmt->fetch();

        if ($conversation) {
            return (int) $conversation['id'];
        }

        $insert = db()->prepare("INSERT INTO rieta_conversations (user_id, session_id, language, status, current_topic) VALUES (?, ?, ?, 'open', ?)");
        $insert->execute([$userId, $sessionId, $lang, $assistantKey]);

        return (int) db()->lastInsertId();
    } catch (Throwable $e) {
        error_log('Chat conversation error: ' . $e->getMessage());
        return null;
    }
}

function unida_chat_save_message($conversationId, $userId, $sender, $lang, $message, $intent) {
    try {
        if (!unida_chat_table_exists('rieta_messages')) {
            return false;
        }

        $stmt = db()->prepare("INSERT INTO rieta_messages (conversation_id, user_id, sender, language, message, intent) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$conversationId, $userId, $sender, $lang, $message, $intent]);
    } catch (Throwable $e) {
        error_log('Chat save message error: ' . $e->getMessage());
        return false;
    }
}

function unida_chat_forward_support($payload, $conversationId, $userId, $lang) {
    $fullName = trim((string) ($payload['full_name'] ?? ''));
    $email = trim((string) ($payload['email'] ?? ''));
    $phone = trim((string) ($payload['phone'] ?? ''));
    $concern = trim((string) ($payload['concern'] ?? ''));

    if ($fullName === '' || $email === '' || $phone === '' || $concern === '') {
        return [
            'success' => false,
            'reply' => $lang === 'sw'
                ? 'Tafadhali jaza jina kamili, email, namba ya simu na concern yako.'
                : 'Please fill full name, email, phone number and your concern.',
            'needs_human_details' => true
        ];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [
            'success' => false,
            'reply' => $lang === 'sw' ? 'Tafadhali weka email sahihi.' : 'Please enter a valid email address.',
            'needs_human_details' => true
        ];
    }

    $assistant = unida_chat_assistant($lang);
    $subject = 'UNIDA Gateway Support Request from ' . $assistant['name'];
    $body = "New support request from {$assistant['name']} chatbot\n\n";
    $body .= "Name: {$fullName}\nEmail: {$email}\nPhone: {$phone}\nLanguage: {$lang}\n\nConcern:\n{$concern}\n\nConversation ID: {$conversationId}\n";

    $headers = "From: UNIDA Gateway <no-reply@unidagateway.co.tz>\r\n";
    $headers .= "Reply-To: {$email}\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    $mailSent = @mail(unida_chat_support_email(), $subject, $body, $headers);
    $mailError = $mailSent ? null : 'PHP mail returned false. Request saved in database.';

    try {
        if (unida_chat_table_exists('rieta_support_requests')) {
            $stmt = db()->prepare("
                INSERT INTO rieta_support_requests (
                    conversation_id, user_id, full_name, email, phone, subject, concern,
                    language, status, forwarded_to, mail_sent, mail_error, ip_address, user_agent
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'new', ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $conversationId,
                $userId,
                $fullName,
                $email,
                $phone,
                $subject,
                $concern,
                $lang,
                unida_chat_support_email(),
                $mailSent ? 1 : 0,
                $mailError,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null,
            ]);
        }
    } catch (Throwable $e) {
        error_log('Support save error: ' . $e->getMessage());
    }

    return [
        'success' => true,
        'reply' => unida_chat_answer('support_success', $lang),
        'mail_sent' => $mailSent,
        'saved' => true,
        'needs_human_details' => false
    ];
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    $input = $_POST;
}

$action = trim((string) ($input['action'] ?? 'message'));
$message = trim((string) ($input['message'] ?? ''));
$topic = trim((string) ($input['topic'] ?? ''));
$lang = unida_chat_lang($input['lang'] ?? '', $message);

$_SESSION['lang'] = $lang;

$assistant = unida_chat_assistant($lang);
$sessionId = session_id() ?: bin2hex(random_bytes(16));
$userId = $_SESSION['user_id'] ?? null;
$conversationId = unida_chat_get_conversation($sessionId, $userId, $lang, $assistant['key']);

if ($action === 'support_request') {
    $result = unida_chat_forward_support($input, $conversationId, $userId, $lang);

    if (!empty($result['success'])) {
        unida_chat_save_message($conversationId, $userId, 'user', $lang, $input['concern'] ?? '', 'human_agent_request');
        unida_chat_save_message($conversationId, $userId, 'assistant', $lang, $result['reply'], 'support_success');
    }

    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$intent = $topic !== '' ? $topic : unida_chat_detect_intent($message);
$needsHuman = $intent === 'unknown' || $intent === 'human_agent';
$reply = unida_chat_answer($intent, $lang);

unida_chat_save_message($conversationId, $userId, 'user', $lang, $message ?: $topic, $intent);
unida_chat_save_message($conversationId, $userId, 'assistant', $lang, $reply, $intent);

echo json_encode([
    'success' => true,
    'assistant' => $assistant['key'],
    'assistant_name' => $assistant['name'],
    'lang' => $lang,
    'intent' => $intent,
    'reply' => $reply,
    'needs_human_details' => $needsHuman,
    'conversation_id' => $conversationId,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);