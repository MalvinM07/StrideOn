// ============================================================
// StrideOn — Main JavaScript
// Desenvolvido por: Eng. Software Malvin Manguele
// ============================================================

document.addEventListener('DOMContentLoaded', () => {

  // ── LOADING SCREEN ──────────────────────────────────────────
  const loader = document.getElementById('loading-screen');
  if (loader) {
    setTimeout(() => loader.classList.add('hidden'), 2000);
  }

  // ── NAVBAR SCROLL ───────────────────────────────────────────
  const navbar = document.querySelector('.navbar');
  if (navbar) {
    window.addEventListener('scroll', () => {
      navbar.classList.toggle('scrolled', window.scrollY > 50);
    }, { passive: true });
  }

  // ── HAMBURGER MENU ──────────────────────────────────────────
  const hamburger = document.querySelector('.hamburger');
  const mobileMenu = document.querySelector('.mobile-menu');
  if (hamburger && mobileMenu) {
    hamburger.addEventListener('click', () => {
      hamburger.classList.toggle('open');
      mobileMenu.classList.toggle('open');
    });
    document.addEventListener('click', (e) => {
      if (!hamburger.contains(e.target) && !mobileMenu.contains(e.target)) {
        hamburger.classList.remove('open');
        mobileMenu.classList.remove('open');
      }
    });
  }

  // ── HERO CANVAS ANIMATION ───────────────────────────────────
  const canvas = document.getElementById('heroCanvas');
  if (canvas) {
    const ctx = canvas.getContext('2d');
    let w, h, particles = [], animId;

    const resize = () => {
      w = canvas.width = canvas.offsetWidth;
      h = canvas.height = canvas.offsetHeight;
    };
    resize();
    window.addEventListener('resize', resize, { passive: true });

    class Particle {
      constructor() { this.reset(); }
      reset() {
        this.x = Math.random() * w;
        this.y = Math.random() * h;
        this.size = Math.random() * 1.5 + 0.3;
        this.speedX = (Math.random() - 0.5) * 0.4;
        this.speedY = -Math.random() * 0.6 - 0.2;
        this.life = Math.random();
        this.maxLife = Math.random() * 0.02 + 0.003;
        this.isRed = Math.random() < 0.3;
      }
      update() {
        this.x += this.speedX;
        this.y += this.speedY;
        this.life -= this.maxLife;
        if (this.life <= 0 || this.y < 0) this.reset();
      }
      draw() {
        const alpha = Math.max(0, this.life);
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        if (this.isRed) {
          ctx.fillStyle = `rgba(232, 0, 28, ${alpha * 0.8})`;
        } else {
          ctx.fillStyle = `rgba(255, 255, 255, ${alpha * 0.25})`;
        }
        ctx.fill();
      }
    }

    // Grid lines
    const drawGrid = () => {
      ctx.strokeStyle = 'rgba(255,255,255,0.02)';
      ctx.lineWidth = 1;
      const cols = 12, rows = 8;
      for (let i = 0; i <= cols; i++) {
        ctx.beginPath();
        ctx.moveTo((w / cols) * i, 0);
        ctx.lineTo((w / cols) * i, h);
        ctx.stroke();
      }
      for (let i = 0; i <= rows; i++) {
        ctx.beginPath();
        ctx.moveTo(0, (h / rows) * i);
        ctx.lineTo(w, (h / rows) * i);
        ctx.stroke();
      }
    };

    for (let i = 0; i < 120; i++) particles.push(new Particle());

    let t = 0;
    const animate = () => {
      animId = requestAnimationFrame(animate);
      ctx.clearRect(0, 0, w, h);
      t += 0.005;

      // Subtle red glow pulse
      const grd = ctx.createRadialGradient(w * 0.75, h * 0.6, 0, w * 0.75, h * 0.6, w * 0.4);
      grd.addColorStop(0, `rgba(232,0,28,${0.04 + Math.sin(t) * 0.02})`);
      grd.addColorStop(1, 'rgba(0,0,0,0)');
      ctx.fillStyle = grd;
      ctx.fillRect(0, 0, w, h);

      drawGrid();
      particles.forEach(p => { p.update(); p.draw(); });
    };
    animate();
  }

  // ── SCROLL REVEAL ───────────────────────────────────────────
  const reveals = document.querySelectorAll('.reveal');
  if (reveals.length > 0) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.classList.add('visible');
          observer.unobserve(e.target);
        }
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
    reveals.forEach(el => observer.observe(el));
  }

  // ── BACK TO TOP ─────────────────────────────────────────────
  const backTop = document.getElementById('back-top');
  if (backTop) {
    window.addEventListener('scroll', () => {
      backTop.classList.toggle('visible', window.scrollY > 400);
    }, { passive: true });
    backTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
  }

  // ── ACTIVE NAV LINK ─────────────────────────────────────────
  const currentPath = window.location.pathname.split('/').pop() || 'index.php';
  document.querySelectorAll('.nav-links a, .mobile-menu a').forEach(a => {
    const href = a.getAttribute('href') || '';
    if (href === currentPath || (currentPath === '' && href === 'index.php')) {
      a.classList.add('active');
    }
  });

  // ── PRODUCT FILTER ──────────────────────────────────────────
  const filterBtns = document.querySelectorAll('.filter-btn');
  const productCards = document.querySelectorAll('.product-card[data-category]');
  if (filterBtns.length > 0) {
    filterBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        filterBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const cat = btn.dataset.cat;
        productCards.forEach(card => {
          const show = cat === 'all' || card.dataset.category === cat;
          card.style.display = show ? '' : 'none';
        });
      });
    });
  }

  // ── TOAST NOTIFICATIONS ─────────────────────────────────────
  window.showToast = (msg, type = 'default') => {
    let wrap = document.querySelector('.toast-wrap');
    if (!wrap) {
      wrap = document.createElement('div');
      wrap.className = 'toast-wrap';
      document.body.appendChild(wrap);
    }
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = msg;
    wrap.appendChild(toast);
    setTimeout(() => toast.remove(), 3100);
  };

  // ── CART QUANTITY UPDATE ─────────────────────────────────────
  document.querySelectorAll('.qty-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
      const action = btn.dataset.action;
      const cartId = btn.dataset.id;
      const valEl = btn.parentElement.querySelector('.qty-val');
      let qty = parseInt(valEl.textContent);
      if (action === 'inc') qty++;
      else if (action === 'dec' && qty > 1) qty--;
      else return;

      try {
        const res = await fetch('cart_action.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `action=update&cart_id=${cartId}&qty=${qty}&csrf=${document.querySelector('meta[name="csrf"]')?.content || ''}`
        });
        const data = await res.json();
        if (data.success) {
          valEl.textContent = qty;
          // Update price row
          const priceEl = btn.closest('tr')?.querySelector('.item-total');
          if (priceEl && data.item_total) priceEl.textContent = data.item_total + ' MT';
          // Update cart total
          const totalEl = document.querySelector('.cart-grand-total');
          if (totalEl && data.total) totalEl.textContent = data.total + ' MT';
          const badgeEl = document.querySelector('.cart-badge');
          if (badgeEl && data.count) badgeEl.textContent = data.count;
        }
      } catch(e) {}
    });
  });

  // ── REMOVE FROM CART ─────────────────────────────────────────
  document.querySelectorAll('.btn-remove').forEach(btn => {
    btn.addEventListener('click', async () => {
      const cartId = btn.dataset.id;
      if (!confirm('Remover este item?')) return;
      try {
        const res = await fetch('cart_action.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `action=remove&cart_id=${cartId}&csrf=${document.querySelector('meta[name="csrf"]')?.content || ''}`
        });
        const data = await res.json();
        if (data.success) {
          btn.closest('tr')?.remove();
          const totalEl = document.querySelector('.cart-grand-total');
          if (totalEl && data.total !== undefined) totalEl.textContent = data.total + ' MT';
          const badgeEl = document.querySelector('.cart-badge');
          if (badgeEl && data.count !== undefined) badgeEl.textContent = data.count;
          if (data.count === 0) location.reload();
          showToast('Produto removido', 'error');
        }
      } catch(e) {}
    });
  });

  // ── ADD TO CART ──────────────────────────────────────────────
  document.querySelectorAll('.btn-add-cart').forEach(btn => {
    btn.addEventListener('click', async () => {
      const productId = btn.dataset.id;
      const csrf = document.querySelector('meta[name="csrf"]')?.content || '';
      const original = btn.innerHTML;
      btn.disabled = true;
      try {
        const res = await fetch('cart_action.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `action=add&product_id=${productId}&csrf=${csrf}`
        });
        const data = await res.json();
        if (data.success) {
          btn.classList.add('added');
          btn.innerHTML = '✓ Adicionado';
          const badge = document.querySelector('.cart-badge');
          if (badge) badge.textContent = data.count;
          showToast('Adicionado ao carrinho! 🛒', 'success');
          setTimeout(() => {
            btn.classList.remove('added');
            btn.innerHTML = original;
            btn.disabled = false;
          }, 2000);
        } else {
          showToast(data.message || 'Erro ao adicionar', 'error');
          btn.disabled = false;
        }
      } catch(e) {
        btn.disabled = false;
      }
    });
  });

  // ── WHATSAPP CHECKOUT ────────────────────────────────────────
  const btnCheckout = document.querySelector('.btn-checkout');
  if (btnCheckout) {
    btnCheckout.addEventListener('click', () => {
      const items = [];
      document.querySelectorAll('.cart-table tbody tr').forEach(row => {
        const name = row.querySelector('.cart-product-name')?.textContent?.trim();
        const qty  = row.querySelector('.qty-val')?.textContent?.trim();
        const price= row.querySelector('.item-total')?.textContent?.trim();
        if (name) items.push(`• ${name} x${qty} — ${price}`);
      });
      const total = document.querySelector('.cart-grand-total')?.textContent?.trim();
      if (items.length === 0) { showToast('O carrinho está vazio', 'error'); return; }
      const msg = `Olá StrideOn, gostaria de finalizar esta compra:\n\n${items.join('\n')}\n\n*Total: ${total}*`;
      const url = `https://wa.me/258879745377?text=${encodeURIComponent(msg)}`;
      window.open(url, '_blank');
    });
  }

});
