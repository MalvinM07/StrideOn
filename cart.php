<?php
// ============================================================
// StrideOn - Carrinho de Compras
// Desenvolvido por: Eng. Software Malvin Manguele
// ============================================================
$pageTitle = 'Carrinho — StrideOn';
require_once 'includes/header.php';

$items = getCartItems($pdo);
$total = getCartTotal($items);
?>

<div class="page-header" data-title="CARRINHO">
  <p class="page-header-label">O Teu Carrinho</p>
  <h1 class="page-header-title">PRODUTOS<br>SELECIONADOS</h1>
</div>

<section class="section">
  <?php if (empty($items)): ?>
    <div class="cart-empty">
      <div class="cart-empty-icon">🛒</div>
      <h2 class="cart-empty-title">Carrinho Vazio</h2>
      <p class="cart-empty-desc">Ainda não adicionaste nenhum produto. Explora a nossa coleção!</p>
      <a href="products.php" class="btn-primary">Ver Produtos</a>
    </div>
  <?php else: ?>
  <div class="cart-layout">
    <div>
      <table class="cart-table">
        <thead>
          <tr>
            <th>Produto</th>
            <th>Preço</th>
            <th>Qtd</th>
            <th>Total</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $item): ?>
          <tr>
            <td>
              <div class="cart-product">
                <img class="cart-img"
                     src="assets/images/<?= htmlspecialchars($item['image']) ?>"
                     onerror="this.src='assets/images/placeholder.svg'"
                     alt="<?= htmlspecialchars($item['name']) ?>">
                <div>
                  <div class="cart-product-name"><?= htmlspecialchars($item['name']) ?></div>
                </div>
              </div>
            </td>
            <td><?= number_format($item['price'], 0, ',', '.') ?> MT</td>
            <td>
              <div class="qty-control">
                <button class="qty-btn" data-action="dec" data-id="<?= $item['id'] ?>">−</button>
                <div class="qty-val"><?= $item['quantity'] ?></div>
                <button class="qty-btn" data-action="inc" data-id="<?= $item['id'] ?>">+</button>
              </div>
            </td>
            <td class="item-total"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> MT</td>
            <td>
              <button class="btn-remove" data-id="<?= $item['id'] ?>" title="Remover">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
              </button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="cart-summary">
      <h3 class="summary-title">Resumo do Pedido</h3>
      <div class="summary-row">
        <span>Subtotal</span>
        <span><?= number_format($total, 0, ',', '.') ?> MT</span>
      </div>
      <div class="summary-row">
        <span>Entrega</span>
        <span>A combinar</span>
      </div>
      <div class="summary-total">
        <span>Total</span>
        <span class="cart-grand-total"><?= number_format($total, 0, ',', '.') ?> MT</span>
      </div>
      <button class="btn-checkout">
        <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>
        Finalizar via WhatsApp
      </button>
      <p style="font-size:11px;color:var(--grey-mid);text-align:center;margin-top:12px;line-height:1.6">
        Serás redirecionado para o WhatsApp para confirmar o teu pedido.
      </p>
    </div>
  </div>
  <?php endif; ?>
</section>

<?php require_once 'includes/footer.php'; ?>
