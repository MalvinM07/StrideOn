<?php
// ============================================================
// StrideOn - Página de Produtos
// Desenvolvido por: Eng. Software Malvin Manguele
// ============================================================
$pageTitle = 'Produtos — StrideOn';
require_once 'includes/header.php';

$stmt = $pdo->query("SELECT * FROM products WHERE active = 1 ORDER BY featured DESC, created_at DESC");
$products = $stmt->fetchAll();

$categories = array_unique(array_column($products, 'category'));
?>

<div class="page-header" data-title="PRODUTOS">
  <p class="page-header-label">Coleção Completa</p>
  <h1 class="page-header-title">TODOS OS<br>MODELOS</h1>
</div>

<section class="section">
  <div class="filter-bar">
    <button class="filter-btn active" data-cat="all">Todos</button>
    <?php foreach ($categories as $cat): ?>
      <button class="filter-btn" data-cat="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></button>
    <?php endforeach; ?>
  </div>
  <div class="products-grid">
    <?php foreach ($products as $p): ?>
    <div class="product-card reveal" data-category="<?= htmlspecialchars($p['category']) ?>">
      <div class="product-img-wrap">
        <div class="product-overlay"></div>
        <img src="assets/images/<?= htmlspecialchars($p['image']) ?>"
             alt="<?= htmlspecialchars($p['name']) ?>"
             onerror="this.src='assets/images/placeholder.svg'"
             loading="lazy">
        <?php if ($p['featured']): ?>
          <span class="product-badge">Destaque</span>
        <?php endif; ?>
      </div>
      <div class="product-info">
        <div class="product-cat"><?= htmlspecialchars($p['category']) ?></div>
        <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
        <div class="product-desc"><?= htmlspecialchars($p['description']) ?></div>
        <div class="product-footer">
          <div class="product-price">
            <?= number_format($p['price'], 0, ',', '.') ?>
            <span><?= CURRENCY ?></span>
          </div>
          <?php if ($p['stock'] > 0): ?>
            <button class="btn-add-cart" data-id="<?= $p['id'] ?>">
              <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
              Adicionar
            </button>
          <?php else: ?>
            <span style="font-family:var(--font-cond);font-size:11px;color:var(--grey-mid);letter-spacing:.1em;text-transform:uppercase">Esgotado</span>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
