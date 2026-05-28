<?php
// ============================================================
// StrideOn - Login
// Desenvolvido por: Eng. Software Malvin Manguele
// ============================================================
$pageTitle = 'Login — StrideOn';
require_once 'includes/config.php';
require_once 'includes/functions.php';
startSecureSession();

if (isLoggedIn()) { redirect(SITE_URL . '/index.php'); }

$error = '';
$csrf  = generateCSRF();

// ── Rate Limiting: máx. 5 tentativas por email em 15 minutos ─
function isLoginBlocked(string $email): bool {
    $key = 'login_fail_' . md5($email);
    $data = $_SESSION[$key] ?? ['count' => 0, 'time' => 0];
    // Resetar janela após 15 minutos
    if (time() - $data['time'] > 900) {
        $_SESSION[$key] = ['count' => 0, 'time' => time()];
        return false;
    }
    return $data['count'] >= 5;
}

function recordFailedLogin(string $email): void {
    $key = 'login_fail_' . md5($email);
    $data = $_SESSION[$key] ?? ['count' => 0, 'time' => time()];
    $data['count']++;
    $data['time'] = $data['time'] ?: time();
    $_SESSION[$key] = $data;
}

function clearLoginAttempts(string $email): void {
    unset($_SESSION['login_fail_' . md5($email)]);
}

// ── Processamento do formulário ───────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCSRF($_POST['csrf'] ?? '');

    $email    = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = 'Por favor preenche todos os campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email inválido.';
    } elseif (isLoginBlocked($email)) {
        // Não diz quantas tentativas restam para não ajudar atacantes
        $error = 'Demasiadas tentativas falhadas. Aguarda 15 minutos e tenta novamente.';
    } else {
        $pdo  = getDB();
        $stmt = $pdo->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Login bem-sucedido — limpa tentativas e regenera sessão
            clearLoginAttempts($email);
            session_regenerate_id(true);
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            setMessage('success', 'Bem-vindo de volta, ' . $user['name'] . '!');
            redirect(SITE_URL . '/index.php');
        } else {
            // Regista a tentativa falhada
            recordFailedLogin($email);
            // Mensagem genérica — não revela se o email existe ou não
            $error = 'Email ou password incorretos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-MZ">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="/strideon/assets/css/style.css">
</head>
<body>
<div id="loading-screen">
  <div class="loader-logo">
    <img src="/strideon/assets/images/logo.png" alt="StrideOn" class="loader-logo-img">
  </div>
  <div class="loader-bar"><div class="loader-bar-fill"></div></div>
  <div class="loader-sub">Carregando</div>
</div>
<div class="auth-page">
  <div class="auth-box">
    <div class="auth-logo">
      <img src="/strideon/assets/images/logo.png" alt="StrideOn" class="auth-logo-img">
    </div>
    <div class="auth-title">Acesso à Conta</div>
    <?php if ($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" action="">
      <input type="hidden" name="csrf" value="<?= $csrf ?>">
      <div class="form-group">
        <label class="form-label" for="email">Email</label>
        <input class="form-input" type="email" id="email" name="email"
               placeholder="teu@email.com" required
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input class="form-input" type="password" id="password" name="password"
               placeholder="A tua password" required>
      </div>
      <button type="submit" class="btn-submit">Entrar</button>
    </form>
    <div class="auth-switch">
      Não tens conta? <a href="register.php">Criar Conta</a>
    </div>
    <div class="auth-switch" style="margin-top:12px">
      <a href="index.php">← Voltar à Loja</a>
    </div>
  </div>
</div>
<button id="back-top" style="display:none"></button>
<script src="/strideon/assets/js/main.js"></script>
</body>
</html>
