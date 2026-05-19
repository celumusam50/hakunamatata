<?php
// customer.php — Cart, Checkout, Orders, Tracking
// Auth handled inline via localStorage (same pattern as login.php / index.php)
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Hakuna Matata — My Account</title>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@700;800;900&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
<style>
/* ── Reset ── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; }
body { font-family: 'DM Sans', sans-serif; background: #F7F6F3; color: #2E2E2E; min-height: 100vh; }

/* ── Tokens ── */
:root {
  --gold:       #F5A623;
  --gold-hover: #E09418;
  --gold-light: rgba(245,166,35,.1);
  --charcoal:   #363636;
  --dark:       #252525;
  --bg:         #F7F6F3;
  --surface:    #FFFFFF;
  --border:     #E6E5E1;
  --muted:      #9A9993;
  --text:       #2E2E2E;
  --red:        #E8394D;
  --green:      #22C55E;
  --radius:     14px;
  --shadow:     0 2px 12px rgba(0,0,0,.07), 0 1px 3px rgba(0,0,0,.04);
}

/* ══════════════════════════════════════════
   HEADER — identical to login.php / index.php
══════════════════════════════════════════ */
.site-header {
  position: sticky; top: 0; z-index: 100;
  width: 100%; background: var(--dark);
  box-shadow: 0 2px 16px rgba(0,0,0,.3);
  overflow: hidden;
}
.header-blob { position:absolute; border-radius:50%; background:var(--gold); opacity:.07; pointer-events:none; }
.header-blob.b1 { width:300px; height:300px; bottom:-150px; left:-80px; }
.header-blob.b2 { width:220px; height:220px; top:-100px;   right:60px; }
.header-blob.b3 { width:140px; height:140px; top:-40px;    left:45%; opacity:.04; }

.header-inner {
  position:relative; z-index:1;
  display:flex; align-items:center; gap:16px;
  width:100%; padding:0 40px; height:100px;
}
.brand { display:flex; align-items:center; gap:14px; text-decoration:none; flex-shrink:0; }
.brand-logo {
  width:68px; height:68px; background:white; border-radius:14px;
  overflow:hidden; display:flex; align-items:center; justify-content:center;
  box-shadow:0 6px 20px rgba(0,0,0,.3); flex-shrink:0;
  animation: popIn .55s cubic-bezier(.34,1.56,.64,1) both;
}
.brand-logo img { width:100%; height:100%; object-fit:cover; }
.brand-name { font-family:'Nunito',sans-serif; font-weight:900; font-size:22px; color:#fff; line-height:1.15; display:block; }
.brand-name em { font-style:normal; color:var(--gold); }
.brand-sub  { font-size:11px; color:rgba(255,255,255,.4); display:block; margin-top:2px; }

.header-actions { display:flex; align-items:center; gap:10px; margin-left:auto; flex-shrink:0; }
.user-chip { display:flex; align-items:center; gap:8px; }
.user-name  { font-size:13px; font-weight:600; color:rgba(255,255,255,.75); white-space:nowrap; }
.btn-logout {
  padding:6px 13px; border-radius:16px;
  border:1.5px solid rgba(255,255,255,.2); background:transparent;
  color:rgba(255,255,255,.5); font-size:12px; font-weight:600;
  cursor:pointer; transition:all .2s; font-family:'DM Sans',sans-serif;
}
.btn-logout:hover { border-color:var(--red); color:var(--red); }
.btn-login-link {
  padding:8px 20px; border-radius:22px; background:var(--gold);
  color:var(--dark); font-family:'Nunito',sans-serif; font-size:14px;
  font-weight:800; text-decoration:none; transition:background .2s;
}
.btn-login-link:hover { background:var(--gold-hover); }

/* ══════════════════════════════════════════
   TAB BAR
══════════════════════════════════════════ */
.tab-bar {
  background:#fff; border-bottom:1px solid var(--border);
  position:sticky; top:100px; z-index:90;
  box-shadow:0 2px 8px rgba(0,0,0,.05);
}
.tab-bar-inner { display:flex; align-items:center; gap:4px; padding:0 40px; height:52px; }
.tab-btn {
  display:flex; align-items:center; gap:7px;
  padding:8px 18px; border-radius:22px; border:none;
  background:transparent; color:var(--muted);
  font-family:'DM Sans',sans-serif; font-size:13px; font-weight:600;
  cursor:pointer; transition:all .2s; white-space:nowrap;
}
.tab-btn:hover  { color:var(--text); background:var(--bg); }
.tab-btn.active { background:var(--dark); color:#fff; }
.tab-btn svg    { width:15px; height:15px; flex-shrink:0; }
.tab-badge {
  background:var(--gold); color:var(--dark); border-radius:10px;
  min-width:18px; height:18px; font-size:10px; font-weight:800;
  padding:0 5px; display:inline-flex; align-items:center; justify-content:center;
}
.tab-badge.hidden { display:none; }

/* ══════════════════════════════════════════
   PAGE CONTENT
══════════════════════════════════════════ */
.page-wrap { padding:32px 40px 60px; max-width:860px; }
.section { display:none; }
.section.active { display:block; }

.section-title { font-family:'Nunito',sans-serif; font-size:22px; font-weight:900; margin-bottom:4px; }
.section-sub   { font-size:13.5px; color:var(--muted); margin-bottom:24px; }

/* ── Panel card ── */
.panel {
  background:var(--surface); border-radius:var(--radius);
  border:1px solid var(--border); box-shadow:var(--shadow);
  padding:20px; margin-bottom:16px;
}

/* ══════════════════════════════════════════
   CART ITEMS
══════════════════════════════════════════ */
.cart-item {
  display:flex; align-items:center; gap:14px;
  padding:14px 0; border-bottom:1px solid var(--border);
}
.cart-item:last-child { border-bottom:none; }
.cart-img {
  width:58px; height:58px; border-radius:10px; background:#F2EDE4;
  overflow:hidden; flex-shrink:0;
  display:flex; align-items:center; justify-content:center;
}
.cart-img img { width:80%; height:80%; object-fit:contain; }
.cart-img svg { width:28px; height:28px; opacity:.25; }
.cart-info { flex:1; min-width:0; }
.cart-name {
  font-family:'Nunito',sans-serif; font-size:14px; font-weight:800;
  white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}
.cart-unit { font-size:12px; color:var(--muted); margin-top:2px; }

.qty-ctrl {
  display:flex; align-items:center;
  border:1.5px solid var(--border); border-radius:22px; overflow:hidden; flex-shrink:0;
}
.qty-btn {
  width:30px; height:30px; border:none; background:transparent;
  cursor:pointer; font-size:16px; font-weight:700; color:var(--text);
  display:flex; align-items:center; justify-content:center; transition:background .15s;
}
.qty-btn:hover { background:var(--bg); }
.qty-val  { min-width:28px; text-align:center; font-size:13px; font-weight:700; }

.cart-line {
  font-family:'Nunito',sans-serif; font-size:15px; font-weight:900;
  min-width:70px; text-align:right; flex-shrink:0;
}
.btn-remove {
  width:28px; height:28px; border-radius:50%;
  border:1.5px solid var(--border); background:transparent;
  cursor:pointer; color:var(--muted);
  display:flex; align-items:center; justify-content:center;
  font-size:13px; transition:all .2s; flex-shrink:0;
}
.btn-remove:hover { border-color:var(--red); color:var(--red); background:#FEE2E2; }

/* Cart summary */
.sum-row {
  display:flex; justify-content:space-between; align-items:center;
  padding:8px 0; font-size:14px; color:var(--muted);
  border-bottom:1px solid var(--border);
}
.sum-row:last-of-type { border-bottom:none; }
.sum-row.total {
  font-family:'Nunito',sans-serif; font-size:18px; font-weight:900;
  color:var(--text); margin-top:4px;
}
.sum-val { font-weight:700; color:var(--text); }

/* Discount */
.disc-row { display:flex; gap:8px; margin:12px 0 6px; }
.disc-input {
  flex:1; padding:9px 14px; border:1.5px solid var(--border); border-radius:22px;
  font-family:'DM Sans',sans-serif; font-size:13px; outline:none;
  background:var(--bg); text-transform:uppercase; letter-spacing:.05em;
}
.disc-input:focus { border-color:var(--gold); background:#fff; }
.btn-apply {
  padding:9px 16px; border-radius:22px; background:var(--bg);
  border:1.5px solid var(--border); font-size:13px; font-weight:600;
  cursor:pointer; transition:all .2s; font-family:'DM Sans',sans-serif;
}
.btn-apply:hover { border-color:var(--gold); }
.disc-msg { font-size:12px; font-weight:600; display:none; }
.disc-msg.ok  { display:block; color:var(--green); }
.disc-msg.err { display:block; color:var(--red); }

/* ══════════════════════════════════════════
   BUTTONS
══════════════════════════════════════════ */
.btn-gold {
  width:100%; padding:13px; border-radius:22px;
  background:var(--gold); color:var(--dark);
  font-family:'Nunito',sans-serif; font-size:15px; font-weight:800;
  border:none; cursor:pointer; transition:background .2s, transform .1s;
  display:flex; align-items:center; justify-content:center; gap:8px;
  margin-top:16px;
}
.btn-gold:hover    { background:var(--gold-hover); }
.btn-gold:active   { transform:scale(.98); }
.btn-gold:disabled { opacity:.5; cursor:not-allowed; transform:none; }

.btn-ghost {
  width:100%; padding:11px; border-radius:22px;
  background:transparent; color:var(--text);
  font-family:'DM Sans',sans-serif; font-size:14px; font-weight:600;
  border:1.5px solid var(--border); cursor:pointer; transition:all .2s;
  display:flex; align-items:center; justify-content:center; gap:8px;
  margin-top:8px;
}
.btn-ghost:hover { border-color:var(--dark); }

.btn-back {
  display:inline-flex; align-items:center; gap:5px;
  font-size:13px; font-weight:600; color:var(--muted);
  background:none; border:none; cursor:pointer; transition:color .2s; padding:0;
}
.btn-back:hover { color:var(--text); }
.btn-back svg { width:15px; height:15px; }

/* Spinner */
.spin { width:16px; height:16px; border:2.5px solid rgba(0,0,0,.2); border-top-color:var(--dark); border-radius:50%; animation:spin .7s linear infinite; display:none; }
.loading .spin { display:block; }
.loading .lbl  { display:none; }
@keyframes spin { to { transform:rotate(360deg); } }

/* ══════════════════════════════════════════
   CHECKOUT FORM
══════════════════════════════════════════ */
.co-header { display:flex; align-items:center; gap:10px; margin-bottom:20px; }
.co-title  { font-family:'Nunito',sans-serif; font-size:22px; font-weight:900; margin:0; }

/* Fulfillment toggle */
.fulfill-row { display:flex; gap:10px; margin-bottom:20px; }
.fulfill-opt {
  flex:1; padding:14px 10px; border-radius:var(--radius);
  border:2px solid var(--border); background:var(--bg);
  cursor:pointer; text-align:center; transition:all .2s;
}
.fulfill-opt.sel { border-color:var(--gold); background:rgba(245,166,35,.06); }
.fulfill-opt svg { width:22px; height:22px; color:var(--muted); display:block; margin:0 auto 6px; }
.fulfill-opt.sel svg { color:var(--gold); }
.fulfill-opt h4  { font-family:'Nunito',sans-serif; font-size:14px; font-weight:800; margin-bottom:2px; }
.fulfill-opt p   { font-size:11px; color:var(--muted); }

/* Fields */
.f-group  { margin-bottom:14px; }
.f-label  { display:block; font-size:11px; font-weight:700; color:var(--muted); margin-bottom:5px; text-transform:uppercase; letter-spacing:.4px; }
.f-input, .f-select {
  width:100%; padding:10px 14px; border:1.5px solid var(--border); border-radius:10px;
  font-family:'DM Sans',sans-serif; font-size:14px; color:var(--text);
  background:var(--bg); outline:none; transition:border-color .2s, background .2s;
  -webkit-appearance:none;
}
.f-input:focus, .f-select:focus { border-color:var(--gold); background:#fff; }
.f-row  { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.f-note { font-size:11.5px; color:var(--muted); margin-top:4px; }

/* Payment warning */
.pay-warn {
  background:#FFF8EC; border:1.5px solid rgba(245,166,35,.35);
  border-radius:10px; padding:12px 14px; margin-bottom:14px;
  font-size:13px; color:#78350F; line-height:1.55;
}

/* Mini order summary */
.mini-row {
  display:flex; justify-content:space-between;
  font-size:13px; color:var(--muted); padding:4px 0;
}
.mini-row.tot {
  font-family:'Nunito',sans-serif; font-size:17px; font-weight:900;
  color:var(--text); border-top:1px solid var(--border);
  padding-top:8px; margin-top:4px;
}
.mini-row .v { font-weight:700; color:var(--text); }

/* Alert */
.alert {
  padding:11px 14px; border-radius:10px; font-size:13px;
  font-weight:500; margin-bottom:14px; display:none;
  border:1px solid;
}
.alert.show { display:block; }
.alert.err  { background:#FEE2E2; color:#991B1B; border-color:#FECACA; }
.alert.ok   { background:#D1FAE5; color:#065F46; border-color:#A7F3D0; }

/* ══════════════════════════════════════════
   ORDERS
══════════════════════════════════════════ */
.order-card {
  background:var(--surface); border-radius:var(--radius);
  border:1px solid var(--border); box-shadow:var(--shadow);
  padding:16px 20px; margin-bottom:12px;
  display:flex; align-items:center; gap:14px;
  cursor:pointer; transition:box-shadow .2s, transform .2s;
}
.order-card:hover { box-shadow:0 6px 20px rgba(0,0,0,.1); transform:translateY(-1px); }
.order-ref { font-family:'Nunito',sans-serif; font-size:18px; font-weight:900; flex-shrink:0; min-width:60px; }
.order-meta { flex:1; }
.order-items-txt { font-size:12.5px; color:var(--muted); margin-top:3px; }
.order-date-txt  { font-size:12px;   color:var(--muted); margin-top:2px; }
.order-amt  { font-family:'Nunito',sans-serif; font-size:16px; font-weight:900; flex-shrink:0; }
.chev svg   { width:16px; height:16px; color:var(--muted); }

/* Status badges */
.badge {
  display:inline-block; padding:3px 10px; border-radius:12px;
  font-size:11px; font-weight:700; font-family:'DM Sans',sans-serif;
}
.b-pending   { background:#FEF3C7; color:#92400E; }
.b-confirmed { background:#DBEAFE; color:#1E40AF; }
.b-preparing { background:#FEF3C7; color:#B45309; }
.b-ready     { background:#D1FAE5; color:#065F46; }
.b-dispatched{ background:#EDE9FE; color:#5B21B6; }
.b-done      { background:#D1FAE5; color:#065F46; }
.b-cancelled { background:#FEE2E2; color:#991B1B; }
.b-default   { background:#F3F4F6; color:#374151; }

/* ══════════════════════════════════════════
   TRACKING TIMELINE
══════════════════════════════════════════ */
.timeline {
  display:flex; align-items:flex-start;
  padding:20px 0; overflow-x:auto;
}
.tl-step {
  display:flex; flex-direction:column; align-items:center;
  flex:1; min-width:72px; position:relative;
}
.tl-dot {
  width:16px; height:16px; border-radius:50%;
  border:2.5px solid var(--border); background:#fff; z-index:1; flex-shrink:0;
}
.tl-line {
  position:absolute; top:7px; left:calc(50% + 8px);
  width:calc(100% - 16px); height:2px; background:var(--border);
}
.tl-step:last-child .tl-line { display:none; }
.tl-lbl { font-size:10.5px; color:var(--muted); text-align:center; margin-top:8px; line-height:1.3; }
.tl-step.done .tl-dot  { background:var(--green); border-color:var(--green); }
.tl-step.done .tl-line { background:var(--green); }
.tl-step.done .tl-lbl  { color:var(--green); font-weight:600; }
.tl-step.curr .tl-dot  { background:var(--gold); border-color:var(--gold); box-shadow:0 0 0 4px rgba(245,166,35,.2); }
.tl-step.curr .tl-lbl  { color:var(--charcoal); font-weight:700; }

/* OTP box */
.otp-box {
  background:linear-gradient(135deg, var(--dark) 0%, #1a1a1a 100%);
  border-radius:16px; padding:24px; text-align:center; color:#fff;
  border:1px solid rgba(245,166,35,.3); box-shadow:0 4px 24px rgba(0,0,0,.15);
  margin:20px 0;
}
.otp-label { font-size:13px; color:rgba(255,255,255,.5); margin-bottom:6px; }
.otp-code  { font-family:'Nunito',sans-serif; font-size:52px; font-weight:900; color:var(--gold); letter-spacing:14px; }
.otp-hint  { font-size:12px; color:rgba(255,255,255,.4); margin-top:8px; }

/* ══════════════════════════════════════════
   EMPTY STATES
══════════════════════════════════════════ */
.empty { text-align:center; padding:60px 24px; }
.empty svg { width:52px; height:52px; color:#d4d0cb; margin:0 auto 14px; display:block; }
.empty h3  { font-family:'Nunito',sans-serif; font-size:18px; font-weight:800; margin-bottom:6px; }
.empty p   { font-size:13.5px; color:var(--muted); }

/* Success */
.success-view { text-align:center; padding:40px 24px; }
.success-view svg { width:56px; height:56px; margin:0 auto 14px; display:block; color:var(--green); }
.success-view h2  { font-family:'Nunito',sans-serif; font-size:24px; font-weight:900; margin-bottom:8px; }
.success-view p   { font-size:14px; color:var(--muted); margin-bottom:16px; }
.success-ref { font-family:'Nunito',sans-serif; font-size:36px; font-weight:900; color:var(--gold); margin:16px 0; }

/* Toast */
.toast {
  position:fixed; bottom:28px; left:50%;
  transform:translateX(-50%) translateY(16px);
  padding:11px 24px; border-radius:24px;
  font-size:13px; font-weight:600; font-family:'DM Sans',sans-serif;
  box-shadow:0 4px 20px rgba(0,0,0,.22);
  opacity:0; pointer-events:none; transition:opacity .25s, transform .25s;
  z-index:999; white-space:nowrap; background:var(--dark); color:#fff;
}
.toast.show    { opacity:1; transform:translateX(-50%) translateY(0); }
.toast.success { background:#166534; }
.toast.error   { background:#991b1b; }

/* ── Animations ── */
@keyframes fadeUp { from { opacity:0; transform:translateY(14px); } to { opacity:1; transform:translateY(0); } }
@keyframes popIn  { from { opacity:0; transform:scale(.7); } to { opacity:1; transform:scale(1); } }

/* ── Responsive ── */
@media (max-width:700px) {
  .header-inner, .tab-bar-inner, .page-wrap { padding-left:20px; padding-right:20px; }
  .tab-bar { top:80px; }
  .header-inner { height:80px; }
  .brand-logo { width:52px; height:52px; }
  .brand-name { font-size:18px; }
  .brand-sub  { display:none; }
  .f-row { grid-template-columns:1fr; }
  .fulfill-row { flex-direction:column; }
}
</style>
</head>
<body>

<!-- ══ HEADER ══════════════════════════════════════════════════ -->
<header class="site-header">
  <div class="header-blob b1"></div>
  <div class="header-blob b2"></div>
  <div class="header-blob b3"></div>
  <div class="header-inner">
    <a href="index.php" class="brand">
      <div class="brand-logo">
        <img src="hakuna matata.png" alt="Hakuna Matata Logo"/>
      </div>
      <div>
        <span class="brand-name">Hakuna <em>Matata</em></span>
        <span class="brand-sub">Online Point of Sale</span>
      </div>
    </a>
    <div class="header-actions" id="auth-area">
      <!-- filled by JS -->
    </div>
  </div>
</header>

<!-- ══ TAB BAR ══════════════════════════════════════════════════ -->
<div class="tab-bar">
  <div class="tab-bar-inner">
    <button class="tab-btn active" id="tab-cart" onclick="switchTab('cart')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 001.99 1.61h9.72a2 2 0 001.99-1.61L23 6H6"/></svg>
      My Cart
      <span class="tab-badge hidden" id="cart-badge">0</span>
    </button>
    <button class="tab-btn" id="tab-orders" onclick="switchTab('orders')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
      My Orders
    </button>
    <button class="tab-btn" id="tab-track" onclick="switchTab('track')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      Track Order
    </button>
  </div>
</div>

<!-- ══ PAGE CONTENT ══════════════════════════════════════════════ -->
<div class="page-wrap">

  <!-- ─── CART SECTION ───────────────────────────────────────── -->
  <div id="section-cart" class="section active">

    <!-- Cart view -->
    <div id="view-cart">
      <h2 class="section-title">My Cart</h2>
      <p class="section-sub">Review your items before checkout</p>

      <div class="panel" id="cart-panel" style="display:none">
        <div id="cart-list"></div>
      </div>

      <div id="cart-empty" class="empty" style="display:none">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 001.99 1.61h9.72a2 2 0 001.99-1.61L23 6H6"/></svg>
        <h3>Your cart is empty</h3>
        <p>Browse our menu and add something delicious!</p>
        <a href="index.php" style="display:inline-block;margin-top:16px;padding:10px 24px;background:var(--gold);color:var(--dark);border-radius:22px;font-weight:800;font-family:'Nunito',sans-serif;text-decoration:none;">Browse Menu</a>
      </div>

      <div class="panel" id="summary-panel" style="display:none">
        <div class="disc-row">
          <input class="disc-input" type="text" id="disc-code" placeholder="PROMO CODE"/>
          <button class="btn-apply" onclick="applyDiscount()">Apply</button>
        </div>
        <div class="disc-msg" id="disc-msg"></div>
        <div id="sum-rows" style="margin-top:10px"></div>
        <button class="btn-gold" onclick="showCheckout()">
          <span class="lbl">Proceed to Checkout →</span>
          <span class="spin"></span>
        </button>
      </div>
    </div>

    <!-- Checkout view -->
    <div id="view-checkout" style="display:none">
      <div class="co-header">
        <button class="btn-back" onclick="showCartView()">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>Back to cart
        </button>
        <h2 class="co-title">Checkout</h2>
      </div>

      <div class="alert err" id="co-alert"></div>

      <!-- Fulfillment -->
      <div class="panel">
        <p class="f-label" style="margin-bottom:12px">How would you like to receive your order?</p>
        <div class="fulfill-row">
          <div class="fulfill-opt sel" id="opt-del" onclick="setFulfillment('delivery')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l19-9-9 19-2-8-8-2z"/></svg>
            <h4>Delivery</h4><p>We bring it to your door</p>
          </div>
          <div class="fulfill-opt" id="opt-col" onclick="setFulfillment('collection')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <h4>Collection</h4><p>Pick up in store</p>
          </div>
        </div>
      </div>

      <!-- Delivery address -->
      <div class="panel" id="addr-panel">
        <div class="f-group">
          <label class="f-label" for="f-street">Street / Area</label>
          <input class="f-input" type="text" id="f-street" placeholder="e.g. 12 Gwamile Street"/>
        </div>
        <div class="f-row">
          <div class="f-group">
            <label class="f-label" for="f-city">City</label>
            <select class="f-select" id="f-city">
              <option value="">Select city</option>
              <option>Mbabane</option><option>Manzini</option><option>Lobamba</option>
              <option>Siteki</option><option>Nhlangano</option><option>Big Bend</option>
              <option>Pigg's Peak</option><option>Lavumisa</option><option>Hlathikhulu</option>
              <option>Tshaneni</option>
            </select>
          </div>
          <div class="f-group">
            <label class="f-label" for="f-region">Region</label>
            <select class="f-select" id="f-region">
              <option value="">Select region</option>
              <option>Hhohho</option><option>Manzini</option>
              <option>Lubombo</option><option>Shiselweni</option>
            </select>
          </div>
        </div>
        <div class="f-group">
          <label class="f-label" for="f-notes">Special instructions <span style="font-weight:400;text-transform:none">(optional)</span></label>
          <input class="f-input" type="text" id="f-notes" placeholder="e.g. Gate code 1234, call on arrival"/>
        </div>
      </div>

      <!-- Payment -->
      <div class="panel">
        <p class="f-label" style="margin-bottom:12px">Payment — Mobile Money</p>
        <div class="pay-warn">
          ⚠️ <strong>Pay first, then enter your reference number below.</strong><br/>
          Transfer the total amount via Mobile Money, then enter the transaction reference you received. <strong>No reference = no order.</strong>
        </div>
        <div class="f-group">
          <label class="f-label" for="f-mobile">Mobile Money Number Used</label>
          <input class="f-input" type="text" id="f-mobile" placeholder="e.g. 26876000001" inputmode="numeric" maxlength="11"/>
        </div>
        <div class="f-group">
          <label class="f-label" for="f-ref">Transaction Reference * <span style="color:var(--red)">Required</span></label>
          <input class="f-input" type="text" id="f-ref" placeholder="e.g. MM-REF-123456" style="text-transform:uppercase" oninput="togglePlaceBtn()"/>
          <p class="f-note">* Order will NOT be created without a valid reference number.</p>
        </div>
      </div>

      <!-- Order summary -->
      <div class="panel">
        <p class="f-label" style="margin-bottom:10px">Order summary</p>
        <div id="co-summary"></div>
        <button class="btn-gold" id="btn-place" onclick="placeOrder()" disabled>
          <span class="lbl">Place Order</span>
          <span class="spin"></span>
        </button>
        <button class="btn-ghost" onclick="showCartView()">← Back to cart</button>
      </div>
    </div>

    <!-- Order success view -->
    <div id="view-success" style="display:none">
      <div class="success-view">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <h2>Order Placed!</h2>
        <p id="success-msg">Your order has been received and sent to the kitchen.</p>
        <div class="success-ref" id="success-ref"></div>
        <button class="btn-gold" style="max-width:260px;margin:0 auto" onclick="switchTab('orders')">View My Orders</button>
      </div>
    </div>

  </div><!-- /section-cart -->

  <!-- ─── ORDERS SECTION ─────────────────────────────────────── -->
  <div id="section-orders" class="section">
    <h2 class="section-title">My Orders</h2>
    <p class="section-sub">Your complete order history</p>
    <div id="orders-list"></div>
    <div id="orders-empty" class="empty" style="display:none">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/></svg>
      <h3>No orders yet</h3><p>Your orders will appear here once placed.</p>
    </div>
  </div>

  <!-- ─── TRACK SECTION ──────────────────────────────────────── -->
  <div id="section-track" class="section">
    <h2 class="section-title">Track Order</h2>
    <p class="section-sub">Live status of your active orders</p>
    <div id="track-list"></div>
    <div id="track-empty" class="empty" style="display:none">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      <h3>Nothing to track</h3><p>Active orders will show here once placed.</p>
    </div>
  </div>

</div><!-- /page-wrap -->

<div class="toast" id="toast"></div>

<!-- ══════════════════════════════════════════════════════════════
     JAVASCRIPT — all inline, same pattern as login.php / index.php
══════════════════════════════════════════════════════════════════ -->
<script>
// ── Config ──────────────────────────────────────────────────────────
const API = window.location.origin + '/api';
const DELIVERY_FEE = 80.00;

// ── Auth check ──────────────────────────────────────────────────────
const token = localStorage.getItem('access_token');
const user  = JSON.parse(localStorage.getItem('user') || 'null');

if (!token || !user) {
  window.location.href = 'login.php';
}

// Build header auth area
const authEl = document.getElementById('auth-area');
if (user && token) {
  authEl.innerHTML =
    '<div class="user-chip">' +
      '<span class="user-name">Hi, ' + esc(user.full_name.split(' ')[0]) + '</span>' +
      '<button class="btn-logout" onclick="logout()">Logout</button>' +
    '</div>';
}

function logout() {
  const rt = localStorage.getItem('refresh_token');
  if (rt) {
    fetch(API + '/auth/logout', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ refresh_token: rt })
    }).catch(() => {});
  }
  localStorage.removeItem('access_token');
  localStorage.removeItem('refresh_token');
  localStorage.removeItem('user');
  window.location.href = 'login.php';
}

// ── Helpers ──────────────────────────────────────────────────────────
function esc(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
function E(s)   { return 'E' + parseFloat(s || 0).toFixed(2); }
function $(id)  { return document.getElementById(id); }

function fmtDate(iso) {
  if (!iso) return '';
  return new Date(iso).toLocaleString('en-GB', {
    day:'2-digit', month:'short', year:'numeric',
    hour:'2-digit', minute:'2-digit', hour12:false
  }).replace(',', ' at');
}

function showToast(msg, type) {
  const el = $('toast');
  el.textContent = msg;
  el.className = 'toast show' + (type ? ' ' + type : '');
  clearTimeout(el._t);
  el._t = setTimeout(() => { el.className = 'toast'; }, 2600);
}

// Live token — updated on silent refresh
let _liveToken = localStorage.getItem('access_token') || '';

async function refreshAccessToken() {
  const rt = localStorage.getItem('refresh_token');
  if (!rt) return false;
  try {
    const r = await fetch(API + '/auth/refresh', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({refresh_token: rt})
    });
    if (!r.ok) return false;
    const d = await r.json();
    const newAccess  = d.data?.access_token  || d.access_token;
    const newRefresh = d.data?.refresh_token || d.refresh_token;
    if (!newAccess) return false;
    _liveToken = newAccess;
    localStorage.setItem('access_token', newAccess);
    if (newRefresh) localStorage.setItem('refresh_token', newRefresh);
    return true;
  } catch(e) { return false; }
}

// Authenticated fetch — silently refreshes token on 401
async function apiFetch(method, path, body, _isRetry=false) {
  const tok = _liveToken || localStorage.getItem('access_token') || '';
  const headers = { 'Accept': 'application/json', 'Authorization': 'Bearer ' + tok };
  const opts    = { method, headers };
  if (body) { headers['Content-Type'] = 'application/json'; opts.body = JSON.stringify(body); }
  try {
    const res = await fetch(API + path, opts);
    if (res.status === 401 && !_isRetry) {
      const ok = await refreshAccessToken();
      if (ok) return apiFetch(method, path, body, true);
      logout(); return null;
    }
    return res;
  } catch { return null; }
}

// Status badge
function badge(status) {
  const m = {
    pending:'b-pending', confirmed:'b-confirmed', preparing:'b-preparing',
    ready:'b-ready', dispatched:'b-dispatched',
    delivered:'b-done', completed:'b-done', collected:'b-done',
    cancelled:'b-cancelled'
  };
  const labels = {
    pending:'Pending', confirmed:'Confirmed', preparing:'Preparing',
    ready:'Ready', dispatched:'On the way',
    delivered:'Delivered', completed:'Completed', collected:'Collected',
    cancelled:'Cancelled'
  };
  return '<span class="badge ' + (m[status] || 'b-default') + '">' + esc(labels[status] || status) + '</span>';
}

// Timeline builder
function buildTimeline(status, orderType) {
  if (status === 'cancelled') return '<p style="color:var(--red);font-weight:600;padding:10px 0">Order was cancelled</p>';
  const DELIVERY    = [{k:'pending',l:'Placed'},{k:'confirmed',l:'Confirmed'},{k:'preparing',l:'Preparing'},{k:'ready',l:'Ready'},{k:'dispatched',l:'On the way'},{k:'delivered',l:'Delivered'}];
  const COLLECTION  = [{k:'pending',l:'Placed'},{k:'confirmed',l:'Confirmed'},{k:'preparing',l:'Preparing'},{k:'ready',l:'Ready'},{k:'collected',l:'Collected'}];
  const STEPS_ORDER = {pending:0,confirmed:1,preparing:2,ready:3,dispatched:4,delivered:5,completed:5,collected:4};
  const steps  = orderType === 'delivery' ? DELIVERY : COLLECTION;
  const curStep = STEPS_ORDER[status] ?? 0;
  return '<div class="timeline">' +
    steps.map((s, i) => {
      const sIdx = STEPS_ORDER[s.k] ?? i;
      const cls  = sIdx < curStep ? 'done' : sIdx === curStep ? 'curr' : '';
      return '<div class="tl-step ' + cls + '"><div class="tl-dot"></div><div class="tl-line"></div><div class="tl-lbl">' + s.l + '</div></div>';
    }).join('') + '</div>';
}

// ── Tab switching ────────────────────────────────────────────────────
function switchTab(tab) {
  ['cart','orders','track'].forEach(t => {
    $('section-' + t).classList.toggle('active', t === tab);
    $('tab-' + t).classList.toggle('active', t === tab);
  });
  if (tab === 'orders') loadOrders();
  if (tab === 'track')  loadTracking();
}

// ══════════════════════════════════════════════════════════════
//  CART
// ══════════════════════════════════════════════════════════════
let cartItems    = [];
let cartSubtotal = 0;
let discountAmt  = 0;
let discountCode = '';
let fulfillment  = 'delivery';

async function loadCart() {
  const res = await apiFetch('GET', '/cart');
  if (!res) return;
  const data = await res.json();
  cartItems = Array.isArray(data.data) ? data.data : (data.data?.items || []);
  renderCart();
}

function renderCart() {
  const hasCarts = cartItems.length > 0;
  $('cart-panel').style.display   = hasCarts ? 'block' : 'none';
  $('summary-panel').style.display= hasCarts ? 'block' : 'none';
  $('cart-empty').style.display   = hasCarts ? 'none'  : 'block';

  const cb = $('cart-badge');
  cb.textContent = cartItems.length;
  cb.classList.toggle('hidden', !hasCarts);

  if (!hasCarts) return;

  $('cart-list').innerHTML = cartItems.map(item => {
    const lineTotal = parseFloat(item.line_total || ((item.unit_price || item.price) * item.quantity) || 0);
    return `<div class="cart-item" id="ci-${item.cart_id}">
      <div class="cart-img">
        ${(()=>{const _r=item.img_url||item.image_url||'';const _s=_r?(_r.startsWith('http')?_r:'window.location.origin'+_r):'';return _s?`<img src="${esc(_s)}" alt="${esc(item.name)}" onerror="this.style.display='none'"/>`:`<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>`;})()}
      </div>
      <div class="cart-info">
        <div class="cart-name">${esc(item.name)}</div>
        <div class="cart-unit">${E(item.unit_price || item.price)} each</div>
      </div>
      <div class="qty-ctrl">
        <button class="qty-btn" onclick="changeQty(${item.cart_id},${item.quantity - 1})">−</button>
        <span class="qty-val">${item.quantity}</span>
        <button class="qty-btn" onclick="changeQty(${item.cart_id},${item.quantity + 1})">+</button>
      </div>
      <div class="cart-line">${E(lineTotal)}</div>
      <button class="btn-remove" onclick="removeItem(${item.cart_id})">✕</button>
    </div>`;
  }).join('');

  renderSummary();
}

function renderSummary() {
  cartSubtotal = cartItems.reduce((s, i) =>
    s + parseFloat(i.line_total || ((i.unit_price || i.price) * i.quantity) || 0), 0);
  const fee   = fulfillment === 'delivery' ? DELIVERY_FEE : 0;
  const total = Math.max(0, cartSubtotal + fee - discountAmt);
  $('sum-rows').innerHTML = `
    <div class="sum-row"><span>Subtotal</span><span class="sum-val">${E(cartSubtotal)}</span></div>
    <div class="sum-row"><span>Delivery fee</span><span class="sum-val">${fee ? E(fee) : 'Free'}</span></div>
    ${discountAmt ? `<div class="sum-row" style="color:var(--green)"><span>Discount (${esc(discountCode)})</span><span class="sum-val">−${E(discountAmt)}</span></div>` : ''}
    <div class="sum-row total"><span>Total</span><span class="sum-val">${E(total)}</span></div>
  `;
}

async function changeQty(cartId, qty) {
  if (qty < 1) { await removeItem(cartId); return; }
  const res = await apiFetch('POST', '/cart', { cart_id: cartId, quantity: qty });
  if (res?.ok) await loadCart();
  else showToast('Could not update quantity', 'error');
}

async function removeItem(cartId) {
  const res = await apiFetch('DELETE', '/cart/' + cartId);
  if (res?.ok) { cartItems = cartItems.filter(i => i.cart_id !== cartId); renderCart(); showToast('Item removed'); }
}

async function applyDiscount() {
  const code = $('disc-code').value.trim().toUpperCase();
  if (!code) return;
  const res  = await apiFetch('POST', '/discounts/validate', { code, subtotal: cartSubtotal });
  const data = await res?.json();
  const msg  = $('disc-msg');
  if (res?.ok && data?.data?.discount_amount) {
    discountAmt  = parseFloat(data.data.discount_amount);
    discountCode = code;
    msg.textContent = '✓ ' + code + ' applied — you save ' + E(discountAmt);
    msg.className   = 'disc-msg ok';
    renderSummary(); renderCoSummary();
  } else {
    msg.textContent = '✕ ' + (data?.error || 'Invalid or expired code');
    msg.className   = 'disc-msg err';
  }
}

// ══════════════════════════════════════════════════════════════
//  CHECKOUT
// ══════════════════════════════════════════════════════════════
function showCheckout() {
  if (!cartItems.length) { showToast('Your cart is empty', 'error'); return; }
  $('view-cart').style.display     = 'none';
  $('view-checkout').style.display = 'block';
  renderCoSummary();
}

function showCartView() {
  $('view-checkout').style.display = 'none';
  $('view-cart').style.display     = 'block';
}

function setFulfillment(type) {
  fulfillment = type;
  $('opt-del').classList.toggle('sel', type === 'delivery');
  $('opt-col').classList.toggle('sel', type === 'collection');
  $('addr-panel').style.display = type === 'delivery' ? 'block' : 'none';
  renderSummary(); renderCoSummary();
}

function renderCoSummary() {
  const fee   = fulfillment === 'delivery' ? DELIVERY_FEE : 0;
  const total = Math.max(0, cartSubtotal + fee - discountAmt);
  $('co-summary').innerHTML = `
    <div class="mini-row"><span>Subtotal</span><span class="v">${E(cartSubtotal)}</span></div>
    <div class="mini-row"><span>Delivery fee</span><span class="v">${fee ? E(fee) : 'Free'}</span></div>
    ${discountAmt ? `<div class="mini-row" style="color:var(--green)"><span>Discount</span><span class="v">−${E(discountAmt)}</span></div>` : ''}
    <div class="mini-row tot"><span>Total</span><span class="v">${E(total)}</span></div>
  `;
}

function togglePlaceBtn() {
  $('btn-place').disabled = $('f-ref').value.trim().length < 4;
}

async function placeOrder() {
  const alert = $('co-alert');
  alert.className = 'alert err';

  const ref    = $('f-ref').value.trim();
  const mobile = $('f-mobile').value.trim();
  if (!ref || ref.length < 4) {
    alert.textContent = 'Please enter your payment transaction reference number.';
    alert.className += ' show'; return;
  }
  if (fulfillment === 'delivery') {
    if (!$('f-street').value.trim() || !$('f-city').value || !$('f-region').value) {
      alert.textContent = 'Please complete your delivery address (street, city, region).';
      alert.className += ' show'; return;
    }
  }

  const btn = $('btn-place');
  btn.classList.add('loading'); btn.disabled = true;

  const fee      = fulfillment === 'delivery' ? DELIVERY_FEE : 0;
  const subtotal = cartSubtotal;
  const body = {
    order_type:     fulfillment,
    channel:        'web',
    payment_method: 'Mobile Money',
    mobile_number:  mobile || undefined,
    reference_no:   ref,
    subtotal,
    delivery_fee:   fee,
    discount_code:  discountCode || undefined,
    special_notes:  $('f-notes')?.value.trim() || undefined,
    items: cartItems.map(i => ({
      product_id: i.product_id,
      name:       i.name,
      unit_price: parseFloat(i.unit_price || i.price || 0),
      quantity:   i.quantity,
      addon_total:0,
      line_total: parseFloat(i.line_total || ((i.unit_price || i.price) * i.quantity) || 0),
    })),
  };
  if (fulfillment === 'delivery') {
    body.delivery_type = 'Standard';
    body.street        = $('f-street').value.trim();
    body.city          = $('f-city').value;
    body.region        = $('f-region').value;
  }

  const res  = await apiFetch('POST', '/orders', body);
  btn.classList.remove('loading');

  if (!res) { btn.disabled = false; return; }
  const data = await res.json();

  if (!res.ok) {
    alert.textContent = data.error || 'Could not place order. Please try again.';
    alert.className += ' show';
    btn.disabled = false; return;
  }

  // Success
  const ref_num = data.data?.order_ref || data.data?.order?.order_ref || '';
  $('success-ref').textContent  = ref_num;
  $('success-msg').textContent  = fulfillment === 'delivery'
    ? 'Your order is in the kitchen. A driver will be assigned when it\'s ready.'
    : 'Your order is in the kitchen. We\'ll call your order number when ready!';
  $('view-checkout').style.display = 'none';
  $('view-success').style.display  = 'block';

  // Reset cart state
  cartItems = []; discountAmt = 0; discountCode = '';
  $('cart-badge').classList.add('hidden');
}

// ══════════════════════════════════════════════════════════════
//  ORDERS
// ══════════════════════════════════════════════════════════════
async function loadOrders() {
  const res  = await apiFetch('GET', '/orders?per_page=50');
  if (!res) return;
  const data = await res.json();
  const list = Array.isArray(data.data) ? data.data : (data.data?.data || []);
  const el   = $('orders-list');
  const em   = $('orders-empty');
  if (!list.length) { el.innerHTML = ''; em.style.display = 'block'; return; }
  em.style.display = 'none';
  el.innerHTML = list.map(o => {
    const total = E(o.grand_total || o.total || o.subtotal || 0);
    const items = o.items_count ? o.items_count + ' item' + (o.items_count !== 1 ? 's' : '') : '';
    return `<div class="order-card" onclick="openOrder(${o.order_id})">
      <div class="order-ref">${esc(o.order_ref || '#' + o.order_id)}</div>
      <div class="order-meta">
        ${badge(o.status)}
        <div class="order-items-txt">${items}</div>
        <div class="order-date-txt">${fmtDate(o.created_at)}</div>
      </div>
      <div class="order-amt">${total}</div>
      <div class="chev"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></div>
    </div>`;
  }).join('');
}

function openOrder(id) { switchTab('track'); loadOrderDetail(id, false); }

// ══════════════════════════════════════════════════════════════
//  TRACKING
// ══════════════════════════════════════════════════════════════
async function loadTracking() {
  const DONE  = ['delivered','completed','collected','cancelled'];
  const res   = await apiFetch('GET', '/orders?per_page=20');
  if (!res) return;
  const data  = await res.json();
  const all   = Array.isArray(data.data) ? data.data : (data.data?.data || []);
  const active= all.filter(o => !DONE.includes(o.status));
  const list  = $('track-list');
  const em    = $('track-empty');
  if (!active.length) { list.innerHTML = ''; em.style.display = 'block'; return; }
  em.style.display = 'none';
  list.innerHTML   = '';
  active.forEach(o => loadOrderDetail(o.order_id, true));
}

async function loadOrderDetail(orderId, append) {
  const res  = await apiFetch('GET', '/orders/' + orderId);
  if (!res) return;
  const data = await res.json();
  if (!res.ok) return;
  const o    = data.data;
  const html = buildTrackCard(o);
  const list = $('track-list');
  if (append) list.insertAdjacentHTML('beforeend', html);
  else        list.innerHTML = html;
  $('track-empty').style.display = 'none';
}

function buildTrackCard(o) {
  const isDel  = o.order_type === 'delivery';
  const isDone = ['delivered','completed','collected','cancelled'].includes(o.status);
  const isDisp = o.status === 'dispatched';

  const otpHtml = (isDel && isDisp && o.delivery_otp)
    ? `<div class="otp-box">
         <div class="otp-label">Your Delivery OTP</div>
         <div class="otp-code">${esc(o.delivery_otp)}</div>
         <div class="otp-hint">Give this code to the driver upon arrival to confirm delivery</div>
       </div>` : '';

  const driverHtml = (isDel && o.driver && isDisp)
    ? `<div style="padding:10px 14px;background:var(--bg);border-radius:10px;font-size:13px;margin-bottom:12px">
         🚗 Driver <strong>${esc(o.driver.full_name || 'assigned')}</strong> is on the way
       </div>` : '';

  const itemsHtml = (o.items || []).map(i =>
    `<div style="display:flex;justify-content:space-between;font-size:13px;padding:5px 0;border-bottom:1px solid var(--border)">
       <span>${esc(i.name)} × ${i.quantity}</span>
       <span style="font-weight:700">${E(i.line_total)}</span>
     </div>`
  ).join('');

  return `<div class="panel" style="margin-bottom:20px">
    <div style="font-family:'Nunito',sans-serif;font-size:26px;font-weight:900;margin-bottom:4px">${esc(o.order_ref || '#' + o.order_id)}</div>
    <span style="display:inline-block;padding:3px 12px;border-radius:12px;font-size:11px;font-weight:700;background:var(--bg);border:1px solid var(--border);margin-bottom:14px">
      ${isDel ? '🚚 Delivery' : '🏪 Collection'}
    </span>
    ${badge(o.status)}
    <div style="margin:16px 0">${buildTimeline(o.status, o.order_type)}</div>
    ${driverHtml}${otpHtml}
    ${itemsHtml ? `<div style="margin-top:16px"><p class="f-label" style="margin-bottom:8px">Items</p>${itemsHtml}</div>` : ''}
    ${!isDone ? `<button class="btn-ghost" style="margin-top:14px" onclick="loadOrderDetail(${o.order_id},false)">↻ Refresh status</button>` : ''}
  </div>`;
}

// ── Boot ───────────────────────────────────────────────────────
loadCart();
</script>
</body>
</html>
