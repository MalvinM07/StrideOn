<?php
// ============================================================
// StrideOn - Contactos
// Desenvolvido por: Eng. Software Malvin Manguele
// ============================================================
$pageTitle = 'Contactos — StrideOn';
require_once 'includes/header.php';
?>

<div class="page-header" data-title="CONTACTOS">
  <p class="page-header-label">Fala Connosco</p>
  <h1 class="page-header-title">CONTACTOS<br>& SUPORTE</h1>
</div>

<section class="section">
  <div style="max-width:800px;margin:0 auto">
    <div style="margin-bottom:40px">
      <span class="section-label">Estamos aqui para ti</span>
      <h2 class="section-title" style="font-size:clamp(28px,4vw,48px)">COMO PODEMOS<br><span class="dim">AJUDAR?</span></h2>
    </div>
    <div class="contacts-grid reveal">
      <div class="contact-card">
        <div class="contact-icon">📞</div>
        <div>
          <div class="contact-label">Telefone</div>
          <div class="contact-value"><a href="tel:+258847859843">+258 84 785 9843</a></div>
        </div>
      </div>
      <div class="contact-card">
        <div class="contact-icon">💬</div>
        <div>
          <div class="contact-label">WhatsApp</div>
          <div class="contact-value"><a href="https://wa.me/258879745377" target="_blank">+258 87 974 5377</a></div>
        </div>
      </div>
      <div class="contact-card">
        <div class="contact-icon">📘</div>
        <div>
          <div class="contact-label">Facebook</div>
          <div class="contact-value"><a href="https://www.facebook.com/share/1HDpdk7jPY/?mibextid=wwXIfr" target="_blank" rel="noopener">StrideOn Official</a></div>
        </div>
      </div>
      <div class="contact-card">
        <div class="contact-icon">📍</div>
        <div>
          <div class="contact-label">Localização</div>
          <div class="contact-value">Maputo, Moçambique</div>
        </div>
      </div>
    </div>

    <div class="promo-banner reveal" style="margin:60px 0 0">
      <div>
        <p class="promo-label">Pedidos Rápidos</p>
        <h3 class="promo-title" style="font-size:clamp(28px,4vw,48px)">FINALIZA<br>VIA WHATSAPP</h3>
        <p class="promo-desc">Adiciona produtos ao carrinho e finaliza a compra diretamente pelo WhatsApp.</p>
      </div>
      <a href="https://wa.me/258879745377" target="_blank" rel="noopener" class="btn-primary">
        Abrir WhatsApp
      </a>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
