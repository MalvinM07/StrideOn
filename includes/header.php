<?php
// ============================================================
// StrideOn - Header Parcial
// ============================================================
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
setSecurityHeaders();
startSecureSession();
$pdo = getDB();
$cartCount = getCartCount($pdo);
$csrf = generateCSRF();
$msg = getMessage();
$pageTitle = $pageTitle ?? 'StrideOn — Where Every Step Is Success';
?>
<!DOCTYPE html>
<html lang="pt-MZ">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="StrideOn — Loja premium de calçados streetwear em Moçambique. Os melhores sneakers com estilo urbano.">
<meta name="theme-color" content="#e8001c">
<meta name="csrf" content="<?= $csrf ?>">
<title><?= htmlspecialchars($pageTitle) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="/strideon/assets/css/style.css">
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>👟</text></svg>">
</head>
<body>

<!-- Loading Screen -->
<div id="loading-screen">
  <div class="loader-logo">
  <img src="/strideon/assets/images/logo.png" alt="StrideOn" class="loader-logo-img">
</div>
  <div class="loader-bar"><div class="loader-bar-fill"></div></div>
  <div class="loader-sub">Carregando experiência</div>
</div>

<!-- Navbar -->
<nav class="navbar">
  <a href="/strideon/index.php" class="nav-logo">
  <img src="/strideon/assets/images/logo.png" alt="StrideOn" class="nav-logo-img">
</a>
  <ul class="nav-links">
    <li><a href="/strideon/index.php">Home</a></li>
    <li><a href="/strideon/products.php">Produtos</a></li>
    <li><a href="/strideon/contacts.php">Contactos</a></li>
  </ul>
  <div class="nav-actions">
    <a href="/strideon/cart.php" class="btn-cart" title="Carrinho">
      <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/>
        <path d="M16 10a4 4 0 01-8 0"/>
      </svg>
      <?php if ($cartCount > 0): ?>
        <span class="cart-badge"><?= $cartCount ?></span>
      <?php endif; ?>
    </a>
    <?php if (isLoggedIn()): ?>
      <a href="/strideon/logout.php" class="btn-nav-auth">Sair</a>
    <?php else: ?>
      <a href="/strideon/login.php" class="btn-nav-auth">Entrar</a>
    <?php endif; ?>
    <button class="hamburger" aria-label="Menu">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>

<!-- Mobile Menu -->
<div class="mobile-menu">
  <a href="/strideon/index.php">Home</a>
  <a href="/strideon/products.php">Produtos</a>
  <a href="/strideon/contacts.php">Contactos</a>
  <a href="/strideon/cart.php">Carrinho (<?= $cartCount ?>)</a>
  <?php if (isLoggedIn()): ?>
    <a href="/strideon/logout.php">Sair</a>
  <?php else: ?>
    <a href="/strideon/login.php">Login</a>
    <a href="/strideon/register.php">Criar Conta</a>
  <?php endif; ?>
</div>

<?php if ($msg): ?>
<div class="toast-wrap">
  <div class="toast <?= $msg['type'] === 'error' ? 'error' : 'success' ?>"><?= htmlspecialchars($msg['text']) ?></div>
</div>
<?php endif; ?>
