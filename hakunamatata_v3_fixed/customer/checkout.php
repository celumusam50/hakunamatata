<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Checkout — Hakuna Matata</title>
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
.page-sub{font-size:14px;color:var(--muted);margin-bottom:28px;padding-left:18px}
/* ── CHECKOUT LAYOUT ── */
.checkout-layout{display:block;max-width:600px;margin:0 auto}
/* ── FORM SECTIONS ── */
.form-section{background:var(--card);border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow);padding:22px;margin-bottom:16px}
.section-header{display:flex;align-items:center;gap:12px;margin-bottom:20px;padding-bottom:14px;border-bottom:1px solid var(--border)}
.section-icon{width:38px;height:38px;background:var(--gold-light);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:1px solid rgba(245,166,35,.2)}
.section-icon svg{color:var(--gold)}
.section-title{font-family:'Nunito',sans-serif;font-size:16px;font-weight:900;color:var(--text)}
.section-step{font-size:11px;color:var(--muted);font-weight:600;margin-left:auto;background:var(--bg);padding:3px 9px;border-radius:12px;border:1px solid var(--border)}
.form-group{margin-bottom:14px}
.form-group:last-child{margin-bottom:0}
.form-label{display:block;font-size:11.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px}
.required{color:var(--red);margin-left:1px}
.form-input,.form-select,.form-textarea{width:100%;padding:11px 14px;border-radius:10px;border:1.5px solid var(--border);background:var(--bg);font-family:'DM Sans',sans-serif;font-size:14px;color:var(--text);outline:none;transition:border-color .2s,box-shadow .2s}
.form-input:focus,.form-select:focus,.form-textarea:focus{border-color:var(--gold);box-shadow:0 0 0 3px rgba(245,166,35,.12);background:#fff}
.form-input[readonly]{background:#F0EDE8;color:var(--charcoal);cursor:not-allowed;border-style:dashed}
.form-input.invalid,.form-select.invalid{border-color:var(--red)!important;box-shadow:0 0 0 3px rgba(232,57,77,.1)!important}
.form-textarea{resize:vertical;min-height:72px}
.form-row-2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.select-wrap{position:relative}
.select-wrap::after{content:'';position:absolute;right:14px;top:50%;transform:translateY(-50%);width:0;height:0;border:5px solid transparent;border-top:6px solid var(--muted);pointer-events:none}
.select-wrap .form-select{padding-right:38px;appearance:none;-webkit-appearance:none}
.prefix-wrap{display:flex;align-items:stretch;border-radius:10px;border:1.5px solid var(--border);background:var(--bg);overflow:hidden;transition:border-color .2s,box-shadow .2s}
.prefix-wrap:focus-within{border-color:var(--gold);box-shadow:0 0 0 3px rgba(245,166,35,.12);background:#fff}
.prefix-wrap.invalid{border-color:var(--red)!important;box-shadow:0 0 0 3px rgba(232,57,77,.1)!important}
.input-prefix{padding:11px 12px;font-size:14px;font-weight:700;color:var(--muted);background:#F0EDE8;border-right:1.5px solid var(--border);white-space:nowrap;flex-shrink:0;display:flex;align-items:center}
.prefix-input{border:none!important;background:transparent!important;border-radius:0!important;box-shadow:none!important;outline:none;flex:1;padding:11px 14px;font-family:'DM Sans',sans-serif;font-size:14px;color:var(--text)}
.card-wrap{position:relative}
.card-wrap .form-input{padding-right:76px}
.card-badges{position:absolute;right:12px;top:50%;transform:translateY(-50%);display:flex;gap:4px}
.cb{font-size:9px;font-weight:900;padding:3px 6px;border-radius:4px;letter-spacing:.04em}
.cb-visa{background:#1A1F36;color:#fff}.cb-mc{background:#EB001B;color:#fff}
.cvv-wrap{position:relative}
.cvv-wrap .form-input{padding-right:40px}
.cvv-icon{position:absolute;right:12px;top:50%;transform:translateY(-50%);color:var(--muted);display:flex}
/* ── PAYMENT TOGGLE ── */
.payment-toggle{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:22px}
.pay-method{padding:16px 10px;border-radius:12px;border:2px solid var(--border);background:var(--bg);cursor:pointer;transition:all .2s;display:flex;flex-direction:column;align-items:center;gap:8px;font-family:'DM Sans',sans-serif}
.pay-method:hover{border-color:var(--gold);background:var(--gold-light)}
.pay-method.active{border-color:var(--gold);background:var(--gold-light);box-shadow:0 2px 10px rgba(245,166,35,.2)}
.pay-icon{width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.pay-label{font-size:12px;font-weight:700;color:var(--charcoal-2);text-align:center;line-height:1.35}
/* ── BALANCE CHIP (shown after verify) ── */
.balance-chip{display:none;align-items:center;gap:8px;margin-top:12px;padding:10px 14px;border-radius:10px;font-size:13px;font-weight:600}
.balance-chip.ok{background:#F0FDF4;border:1px solid #BBF7D0;color:#166534}
.balance-chip.low{background:#FFF2F2;border:1px solid #FFCACA;color:#991b1b}
.balance-chip svg{flex-shrink:0}
.balance-chip.show{display:flex}
/* ── DEMO NOTICE ── */
.demo-notice{background:#FFF7E6;border:1px solid rgba(245,166,35,.35);border-radius:10px;padding:10px 14px;font-size:12px;color:#92600a;margin-top:14px;display:flex;align-items:flex-start;gap:8px;font-weight:500;line-height:1.45}
.demo-notice svg{flex-shrink:0;margin-top:1px}
/* ── PLACE ORDER BUTTON ── */
.btn-place-order{width:100%;padding:16px;border-radius:14px;background:var(--gold);color:var(--charcoal-2);font-family:'Nunito',sans-serif;font-size:17px;font-weight:900;border:none;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:10px;margin-top:4px;box-shadow:0 4px 14px rgba(245,166,35,.25)}
.btn-place-order:hover:not(:disabled){background:var(--gold-hover);transform:translateY(-1px);box-shadow:0 8px 24px rgba(245,166,35,.4)}
.btn-place-order:disabled{opacity:.5;cursor:not-allowed;transform:none;box-shadow:none}
.btn-place-order svg{width:20px;height:20px;flex-shrink:0}
/* ── SUCCESS STATE ── */
.success-panel{text-align:center;padding:60px 24px}
.success-circle{width:86px;height:86px;background:linear-gradient(135deg,#22C55E,#16A34A);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 22px;box-shadow:0 8px 28px rgba(34,197,94,.3)}
.success-circle svg{width:42px;height:42px;color:#fff}
.success-panel h2{font-family:'Nunito',sans-serif;font-size:26px;font-weight:900;color:var(--text);margin-bottom:10px}
.success-panel p{font-size:14px;color:var(--muted);margin-bottom:4px;max-width:380px;margin-left:auto;margin-right:auto;line-height:1.55}
.order-ref{display:inline-block;margin:16px 0 6px;font-size:13px;font-weight:800;color:var(--charcoal-2);background:#F0EDE8;border-radius:8px;padding:7px 18px;letter-spacing:.05em;border:1px solid var(--border)}
.btn-shop{display:inline-flex;align-items:center;gap:6px;padding:12px 28px;border-radius:22px;background:var(--gold);color:var(--charcoal-2);font-family:'Nunito',sans-serif;font-size:14px;font-weight:800;text-decoration:none;transition:background .2s;margin-top:14px}
.btn-shop:hover{background:var(--gold-hover)}
.btn-track{display:inline-flex;align-items:center;gap:6px;padding:12px 28px;border-radius:22px;background:var(--charcoal-2);color:#fff;font-family:'Nunito',sans-serif;font-size:14px;font-weight:800;text-decoration:none;transition:background .2s;margin-top:14px;margin-right:10px}
.btn-track:hover{background:var(--charcoal)}
.success-actions{display:flex;flex-wrap:wrap;justify-content:center;gap:4px}
.redirect-note{font-size:12px;color:var(--muted);margin-top:18px}
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
  .header-inner,.page{padding-left:20px;padding-right:20px}
  .brand-sub{display:none}
}
@media(max-width:560px){
  .form-row-2{grid-template-columns:1fr}
  .payment-toggle{grid-template-columns:1fr}
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
      <a href="cart.php" class="btn-back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Back to Cart
      </a>
      <div id="auth-area"></div>
    </div>
  </div>
</header>

<!-- PAGE -->
<div class="page">
  <h1 class="page-heading">Checkout</h1>
  <p class="page-sub">Review your order and complete payment</p>

  <div id="loading-state" class="spinner-wrap"><div class="spinner"></div></div>

  <div id="checkout-content" class="checkout-layout" style="display:none">

    <!-- ── LEFT COLUMN ── -->
    <div>

      <!-- ① Delivery Details -->
      <div class="form-section">
        <div class="section-header">
          <div class="section-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          </div>
          <h2 class="section-title">Delivery Details</h2>
          <span class="section-step">1 of 2</span>
        </div>
        <div class="form-group">
          <label class="form-label">Delivery Address <span class="required">*</span></label>
          <input type="text" class="form-input" id="delivery-address" placeholder="e.g. 12 Gwamile Street, Mbabane"/>
        </div>
        <div class="form-group">
          <label class="form-label">Delivery Instructions</label>
          <div class="select-wrap">
            <select class="form-select" id="delivery-instr" onchange="handleInstrChange()">
              <option value="">Choose an option...</option>
              <option value="Leave at door">Leave at door</option>
              <option value="Call when arrived">Call when arrived</option>
              <option value="Ring doorbell">Ring doorbell</option>
              <option value="Hand to me directly">Hand to me directly</option>
              <option value="Leave with neighbour">Leave with neighbour</option>
              <option value="Other">Other (specify below)</option>
            </select>
          </div>
        </div>
        <div class="form-group" id="custom-instr-wrap" style="display:none">
          <label class="form-label">Custom Instructions</label>
          <textarea class="form-textarea" id="custom-instr" rows="2" placeholder="e.g. Gate code is 1234, leave by the blue door..."></textarea>
        </div>
      </div>

      <!-- ③ Payment Method -->
      <div class="form-section">
        <div class="section-header">
          <div class="section-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
          </div>
          <h2 class="section-title">Payment Method</h2>
          <span class="section-step">2 of 2</span>
        </div>

        <div class="payment-toggle">
          <button class="pay-method" id="btn-mtn" onclick="selectPayment('mtn')">
            <div class="pay-icon" style="background:#FFC107">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#222" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18" stroke-width="3"/></svg>
            </div>
            <span class="pay-label">MTN Mobile Money</span>
          </button>
          <button class="pay-method" id="btn-bank" onclick="selectPayment('bank')">
            <div class="pay-icon" style="background:#1A1F36">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            </div>
            <span class="pay-label">Bank Card</span>
          </button>
        </div>

        <!-- MTN Fields -->
        <div id="mtn-fields" style="display:none">
          <div class="form-group">
            <label class="form-label">Mobile Money Number <span class="required">*</span></label>
            <div class="prefix-wrap" id="mtn-num-wrap">
              <span class="input-prefix">+268</span>
              <input type="tel" class="prefix-input" id="mtn-number" placeholder="76 XXX XXXX" maxlength="12" oninput="fmtPhone(this); resetBalance()"/>
            </div>
          </div>
          <!-- Balance chip (shown after verification) -->
          <div class="balance-chip" id="mtn-balance-chip">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            <span id="mtn-balance-text">Balance: E0.00</span>
          </div>

        </div>

        <!-- Bank Card Fields -->
        <div id="bank-fields" style="display:none">
          <div class="form-group">
            <label class="form-label">Cardholder Name <span class="required">*</span></label>
            <input type="text" class="form-input" id="card-name" placeholder="Name as it appears on card" oninput="resetBalance()"/>
          </div>
          <div class="form-group">
            <label class="form-label">Card Number <span class="required">*</span></label>
            <div class="card-wrap">
              <input type="text" class="form-input" id="card-number" placeholder="XXXX XXXX XXXX XXXX" maxlength="19" oninput="fmtCard(this); resetBalance()" inputmode="numeric"/>
              <div class="card-badges">
                <span class="cb cb-visa">VISA</span>
                <span class="cb cb-mc">MC</span>
              </div>
            </div>
          </div>
          <div class="form-row-2">
            <div class="form-group">
              <label class="form-label">Expiry Date <span class="required">*</span></label>
              <input type="text" class="form-input" id="card-expiry" placeholder="MM / YY" maxlength="7" oninput="fmtExpiry(this); resetBalance()" inputmode="numeric"/>
            </div>
            <div class="form-group">
              <label class="form-label">CVV <span class="required">*</span></label>
              <div class="cvv-wrap">
                <input type="password" class="form-input" id="card-cvv" placeholder="&bull;&bull;&bull;" maxlength="4" oninput="resetBalance()" inputmode="numeric"/>
                <span class="cvv-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </span>
              </div>
            </div>
          </div>
          <!-- Balance chip (shown after verification) -->
          <div class="balance-chip" id="bank-balance-chip">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            <span id="bank-balance-text">Balance: E0.00</span>
          </div>

        </div>
      </div>

      <!-- Place Order -->
      <button class="btn-place-order" id="btn-place-order" onclick="placeOrder()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        <span id="btn-label">Place Order &middot; E0.00</span>
      </button>
    </div>

  </div>

  <!-- Success State -->
  <div id="success-state" style="display:none" class="success-panel">
    <div class="success-circle">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
    </div>
    <h2>Order Placed Successfully!</h2>
    <p>Thank you! Our team will prepare your order shortly.</p>
    <div class="order-ref" id="order-ref">Order #HM-000000</div>
    <p>You&rsquo;ll receive a confirmation on your registered number.</p>
    <div class="success-actions">
      <a href="#" id="btn-track-order" class="btn-track">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        Track My Order
      </a>
      <a href="../shop.php" class="btn-shop">Continue Shopping</a>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
const API = window.location.origin + '/api';
const VAT_RATE = 0.15;
let cartTotals  = { subtotal:0, addonTotal:0, vat:0, discountAmt:0, total:0 };
let cartDiscount= null;
let payMethod   = null;
let balanceVerified = false;  // tracks if balance was already checked

// ── Auth ──────────────────────────────────────────────────────────────
function getToken()   { return localStorage.getItem('access_token'); }
function getRefresh() { return localStorage.getItem('refresh_token'); }
function getUser()    { return JSON.parse(localStorage.getItem('user')||'null'); }
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

function esc(s){const d=document.createElement('div');d.textContent=s||'';return d.innerHTML;}
function fmt(v){return 'E'+parseFloat(v||0).toFixed(2);}
function showToast(msg,type){
  const el=document.getElementById('toast');
  el.textContent=msg; el.className='toast show'+(type?' '+type:'');
  clearTimeout(el._t); el._t=setTimeout(()=>{el.className='toast';},3200);
}

// ── Boot ──────────────────────────────────────────────────────────────
(async function init(){
  const user=getUser();
  if(!getToken()||!user){forceLogout();return;}
  const name=(user.full_name||user.email||'there').split(' ')[0];
  document.getElementById('auth-area').innerHTML=
    '<div class="user-chip"><span class="user-name">Hi, '+esc(name)+'</span>'+
    '<button class="btn-logout" onclick="logout()">Logout</button></div>';
  try{
    const t=sessionStorage.getItem('cart_totals');
    if(t) cartTotals=JSON.parse(t);
    const d=sessionStorage.getItem('cart_discount');
    if(d) cartDiscount=JSON.parse(d);
  }catch{}
  document.getElementById('loading-state').style.display='none';
  document.getElementById('checkout-content').style.display='block';
  updateOrderBtn();
})();

function updateOrderBtn(){
  const lbl = document.getElementById('btn-label');
  if(lbl) lbl.innerHTML = 'Place Order &middot; ' + fmt(cartTotals.total);
}

// ── Payment toggle ────────────────────────────────────────────────────
function selectPayment(m){
  payMethod=m; balanceVerified=false;
  document.getElementById('btn-mtn').classList.toggle('active',m==='mtn');
  document.getElementById('btn-bank').classList.toggle('active',m==='bank');
  document.getElementById('mtn-fields').style.display =m==='mtn' ?'block':'none';
  document.getElementById('bank-fields').style.display=m==='bank'?'block':'none';
  hideBalanceChips();
}
function hideBalanceChips(){
  document.getElementById('mtn-balance-chip').className='balance-chip';
  document.getElementById('bank-balance-chip').className='balance-chip';
}
function resetBalance(){ balanceVerified=false; hideBalanceChips(); }

function handleInstrChange(){
  document.getElementById('custom-instr-wrap').style.display=
    document.getElementById('delivery-instr').value==='Other'?'block':'none';
}

// ── Formatters ────────────────────────────────────────────────────────
function fmtPhoneVal(v){
  const d=v.replace(/\D/g,'');
  if(d.length<=2) return d;
  if(d.length<=5) return d.slice(0,2)+' '+d.slice(2);
  return d.slice(0,2)+' '+d.slice(2,5)+' '+d.slice(5,9);
}
function fmtPhone(inp){inp.value=fmtPhoneVal(inp.value.replace(/\D/g,'').slice(0,9));}
function fmtCard(inp){const v=inp.value.replace(/\D/g,'').slice(0,16);inp.value=v.replace(/(.{4})/g,'$1 ').trim();}
function fmtExpiry(inp){let v=inp.value.replace(/\D/g,'').slice(0,4);if(v.length>=3) v=v.slice(0,2)+' / '+v.slice(2);inp.value=v;}

// ── Validation ────────────────────────────────────────────────────────
function setValid(id,ok,isWrap){
  const el=document.getElementById(id);
  if(el) el.classList.toggle('invalid',!ok);
  return ok;
}
function validate(){
  let ok=true;
  const need=r=>{if(!r) ok=false; return r;};
  need(setValid('delivery-address',document.getElementById('delivery-address').value.trim().length>3));
  if(!payMethod){ ok=false; showToast('Please select a payment method.','error'); }
  else if(payMethod==='mtn'){
    need(setValid('mtn-num-wrap',document.getElementById('mtn-number').value.replace(/\D/g,'').length>=8,true));
  } else {
    need(setValid('card-name',  document.getElementById('card-name').value.trim().length>1));
    need(setValid('card-number',document.getElementById('card-number').value.replace(/\D/g,'').length===16));
    need(setValid('card-expiry',document.getElementById('card-expiry').value.replace(/\D/g,'').length===4));
    need(setValid('card-cvv',   document.getElementById('card-cvv').value.replace(/\D/g,'').length>=3));
  }
  return ok;
}

// ── Balance Verification (calls /api/payments/verify) ─────────────────
async function verifyBalance(){
  const amount=cartTotals.total;
  const payload={ amount, method: payMethod==='mtn'?'mtn_mobile_money':'bank_card' };
  if(payMethod==='mtn'){
    payload.phone='+268'+document.getElementById('mtn-number').value.replace(/\D/g,'');
  } else {
    payload.card_number    =document.getElementById('card-number').value.replace(/\s/g,'');
    payload.cardholder_name=document.getElementById('card-name').value.trim();
    payload.expiry         =document.getElementById('card-expiry').value.replace(/[\s\/]/g,'');
    payload.cvv            =document.getElementById('card-cvv').value.trim();
  }
  const tok=getToken();
  const res=await fetch(API+'/payments/verify',{
    method:'POST',
    headers:{'Content-Type':'application/json','Accept':'application/json','Authorization':'Bearer '+tok},
    body:JSON.stringify(payload)
  });
  return { res, data: await res.json() };
}

function showBalanceChip(method, holderName, balance, sufficient){
  // Balance is verified silently — chip hidden, insufficient shown via toast only
}

// ── Place Order (verify then order) ───────────────────────────────────
async function placeOrder(){
  if(!validate()){showToast('Please fill in all required fields.','error');return;}

  const btn=document.getElementById('btn-place-order');
  const lbl=document.getElementById('btn-label');
  btn.disabled=true;

  // ── STEP 1: Verify balance ────────────────────────────────────────
  lbl.textContent='Checking balance\u2026';
  let verifyOk=false;
  try{
    const {res,data}=await verifyBalance();
    if(!res.ok||!data.success){
      showToast('Insufficient balance. Please top up and try again.','error');
      btn.disabled=false; lbl.innerHTML='Place Order &middot; '+fmt(cartTotals.total);
      return;
    }
    verifyOk=true;
    balanceVerified=true;
  }catch{
    showToast('Could not verify payment. Check your connection.','error');
    btn.disabled=false; lbl.innerHTML='Place Order &middot; '+fmt(cartTotals.total);
    return;
  }

  if(!verifyOk){btn.disabled=false; lbl.innerHTML='Place Order &middot; '+fmt(cartTotals.total); return;}

  // ── STEP 2: Place the order ───────────────────────────────────────
  lbl.textContent='Placing order\u2026';
  const instrVal   =document.getElementById('delivery-instr').value;
  const instrCustom=document.getElementById('custom-instr').value.trim();
  const delivInstr =instrVal==='Other'&&instrCustom?instrCustom:instrVal;

  const payload={
    delivery_address:      document.getElementById('delivery-address').value.trim(),
    delivery_instructions: delivInstr,
    payment_method:        payMethod==='mtn'?'mtn_mobile_money':'bank_card',
  };
  if(payMethod==='mtn'){
    payload.mtn_number='+268'+document.getElementById('mtn-number').value.replace(/\D/g,'');
  } else {
    payload.card_name  =document.getElementById('card-name').value.trim();
    payload.card_number=document.getElementById('card-number').value.replace(/\s/g,'');
    payload.card_expiry=document.getElementById('card-expiry').value.replace(/[\s\/]/g,'');
    payload.card_cvv   =document.getElementById('card-cvv').value.trim();
  }

  let tok=getToken();
  try{
    let res=await fetch(API+'/orders/checkout',{
      method:'POST',
      headers:{'Content-Type':'application/json','Accept':'application/json','Authorization':'Bearer '+tok},
      body:JSON.stringify(payload)
    });
    if(res.status===401){tok=await refreshToken();if(!tok){forceLogout();return;}
      res=await fetch(API+'/orders/checkout',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','Authorization':'Bearer '+tok},body:JSON.stringify(payload)});}

    // Safe parse — XAMPP may output PHP notices before JSON
    const rawText = await res.text();
    let data;
    try{ data = JSON.parse(rawText); }
    catch{
      // DEBUG: show actual server response so we can see the real PHP error
      const preview = rawText ? rawText.replace(/<[^>]+>/g,'').trim().slice(0,300) : '(empty response)';
      showToast('PHP Error: ' + preview, 'error');
      console.error('RAW SERVER RESPONSE:', rawText);
      btn.disabled=false; lbl.innerHTML='Place Order &middot; '+fmt(cartTotals.total);
      return;
    }

    if(!res.ok){
      showToast(data.error||data.message||'Could not place order. Please try again.','error');
      btn.disabled=false; lbl.innerHTML='Place Order &middot; '+fmt(cartTotals.total); return;
    }
    const ordId=data.data?.order_id||data.data?.id||Math.floor(Math.random()*900000+100000);
    const ordRef=data.data?.order_ref||('HM-'+String(ordId).padStart(6,'0'));
    sessionStorage.removeItem('cart_totals');
    sessionStorage.removeItem('cart_discount');
    document.getElementById('checkout-content').style.display='none';
    document.getElementById('order-ref').textContent='Order #'+ordRef;

    // Wire up the Track My Order button — redirect only on click
    const trackUrl = 'tracking.php?order_id='+ordId;
    document.getElementById('btn-track-order').href = trackUrl;

    document.getElementById('success-state').style.display='block';
    window.scrollTo({top:0,behavior:'smooth'});
  }catch(netErr){
    showToast('Network error: '+(netErr.message||'Could not connect.'),'error');
    btn.disabled=false; lbl.innerHTML='Place Order &middot; '+fmt(cartTotals.total);
  }
}
</script>
</body>
</html>