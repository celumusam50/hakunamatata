<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Track Order — Hakuna Matata</title>
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
.page-heading{font-family:'Nunito',sans-serif;font-size:28px;font-weight:900;color:var(--text);margin-bottom:6px;display:block;width:100%;padding-left:14px;border-left:4px solid var(--gold)}
.page-sub{font-size:14px;color:var(--muted);margin-bottom:28px;padding-left:18px}

/* ── SPINNER / TOAST ── */
.spinner-wrap{display:flex;justify-content:center;padding:80px}
.spinner{width:36px;height:36px;border:3px solid var(--border);border-top-color:var(--gold);border-radius:50%;animation:spin .7s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.toast{position:fixed;bottom:28px;left:50%;transform:translateX(-50%) translateY(16px);padding:11px 24px;border-radius:24px;font-size:13px;font-weight:600;font-family:'DM Sans',sans-serif;box-shadow:0 4px 20px rgba(0,0,0,.22);opacity:0;pointer-events:none;transition:opacity .25s,transform .25s;z-index:999;white-space:nowrap;background:var(--charcoal-2);color:#fff}
.toast.show{opacity:1;transform:translateX(-50%) translateY(0)}
.toast.success{background:#166534}
.toast.error{background:#991b1b}

/* ── LOOKUP CARD ── */
.lookup-card{background:var(--card);border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow);padding:32px;max-width:480px;margin:0 auto}
.lookup-card h2{font-family:'Nunito',sans-serif;font-size:18px;font-weight:900;color:var(--text);margin-bottom:6px}
.lookup-card p{font-size:13px;color:var(--muted);margin-bottom:20px;line-height:1.5}
.form-label{display:block;font-size:11.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px}
.form-input{width:100%;padding:11px 14px;border-radius:10px;border:1.5px solid var(--border);background:var(--bg);font-family:'DM Sans',sans-serif;font-size:14px;color:var(--text);outline:none;transition:border-color .2s,box-shadow .2s}
.form-input:focus{border-color:var(--gold);box-shadow:0 0 0 3px rgba(245,166,35,.12);background:#fff}
.btn-lookup{width:100%;margin-top:14px;padding:13px;border-radius:12px;background:var(--gold);color:var(--charcoal-2);font-family:'Nunito',sans-serif;font-size:15px;font-weight:900;border:none;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 4px 14px rgba(245,166,35,.25)}
.btn-lookup:hover:not(:disabled){background:var(--gold-hover);transform:translateY(-1px);box-shadow:0 8px 24px rgba(245,166,35,.4)}
.btn-lookup:disabled{opacity:.5;cursor:not-allowed;transform:none;box-shadow:none}

/* ── TRACKING LAYOUT ── */
.tracking-layout{display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start}

/* ── STATUS CARD (main) ── */
.track-card{background:var(--card);border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow);padding:24px;margin-bottom:16px}
.track-card-header{display:flex;align-items:center;gap:12px;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid var(--border)}
.track-icon{width:38px;height:38px;background:var(--gold-light);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:1px solid rgba(245,166,35,.2)}
.track-icon svg{color:var(--gold)}
.track-card-title{font-family:'Nunito',sans-serif;font-size:16px;font-weight:900;color:var(--text)}
.track-card-sub{font-size:12px;color:var(--muted);margin-top:1px}
.order-ref-badge{margin-left:auto;font-size:12px;font-weight:800;color:var(--charcoal-2);background:#F0EDE8;border-radius:8px;padding:5px 14px;letter-spacing:.04em;border:1px solid var(--border);white-space:nowrap}

/* ── STATUS BADGE ── */
.status-badge{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700;letter-spacing:.03em}
.status-badge .dot{width:7px;height:7px;border-radius:50%;flex-shrink:0}
.status-badge.pending{background:#FFF7E6;color:#92600a;border:1px solid rgba(245,166,35,.35)}
.status-badge.pending .dot{background:var(--gold);animation:pulse 1.5s infinite}
.status-badge.confirmed{background:#EFF6FF;color:#1e40af;border:1px solid #BFDBFE}
.status-badge.confirmed .dot{background:#3B82F6}
.status-badge.preparing{background:#FFF7E6;color:#92600a;border:1px solid rgba(245,166,35,.35)}
.status-badge.preparing .dot{background:var(--gold);animation:pulse 1.5s infinite}
.status-badge.out_for_delivery{background:#F0FDF4;color:#166534;border:1px solid #BBF7D0}
.status-badge.out_for_delivery .dot{background:var(--green);animation:pulse 1.5s infinite}
.status-badge.delivered{background:#F0FDF4;color:#166534;border:1px solid #BBF7D0}
.status-badge.delivered .dot{background:var(--green)}
.status-badge.cancelled{background:#FFF2F2;color:#991b1b;border:1px solid #FFCACA}
.status-badge.cancelled .dot{background:var(--red)}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}

/* ── TIMELINE ── */
.timeline{padding:4px 0}
.tl-step{display:flex;gap:16px;position:relative;padding-bottom:24px}
.tl-step:last-child{padding-bottom:0}
.tl-left{display:flex;flex-direction:column;align-items:center;flex-shrink:0;width:36px}
.tl-circle{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:2px solid var(--border);background:var(--bg);transition:all .3s}
.tl-circle.done{background:var(--green);border-color:var(--green)}
.tl-circle.done svg{color:#fff}
.tl-circle.active{background:var(--gold);border-color:var(--gold);box-shadow:0 0 0 4px rgba(245,166,35,.18)}
.tl-circle.active svg{color:var(--charcoal-2)}
.tl-circle.idle svg{color:var(--muted)}
.tl-line{flex:1;width:2px;background:var(--border);min-height:16px;margin-top:4px;border-radius:2px;transition:background .3s}
.tl-line.done{background:var(--green)}
.tl-body{flex:1;padding-top:6px}
.tl-title{font-family:'Nunito',sans-serif;font-size:14px;font-weight:900;color:var(--muted);line-height:1.2;transition:color .3s}
.tl-title.done,.tl-title.active{color:var(--text)}
.tl-desc{font-size:12px;color:var(--muted);margin-top:3px;line-height:1.45}
.tl-time{font-size:11px;color:var(--gold);font-weight:700;margin-top:4px}

/* ── REFRESH BAR ── */
.refresh-bar{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px}
.refresh-hint{font-size:12px;color:var(--muted);display:flex;align-items:center;gap:6px}
.btn-refresh{display:flex;align-items:center;gap:6px;padding:7px 14px;border-radius:20px;background:var(--card);border:1.5px solid var(--border);color:var(--text);font-family:'DM Sans',sans-serif;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s}
.btn-refresh:hover{border-color:var(--gold);color:var(--gold)}
.btn-refresh svg{transition:transform .4s}
.btn-refresh.spinning svg{transform:rotate(360deg)}

/* ── SIDEBAR CARDS ── */
.sidebar-card{background:var(--card);border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow);padding:20px;margin-bottom:16px}
.sidebar-card-title{font-family:'Nunito',sans-serif;font-size:14px;font-weight:900;color:var(--text);margin-bottom:14px;display:flex;align-items:center;gap:8px}
.sidebar-card-title svg{color:var(--gold)}
.info-row{display:flex;justify-content:space-between;align-items:flex-start;font-size:12.5px;margin-bottom:9px;gap:8px}
.info-row:last-child{margin-bottom:0}
.info-label{color:var(--muted);font-weight:500;flex-shrink:0}
.info-val{font-weight:600;color:var(--text);text-align:right;word-break:break-word}
.info-divider{border:none;border-top:1px solid var(--border);margin:12px 0}

/* Items list in sidebar */
.item-row{display:grid;grid-template-columns:1fr auto auto;align-items:center;gap:8px;font-size:12px;padding:6px 0;border-bottom:1px solid var(--border)}
.item-row:last-child{border-bottom:none}
.item-name{font-weight:600;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.item-qty{color:#92600a;font-size:11px;font-weight:700;white-space:nowrap;background:var(--gold-light);border:1px solid rgba(245,166,35,.25);border-radius:6px;padding:2px 7px}
.item-price{font-weight:700;color:var(--charcoal-2);white-space:nowrap;text-align:right}
.items-wrap{background:var(--bg);border-radius:10px;padding:8px 10px;margin-bottom:10px}

.total-row{display:flex;justify-content:space-between;font-family:'Nunito',sans-serif;font-size:16px;font-weight:900;color:var(--text);margin-top:8px}

/* ── SEARCH ANOTHER ── */
.btn-search-again{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:20px;background:var(--bg);border:1.5px solid var(--border);color:var(--text);font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;cursor:pointer;transition:all .2s;margin-bottom:20px}
.btn-search-again:hover{border-color:var(--gold);color:var(--gold)}

/* ── ERROR STATE ── */
.error-state{text-align:center;padding:60px 24px}
.error-circle{width:72px;height:72px;background:linear-gradient(135deg,#E8394D,#c0213a);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;box-shadow:0 6px 22px rgba(232,57,77,.3)}
.error-circle svg{width:34px;height:34px;color:#fff}
.error-state h3{font-family:'Nunito',sans-serif;font-size:20px;font-weight:900;color:var(--text);margin-bottom:8px}
.error-state p{font-size:13px;color:var(--muted);margin-bottom:20px;line-height:1.5}
.btn-try-again{display:inline-flex;align-items:center;gap:6px;padding:11px 24px;border-radius:20px;background:var(--gold);color:var(--charcoal-2);font-family:'Nunito',sans-serif;font-size:13px;font-weight:800;text-decoration:none;transition:background .2s;border:none;cursor:pointer}
.btn-try-again:hover{background:var(--gold-hover)}

/* ── OTP DIGITS ── */
.otp-digit{display:inline-flex;align-items:center;justify-content:center;width:36px;height:44px;background:#fff;border:2px solid #86efac;border-radius:10px;font-family:'Nunito',sans-serif;font-size:22px;font-weight:900;color:#166534;box-shadow:0 2px 8px rgba(34,197,94,.15)}
.otp-sep{width:8px;height:2px;background:#86efac;border-radius:2px;flex-shrink:0}

/* ── RESPONSIVE ── */
@media(max-width:800px){
  .tracking-layout{grid-template-columns:1fr}
  .header-inner,.page{padding-left:20px;padding-right:20px}
  .brand-sub{display:none}
}
@media(max-width:560px){
  .brand-logo{width:52px;height:52px}
  .brand-name{font-size:18px}
  .header-inner{height:80px}
  .order-ref-badge{display:none}
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
        Back to Shop
      </a>
      <div id="auth-area"></div>
    </div>
  </div>
</header>

<!-- PAGE -->
<div class="page">
  <h1 class="page-heading">Track Order</h1>
  <p class="page-sub">Real-time status of your delivery</p>

  <!-- Loading -->
  <div id="loading-state" class="spinner-wrap" style="display:none"><div class="spinner"></div></div>

  <!-- My Orders list (shown when no order_id in URL) -->
  <div id="lookup-state" style="display:none">
    <div id="orders-list-wrap"></div>
  </div>

  <!-- Error state -->
  <div id="error-state" style="display:none" class="error-state">
    <div class="error-circle">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    </div>
    <h3 id="error-title">Order Not Found</h3>
    <p id="error-msg">We couldn't find an order with that number. Please double-check and try again.</p>
    <button class="btn-try-again" onclick="resetToLookup()">Try Another Order</button>
  </div>

  <!-- Tracking view -->
  <div id="tracking-state" style="display:none">

    <button class="btn-search-again" onclick="resetToLookup()">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
      Track another order
    </button>

    <div class="refresh-bar">
      <span class="refresh-hint">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        Last updated: <span id="last-updated">—</span>
      </span>
      <button class="btn-refresh" id="btn-refresh" onclick="refreshOrder()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
        Refresh
      </button>
    </div>

    <div style="max-width:560px;margin:0 auto">

      <!-- Current Status Card -->
      <div class="track-card">
        <div class="track-card-header">
          <div class="track-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </div>
          <div>
            <div class="track-card-title">Order Status</div>
            <div class="track-card-sub" id="status-updated-sub">Checking…</div>
          </div>
          <div class="order-ref-badge" id="order-ref-badge">—</div>
        </div>
        <div id="status-badge-wrap" style="margin-bottom:0"></div>
      </div>

      <!-- Timeline Card -->
      <div class="track-card">
        <div class="track-card-header" style="margin-bottom:16px">
          <div class="track-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
          </div>
          <div class="track-card-title">Delivery Timeline</div>
        </div>
        <div class="timeline" id="timeline"></div>
      </div>

      <!-- OTP Card — shown only when out for delivery -->
      <div class="track-card" id="otp-card" style="display:none;border:2px solid rgba(34,197,94,.4);background:linear-gradient(135deg,#f0fdf4,#fff)">
        <div class="track-card-header" style="margin-bottom:14px">
          <div class="track-icon" style="background:#dcfce7;border-color:rgba(34,197,94,.3)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#22C55E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
          </div>
          <div>
            <div class="track-card-title" style="color:#166534">Delivery OTP</div>
            <div class="track-card-sub">Share this code with your driver on arrival</div>
          </div>
        </div>
        <div id="otp-display" style="display:flex;align-items:center;justify-content:center;gap:6px;margin-bottom:12px"></div>
        <p style="font-size:11.5px;color:var(--muted);text-align:center;line-height:1.5">Keep this code private — only share it face-to-face with your driver.</p>
      </div>

    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
const API = window.location.origin + '/hakunamatata_v3/api';

// ── Auth ──────────────────────────────────────────────────────────────
function getToken()   { return localStorage.getItem('access_token'); }
function getRefresh() { return localStorage.getItem('refresh_token'); }
function getUser()    { return JSON.parse(localStorage.getItem('user')||'null'); }
function esc(s){const d=document.createElement('div');d.textContent=s||'';return d.innerHTML;}
function fmt(v){return 'E'+parseFloat(v||0).toFixed(2);}

function forceLogout(){
  ['access_token','refresh_token','user'].forEach(k=>localStorage.removeItem(k));
  window.location.href='../login.php';
}
function logout(){
  const rt=getRefresh();
  if(rt) fetch(API+'/auth/logout',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({refresh_token:rt})}).catch(()=>{});
  ['access_token','refresh_token','user'].forEach(k=>localStorage.removeItem(k));
  window.location.href='../login.php';
}
async function refreshToken(){
  const rt=getRefresh(); if(!rt) return null;
  try{
    const res=await fetch(API+'/auth/refresh',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({refresh_token:rt})});
    const data=await res.json(); if(!res.ok) return null;
    localStorage.setItem('access_token',data.data.access_token);
    localStorage.setItem('refresh_token',data.data.refresh_token);
    return data.data.access_token;
  }catch{return null;}
}

function showToast(msg,type){
  const el=document.getElementById('toast');
  el.textContent=msg; el.className='toast show'+(type?' '+type:'');
  clearTimeout(el._t); el._t=setTimeout(()=>{el.className='toast';},3200);
}

// ── State ─────────────────────────────────────────────────────────────
let currentOrderId = null;
let refreshTimer   = null;

// ── Timeline config ───────────────────────────────────────────────────
const STEPS = [
  {
    key:'order_placed',
    label:'Order Placed',
    desc:'Your order has been received and payment confirmed.',
    icon:'<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
    statuses:['pending','confirmed','preparing','out_for_delivery','arrived','delivered']
  },
  {
    key:'order_confirmed',
    label:'Order Confirmed',
    desc:'The kitchen has confirmed your order and is preparing it now.',
    icon:'<polyline points="20 6 9 17 4 12"/>',
    statuses:['confirmed','preparing','out_for_delivery','arrived','delivered']
  },
  {
    key:'out_for_delivery',
    label:'Out for Delivery',
    desc:'Your driver is on the way to you now.',
    icon:'<rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>',
    statuses:['out_for_delivery','arrived','delivered']
  },
  {
    key:'arrived',
    label:'Driver Arrived',
    desc:'Your driver is outside. Check your notifications for the OTP.',
    icon:'<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>',
    statuses:['arrived','delivered']
  },
  {
    key:'delivered',
    label:'Delivered',
    desc:'Your order has been delivered. Enjoy your meal!',
    icon:'<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
    statuses:['delivered']
  }
];

const STATUS_LABELS = {
  pending:          'Order Placed',
  confirmed:        'Order Confirmed',
  preparing:        'Order Confirmed',
  out_for_delivery: 'Out for Delivery',
  arrived:          'Driver Arrived',
  delivered:        'Delivered',
  cancelled:        'Cancelled'
};

// ── Screen helpers ────────────────────────────────────────────────────
function showScreen(id){
  ['loading-state','lookup-state','error-state','tracking-state'].forEach(s=>{
    document.getElementById(s).style.display = (s===id ? 'block' : 'none');
  });
  if(id==='tracking-state') document.getElementById('tracking-state').style.display='block';
}

function resetToLookup(){
  clearTimeout(refreshTimer);
  currentOrderId=null;
  showScreen('lookup-state');
  history.replaceState(null,'','tracking.php');
  loadMyOrders();
}

// ── Load & render the customer's own pending/active orders ─────────────
async function loadMyOrders(){
  const wrap=document.getElementById('orders-list-wrap');
  wrap.innerHTML='<div class="spinner-wrap"><div class="spinner"></div></div>';
  let tok=getToken();
  if(!tok){forceLogout();return;}
  try{
    let res=await fetch(`${API}/orders?per_page=20`,{
      headers:{Accept:'application/json','Authorization':'Bearer '+tok}
    });
    if(res.status===401){
      tok=await refreshToken();
      if(!tok){forceLogout();return;}
      res=await fetch(`${API}/orders?per_page=20`,{
        headers:{Accept:'application/json','Authorization':'Bearer '+tok}
      });
    }
    let data;
    try{ data=await res.json(); } catch(e){
      wrap.innerHTML=`<div class="lookup-card" style="text-align:center;padding:40px 24px">
        <p style="font-size:13px;color:var(--muted)">Could not load your orders. Please refresh the page.</p>
        <button onclick="loadMyOrders()" style="margin-top:14px;padding:9px 20px;border-radius:20px;background:var(--gold);border:none;font-family:'Nunito',sans-serif;font-weight:800;font-size:13px;cursor:pointer">Retry</button>
      </div>`;
      return;
    }
    const orders=Array.isArray(data.data?.data) ? data.data.data
                : Array.isArray(data.data)       ? data.data
                : [];
    if(!orders.length){
      wrap.innerHTML=`<div class="lookup-card" style="text-align:center;padding:48px 32px">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 16px;display:block"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        <h2 style="font-family:'Nunito',sans-serif;font-size:18px;font-weight:900;margin-bottom:8px">No orders yet</h2>
        <p style="font-size:13px;color:var(--muted)">Place your first order and it will appear here.</p>
        <a href="../shop.php" style="display:inline-flex;align-items:center;gap:6px;margin-top:18px;padding:10px 22px;border-radius:20px;background:var(--gold);color:var(--charcoal-2);font-family:'Nunito',sans-serif;font-size:13px;font-weight:800;text-decoration:none">Shop Now</a>
      </div>`;
      return;
    }
    const statusColor={
      pending:'#92600a',confirmed:'#1e40af',
      out_for_delivery:'#166534',delivered:'#166534',cancelled:'#991b1b'
    };
    const statusBg={
      pending:'#FFF7E6',confirmed:'#EFF6FF',
      out_for_delivery:'#F0FDF4',delivered:'#F0FDF4',cancelled:'#FFF2F2'
    };
    wrap.innerHTML=`<p style="font-size:13px;color:var(--muted);margin-bottom:16px">Tap an order to see live tracking.</p>`+
      orders.map(o=>{
        const ref=o.order_ref||('HM-'+String(o.order_id).padStart(6,'0'));
        const st=o.order_status||'pending';
        const label=STATUS_LABELS[st]||st;
        const active=['pending','confirmed','out_for_delivery'].includes(st);
        const dateStr=o.created_at?new Date(o.created_at).toLocaleDateString([],{day:'numeric',month:'short',hour:'2-digit',minute:'2-digit'}):'';
        return `<div onclick="selectOrder(${o.order_id})" style="cursor:pointer;background:var(--card);border-radius:var(--radius);border:1.5px solid ${active?'rgba(245,166,35,.4)':'var(--border)'};box-shadow:var(--shadow);padding:18px 20px;margin-bottom:12px;display:flex;align-items:center;gap:16px;transition:border-color .2s,box-shadow .2s"
          onmouseenter="this.style.borderColor='var(--gold)';this.style.boxShadow='0 4px 18px rgba(245,166,35,.18)'"
          onmouseleave="this.style.borderColor='${active?'rgba(245,166,35,.4)':'var(--border)'}';this.style.boxShadow='var(--shadow)'">
          <div style="flex:1;min-width:0">
            <div style="font-family:'Nunito',sans-serif;font-weight:900;font-size:15px;color:var(--text)">${esc(ref)}</div>
            <div style="font-size:12px;color:var(--muted);margin-top:2px">${esc(dateStr)}${o.customer_name?' · '+esc(o.customer_name):''}</div>
          </div>
          <div style="display:flex;align-items:center;gap:12px;flex-shrink:0">
            <span style="font-family:'Nunito',sans-serif;font-weight:800;font-size:14px;color:var(--charcoal-2)">${fmt(o.total_amount)}</span>
            <span style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:16px;font-size:11.5px;font-weight:700;background:${statusBg[st]||'#f5f5f5'};color:${statusColor[st]||'#555'}">${esc(label)}</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
          </div>
        </div>`;
      }).join('');
  }catch(e){
    wrap.innerHTML=`<div class="lookup-card" style="text-align:center;padding:40px 24px">
      <p style="font-size:13px;color:var(--muted)">Could not connect to the server. Please check your connection.</p>
      <button onclick="loadMyOrders()" style="margin-top:14px;padding:9px 20px;border-radius:20px;background:var(--gold);border:none;font-family:'Nunito',sans-serif;font-weight:800;font-size:13px;cursor:pointer">Retry</button>
    </div>`;
  }
}

function selectOrder(orderId){
  showScreen('loading-state');
  fetchOrder(orderId);
}

async function refreshOrder(){
  if(!currentOrderId) return;
  const btn=document.getElementById('btn-refresh');
  btn.classList.add('spinning');
  await fetchOrder(currentOrderId, true);
  setTimeout(()=>btn.classList.remove('spinning'),400);
}

async function fetchOrder(orderId, isRefresh=false){
  let tok=getToken();
  if(!tok){forceLogout();return;}
  try{
    let res=await fetch(`${API}/orders/${orderId}`,{
      headers:{Accept:'application/json','Authorization':'Bearer '+tok}
    });
    if(res.status===401){
      tok=await refreshToken();
      if(!tok){forceLogout();return;}
      res=await fetch(`${API}/orders/${orderId}`,{
        headers:{Accept:'application/json','Authorization':'Bearer '+tok}
      });
    }
    const rawText=await res.text();
    let data;
    try{data=JSON.parse(rawText);}
    catch{
      showError('Server Error','The server returned an unexpected response. Please try again.');
      return;
    }
    if(!res.ok||!data.success){
      if(res.status===404){
        showError('Order Not Found','We couldn\'t find that order. Please try again.');
      } else {
        // Don't kill polling on a transient error — retry in 10s
        clearTimeout(refreshTimer);
        if(currentOrderId) refreshTimer=setTimeout(()=>refreshOrder(),10000);
        if(!isRefresh) showError('Something Went Wrong', data.error||data.message||'Could not load order details.');
      }
      return;
    }
    currentOrderId=orderId;
    history.replaceState(null,'',`tracking.php?order_id=${orderId}`);
    renderOrder(data.data||data);
    if(isRefresh) showToast('Order status updated.','success');
    // Auto-refresh every 10s while not delivered/cancelled
    clearTimeout(refreshTimer);
    const status=(data.data||data).order_status||(data.data||data).status||'';
    if(!['delivered','cancelled'].includes(status)){
      refreshTimer=setTimeout(()=>refreshOrder(),10000);
    }
  }catch(err){
    // If we came from the orders list (no currentOrderId), go back to list instead of error screen
    if(!currentOrderId){
      showScreen('lookup-state');
      await loadMyOrders();
      showToast('Could not connect. Please try again.','error');
    } else {
      // Keep polling even on network error — retry in 10s
      clearTimeout(refreshTimer);
      refreshTimer=setTimeout(()=>refreshOrder(),10000);
      if(isRefresh) showToast('Could not connect — retrying...','error');
    }
  }
}

function showError(title, msg){
  document.getElementById('error-title').textContent=title;
  document.getElementById('error-msg').textContent=msg;
  showScreen('error-state');
}

// ── Render ────────────────────────────────────────────────────────────
function renderOrder(order){
  const status=order.order_status||order.status||'pending';
  const ordId=order.order_id||order.id||currentOrderId;
  const refStr='Order #'+(order.order_ref||('HM-'+String(ordId).padStart(6,'0')));

  // Ref badge
  document.getElementById('order-ref-badge').textContent=refStr;

  // Last-updated
  const now=new Date();
  const timeStr=now.toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'});
  document.getElementById('last-updated').textContent=timeStr;
  document.getElementById('status-updated-sub').textContent='Updated at '+timeStr;

  // Status badge
  const label=STATUS_LABELS[status]||status;
  document.getElementById('status-badge-wrap').innerHTML=
    `<span class="status-badge ${status}"><span class="dot"></span>${label}</span>`;

  // Timeline
  renderTimeline(status, order);

  // OTP card — visible only when driver has arrived
  const otpCard=document.getElementById('otp-card');
  const otpDisplay=document.getElementById('otp-display');
  const rawOtp=order.delivery_otp||'';
  if(status==='arrived'){
    if(rawOtp && /^\d{4,6}$/.test(rawOtp)){
      otpDisplay.innerHTML=rawOtp.split('').map((d,i)=>
        (i===2?'<span class="otp-sep"></span>':'')+
        `<span class="otp-digit">${d}</span>`
      ).join('');
    } else {
      otpDisplay.innerHTML='<span style="font-size:13px;color:var(--muted);text-align:center">Your OTP was sent to your notifications.<br>Check your notification bell 🔔</span>';
    }
    otpCard.style.display='block';
  } else {
    otpCard.style.display='none';
  }

  showScreen('tracking-state');
}

function renderTimeline(currentStatus, order){
  const statusOrder=['pending','confirmed','preparing','out_for_delivery','arrived','delivered'];
  const isCancelled=currentStatus==='cancelled';
  const currentIdx=statusOrder.indexOf(currentStatus);

  const tl=document.getElementById('timeline');
  tl.innerHTML='';

  if(isCancelled){
    tl.innerHTML=`
      <div class="tl-step">
        <div class="tl-left">
          <div class="tl-circle" style="background:var(--red);border-color:var(--red)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </div>
        </div>
        <div class="tl-body">
          <div class="tl-title active" style="color:var(--red)">Order Cancelled</div>
          <div class="tl-desc">This order was cancelled. Please contact us if you have any questions.</div>
        </div>
      </div>`;
    return;
  }

  STEPS.forEach((step,i)=>{
    const stepIdx=statusOrder.indexOf(step.statuses[0]);
    const isDone = currentIdx > stepIdx;
    const isActive = currentIdx === stepIdx;
    const isLast = i===STEPS.length-1;

    const circleClass = isDone ? 'done' : (isActive ? 'active' : 'idle');
    const lineClass   = isDone ? 'done' : '';
    const titleClass  = (isDone||isActive) ? (isDone?'done':'active') : '';

    // Try to get a timestamp from the order for done steps
    const tsMap={
      order_placed:     order.created_at||order.order_date,
      order_confirmed:  order.confirmed_at||order.preparing_at,
      out_for_delivery: order.dispatched_at||order.out_for_delivery_at,
      arrived:          order.arrived_at,
      delivered:        order.delivered_at
    };
    const ts=tsMap[step.key];
    const timeHtml = (isDone||isActive) && ts
      ? `<div class="tl-time">${new Date(ts).toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'})}</div>`
      : (isActive ? '<div class="tl-time">In progress…</div>' : '');

    tl.innerHTML+=`
      <div class="tl-step">
        <div class="tl-left">
          <div class="tl-circle ${circleClass}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">${step.icon}</svg>
          </div>
          ${!isLast?`<div class="tl-line ${lineClass}"></div>`:''}
        </div>
        <div class="tl-body">
          <div class="tl-title ${titleClass}">${step.label}</div>
          <div class="tl-desc">${isDone||isActive?step.desc:'Waiting…'}</div>
          ${timeHtml}
        </div>
      </div>`;
  });
}

// ── Boot ──────────────────────────────────────────────────────────────
(async function init(){
  const user=getUser();
  if(!getToken()||!user){forceLogout();return;}
  const name=(user.full_name||user.email||'there').split(' ')[0];
  document.getElementById('auth-area').innerHTML=
    '<div class="user-chip"><span class="user-name">Hi, '+esc(name)+'</span>'+
    '<button class="btn-logout" onclick="logout()">Logout</button></div>';

  // Check for order_id in URL query string
  const params=new URLSearchParams(window.location.search);
  const urlOrderId=params.get('order_id');
  if(urlOrderId&&/^\d+$/.test(urlOrderId)){
    showScreen('loading-state');
    await fetchOrder(parseInt(urlOrderId,10));
    // If fetchOrder failed and left us on error-state, that's fine — user can retry
  } else {
    // Clear any stale order_id from URL
    history.replaceState(null,'','tracking.php');
    showScreen('lookup-state');
    await loadMyOrders();
  }
})();
</script>
</body>
</html>
