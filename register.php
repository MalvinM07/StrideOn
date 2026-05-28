<?php
// ============================================================
// StrideOn - Cadastro
// Desenvolvido por: Eng. Software Malvin Manguele
// ============================================================
$pageTitle = 'Criar Conta — StrideOn';
require_once 'includes/config.php';
require_once 'includes/functions.php';
startSecureSession();

if (isLoggedIn()) { redirect(SITE_URL . '/index.php'); }

$error = '';
$success = '';
$csrf = generateCSRF();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCSRF($_POST['csrf'] ?? '');
    $name     = sanitize($_POST['name'] ?? '');
    $email    = sanitize($_POST['email'] ?? '');
    $phone    = sanitize($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if (!$name || !$email || !$password || !$confirm) {
        $error = 'Por favor preenche todos os campos obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email inválido.';
    } elseif (strlen($password) < 8) {
        $error = 'A password deve ter pelo menos 8 caracteres.';
    } elseif ($password !== $confirm) {
        $error = 'As passwords não coincidem.';
    } else {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Este email já está registado.';
        } else {
            $hashed = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password) VALUES (?,?,?,?)");
            $stmt->execute([$name, $email, $phone, $hashed]);
            $success = 'Conta criada com sucesso! Podes fazer login agora.';
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
    <div class="auth-title">Criar Nova Conta</div>
    <?php if ($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if (!$success): ?>
    <form method="POST" action="">
      <input type="hidden" name="csrf" value="<?= $csrf ?>">
      <div class="form-group">
        <label class="form-label" for="name">Nome Completo *</label>
        <input class="form-input" type="text" id="name" name="name"
               placeholder="O teu nome" required
               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="email">Email *</label>
        <input class="form-input" type="email" id="email" name="email"
               placeholder="teu@email.com" required
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="phone">Telefone</label>
        <input class="form-input" type="tel" id="phone" name="phone"
               placeholder="+258 84 000 0000"
               value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="password">Password *</label>
        <input class="form-input" type="password" id="password" name="password"
               placeholder="Mínimo 8 caracteres" required minlength="8">
      </div>
      <div class="form-group">
        <label class="form-label" for="confirm">Confirmar Password *</label>
        <input class="form-input" type="password" id="confirm" name="confirm"
               placeholder="Repete a password" required>
      </div>
      <button type="submit" class="btn-submit">Criar Conta</button>
    </form>
    <?php else: ?>
      <a href="login.php" class="btn-submit" style="display:block;text-align:center;text-decoration:none;margin-top:8px">Fazer Login</a>
    <?php endif; ?>
    <div class="auth-switch">
      Já tens conta? <a href="login.php">Fazer Login</a>
    </div>
    <div class="auth-switch" style="margin-top:12px">
      <a href="index.php">← Voltar à Loja</a>
    </div>
  </div>
</div>
<script src="/strideon/assets/js/main.js"></script>
</body>
</html>
