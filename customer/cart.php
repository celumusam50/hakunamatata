<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>My Cart — Hakuna Matata</title>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@700;800;900&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:'DM Sans',sans-serif;background:#F7F6F3;color:#2E2E2E;min-height:100vh}
:root{
  --gold:#F5A623;--gold-hover:#E09418;--gold-light:rgba(245,166,35,.1);
  --charcoal:#363636;--charcoal-2:#252525;
  --bg:#F7F6F3;--card:#fff;--border:#E6E5E1;--muted:#9A9993;--text:#2E2E2E;
  --red:#E8394D;--green:#22C55E;--radius:14px;
  --shadow:0 2px 12px rgba(0,0,0,.07),0 1px 3px rgba(0,0,0,.04);
}

/* ── HEADER ── */
.site-header{position:sticky;top:0;z-index:100;width:100%;background:var(--charcoal-2);box-shadow:0 2px 16px rgba(0,0,0,.3);overflow:hidden}
.header-blob{position:absolute;border-radius:50%;background:var(--gold);opacity:.07;pointer-events:none}
.header-blob.b1{width:300px;height:300px;bottom:-150px;left:-80px}
.header-blob.b2{width:220px;height:220px;top:-100px;right:60px}
.header-inner{position:relative;z-index:1;display:flex;align-items:center;gap:16px;width:100%;padding:0 40px;height:100px}
.brand{display:flex;align-items:center;gap:14px;text-decoration:none;flex-shrink:0}
.brand-logo{width:68px;height:68px;background:#fff;border-radius:14px;overflow:hidden;display:flex;align-items:center;justify-content:center;box-shadow:0 6px 20px rgba(0,0,0,.3);flex-shrink:0}
.brand-logo img{width:100%;height:100%;object-fit:cover}
.brand-name{font-family:'Nunito',sans-serif;font-weight:900;font-size:22px;color:#fff;line-height:1.15}
.brand-name em{font-style:normal;color:var(--gold)}
.brand-sub{font-size:11px;color:rgba(255,255,255,.4);display:block;margin-top:2px}
.header-right{margin-left:auto;display:flex;align-items:center;gap:12px}
.btn-back{display:flex;align-items:center;gap:6px;padding:8px 18px;border-radius:22px;background:rgba(255,255,255,.08);border:1.5px solid rgba(255,255,255,.15);color:#fff;font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;text-decoration:none;transition:all .2s;white-space:nowrap}
.btn-back:hover{border-color:var(--gold);color:var(--gold)}
.btn-back svg{width:15px;height:15px}
.user-chip{display:flex;align-items:center;gap:8px}
.user-name{font-size:13px;font-weight:600;color:rgba(255,255,255,.75);white-space:nowrap}
.btn-logout{padding:6px 13px;border-radius:16px;border:1.5px solid rgba(255,255,255,.2);background:transparent;color:rgba(255,255,255,.5);font-size:12px;font-weight:600;cursor:pointer;transition:all .2s;font-family:'DM Sans',sans-serif}
.btn-logout:hover{border-color:#E8394D;color:#E8394D}

/* ── PAGE ── */
.page{max-width:960px;margin:0 auto;padding:36px 24px 80px}
.page-heading{font-family:'Nunito',sans-serif;font-size:28px;font-weight:900;color:var(--text);margin-bottom:6px;text-align:left!important;display:block;width:100%;padding-left:14px;border-left:4px solid var(--gold)}
.page-sub{font-size:14px;color:var(--muted);margin-bottom:28px}

/* ── STATE PANELS ── */
.state-panel{text-align:center;padding:80px 24px}
.state-panel svg{width:56px;height:56px;color:#d4d0cb;margin:0 auto 16px;display:block}
.state-panel h3{font-family:'Nunito',sans-serif;font-size:20px;font-weight:800;color:var(--charcoal);margin-bottom:8px}
.state-panel p{font-size:14px;color:var(--muted);margin-bottom:20px}
.btn-shop{display:inline-flex;align-items:center;gap:6px;padding:11px 24px;border-radius:22px;background:var(--gold);color:var(--charcoal-2);font-family:'Nunito',sans-serif;font-size:14px;font-weight:800;text-decoration:none;transition:background .2s}
.btn-shop:hover{background:var(--gold-hover)}

/* ── CART LAYOUT ── */
.cart-layout{display:grid;grid-template-columns:1fr 340px;gap:24px;align-items:start}

/* ── CART CARDS ── */
.cart-items{display:flex;flex-direction:column;gap:14px}
.cart-card{background:var(--card);border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow);padding:16px;display:flex;align-items:flex-start;gap:16px;transition:box-shadow .2s}
.cart-card:hover{box-shadow:0 6px 20px rgba(0,0,0,.1)}
.cart-img{width:72px;height:72px;border-radius:10px;background:#F2EDE4;flex-shrink:0;display:flex;align-items:center;justify-content:center;overflow:hidden}
.cart-img img{width:100%;height:100%;object-fit:contain}
.cart-img-emoji{font-size:28px}
.cart-info{flex:1;min-width:0}
.cart-name{font-family:'Nunito',sans-serif;font-weight:800;font-size:15px;color:var(--text);margin-bottom:2px}
.cart-cat{font-size:12px;color:var(--muted);margin-bottom:6px}

/* Add-on pills */
.addon-list{display:flex;flex-wrap:wrap;gap:4px;margin-bottom:8px}
.addon-pill{display:inline-flex;align-items:center;gap:4px;font-size:11px;padding:3px 9px;border-radius:20px;background:var(--gold-light);border:1px solid rgba(245,166,35,.25);color:#92600a;font-weight:600}
.addon-pill svg{width:10px;height:10px}

.cart-qty-row{display:flex;align-items:center;gap:8px;margin-top:2px}
.qty-btn{width:30px;height:30px;border-radius:8px;border:1.5px solid var(--border);background:var(--bg);color:var(--text);font-size:18px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .2s;flex-shrink:0;line-height:1}
.qty-btn:hover:not(:disabled){border-color:var(--gold);background:var(--gold-light)}
.qty-btn.minus:hover:not(:disabled){border-color:var(--red);background:#FFF2F2;color:var(--red)}
.qty-btn:disabled{opacity:.4;cursor:not-allowed}
.qty-val{font-size:14px;font-weight:700;color:var(--text);min-width:20px;text-align:center}
.cart-right{display:flex;flex-direction:column;align-items:flex-end;gap:8px;flex-shrink:0}
.line-total{font-family:'Nunito',sans-serif;font-size:18px;font-weight:900;color:var(--text)}
.unit-price{font-size:11px;color:var(--muted)}
.btn-remove{background:none;border:none;cursor:pointer;color:#d4d0cb;padding:4px;border-radius:6px;transition:color .2s;display:flex;align-items:center}
.btn-remove:hover{color:var(--red)}
.btn-remove svg{width:17px;height:17px}
.unavail-badge{display:inline-block;font-size:11px;padding:2px 7px;border-radius:6px;background:#FFF2F2;color:#C0392B;border:1px solid #FFCACA;font-weight:600;margin-left:6px;vertical-align:middle}

/* ── ORDER SUMMARY ── */
.summary-card{background:var(--card);border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow);padding:22px;position:sticky;top:120px}
.summary-title{font-family:'Nunito',sans-serif;font-size:17px;font-weight:900;color:var(--text);margin-bottom:18px}
.s-row{display:flex;justify-content:space-between;align-items:center;font-size:13.5px;color:var(--muted);margin-bottom:9px}
.s-row .label{font-weight:500}
.s-row .val{font-weight:600;color:var(--text)}
.s-row.vat .val{color:#6B4C00}
.s-row.discount .val{color:var(--green);font-weight:700}
.s-row.discount .label{color:var(--green)}
.s-divider{border:none;border-top:1.5px solid var(--border);margin:12px 0}
.s-row.total{font-family:'Nunito',sans-serif;font-size:20px;font-weight:900;color:var(--text)}
.s-row.total .val{color:var(--charcoal-2)}

/* Promo code area */
.promo-wrap{margin:14px 0}
.promo-label{font-size:12px;font-weight:600;color:var(--text);margin-bottom:6px;display:block}
.promo-row{display:flex;gap:8px}
.promo-input{flex:1;padding:9px 12px;border-radius:10px;border:1.5px solid var(--border);background:var(--bg);font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);outline:none;transition:border-color .2s;text-transform:uppercase;letter-spacing:.05em}
.promo-input:focus{border-color:var(--gold);background:#fff}
.promo-input.applied{border-color:var(--green);background:#F0FDF4;color:#166534}
.btn-apply{padding:9px 14px;border-radius:10px;background:var(--charcoal-2);color:#fff;font-family:'DM Sans',sans-serif;font-size:13px;font-weight:700;border:none;cursor:pointer;transition:background .2s;white-space:nowrap}
.btn-apply:hover{background:var(--charcoal)}
.btn-apply:disabled{opacity:.5;cursor:not-allowed}
.promo-msg{font-size:11.5px;margin-top:5px;padding:5px 10px;border-radius:7px;display:none}
.promo-msg.ok{display:block;color:#166534;background:#F0FDF4;border:1px solid #BBF7D0}
.promo-msg.err{display:block;color:#991b1b;background:#FFF2F2;border:1px solid #FFCACA}


/* ── SUMMARY ITEM LIST ── */
.s-items-list{margin-bottom:10px;background:var(--bg);border-radius:10px;padding:10px 12px}
.s-item-row{display:grid;grid-template-columns:1fr auto auto;align-items:center;gap:8px;font-size:12.5px;padding:6px 0;border-bottom:1px solid var(--border)}
.s-item-row:last-child{border-bottom:none}
.s-item-name{font-weight:600;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;text-align:left}
.s-item-qty{color:#92600a;font-size:11px;font-weight:700;white-space:nowrap;background:var(--gold-light);border:1px solid rgba(245,166,35,.25);border-radius:6px;padding:2px 7px}
.s-item-price{font-weight:700;color:var(--charcoal-2);white-space:nowrap;text-align:right;min-width:52px}

/* Checkout button */
.btn-pay{width:100%;padding:15px;border-radius:12px;background:var(--gold);color:var(--charcoal-2);font-family:'Nunito',sans-serif;font-size:16px;font-weight:900;border:none;cursor:pointer;transition:all .2s;margin-top:16px;display:flex;align-items:center;justify-content:center;gap:8px}
.btn-pay:hover:not(:disabled){background:var(--gold-hover);transform:translateY(-1px);box-shadow:0 6px 20px rgba(245,166,35,.4)}
.btn-pay:disabled{opacity:.5;cursor:not-allowed;transform:none}
.btn-pay svg{width:18px;height:18px}
.summary-note{font-size:11px;color:var(--muted);text-align:center;margin-top:10px;line-height:1.5}

/* ── SPINNER / TOAST ── */
.spinner-wrap{display:flex;justify-content:center;padding:60px}
.spinner{width:36px;height:36px;border:3px solid var(--border);border-top-color:var(--gold);border-radius:50%;animation:spin .7s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.toast{position:fixed;bottom:28px;left:50%;transform:translateX(-50%) translateY(16px);padding:11px 24px;border-radius:24px;font-size:13px;font-weight:600;font-family:'DM Sans',sans-serif;box-shadow:0 4px 20px rgba(0,0,0,.22);opacity:0;pointer-events:none;transition:opacity .25s,transform .25s;z-index:999;white-space:nowrap;background:var(--charcoal-2);color:#fff}
.toast.show{opacity:1;transform:translateX(-50%) translateY(0)}
.toast.success{background:#166534}
.toast.error{background:#991b1b}

/* ── RESPONSIVE ── */
@media(max-width:800px){
  .cart-layout{grid-template-columns:1fr}
  .header-inner,.page{padding-left:20px;padding-right:20px}
  .brand-sub{display:none}
  .summary-card{position:static}
}
@media(max-width:480px){
  .brand-logo{width:52px;height:52px}
  .brand-name{font-size:18px}
  .header-inner{height:80px}
}
</style>
</head>
<body>

<!-- HEADER -->
<header class="site-header">
  <div class="header-blob b1"></div>
  <div class="header-blob b2"></div>
  <div class="header-inner">
    <a href="../shop.php" class="brand">
      <div class="brand-logo">
        <img src="../hakuna matata.png" alt="Hakuna Matata Logo"/>
      </div>
      <div>
        <span class="brand-name">Hakuna <em>Matata</em></span>
        <span class="brand-sub">Online Point of Sale</span>
      </div>
    </a>
    <div class="header-right">
      <a href="../shop.php" class="btn-back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Continue Shopping
      </a>
      <div id="auth-area"></div>
    </div>
  </div>
</header>

<!-- PAGE -->
<div class="page">
  <h1 class="page-heading">My Cart</h1>
  <p class="page-sub" id="cart-sub">Loading your cart…</p>

  <div id="loading-state" class="spinner-wrap"><div class="spinner"></div></div>

  <div id="empty-state" class="state-panel" style="display:none">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
      <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
      <path d="M1 1h4l2.68 13.39a2 2 0 001.99 1.61h9.72a2 2 0 001.99-1.61L23 6H6"/>
    </svg>
    <h3>Your cart is empty</h3>
    <p>Add some items from our menu!</p>
    <a href="../shop.php" class="btn-shop">Browse Products</a>
  </div>

  <div id="error-state" class="state-panel" style="display:none">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
      <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    <h3>Could not load cart</h3>
    <p>Please check your connection and try again.</p>
    <a href="../shop.php" class="btn-shop">Back to Shop</a>
  </div>

  <!-- Cart content -->
  <div id="cart-content" class="cart-layout" style="display:none">
    <!-- Left: items -->
    <div class="cart-items" id="cart-items"></div>

    <!-- Right: Order Summary -->
    <div class="summary-card">
      <div class="summary-title">Order Summary</div>

      <!-- Product list -->
      <div class="s-items-list" id="s-items-list"></div>
      <hr class="s-divider" id="s-items-divider" style="display:none"/>

      <div class="s-row"><span class="label">Subtotal</span><span class="val" id="s-subtotal">E0.00</span></div>
      <div class="s-row" id="s-addon-row" style="display:none"><span class="label">Add-ons</span><span class="val" id="s-addons">E0.00</span></div>
      <div class="s-row vat"><span class="label">VAT (15%)</span><span class="val" id="s-vat">E0.00</span></div>
      <div class="s-row discount" id="s-discount-row" style="display:none">
        <span class="label" id="s-discount-label">Discount</span>
        <span class="val" id="s-discount">-E0.00</span>
      </div>

      <hr class="s-divider"/>
      <div class="s-row total"><span>Total</span><span id="s-total">E0.00</span></div>

      <!-- Promo code -->
      <div class="promo-wrap">
        <span class="promo-label">Promo Code</span>
        <div class="promo-row">
          <input type="text" class="promo-input" id="promo-input" placeholder="e.g. WELCOME10" maxlength="30"/>
          <button class="btn-apply" id="btn-apply" onclick="applyPromo()">Apply</button>
        </div>
        <div class="promo-msg" id="promo-msg"></div>
      </div>

      <button class="btn-pay" id="btn-pay" onclick="goPayment()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>
        </svg>
        Proceed to Checkout
      </button>
      <p class="summary-note">Prices include 15% VAT · Delivery fee at checkout</p>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
const API     = 'http://localhost/hakunamatata_v3/api';
const VAT_RATE = 0.15;

// ── Auth helpers ──────────────────────────────────────────────────────
function getToken()   { return localStorage.getItem('access_token'); }
function getRefresh() { return localStorage.getItem('refresh_token'); }
function getUser()    { return JSON.parse(localStorage.getItem('user') || 'null'); }

function forceLogout() {
  ['access_token','refresh_token','user'].forEach(k => localStorage.removeItem(k));
  window.location.href = '../login.php';
}
function logout() {
  const rt = getRefresh();
  if (rt) fetch(API+'/auth/logout',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({refresh_token:rt})}).catch(()=>{});
  ['access_token','refresh_token','user'].forEach(k => localStorage.removeItem(k));
  window.location.href = '../login.php';
}
async function refreshToken() {
  const rt = getRefresh(); if (!rt) return null;
  try {
    const res  = await fetch(API+'/auth/refresh',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({refresh_token:rt})});
    const data = await res.json(); if (!res.ok) return null;
    localStorage.setItem('access_token', data.data.access_token);
    localStorage.setItem('refresh_token',data.data.refresh_token);
    return data.data.access_token;
  } catch { return null; }
}

// ── State ─────────────────────────────────────────────────────────────
let cartMap    = {};   // cart_id → full item (including addon_ids)
let addonMap   = {};   // addon_id → {addon_id, name, price}
let discount   = null; // null | {type:'percentage'|'fixed', value:X, code:Y}
let totals     = { subtotal:0, addonTotal:0, vat:0, discountAmt:0, total:0 };

// ── Boot ──────────────────────────────────────────────────────────────
(function init() {
  const user = getUser(); if (!getToken() || !user) { forceLogout(); return; }
  const name = (user.full_name || user.email || 'there').split(' ')[0];
  document.getElementById('auth-area').innerHTML =
    '<div class="user-chip"><span class="user-name">Hi, '+esc(name)+'</span>'+
    '<button class="btn-logout" onclick="logout()">Logout</button></div>';
  loadCart();
})();

// ── Helpers ───────────────────────────────────────────────────────────
function esc(s) { const d=document.createElement('div'); d.textContent=s||''; return d.innerHTML; }
function fmt(v) { return 'E'+parseFloat(v||0).toFixed(2); }
function showToast(msg,type) {
  const el=document.getElementById('toast');
  el.textContent=msg; el.className='toast show'+(type?' '+type:'');
  clearTimeout(el._t); el._t=setTimeout(()=>{el.className='toast';},2800);
}
function showState(s) {
  document.getElementById('loading-state').style.display = s==='loading'?'flex':'none';
  document.getElementById('empty-state').style.display   = s==='empty'  ?'block':'none';
  document.getElementById('error-state').style.display   = s==='error'  ?'block':'none';
  document.getElementById('cart-content').style.display  = s==='cart'   ?'grid' :'none';
}

// ── Fetch addon details for all products that have addons ──────────────
async function loadAddonDetails(items) {
  const pidsWithAddons = [...new Set(
    items.filter(i => i.addon_ids && i.addon_ids.length > 0).map(i => i.product_id)
  )];
  await Promise.all(pidsWithAddons.map(async (pid) => {
    try {
      const res  = await fetch(API+'/products/'+pid, {headers:{Accept:'application/json'}});
      const data = await res.json();
      if (res.ok && data.data && data.data.addons) {
        data.data.addons.forEach(a => { addonMap[a.addon_id] = a; });
      }
    } catch {}
  }));
}

// ── Load Cart ─────────────────────────────────────────────────────────
async function loadCart() {
  showState('loading');
  let authToken = getToken();
  try {
    let res = await fetch(API+'/cart',{headers:{Accept:'application/json','Authorization':'Bearer '+authToken}});
    if (res.status===401) {
      authToken = await refreshToken();
      if (!authToken) { forceLogout(); return; }
      res = await fetch(API+'/cart',{headers:{Accept:'application/json','Authorization':'Bearer '+authToken}});
    }
    if (!res.ok) throw new Error();
    const data  = await res.json();
    const items = data.data?.items || [];

    // Build cartMap for addon_ids lookup
    cartMap = {};
    items.forEach(i => { cartMap[i.cart_id] = i; });

    // Load addon names if any item has addon_ids
    await loadAddonDetails(items);

    renderCart(items);
  } catch { showState('error'); }
}

// ── Calculate & update summary ─────────────────────────────────────────
function calcTotals(items) {
  let subtotal   = 0;
  let addonTotal = 0;

  items.forEach(item => {
    subtotal += parseFloat(item.subtotal || 0); // base price × qty from API
    // Add addon prices
    const ids = item.addon_ids || [];
    ids.forEach(id => {
      const a = addonMap[id];
      if (a) addonTotal += parseFloat(a.price || 0) * item.quantity;
    });
  });

  const preVat     = subtotal + addonTotal;
  const vat        = preVat * VAT_RATE;
  let   discountAmt= 0;
  if (discount) {
    if (discount.type === 'percentage') {
      discountAmt = (preVat + vat) * (discount.value / 100);
    } else {
      discountAmt = Math.min(discount.value, preVat + vat); // fixed, can't exceed total
    }
    discountAmt = Math.max(0, discountAmt);
  }
  const total = preVat + vat - discountAmt;

  totals = { subtotal, addonTotal, vat, discountAmt, total };


  // Render per-item rows in order summary
  const listEl  = document.getElementById('s-items-list');
  const divEl   = document.getElementById('s-items-divider');
  if (listEl) {
    listEl.innerHTML = items.map(item => {
      const addonIds      = item.addon_ids || [];
      const addonLineAmt  = addonIds.reduce((s,id)=>{
        const a=addonMap[id]; return s+(a?parseFloat(a.price||0):0);
      },0) * item.quantity;
      const lineTotal = parseFloat(item.subtotal||0) + addonLineAmt;
      return '<div class="s-item-row">'+
        '<span class="s-item-name" title="'+esc(item.name)+'">'+esc(item.name)+'</span>'+
        '<span class="s-item-qty">&times;'+item.quantity+'</span>'+
        '<span class="s-item-price">'+fmt(lineTotal)+'</span>'+
      '</div>';
    }).join('');
    if (divEl) divEl.style.display = items.length ? '' : 'none';
  }

  // Update DOM
  document.getElementById('s-subtotal').textContent = fmt(subtotal);
  const addonRow = document.getElementById('s-addon-row');
  if (addonTotal > 0) {
    document.getElementById('s-addons').textContent = fmt(addonTotal);
    addonRow.style.display = 'flex';
  } else { addonRow.style.display = 'none'; }
  document.getElementById('s-vat').textContent = fmt(vat);

  const discRow = document.getElementById('s-discount-row');
  if (discountAmt > 0) {
    document.getElementById('s-discount').textContent = '-'+fmt(discountAmt);
    document.getElementById('s-discount-label').textContent =
      'Discount ('+(discount.type==='percentage'?discount.value+'%':'Fixed')+')';
    discRow.style.display = 'flex';
  } else { discRow.style.display = 'none'; }

  document.getElementById('s-total').textContent = fmt(total);
}

// ── Render Cart ───────────────────────────────────────────────────────
function renderCart(items) {
  if (!items.length) {
    document.getElementById('cart-sub').textContent = 'Your cart is empty.';
    showState('empty'); return;
  }

  document.getElementById('cart-sub').textContent =
    items.length+' item'+(items.length>1?'s':'')+' in your cart';

  document.getElementById('cart-items').innerHTML = items.map(item => {
    const addonIds  = item.addon_ids || [];
    const addonHtml = addonIds.length
      ? '<div class="addon-list">'+
          addonIds.map(id => {
            const a = addonMap[id];
            return a
              ? '<span class="addon-pill">✓ '+esc(a.name)+(parseFloat(a.price)>0?' +'+fmt(a.price):' Free')+'</span>'
              : '';
          }).join('')+
        '</div>'
      : '';

    const imgHtml = item.img_url
      ? '<img src="'+esc(item.img_url)+'" alt="'+esc(item.name)+'" onerror="this.style.display=\'none\'"/>'
      : '<span class="cart-img-emoji">'+(item.emoji||'🥩')+'</span>';

    const unavail = !item.is_available
      ? '<span class="unavail-badge">Unavailable</span>' : '';

    // Line total = base subtotal + addons for this item
    const addonLineTotal = addonIds.reduce((s,id)=>{
      const a=addonMap[id]; return s+(a?parseFloat(a.price||0):0);
    },0) * item.quantity;
    const lineTotal = parseFloat(item.subtotal||0) + addonLineTotal;

    return (
      '<div class="cart-card" id="row-'+item.cart_id+'">'+
        '<div class="cart-img">'+imgHtml+'</div>'+
        '<div class="cart-info">'+
          '<div class="cart-name">'+esc(item.name)+unavail+'</div>'+
          '<div class="cart-cat">'+esc(item.category_name||'')+' · '+esc(item.category_type||'')+'</div>'+
          addonHtml+
          '<div class="cart-qty-row">'+
            '<button class="qty-btn minus" onclick="changeQty('+item.cart_id+','+item.product_id+','+(item.quantity-1)+')">−</button>'+
            '<span class="qty-val">'+item.quantity+'</span>'+
            '<button class="qty-btn" onclick="changeQty('+item.cart_id+','+item.product_id+','+(item.quantity+1)+')">+</button>'+
            '<span style="font-size:11px;color:var(--muted);margin-left:4px">'+fmt(item.price)+' each</span>'+
          '</div>'+
        '</div>'+
        '<div class="cart-right">'+
          '<div class="line-total">'+fmt(lineTotal)+'</div>'+
          '<button class="btn-remove" onclick="removeItem('+item.cart_id+')" title="Remove">'+
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">'+
              '<polyline points="3 6 5 6 21 6"/>'+
              '<path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>'+
              '<path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/>'+
            '</svg>'+
          '</button>'+
        '</div>'+
      '</div>'
    );
  }).join('');

  calcTotals(items);
  showState('cart');
}

// ── Change Quantity — preserves addon_ids ──────────────────────────────
async function changeQty(cartId, productId, newQty) {
  if (newQty < 1) { removeItem(cartId); return; }
  const authToken = getToken();
  // Retrieve stored addon_ids so they are not lost on update
  const savedItem = cartMap[cartId] || {};
  const addonIds  = savedItem.addon_ids || [];
  const notes     = savedItem.notes     || '';

  try {
    const res = await fetch(API+'/cart',{
      method:'POST',
      headers:{'Content-Type':'application/json','Accept':'application/json','Authorization':'Bearer '+authToken},
      body: JSON.stringify({ product_id: productId, quantity: newQty, addon_ids: addonIds, notes })
    });
    if (!res.ok) {
      const d = await res.json();
      showToast(d.error||'Could not update quantity.','error'); return;
    }
    loadCart();
  } catch { showToast('Connection error.','error'); }
}

// ── Remove Item ────────────────────────────────────────────────────────
async function removeItem(cartId) {
  const authToken = getToken();
  const row = document.getElementById('row-'+cartId);
  if (row) { row.style.opacity='0.4'; row.style.pointerEvents='none'; }
  try {
    const res = await fetch(API+'/cart/'+cartId,{
      method:'DELETE',
      headers:{Accept:'application/json','Authorization':'Bearer '+authToken}
    });
    if (!res.ok) {
      const d = await res.json();
      showToast(d.error||'Could not remove.','error');
      if (row) { row.style.opacity='1'; row.style.pointerEvents=''; }
      return;
    }
    showToast('Item removed.','');
    loadCart();
  } catch {
    showToast('Connection error.','error');
    if (row) { row.style.opacity='1'; row.style.pointerEvents=''; }
  }
}

// ── Apply Promo Code ──────────────────────────────────────────────────
async function applyPromo() {
  const inp  = document.getElementById('promo-input');
  const msg  = document.getElementById('promo-msg');
  const btn  = document.getElementById('btn-apply');
  const code = inp.value.trim().toUpperCase();
  if (!code) { showPromoMsg('Please enter a promo code.','err'); return; }

  btn.disabled = true; btn.textContent = '…';
  msg.style.display = 'none'; inp.classList.remove('applied');

  try {
    const res  = await fetch(API+'/discounts/validate',{
      method:'POST',
      headers:{'Content-Type':'application/json','Accept':'application/json'},
      body: JSON.stringify({code})
    });
    const data = await res.json();
    if (!res.ok || !data.success) {
      showPromoMsg(data.error||'Invalid or expired promo code.','err');
      discount = null;
    } else {
      const d = data.data;
      discount = { code, type: d.type || d.discount_type, value: parseFloat(d.value || d.discount_value || 0) };
      inp.classList.add('applied');
      const label = discount.type==='percentage' ? discount.value+'% off' : 'E'+discount.value+' off';
      showPromoMsg('✓ '+label+' applied!','ok');
      // Re-calculate totals with discount
      const items = Object.values(cartMap);
      calcTotals(items);
    }
  } catch { showPromoMsg('Could not validate code. Try again.','err'); }
  finally  { btn.disabled=false; btn.textContent='Apply'; }
}

function showPromoMsg(text, type) {
  const el = document.getElementById('promo-msg');
  el.textContent = text; el.className = 'promo-msg '+type;
}

// Allow Enter key in promo input
document.getElementById('promo-input').addEventListener('keydown', e => {
  if (e.key === 'Enter') applyPromo();
});

// ── Proceed to Payment ─────────────────────────────────────────────────
function goPayment() {
  // Pass totals to checkout page via sessionStorage
  sessionStorage.setItem('cart_totals', JSON.stringify(totals));
  if (discount) sessionStorage.setItem('cart_discount', JSON.stringify(discount));
  window.location.href = 'checkout.php';
}
</script>
</body>
</html>
