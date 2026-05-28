<?php
// ============================================================
// StrideOn - Página Principal
// Desenvolvido por: Eng. Software Malvin Manguele
// ============================================================
$pageTitle = 'StrideOn — Where Every Step Is Success';
require_once 'includes/header.php';

// Buscar produtos em destaque
$stmt = $pdo->prepare("SELECT * FROM products WHERE featured = 1 AND active = 1 ORDER BY created_at DESC LIMIT 4");
$stmt->execute();
$featured = $stmt->fetchAll();
?>

<!-- ── HERO ── -->
<section class="hero">
  <div class="hero-bg">
    <canvas id="heroCanvas" class="hero-canvas"></canvas>
  </div>
  <div class="hero-overlay"></div>
  <div class="hero-content">
    <p class="hero-eyebrow">Nova Coleção — 2025</p>
    <h1 class="hero-title">
      STRIDE<span class="red">ON</span><br>
      <span class="outline">MOVE</span><br>
      DIFFERENT
    </h1>
    <p class="hero-subtitle">
      Calçados premium que definem o teu estilo urbano.<br>
      Cada passo é uma declaração.
    </p>
    <div class="hero-cta">
      <a href="products.php" class="btn-primary">
        Explorar Coleção
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </a>
      <a href="#sobre" class="btn-outline">Sobre Nós</a>
    </div>
  </div>
  <div class="hero-stats">
    <div class="stat-item">
      <span class="stat-num">500+</span>
      <span class="stat-label">Clientes Satisfeitos</span>
    </div>
    <div class="stat-item">
      <span class="stat-num">50+</span>
      <span class="stat-label">Modelos Exclusivos</span>
    </div>
    <div class="stat-item">
      <span class="stat-num">100%</span>
      <span class="stat-label">Qualidade Premium</span>
    </div>
  </div>
  <div class="hero-scroll">
    <span>Scroll</span>
    <div class="scroll-line"></div>
  </div>
</section>

<!-- ── ABOUT STRIP ── -->
<section class="about-strip" id="sobre">
  <div class="about-quote reveal">
    WHERE EVERY<br>STEP IS AN<br>IMPULSE TO<br>SUCCESS.
  </div>
  <div class="reveal">
    <p class="about-text">
      StrideOn nasceu da paixão pelo streetwear urbano e pela cultura dos sneakers. 
      Acreditamos que o calçado certo transforma não apenas o teu visual — transforma a tua confiança, 
      o teu caminhar, a tua presença.
      <br><br>
      Walking with attitude and style is our motto. Cada modelo que selecionamos foi escolhido para 
      elevar o teu estilo ao próximo nível.
    </p>
    <span class="about-tagline">Est. 2024 — Maputo, Moçambique</span>
  </div>
</section>

<!-- ── FEATURED PRODUCTS ── -->
<section class="section" id="produtos">
  <div class="section-header">
    <div>
      <span class="section-label">Em Destaque</span>
      <h2 class="section-title">DROPS <span class="dim">DA</span><br>SEMANA</h2>
    </div>
    <div>
      <p class="section-desc">Os modelos mais procurados da nossa coleção. Qualidade premium, estilo inconfundível.</p>
      <a href="products.php" class="btn-outline" style="margin-top:20px;display:inline-block">Ver Todos</a>
    </div>
  </div>
  <div class="products-grid">
    <?php foreach ($featured as $p): ?>
    <div class="product-card reveal" data-category="<?= htmlspecialchars($p['category']) ?>">
      <div class="product-img-wrap">
        <div class="product-overlay"></div>
        <img src="assets/images/<?= htmlspecialchars($p['image']) ?>"
             alt="<?= htmlspecialchars($p['name']) ?>"
             onerror="this.src='assets/images/placeholder.svg'"
             loading="lazy">
        <span class="product-badge"><?= htmlspecialchars($p['category']) ?></span>
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
          <button class="btn-add-cart" data-id="<?= $p['id'] ?>">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
            Adicionar
          </button>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- ── PROMO BANNER ── -->
<div class="promo-banner reveal">
  <div>
    <p class="promo-label">Oferta Especial</p>
    <h3 class="promo-title">ATÉ 30%<br>OFF</h3>
    <p class="promo-desc">Em modelos selecionados da coleção urbana. Por tempo limitado — aproveita agora.</p>
  </div>
  <a href="products.php" class="btn-primary">Ver Ofertas</a>
</div>

<!-- ── BOTTOM SPACING ── -->
<div style="height: 80px"></div>

<?php require_once 'includes/footer.php'; ?>
