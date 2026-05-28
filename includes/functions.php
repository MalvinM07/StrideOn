<?php
// ============================================================
// StrideOn - Funções de Segurança e Sessão
// Desenvolvido por: Eng. Software Malvin Manguele
// ============================================================

// Headers de segurança
function setSecurityHeaders() {
    header("X-Frame-Options: DENY");
    header("X-Content-Type-Options: nosniff");
    header("Referrer-Policy: strict-origin-when-cross-origin");
    header("Permissions-Policy: camera=(), microphone=(), geolocation=()");
    header("X-XSS-Protection: 1; mode=block");
    // HSTS apenas em HTTPS
    // header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
}

// Iniciar sessão segura
function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_samesite', 'Strict');
        session_start();
    }
}

// Gerar token CSRF
function generateCSRF() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verificar CSRF
function verifyCSRF($token) {
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        die(json_encode(['error' => 'Token de segurança inválido.']));
    }
    return true;
}

// Sanitizar input
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

// Verificar se está logado
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Verificar se é admin
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Obter ID de sessão para carrinho de visitantes
function getSessionCartId() {
    if (empty($_SESSION['cart_session'])) {
        $_SESSION['cart_session'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['cart_session'];
}

// Redirecionar
function redirect($url) {
    header("Location: " . $url);
    exit;
}

// Mensagem de feedback
function setMessage($type, $text) {
    $_SESSION['msg_type'] = $type;
    $_SESSION['msg_text'] = $text;
}

function getMessage() {
    if (!empty($_SESSION['msg_text'])) {
        $msg = ['type' => $_SESSION['msg_type'], 'text' => $_SESSION['msg_text']];
        unset($_SESSION['msg_type'], $_SESSION['msg_text']);
        return $msg;
    }
    return null;
}

// Obter produtos do carrinho
function getCartItems($pdo) {
    $userId = isLoggedIn() ? $_SESSION['user_id'] : null;
    $sessionId = getSessionCartId();

    if ($userId) {
        $stmt = $pdo->prepare("SELECT c.*, p.name, p.price, p.image FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
        $stmt->execute([$userId]);
    } else {
        $stmt = $pdo->prepare("SELECT c.*, p.name, p.price, p.image FROM cart c JOIN products p ON c.product_id = p.id WHERE c.session_id = ?");
        $stmt->execute([$sessionId]);
    }
    return $stmt->fetchAll();
}

function getCartTotal($items) {
    $total = 0;
    foreach ($items as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

function getCartCount($pdo) {
    $userId = isLoggedIn() ? $_SESSION['user_id'] : null;
    $sessionId = getSessionCartId();
    if ($userId) {
        $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
        $stmt->execute([$userId]);
    } else {
        $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart WHERE session_id = ?");
        $stmt->execute([$sessionId]);
    }
    return (int)$stmt->fetchColumn();
}
