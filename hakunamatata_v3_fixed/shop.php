<?php
session_start();
//shop.php is public — auth handled client-side via localStorage
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Hakuna Matata — Order Online</title>
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
  --gold:        #F5A623;
  --gold-hover:  #E09418;
  --gold-light:  rgba(245,166,35,.1);
  --charcoal:    #363636;
  --charcoal-2:  #252525;
  --bg:          #F7F6F3;
  --card-bg:     #FFFFFF;
  --img-bg:      #F2EDE4;
  --border:      #E6E5E1;
  --muted:       #9A9993;
  --text:        #2E2E2E;
  --red:         #E8394D;
  --green:       #22C55E;
  --radius-lg:   18px;
  --shadow-card: 0 2px 12px rgba(0,0,0,.07), 0 1px 3px rgba(0,0,0,.04);
  --shadow-hover:0 8px 28px rgba(0,0,0,.12), 0 2px 6px rgba(0,0,0,.06);
}

/* ══════════════════════════════════════════
   HEADER — identical layout to login.php panel
   Full-width, no max-width, logo flush left
   with 40px padding (same as login.php)
══════════════════════════════════════════ */
.site-header {
  position: sticky;
  top: 0;
  z-index: 100;
  width: 100%;
  background: var(--charcoal-2);
  box-shadow: 0 2px 16px rgba(0,0,0,.3);
  overflow: hidden;
}

/* Decorative blobs — same as login.php panel */
.header-blob {
  position: absolute;
  border-radius: 50%;
  background: var(--gold);
  opacity: .07;
  pointer-events: none;
}
.header-blob.b1 { width: 300px; height: 300px; bottom: -150px; left: -80px; }
.header-blob.b2 { width: 220px; height: 220px; top: -100px;   right: 60px; }
.header-blob.b3 { width: 140px; height: 140px; top: -40px;    left: 45%; opacity: .04; }

/* Inner row — full width, 40px side padding — matches login.php exactly */
.header-inner {
  position: relative;
  z-index: 1;
  display: flex;
  align-items: center;
  gap: 16px;
  width: 100%;
  padding: 0 40px;
  height: 100px;   /* same as login.php min-height */
}

/* Brand — same as login.php brand-group */
.brand {
  display: flex;
  align-items: center;
  gap: 14px;
  text-decoration: none;
  flex-shrink: 0;
}
.brand-logo {
  width: 68px;
  height: 68px;
  background: white;
  border-radius: 14px;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 6px 20px rgba(0,0,0,.3);
  flex-shrink: 0;
  animation: popIn .55s cubic-bezier(.34,1.56,.64,1) both;
}
.brand-logo img { width: 100%; height: 100%; object-fit: cover; }
.brand-name {
  font-family: 'Nunito', sans-serif;
  font-weight: 900;
  font-size: 22px;
  color: #fff;
  line-height: 1.15;
  animation: fadeUp .5s ease both .1s;
}
.brand-name em { font-style: normal; color: var(--gold); }
.brand-sub {
  font-size: 11px;
  color: rgba(255,255,255,.4);
  display: block;
  margin-top: 2px;
  animation: fadeUp .5s ease both .18s;
}

/* Right-side actions (push to far right) */
.header-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-left: auto;
  flex-shrink: 0;
}

/* Cart button */
.cart-btn {
  display: flex;
  align-items: center;
  gap: 7px;
  padding: 7px 16px;
  border-radius: 22px;
  background: rgba(255,255,255,.08);
  border: 1.5px solid rgba(255,255,255,.15);
  color: #fff;
  font-family: 'DM Sans', sans-serif;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all .2s;
  white-space: nowrap;
}
.cart-btn:hover { border-color: var(--gold); color: var(--gold); }
.cart-btn svg   { width: 16px; height: 16px; }
.cart-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: var(--gold);
  color: var(--charcoal-2);
  border-radius: 10px;
  min-width: 20px;
  height: 20px;
  font-size: 11px;
  font-weight: 800;
  padding: 0 5px;
  transition: transform .2s;
}
.cart-badge.hidden { display: none; }
.cart-badge.bump   { transform: scale(1.35); }

/* Login button */
.btn-login {
  padding: 8px 20px;
  border-radius: 22px;
  background: var(--gold);
  color: var(--charcoal-2);
  font-family: 'Nunito', sans-serif;
  font-size: 14px;
  font-weight: 800;
  text-decoration: none;
  transition: background .2s;
  white-space: nowrap;
}
.btn-login:hover { background: var(--gold-hover); }

/* Logged-in user chip */
.user-chip { display: flex; align-items: center; gap: 8px; }
.user-name  {
  font-size: 13px;
  font-weight: 600;
  color: rgba(255,255,255,.75);
  white-space: nowrap;
}
.btn-logout {
  padding: 6px 13px;
  border-radius: 16px;
  border: 1.5px solid rgba(255,255,255,.2);
  background: transparent;
  color: rgba(255,255,255,.5);
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all .2s;
  font-family: 'DM Sans', sans-serif;
}
.btn-logout:hover { border-color: #E8394D; color: #E8394D; }

/* ══════════════════════════════════════════
   FILTER SUB-HEADING BAR
   Full-width, 40px padding — aligns with header
══════════════════════════════════════════ */
.filter-bar {
  width: 100%;
  background: #fff;
  border-bottom: 1px solid var(--border);
  position: sticky;
  top: 100px;   /* below header */
  z-index: 90;
  box-shadow: 0 2px 8px rgba(0,0,0,.05);
}

.filter-bar-inner {
  display: flex;
  align-items: center;
  gap: 12px;
  width: 100%;
  padding: 0 40px;
  height: 56px;
}

/* Category pills — left-aligned, same 40px left edge as header logo */
.cat-pills {
  display: flex;
  align-items: center;
  gap: 6px;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  padding: 2px 0;
  flex-shrink: 0;
}
.cat-pills::-webkit-scrollbar { display: none; }

.cat-tab {
  padding: 7px 18px;
  border-radius: 22px;
  border: 1.5px solid var(--border);
  background: transparent;
  color: var(--charcoal);
  font-family: 'DM Sans', sans-serif;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all .2s;
  white-space: nowrap;
}
.cat-tab:hover  { border-color: var(--gold); background: var(--gold-light); }
.cat-tab.active { background: var(--charcoal-2); border-color: var(--charcoal-2); color: #fff; }

/* Search — right side, same horizontal edge as header actions */
.filter-search-wrap {
  position: relative;
  margin-left: auto;
  flex-shrink: 0;
}
.filter-search-wrap svg {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  width: 15px;
  height: 15px;
  color: var(--muted);
  pointer-events: none;
}
.filter-search {
  padding: 8px 14px 8px 34px;
  border-radius: 22px;
  border: 1.5px solid var(--border);
  background: var(--bg);
  font-family: 'DM Sans', sans-serif;
  font-size: 13px;
  color: var(--text);
  outline: none;
  width: 240px;
  transition: border-color .2s, background .2s, width .3s;
}
.filter-search:focus { border-color: var(--gold); background: #fff; width: 280px; }
.filter-search::placeholder { color: var(--muted); }

/* ══════════════════════════════════════════
   MAIN CONTENT
   40px side padding — same left edge as header
══════════════════════════════════════════ */
.main-content {
  padding: 32px 40px 60px;
}

/* ══════════════════════════════════════════
   PRODUCT GRID
══════════════════════════════════════════ */
.product-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
}

/* ══════════════════════════════════════════
   PRODUCT CARD
══════════════════════════════════════════ */
.product-card {
  background: var(--card-bg);
  border-radius: var(--radius-lg);
  overflow: hidden;
  box-shadow: var(--shadow-card);
  border: 1px solid var(--border);
  transition: box-shadow .25s, transform .25s;
  display: flex;
  flex-direction: column;
}
.product-card:hover {
  box-shadow: var(--shadow-hover);
  transform: translateY(-3px);
}

/* Image */
.card-img-wrap {
  position: relative;
  background: var(--img-bg);
  aspect-ratio: 1 / 1;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}
.card-img {
  width: 70%;
  height: 70%;
  object-fit: contain;
  transition: transform .3s;
}
.product-card:hover .card-img { transform: scale(1.07); }
.card-img-placeholder {
  width: 68px;
  height: 68px;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: .25;
}
.card-img-placeholder svg { width: 100%; height: 100%; }

/* Badge */
.card-badge {
  position: absolute;
  top: 10px;
  left: 10px;
  padding: 4px 11px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 700;
  font-family: 'DM Sans', sans-serif;
}
.card-badge.sale  { background: var(--red);        color: #fff; }
.card-badge.new   { background: var(--green);       color: #fff; }
.card-badge.hot   { background: #FF6B35;            color: #fff; }
.card-badge.other { background: var(--charcoal-2);  color: #fff; }

/* Card body */
.card-body {
  padding: 14px;
  display: flex;
  flex-direction: column;
  flex: 1;
}
.card-name {
  font-family: 'Nunito', sans-serif;
  font-size: 15px;
  font-weight: 800;
  color: var(--text);
  margin-bottom: 4px;
  line-height: 1.3;
}
.card-desc {
  font-size: 12.5px;
  color: var(--muted);
  line-height: 1.5;
  margin-bottom: 14px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  flex: 1;
}
.card-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  margin-top: auto;
}
.price-now {
  font-family: 'Nunito', sans-serif;
  font-size: 19px;
  font-weight: 900;
  color: var(--text);
}
.btn-add {
  padding: 7px 18px;
  border-radius: 22px;
  border: 1.5px solid var(--border);
  background: transparent;
  color: var(--text);
  font-family: 'DM Sans', sans-serif;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all .2s;
  white-space: nowrap;
  flex-shrink: 0;
}
.btn-add:hover { border-color: var(--gold); background: var(--gold-light); }
.btn-add:disabled { opacity: .5; cursor: not-allowed; }
.btn-add.added { border-color: var(--green); background: rgba(34,197,94,.1); color: #16a34a; }

/* ══════════════════════════════════════════
   STATE PANELS
══════════════════════════════════════════ */
.state-panel { text-align: center; padding: 80px 24px; }
.state-panel svg { width: 56px; height: 56px; color: #d4d0cb; margin: 0 auto 16px; display: block; }
.state-panel h3 { font-family: 'Nunito', sans-serif; font-size: 20px; font-weight: 800; color: var(--charcoal); margin-bottom: 6px; }
.state-panel p  { font-size: 14px; color: var(--muted); }

/* Skeleton */
.skeleton-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
.skeleton-card { background: #fff; border-radius: var(--radius-lg); overflow: hidden; border: 1px solid var(--border); }
.skeleton-img  { aspect-ratio: 1/1; background: #EEE9E1; }
.skeleton-body { padding: 14px; }
.skeleton-line {
  height: 12px; border-radius: 6px; margin-bottom: 10px;
  background: linear-gradient(90deg, #EEE9E1 25%, #E5E0D7 50%, #EEE9E1 75%);
  background-size: 200% 100%;
  animation: shimmer 1.4s infinite;
}
.skeleton-line.w80 { width: 80%; }
.skeleton-line.w60 { width: 60%; }
.skeleton-line.w40 { width: 40%; }
@keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

/* ══════════════════════════════════════════
   TOAST
══════════════════════════════════════════ */
.toast {
  position: fixed;
  bottom: 28px;
  left: 50%;
  transform: translateX(-50%) translateY(16px);
  padding: 11px 24px;
  border-radius: 24px;
  font-size: 13px;
  font-weight: 600;
  font-family: 'DM Sans', sans-serif;
  box-shadow: 0 4px 20px rgba(0,0,0,.22);
  opacity: 0;
  pointer-events: none;
  transition: opacity .25s, transform .25s;
  z-index: 999;
  white-space: nowrap;
  background: var(--charcoal-2);
  color: #fff;
}
.toast.show    { opacity: 1; transform: translateX(-50%) translateY(0); }
.toast.success { background: #166534; }
.toast.error   { background: #991b1b; }

/* ══════════════════════════════════════════
   ANIMATIONS (same as login.php)
══════════════════════════════════════════ */
@keyframes fadeUp { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }
@keyframes popIn  { from { opacity: 0; transform: scale(.7); } to { opacity: 1; transform: scale(1); } }

/* ══════════════════════════════════════════
   RESPONSIVE
══════════════════════════════════════════ */
@media (max-width: 1100px) {
  .product-grid, .skeleton-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 860px) {
  .product-grid, .skeleton-grid { grid-template-columns: repeat(2, 1fr); }
  .header-inner, .filter-bar-inner, .main-content { padding-left: 20px; padding-right: 20px; }
  .filter-search { width: 180px; }
}
@media (max-width: 560px) {
  .product-grid, .skeleton-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
  .header-inner { height: 80px; }
  .filter-bar { top: 80px; }
  .filter-bar-inner { flex-wrap: wrap; height: auto; padding: 10px 20px; gap: 8px; }
  .filter-search-wrap { margin-left: 0; width: 100%; }
  .filter-search { width: 100%; }
  .filter-search:focus { width: 100%; }
  .brand-sub { display: none; }
  .brand-name { font-size: 18px; }
  .brand-logo { width: 52px; height: 52px; }
}
</style>
</head>
<body>

<!-- ══════════════════════════════════════════════
     HEADER  — same width, padding & layout as login.php
══════════════════════════════════════════════ -->
<header class="site-header">
  <div class="header-blob b1"></div>
  <div class="header-blob b2"></div>
  <div class="header-blob b3"></div>

  <div class="header-inner">

    <!-- Brand (logo + name — identical to login.php) -->
    <a href="index.php" class="brand">
      <div class="brand-logo">
        <img src="hakuna matata.png" alt="Hakuna Matata Logo"/>
      </div>
      <div>
        <span class="brand-name">Hakuna <em>Matata</em></span>
        <span class="brand-sub">Online Point of Sale</span>
      </div>
    </a>

    <!-- Cart + Auth — pushed to right edge -->
    <div class="header-actions">

      <button class="cart-btn" id="cart-btn" onclick="goToCart()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
          <path d="M1 1h4l2.68 13.39a2 2 0 001.99 1.61h9.72a2 2 0 001.99-1.61L23 6H6"/>
        </svg>
        Cart
        <span class="cart-badge hidden" id="cart-count">0</span>
      </button>

      <div id="auth-area">
        <a href="login.php" class="btn-login">Login</a>
      </div>

    </div>
  </div>
</header>

<!-- ══════════════════════════════════════════════
     FILTER SUB-HEADING
     40px left padding — category pills align with logo
══════════════════════════════════════════════ -->
<div class="filter-bar">
  <div class="filter-bar-inner">

    <div class="cat-pills" id="cat-pills">
      <button class="cat-tab active" data-type="">All items</button>
      <button class="cat-tab" data-type="Butchery">Butchery</button>
      <button class="cat-tab" data-type="Restaurant">Restaurant</button>
      <button class="cat-tab" data-type="Liquor">Liquor</button>
    </div>

    <div class="filter-search-wrap">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
      <input class="filter-search" type="search" id="search-input"
             placeholder="Search by name, category..." autocomplete="off"/>
    </div>

  </div>
</div>


<!-- ══════════════════════════════════════════════
     MAIN CONTENT
     40px side padding — same left edge as header
══════════════════════════════════════════════ -->
<main class="main-content">

  <!-- Skeleton loader -->
  <div id="loading-state" class="skeleton-grid">
    <div class="skeleton-card"><div class="skeleton-img"></div><div class="skeleton-body"><div class="skeleton-line w80"></div><div class="skeleton-line w60"></div><div class="skeleton-line w40"></div></div></div>
    <div class="skeleton-card"><div class="skeleton-img"></div><div class="skeleton-body"><div class="skeleton-line w80"></div><div class="skeleton-line w60"></div><div class="skeleton-line w40"></div></div></div>
    <div class="skeleton-card"><div class="skeleton-img"></div><div class="skeleton-body"><div class="skeleton-line w80"></div><div class="skeleton-line w60"></div><div class="skeleton-line w40"></div></div></div>
    <div class="skeleton-card"><div class="skeleton-img"></div><div class="skeleton-body"><div class="skeleton-line w80"></div><div class="skeleton-line w60"></div><div class="skeleton-line w40"></div></div></div>
  </div>

  <!-- Error -->
  <div id="error-state" class="state-panel" style="display:none">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
      <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    <h3>Could not load products</h3>
    <p>Please check your connection and try again.</p>
  </div>

  <!-- Empty -->
  <div id="empty-state" class="state-panel" style="display:none">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
      <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
    </svg>
    <h3>No products found</h3>
    <p>Try a different category or search term.</p>
  </div>

  <!-- Product grid -->
  <div class="product-grid" id="product-grid" style="display:none"></div>

</main>

<!-- Toast notification -->
<div class="toast" id="toast"></div>


<script>
// ── Config ──────────────────────────────────────────────────────────
const API = window.location.origin + '/api';

// ── State ───────────────────────────────────────────────────────────
let currentType = '';
let searchTimer = null;
let cartCount   = 0;

// ── Auth helpers — always read fresh from localStorage ───────────────
function getToken()    { return localStorage.getItem('access_token'); }
function getRefresh()  { return localStorage.getItem('refresh_token'); }
function getUser()     { return JSON.parse(localStorage.getItem('user') || 'null'); }

function forceLogout() {
  localStorage.removeItem('access_token');
  localStorage.removeItem('refresh_token');
  localStorage.removeItem('user');
  window.location.href = 'login.php';
}

function logout() {
  // Revoke refresh token on server (fire-and-forget)
  const rt = getRefresh();
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
  window.location.reload();
}

// ── Token refresh — call when API returns 401 ─────────────────────────
async function refreshToken() {
  const rt = getRefresh();
  if (!rt) return null;
  try {
    const res  = await fetch(API + '/auth/refresh', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body:    JSON.stringify({ refresh_token: rt })
    });
    const data = await res.json();
    if (!res.ok) return null;
    // Store new tokens
    localStorage.setItem('access_token',  data.data.access_token);
    localStorage.setItem('refresh_token', data.data.refresh_token);
    return data.data.access_token;
  } catch {
    return null;
  }
}

// ── Boot: update header with user info if logged in ──────────────────
(function initAuth() {
  const token = getToken();
  const user  = getUser();
  if (user && token) {
    const firstName = (user.full_name || user.email || 'there').split(' ')[0];
    document.getElementById('auth-area').innerHTML =
      '<div class="user-chip">' +
        '<span class="user-name">Hi, ' + esc(firstName) + '</span>' +
        '<a href="customer/tracking.php" class="cart-btn" style="text-decoration:none">' +
          '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>' +
          'Track Order' +
        '</a>' +
        '<button class="btn-logout" onclick="logout()">Logout</button>' +
      '</div>';
    fetchCartCount();
  }
})();

// ── Helpers ─────────────────────────────────────────────────────────
function esc(s) {
  const d = document.createElement('div');
  d.textContent = s || '';
  return d.innerHTML;
}
function fmtPrice(v) { return 'E' + parseFloat(v || 0).toFixed(2); }

function showState(s) {
  document.getElementById('loading-state').style.display = s === 'loading' ? 'grid'  : 'none';
  document.getElementById('error-state').style.display   = s === 'error'   ? 'block' : 'none';
  document.getElementById('empty-state').style.display   = s === 'empty'   ? 'block' : 'none';
  document.getElementById('product-grid').style.display  = s === 'grid'    ? 'grid'  : 'none';
}

// ── Fetch Products ───────────────────────────────────────────────────
async function fetchProducts(type, search) {
  showState('loading');
  const p = new URLSearchParams({ available: 1 });
  if (type)   p.set('type',   type);
  if (search) p.set('search', search);

  try {
    const res  = await fetch(API + '/products?' + p, {
      headers: { Accept: 'application/json' }
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.error || 'Load failed');

    const list = Array.isArray(data.data)
      ? data.data
      : (data.data && Array.isArray(data.data.data) ? data.data.data : []);

    renderProducts(list);
  } catch {
    showState('error');
  }
}

// ── Render ───────────────────────────────────────────────────────────
function renderProducts(items) {
  if (!items.length) { showState('empty'); return; }

  document.getElementById('product-grid').innerHTML = items.map(p => {
    const b     = (p.badge || '').toLowerCase();
    const badge = b === 'sale'  ? '<span class="card-badge sale">% Sale</span>'
                : b === 'new'   ? '<span class="card-badge new">New</span>'
                : b === 'hot'   ? '<span class="card-badge hot">Hot</span>'
                : b             ? '<span class="card-badge other">' + esc(p.badge) + '</span>'
                : '';

    const _imgRaw = p.img_url || p.image_url || '';
    const _imgSrc = _imgRaw ? (_imgRaw.startsWith('http') ? _imgRaw : window.location.origin + '/api' + _imgRaw) : '';
    const img = _imgSrc
      ? '<img class="card-img" src="' + esc(_imgSrc) + '" alt="' + esc(p.name) + '"' +
        ' onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\'"/>' +
        '<div class="card-img-placeholder" style="display:none">' + noImgSvg() + '</div>'
      : '<div class="card-img-placeholder">' + noImgSvg() + '</div>';

    return (
      '<div class="product-card">' +
        '<div class="card-img-wrap">' + badge + img + '</div>' +
        '<div class="card-body">' +
          '<h3 class="card-name">' + esc(p.name) + '</h3>' +
          '<p class="card-desc">' + esc(p.description || '') + '</p>' +
          '<div class="card-footer">' +
            '<span class="price-now">' + fmtPrice(p.price) + '</span>' +
            '<button class="btn-add" onclick="addToCart(' + p.id + ',this)">+ Add</button>' +
          '</div>' +
        '</div>' +
      '</div>'
    );
  }).join('');

  showState('grid');
}

function noImgSvg() {
  return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">' +
    '<rect x="3" y="3" width="18" height="18" rx="2"/>' +
    '<circle cx="8.5" cy="8.5" r="1.5"/>' +
    '<polyline points="21 15 16 10 5 21"/></svg>';
}

// ── Add to Cart ──────────────────────────────────────────────────────
async function addToCart(productId, btn) {
  // Always read token fresh — catches case where user logged in after page loaded
  let authToken = getToken();
  if (!authToken) { window.location.href = 'login.php'; return; }

  btn.disabled    = true;
  btn.textContent = '...';

  try {
    let res = await fetch(API + '/cart', {
      method:  'POST',
      headers: {
        'Content-Type':  'application/json',
        'Accept':        'application/json',
        'Authorization': 'Bearer ' + authToken
      },
      body: JSON.stringify({ product_id: productId, quantity: 1 })
    });

    // 401 = token expired — try a silent refresh first
    if (res.status === 401) {
      showToast('Refreshing session…', '');
      authToken = await refreshToken();
      if (!authToken) { forceLogout(); return; }

      // Retry once with the new token
      res = await fetch(API + '/cart', {
        method:  'POST',
        headers: {
          'Content-Type':  'application/json',
          'Accept':        'application/json',
          'Authorization': 'Bearer ' + authToken
        },
        body: JSON.stringify({ product_id: productId, quantity: 1 })
      });
      if (res.status === 401) { forceLogout(); return; }
    }

    const data = await res.json();
    if (!res.ok) throw new Error(data.error || 'Failed');

    cartCount++;
    updateBadge();
    btn.textContent = '✓ Added';
    btn.classList.add('added');
    showToast('Added to cart!', 'success');
    setTimeout(() => { btn.textContent = '+ Add'; btn.classList.remove('added'); btn.disabled = false; }, 1600);

  } catch (err) {
    btn.textContent = '+ Add';
    btn.disabled = false;
    showToast(err.message || 'Could not add to cart.', 'error');
  }
}

async function fetchCartCount() {
  const authToken = getToken();
  if (!authToken) return;
  try {
    const res  = await fetch(API + '/cart', {
      headers: { Accept: 'application/json', Authorization: 'Bearer ' + authToken }
    });
    if (res.status === 401) {
      // Try silent refresh
      const newToken = await refreshToken();
      if (!newToken) return;
      const res2  = await fetch(API + '/cart', {
        headers: { Accept: 'application/json', Authorization: 'Bearer ' + newToken }
      });
      if (!res2.ok) return;
      const data2 = await res2.json();
      const items2 = data2.data;
      cartCount = Array.isArray(items2) ? items2.length : (items2?.items?.length || 0);
      updateBadge();
      return;
    }
    const data = await res.json();
    if (res.ok) {
      const items = data.data;
      cartCount   = Array.isArray(items) ? items.length : (items?.items?.length || 0);
      updateBadge();
    }
  } catch {}
}

function updateBadge() {
  const el = document.getElementById('cart-count');
  el.textContent = cartCount;
  el.classList.toggle('hidden', cartCount === 0);
  el.classList.add('bump');
  setTimeout(() => el.classList.remove('bump'), 300);
}

function goToCart() {
  if (!getToken()) { window.location.href = 'login.php'; return; }
  window.location.href = 'customer/cart.php';
}

// ── Category tabs ────────────────────────────────────────────────────
document.getElementById('cat-pills').addEventListener('click', function(e) {
  const tab = e.target.closest('.cat-tab');
  if (!tab) return;
  this.querySelectorAll('.cat-tab').forEach(t => t.classList.remove('active'));
  tab.classList.add('active');
  currentType = tab.dataset.type;
  fetchProducts(currentType, document.getElementById('search-input').value.trim());
});

// ── Search (debounced 400ms) ──────────────────────────────────────────
document.getElementById('search-input').addEventListener('input', function() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => fetchProducts(currentType, this.value.trim()), 400);
});

// ── Toast ─────────────────────────────────────────────────────────────
function showToast(msg, type) {
  const el = document.getElementById('toast');
  el.textContent = msg;
  el.className   = 'toast show' + (type ? ' ' + type : '');
  clearTimeout(el._t);
  el._t = setTimeout(() => { el.className = 'toast'; }, 2600);
}

// ── Boot ──────────────────────────────────────────────────────────────
fetchProducts('', '');
</script>
</body>
</html>
