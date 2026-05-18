<?php
// pos.php — Hakuna Matata POS — Cashier Point of Sale
// Auth handled by JavaScript localStorage (same pattern as other files)
// Cashier role: dine_in orders (walk-in), cash payment, change calculator, receipt print
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>POS — Hakuna Matata</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&family=Nunito:wght@700;800;900&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%;overflow:hidden}
body{font-family:'DM Sans',sans-serif;background:#F7F6F3;color:#2E2E2E;display:flex;flex-direction:column}

:root{
  --gold:#F5A623;--gold-hover:#E09418;--gold-light:rgba(245,166,35,.1);
  --dark:#252525;--charcoal:#363636;--bg:#F7F6F3;--surface:#fff;
  --border:#E6E5E1;--muted:#9A9993;--text:#2E2E2E;
  --red:#E8394D;--green:#22C55E;--orange:#F97316;--blue:#3B82F6;
  --radius:12px;--shadow:0 2px 12px rgba(0,0,0,.07);
}

/* ══ HEADER ══ */
.site-header{flex-shrink:0;width:100%;background:var(--dark);box-shadow:0 2px 16px rgba(0,0,0,.3);overflow:hidden;position:relative}
.hblob{position:absolute;border-radius:50%;background:var(--gold);opacity:.07;pointer-events:none}
.hblob.b1{width:300px;height:300px;bottom:-150px;left:-80px}
.hblob.b2{width:220px;height:220px;top:-100px;right:60px}
.hblob.b3{width:140px;height:140px;top:-40px;left:45%;opacity:.04}
.header-inner{position:relative;z-index:1;display:flex;align-items:center;gap:16px;padding:0 32px;height:76px}
.brand{display:flex;align-items:center;gap:14px;text-decoration:none;flex-shrink:0}
.brand-logo{width:52px;height:52px;background:white;border-radius:12px;overflow:hidden;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 14px rgba(0,0,0,.3);flex-shrink:0;animation:popIn .5s cubic-bezier(.34,1.56,.64,1) both}
.brand-logo img{width:100%;height:100%;object-fit:cover}
.brand-name{font-family:'Nunito',sans-serif;font-weight:900;font-size:18px;color:#fff;line-height:1.2;display:block}
.brand-name em{font-style:normal;color:var(--gold)}
.brand-sub{font-size:10px;color:rgba(255,255,255,.4);display:block;margin-top:1px}
.header-center{flex:1;display:flex;justify-content:center;align-items:center}
.pos-badge{display:flex;align-items:center;gap:8px;padding:6px 16px;border-radius:20px;background:rgba(245,166,35,.15);border:1px solid rgba(245,166,35,.3)}
.pos-badge-dot{width:8px;height:8px;border-radius:50%;background:var(--gold);animation:pulse 2s infinite}
.pos-badge-text{font-family:'Nunito',sans-serif;font-size:13px;font-weight:800;color:var(--gold);letter-spacing:.5px}
.header-right{display:flex;align-items:center;gap:10px;flex-shrink:0}
.cashier-info{text-align:right}
.cashier-name{font-size:13px;font-weight:600;color:rgba(255,255,255,.8);display:block}
.cashier-role{font-size:10px;color:rgba(255,255,255,.4);display:block}
.btn-logout{padding:5px 12px;border-radius:14px;border:1.5px solid rgba(255,255,255,.2);background:transparent;color:rgba(255,255,255,.45);font-size:11px;font-weight:600;cursor:pointer;transition:all .2s;font-family:'DM Sans',sans-serif}
.btn-logout:hover{border-color:var(--red);color:var(--red)}

/* ══ MAIN POS SPLIT ══ */
.pos-body{flex:1;display:grid;grid-template-columns:1fr 380px;min-height:0;overflow:hidden}

/* ══ LEFT: PRODUCTS ══ */
.products-panel{display:flex;flex-direction:column;background:#F7F6F3;border-right:1px solid var(--border);min-height:0;overflow:hidden}
.products-toolbar{flex-shrink:0;padding:12px 16px;background:white;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px}
.search-box{flex:1;height:38px;border-radius:22px;border:1.5px solid var(--border);background:var(--bg);padding:0 14px 0 38px;font-size:13px;font-family:'DM Sans',sans-serif;color:var(--text);outline:none;transition:border-color .2s;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%239A9993' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:12px center;background-size:16px}
.search-box:focus{border-color:var(--gold);background-color:white}
.products-grid{flex:1;overflow-y:auto;padding:14px;display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px;align-content:start}
.products-grid::-webkit-scrollbar{width:4px}
.products-grid::-webkit-scrollbar-track{background:transparent}
.products-grid::-webkit-scrollbar-thumb{background:var(--border);border-radius:2px}
.prod-card{background:white;border-radius:var(--radius);border:1.5px solid var(--border);overflow:hidden;cursor:pointer;transition:all .18s;position:relative}
.prod-card:hover{border-color:var(--gold);box-shadow:0 4px 18px rgba(245,166,35,.15);transform:translateY(-1px)}
.prod-card.unavailable{opacity:.45;cursor:not-allowed;filter:grayscale(.6)}
.prod-card:active:not(.unavailable){transform:scale(.96)}
.prod-img{height:88px;background:#F7F6F3;display:flex;align-items:center;justify-content:center;overflow:hidden;position:relative}
.prod-img img{width:100%;height:100%;object-fit:cover}
.prod-img-placeholder{font-size:28px;color:#D1D0CA}
.prod-badge-tag{position:absolute;top:6px;left:6px;background:var(--gold);color:var(--dark);font-size:9px;font-weight:800;padding:2px 6px;border-radius:4px}
.prod-info{padding:9px 10px}
.prod-name{font-size:12px;font-weight:700;color:var(--text);margin-bottom:4px;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.prod-price{font-family:'Nunito',sans-serif;font-size:15px;font-weight:900;color:var(--gold)}
.prod-stock{font-size:10px;color:var(--muted);margin-top:2px}
.prod-stock.low{color:var(--orange)}
.prod-stock.out{color:var(--red)}
.prod-add-btn{position:absolute;bottom:8px;right:8px;width:24px;height:24px;border-radius:50%;background:var(--gold);border:none;color:var(--dark);font-size:16px;font-weight:900;line-height:1;cursor:pointer;display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity .15s}
.prod-card:hover .prod-add-btn:not(.hidden){opacity:1}
.empty-products{grid-column:1/-1;text-align:center;padding:40px 20px;color:var(--muted)}
.empty-products svg{width:40px;height:40px;opacity:.25;margin-bottom:10px;display:block;margin-left:auto;margin-right:auto}

/* ══ RIGHT: CART ══ */
.cart-panel{display:flex;flex-direction:column;background:white;min-height:0;overflow:hidden}
.cart-header{flex-shrink:0;padding:14px 18px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.cart-title{font-family:'Nunito',sans-serif;font-size:16px;font-weight:900;color:var(--text);display:flex;align-items:center;gap:8px}
.cart-count{background:var(--dark);color:white;border-radius:10px;min-width:20px;height:20px;font-size:11px;font-weight:700;padding:0 6px;display:inline-flex;align-items:center;justify-content:center}
.cart-clear{padding:4px 10px;border-radius:12px;border:1.5px solid #FFCACA;background:transparent;color:var(--red);font-size:11px;font-weight:600;cursor:pointer;transition:all .2s;font-family:'DM Sans',sans-serif}
.cart-clear:hover{background:var(--red);color:white;border-color:var(--red)}

/* Cart items */
.cart-items{flex:1;overflow-y:auto;min-height:0;padding:10px 0}
.cart-items::-webkit-scrollbar{width:3px}
.cart-items::-webkit-scrollbar-thumb{background:var(--border)}
.cart-item{display:flex;align-items:center;gap:10px;padding:8px 16px;border-bottom:1px solid #F5F4F0;transition:background .15s}
.cart-item:hover{background:#FAFAF8}
.cart-item:last-child{border-bottom:none}
.ci-name{flex:1;font-size:13px;font-weight:600;color:var(--text);line-height:1.3}
.ci-qty-controls{display:flex;align-items:center;gap:6px;flex-shrink:0}
.ci-qty-btn{width:24px;height:24px;border-radius:6px;border:1.5px solid var(--border);background:white;color:var(--text);font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1;transition:all .15s}
.ci-qty-btn:hover{border-color:var(--gold);color:var(--gold)}
.ci-qty-btn.minus:hover{border-color:var(--red);color:var(--red)}
.ci-qty{font-family:'Nunito',sans-serif;font-size:14px;font-weight:800;min-width:24px;text-align:center}
.ci-price{font-family:'Nunito',sans-serif;font-size:13px;font-weight:800;color:var(--text);min-width:70px;text-align:right;flex-shrink:0}
.cart-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;padding:32px;color:var(--muted);text-align:center;gap:12px}
.cart-empty svg{width:48px;height:48px;opacity:.2}
.cart-empty p{font-size:13px}
.cart-empty small{font-size:12px;opacity:.7}

/* Cart totals */
.cart-totals{flex-shrink:0;border-top:2px solid var(--border);padding:14px 16px;background:#FAFAF8}
.total-row{display:flex;justify-content:space-between;align-items:center;padding:3px 0;font-size:13px}
.total-row .label{color:var(--muted);font-weight:500}
.total-row .value{font-weight:600}
.total-row.discount .value{color:var(--green)}
.total-row.grand{padding:10px 0 0;margin-top:6px;border-top:1.5px solid var(--border)}
.total-row.grand .label{font-family:'Nunito',sans-serif;font-size:15px;font-weight:900;color:var(--text)}
.total-row.grand .value{font-family:'Nunito',sans-serif;font-size:22px;font-weight:900;color:var(--dark)}

/* Promo code */
.promo-row{display:flex;gap:8px;margin-top:10px}
.promo-input{flex:1;height:36px;border-radius:8px;border:1.5px solid var(--border);background:white;padding:0 12px;font-size:13px;font-family:'DM Sans',sans-serif;text-transform:uppercase;letter-spacing:1px;outline:none;transition:border-color .2s}
.promo-input:focus{border-color:var(--gold)}
.promo-btn{height:36px;padding:0 14px;border-radius:8px;border:none;background:var(--dark);color:white;font-size:12px;font-weight:700;cursor:pointer;white-space:nowrap;font-family:'DM Sans',sans-serif;transition:background .2s}
.promo-btn:hover{background:var(--charcoal)}
.promo-msg{font-size:11px;margin-top:4px;min-height:16px}
.promo-msg.ok{color:var(--green)}
.promo-msg.err{color:var(--red)}

/* Cart actions */
.cart-actions{flex-shrink:0;padding:14px 16px;border-top:1px solid var(--border);display:flex;flex-direction:column;gap:8px}
.btn-pay{width:100%;height:48px;border-radius:14px;border:none;background:var(--gold);color:var(--dark);font-family:'Nunito',sans-serif;font-size:16px;font-weight:900;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:8px}
.btn-pay:hover:not(:disabled){background:var(--gold-hover);transform:translateY(-1px);box-shadow:0 6px 20px rgba(245,166,35,.35)}
.btn-pay:disabled{opacity:.45;cursor:not-allowed;transform:none}
.btn-pay svg{width:18px;height:18px}

/* ══ PAYMENT MODAL ══ */
.modal-overlay{display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.5);align-items:center;justify-content:center;padding:24px}
.modal-overlay.open{display:flex}
.modal{background:white;border-radius:20px;width:100%;max-width:420px;box-shadow:0 24px 64px rgba(0,0,0,.25);animation:slideUp .25s ease both}
.modal-hdr{padding:20px 24px 0;border-bottom:none}
.modal-hdr h3{font-family:'Nunito',sans-serif;font-size:20px;font-weight:900;color:var(--text);margin-bottom:2px}
.modal-hdr p{font-size:13px;color:var(--muted)}
.modal-body{padding:20px 24px}

/* Order summary in modal */
.order-num-display{text-align:center;padding:14px;background:#F7F6F3;border-radius:12px;margin-bottom:18px}
.order-num-display .num{font-family:'Nunito',sans-serif;font-size:32px;font-weight:900;color:var(--dark);letter-spacing:2px}
.order-num-display .num-label{font-size:11px;color:var(--muted);margin-top:2px}

.pay-summary{margin-bottom:18px}
.pay-row{display:flex;justify-content:space-between;align-items:center;padding:5px 0;font-size:13px;border-bottom:1px solid #F0EFEB}
.pay-row:last-child{border:none;padding-top:10px;margin-top:4px}
.pay-row .pl{color:var(--muted)}
.pay-row .pv{font-weight:600}
.pay-row.grand-row .pl{font-family:'Nunito',sans-serif;font-size:16px;font-weight:900}
.pay-row.grand-row .pv{font-family:'Nunito',sans-serif;font-size:20px;font-weight:900;color:var(--dark)}

/* Tendered input */
.tendered-section{margin-bottom:16px}
.tendered-label{font-size:12px;font-weight:700;color:var(--text);margin-bottom:8px;display:block}
.tendered-input-wrap{position:relative}
.currency-prefix{position:absolute;left:14px;top:50%;transform:translateY(-50%);font-family:'Nunito',sans-serif;font-size:20px;font-weight:900;color:var(--muted);pointer-events:none}
.tendered-input{width:100%;height:58px;border-radius:12px;border:2px solid var(--border);background:#F7F6F3;padding:0 14px 0 34px;font-family:'Nunito',sans-serif;font-size:28px;font-weight:900;color:var(--dark);outline:none;transition:border-color .2s;text-align:right}
.tendered-input:focus{border-color:var(--gold);background:white}
.quick-amounts{display:flex;gap:6px;margin-top:8px;flex-wrap:wrap}
.quick-btn{padding:5px 12px;border-radius:8px;border:1.5px solid var(--border);background:white;color:var(--text);font-size:12px;font-weight:700;cursor:pointer;font-family:'Nunito',sans-serif;transition:all .15s}
.quick-btn:hover{border-color:var(--gold);color:var(--gold)}

/* Change display */
.change-display{margin-top:14px;padding:14px 18px;border-radius:12px;display:flex;justify-content:space-between;align-items:center;transition:all .3s}
.change-display.positive{background:#F0FDF4;border:1.5px solid #BBF7D0}
.change-display.negative{background:#FFF1F2;border:1.5px solid #FFCACA}
.change-display.zero{background:#F7F6F3;border:1.5px solid var(--border)}
.change-label{font-size:14px;font-weight:700}
.change-label.positive{color:#15803D}
.change-label.negative{color:var(--red)}
.change-label.zero{color:var(--muted)}
.change-amount{font-family:'Nunito',sans-serif;font-size:26px;font-weight:900}
.change-amount.positive{color:var(--green)}
.change-amount.negative{color:var(--red)}
.change-amount.zero{color:var(--muted)}

.modal-footer{padding:16px 24px;border-top:1px solid var(--border);display:flex;gap:10px}
.btn-cancel{flex:0 0 auto;padding:10px 18px;border-radius:12px;border:1.5px solid var(--border);background:white;color:var(--text);font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;cursor:pointer;transition:all .2s}
.btn-cancel:hover{border-color:var(--charcoal)}
.btn-confirm{flex:1;padding:10px;border-radius:12px;border:none;background:var(--dark);color:white;font-family:'Nunito',sans-serif;font-size:15px;font-weight:900;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:6px}
.btn-confirm:hover:not(:disabled){background:var(--charcoal)}
.btn-confirm:disabled{opacity:.45;cursor:not-allowed}
.btn-confirm svg{width:16px;height:16px}

/* ══ PAYMENT METHOD TABS ══ */
.pay-method-tabs{display:flex;gap:8px;margin-bottom:16px}
.pay-tab{flex:1;height:52px;border-radius:12px;border:2px solid var(--border);background:white;cursor:pointer;font-family:'DM Sans',sans-serif;font-size:13px;font-weight:700;color:var(--muted);display:flex;align-items:center;justify-content:center;gap:8px;transition:all .2s}
.pay-tab:hover{border-color:var(--gold);color:var(--dark)}
.pay-tab.active{border-color:var(--dark);background:var(--dark);color:white}
.pay-tab.active.nfc-tab{border-color:var(--blue);background:var(--blue)}
.pay-tab svg{width:18px;height:18px;flex-shrink:0}
.nfc-section{display:none;flex-direction:column;align-items:center;padding:18px 0 8px;gap:12px}
.nfc-section.visible{display:flex}
.nfc-animation{width:80px;height:80px;border-radius:50%;background:rgba(59,130,246,.1);border:2px solid rgba(59,130,246,.3);display:flex;align-items:center;justify-content:center;position:relative;animation:nfcPulse 1.8s ease-in-out infinite}
.nfc-animation svg{width:36px;height:36px;color:#3B82F6}
@keyframes nfcPulse{0%,100%{box-shadow:0 0 0 0 rgba(59,130,246,.4)}50%{box-shadow:0 0 0 16px rgba(59,130,246,0)}}
.nfc-label{font-size:13px;font-weight:700;color:var(--blue);text-align:center}
.nfc-hint{font-size:11px;color:var(--muted);text-align:center;line-height:1.5}
.cash-section{display:flex;flex-direction:column;gap:0}
.cash-section.hidden{display:none}

/* ══ RESPONSIVE ══ */
@media(max-width:900px){
  .pos-body{grid-template-columns:1fr 320px}
  .header-inner{padding:0 16px}
}
@media(max-width:700px){
  /* Stack panels: show products by default, cart toggled */
  .pos-body{grid-template-columns:1fr;grid-template-rows:auto}
  .cart-panel{position:fixed;bottom:0;left:0;right:0;top:auto;z-index:200;height:60vh;border-radius:20px 20px 0 0;box-shadow:0 -8px 32px rgba(0,0,0,.15);transform:translateY(100%);transition:transform .3s ease}
  .cart-panel.cart-open{transform:translateY(0)}
  .products-panel{height:calc(100vh - 76px - 52px - 60px)}
  .header-inner{padding:0 12px;height:60px}
  .brand-sub{display:none}
  .brand-logo{width:40px;height:40px}
  /* Floating cart button */
  .cart-fab{display:flex}
}
/* Cart FAB — hidden on desktop, shown on mobile */
.cart-fab{display:none;position:fixed;bottom:20px;right:20px;z-index:201;width:56px;height:56px;border-radius:50%;background:var(--gold);border:none;color:var(--dark);font-size:22px;font-weight:900;cursor:pointer;align-items:center;justify-content:center;box-shadow:0 4px 16px rgba(0,0,0,.25);transition:transform .15s}
.cart-fab:active{transform:scale(.93)}
.cart-fab-badge{position:absolute;top:-4px;right:-4px;background:var(--dark);color:white;border-radius:10px;min-width:20px;height:20px;font-size:11px;font-weight:800;padding:0 5px;display:inline-flex;align-items:center;justify-content:center}

/* ══ RECEIPT (print) ══ */
#receipt-frame{display:none}
@media print{
  body > *:not(#receipt-frame){display:none!important}
  #receipt-frame{display:block;width:80mm;margin:0 auto;font-family:'Courier New',monospace}
  .rpt-header{text-align:center;margin-bottom:8px}
  .rpt-logo-img{width:72px;height:72px;object-fit:cover;border-radius:10px;margin:0 auto 8px;display:block;-webkit-print-color-adjust:exact;print-color-adjust:exact}
  .rpt-logo-text{font-size:18px;font-weight:900;letter-spacing:1px}
  .rpt-sub{font-size:10px;color:#555;margin-top:2px}
  .rpt-divider{border:none;border-top:1px dashed #333;margin:6px 0}
  .rpt-info{font-size:11px;margin-bottom:6px}
  .rpt-info div{display:flex;justify-content:space-between}
  .rpt-items{font-size:11px;margin-bottom:6px}
  .rpt-item{display:flex;justify-content:space-between;padding:1px 0}
  .rpt-totals{font-size:11px}
  .rpt-totals div{display:flex;justify-content:space-between;padding:1px 0}
  .rpt-totals .rpt-grand{font-size:14px;font-weight:900;border-top:1px dashed #333;padding-top:4px;margin-top:4px}
  .rpt-pay-method{font-size:10px;text-align:center;margin-top:4px;color:#555;font-style:italic}
  .rpt-footer{text-align:center;font-size:10px;margin-top:8px;color:#555}
}

/* ══ TOAST ══ */
.toast{position:fixed;bottom:20px;left:50%;transform:translateX(-50%) translateY(20px);z-index:9999;padding:10px 20px;border-radius:12px;font-size:13px;font-weight:600;background:var(--dark);color:white;opacity:0;transition:all .3s;pointer-events:none;white-space:nowrap}
.toast.show{transform:translateX(-50%) translateY(0);opacity:1}
.toast.success{background:#166534}
.toast.error{background:var(--red)}
.toast.warn{background:#B45309}

/* ══ ANIMATIONS ══ */
@keyframes popIn{0%{transform:scale(.6);opacity:0}100%{transform:scale(1);opacity:1}}
@keyframes slideUp{0%{transform:translateY(20px);opacity:0}100%{transform:translateY(0);opacity:1}}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
@keyframes addBounce{0%{transform:scale(1)}50%{transform:scale(1.08)}100%{transform:scale(1)}}
.cart-bounce{animation:addBounce .2s ease}

/* ══ LOADING ══ */
.skeleton{background:linear-gradient(90deg,#F0EFEB 25%,#E8E7E3 50%,#F0EFEB 75%);background-size:200% 100%;animation:shimmer 1.4s infinite;border-radius:6px}
@keyframes shimmer{0%{background-position:200% 0}100%{background-position:-200% 0}}
</style>
</head>
<body>

<!-- ══ HEADER ══ -->
<header class="site-header">
  <div class="hblob b1"></div>
  <div class="hblob b2"></div>
  <div class="hblob b3"></div>
  <div class="header-inner">
    <a href="index.php" class="brand">
      <div class="brand-logo">
        <img src="hakuna matata.png" alt="Hakuna Matata Logo">
      </div>
      <div>
        <span class="brand-name">Hakuna <em>Matata</em></span>
        <span class="brand-sub">Online Point of Sale</span>
      </div>
    </a>
    <div class="header-center">
      <div class="pos-badge">
        <div class="pos-badge-dot"></div>
        <span class="pos-badge-text">CASHIER POS</span>
      </div>
    </div>
    <div class="header-right">
      <div class="cashier-info">
        <span class="cashier-name" id="cashier-name">Cashier</span>
        <span class="cashier-role" id="cashier-date"></span>
      </div>
      <button class="btn-logout" onclick="logout()">Logout</button>
    </div>
  </div>
</header>

<!-- ══ POS BODY ══ -->
<div class="pos-body">

  <!-- LEFT: PRODUCTS -->
  <div class="products-panel">
    <!-- Search -->
    <div class="products-toolbar">
      <input type="search" class="search-box" id="prod-search" placeholder="Search product by name..." oninput="filterProducts()">
    </div>
    <!-- TYPE tabs: All / Butchery / Restaurant / Liquor -->
    <div class="type-tabs" id="type-tabs">
      <button class="type-tab active" data-type="" onclick="selectType('',this)">🏷️ All</button>
      <button class="type-tab" data-type="Butchery" onclick="selectType('Butchery',this)">🥩 Butchery</button>
      <button class="type-tab" data-type="Restaurant" onclick="selectType('Restaurant',this)">🍽️ Restaurant</button>
      <button class="type-tab" data-type="Liquor" onclick="selectType('Liquor',this)">🍺 Liquor</button>
    </div>

    <!-- Product grid -->
    <div class="products-grid" id="products-grid">
      <div class="empty-products">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
        <p>Loading products...</p>
      </div>
    </div>
  </div>

  <!-- RIGHT: CART -->
  <div class="cart-panel" id="cartPanel">
    <!-- Cart header -->
    <div class="cart-header">
      <span class="cart-title">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 001.99 1.61h9.72a2 2 0 001.99-1.61L23 6H6"/></svg>
        Order Cart
        <span class="cart-count" id="cart-count">0</span>
      </span>
      <button class="cart-clear" onclick="clearCart()">Clear All</button>
    </div>

    <!-- Cart items -->
    <div class="cart-items" id="cart-items">
      <div class="cart-empty">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 001.99 1.61h9.72a2 2 0 001.99-1.61L23 6H6"/></svg>
        <p>Cart is empty</p>
        <small>Click a product to add it</small>
      </div>
    </div>

    <!-- Totals -->
    <div class="cart-totals">
      <div class="total-row">
        <span class="label">Subtotal</span>
        <span class="value" id="subtotal-display">E 0.00</span>
      </div>
      <div class="total-row discount" id="discount-row" style="display:none">
        <span class="label">Discount</span>
        <span class="value" id="discount-display">- E 0.00</span>
      </div>
      <div class="total-row grand">
        <span class="label">TOTAL</span>
        <span class="value" id="grand-total-display">E 0.00</span>
      </div>
      <!-- Promo code -->
      <div class="promo-row">
        <input type="text" class="promo-input" id="promo-code" placeholder="PROMO CODE" maxlength="30">
        <button class="promo-btn" onclick="applyPromo()">Apply</button>
      </div>
      <div class="promo-msg" id="promo-msg"></div>
    </div>

    <!-- Pay button -->
    <div class="cart-actions">
      <button class="btn-pay" id="pay-btn" onclick="openPayment()" disabled>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
        Process Payment
      </button>
    </div>
  </div>
</div>

<!-- ══ PAYMENT MODAL ══ -->
<div class="modal-overlay" id="payment-modal">
  <div class="modal">
    <div class="modal-hdr">
      <h3 id="pay-modal-title">Process Payment</h3>
      <p id="pay-modal-sub">Collection Order</p>
    </div>
    <div class="modal-body">
      <!-- Order number -->
      <div class="order-num-display">
        <div class="num" id="modal-order-num">A---</div>
        <div class="num-label">Collection Order Number</div>
      </div>
      <!-- Receipt-style item listing -->
      <div class="receipt-items" id="receipt-item-list">
        <!-- filled by openPayment() JS -->
      </div>
      <!-- Summary -->
      <div class="pay-summary">
        <div class="pay-row" id="modal-discount-row" style="display:none">
          <span class="pl">Discount</span>
          <span class="pv" id="modal-discount" style="color:var(--green)">- E 0.00</span>
        </div>
        <div class="pay-row grand-row">
          <span class="pl">TOTAL DUE</span>
          <span class="pv" id="modal-grand-total">E 0.00</span>
        </div>
      </div>

      <!-- ── PAYMENT METHOD TABS ── -->
      <div class="pay-method-tabs">
        <button class="pay-tab active" id="tab-cash" onclick="switchPayTab('cash')">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
          Cash
        </button>
        <button class="pay-tab nfc-tab" id="tab-nfc" onclick="switchPayTab('nfc')">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8.32a7.43 7.43 0 010 7.36"/><path d="M9.46 6.21a11.76 11.76 0 010 11.58"/><path d="M12.91 4.1a15.91 15.91 0 010 15.8"/><line x1="2" y1="12" x2="2.01" y2="12"/></svg>
          NFC / Tap
        </button>
      </div>

      <!-- ── CASH SECTION ── -->
      <div class="cash-section" id="cash-section">
        <div class="tendered-section">
          <span class="tendered-label">Cash Tendered by Customer</span>
          <div class="tendered-input-wrap">
            <span class="currency-prefix">E</span>
            <input type="number" class="tendered-input" id="tendered-input" placeholder="0" min="0" step="0.50" oninput="calcChange()">
          </div>
          <!-- Quick amount buttons -->
          <div class="quick-amounts" id="quick-amounts"></div>
        </div>
        <!-- Change -->
        <div class="change-display zero" id="change-display">
          <span class="change-label zero">Change</span>
          <span class="change-amount zero" id="change-amount">E 0.00</span>
        </div>
      </div>

      <!-- ── NFC SECTION ── -->
      <div class="nfc-section" id="nfc-section">
        <div class="nfc-animation">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8.32a7.43 7.43 0 010 7.36"/><path d="M9.46 6.21a11.76 11.76 0 010 11.58"/><path d="M12.91 4.1a15.91 15.91 0 010 15.8"/><line x1="2" y1="12" x2="2.01" y2="12"/></svg>
        </div>
        <div class="nfc-label">Tap to Pay</div>
        <div class="nfc-hint">Ask customer to tap their card,<br>phone, or wearable on the reader.</div>
      </div>

    </div>
    <div class="modal-footer">
      <button class="btn-cancel" onclick="closePayment()">Cancel</button>
      <button class="btn-confirm" id="confirm-btn" onclick="confirmPayment()" disabled>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        Confirm &amp; Print Receipt
      </button>
    </div>
  </div>
</div>

<!-- ══ RECEIPT (for print) ══ -->
<div id="receipt-frame"></div>

<!-- ══ TOAST ══ -->
<div class="toast" id="toast"></div>

<script>
// ── Config ───────────────────────────────────────────────────────────
const API = 'http://localhost/hakunamatata_v3/api';

// ── Auth ─────────────────────────────────────────────────────────────
const token = localStorage.getItem('access_token');
const user  = JSON.parse(localStorage.getItem('user') || 'null');

if (!token || !user) { window.location.href = 'login.php'; }
if (user && !['cashier','manager','super_admin'].includes(user.role)) {
  window.location.href = 'index.php';
}

// ── Header info ──────────────────────────────────────────────────────
document.getElementById('cashier-name').textContent = user ? user.full_name : 'Cashier';
document.getElementById('cashier-date').textContent = new Date().toLocaleDateString('en-GB',{weekday:'short',day:'numeric',month:'short',year:'numeric'});

// ── State ────────────────────────────────────────────────────────────
let allProducts   = [];
let filteredProds = [];
let activeCat     = '';
let cart          = {}; // { product_id: { product, qty } }
let discount      = { code:'', amount:0, id:null };
let grandTotal    = 0;
let orderCounter  = null; // fetched from last order or set locally
let activePayMethod = 'cash'; // 'cash' | 'nfc'

// ── Helpers ──────────────────────────────────────────────────────────
function esc(s){ const d=document.createElement('div');d.textContent=s||'';return d.innerHTML; }
function E(n)  { return 'E ' + parseFloat(n||0).toFixed(2); }
function $(id) { return document.getElementById(id); }
function showToast(msg,type=''){
  const el=$('toast'); el.textContent=msg;
  el.className='toast show'+(type?' '+type:'');
  clearTimeout(el._t); el._t=setTimeout(()=>{el.className='toast';},3000);
}
// Live token — updated when silent refresh happens mid-session
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

async function authFetch(path, opts={}, _isRetry=false) {
  const tok = _liveToken || localStorage.getItem('access_token') || '';
  opts.headers = Object.assign(
    {'Content-Type':'application/json', 'Authorization':'Bearer '+tok},
    opts.headers || {}
  );
  try {
    const r = await fetch(API + path, opts);
    if (r.status === 401 && !_isRetry) {
      // Token expired — attempt silent refresh then retry once
      const ok = await refreshAccessToken();
      if (ok) {
        delete opts.headers['Authorization'];
        return authFetch(path, opts, true);
      }
      // Refresh failed — session truly expired, safe to logout now
      logout();
      return null;
    }
    return r;
  } catch(e) {
    showToast('Network error — check XAMPP', 'error');
    return null;
  }
}
function logout(){
  const rt=localStorage.getItem('refresh_token');
  if(rt){ fetch(API+'/auth/logout',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({refresh_token:rt})}).catch(()=>{}); }
  localStorage.removeItem('access_token');
  localStorage.removeItem('refresh_token');
  localStorage.removeItem('user');
  window.location.href='login.php';
}

// ── PRODUCTS ─────────────────────────────────────────────────────────
async function loadProducts(){
  const grid = $('products-grid');
  grid.innerHTML = `<div class="empty-products"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width:36px;height:36px;opacity:.25;margin-bottom:8px"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg><p>Loading products…</p></div>`;
  try {
    const r = await authFetch('/products?available=1');
    if(!r){
      grid.innerHTML = `<div class="empty-products"><p style="color:var(--red)">Server unreachable — check XAMPP</p></div>`;
      return;
    }
    const d = await r.json();
    if(!d.success){
      grid.innerHTML = `<div class="empty-products"><p style="color:var(--red)">${esc(d.error||'Failed to load products')}</p></div>`;
      return;
    }
    // Handle both d.data (array) and d (plain array) response shapes
    const raw = Array.isArray(d.data) ? d.data : (Array.isArray(d) ? d : []);
    // Normalise all IDs to numbers — PHP PDO returns integers as strings
    // by default, causing strict === comparisons to fail silently.
    allProducts = raw.map(p => ({
      ...p,
      id:          parseInt(p.id ?? p.product_id, 10),
      category_id: parseInt(p.category_id, 10),
      price:       parseFloat(p.price),
      stock_qty:   parseInt(p.stock_qty, 10),
    }));
    filteredProds = allProducts.slice();
    renderProductGrid(allProducts);
  } catch(e) {
    console.error('loadProducts error', e);
    grid.innerHTML = `<div class="empty-products"><p style="color:var(--red)">Error loading products</p></div>`;
    showToast('Error loading products','error');
  }
}

// All categories cached by type for fast filtering
let allCategories = [];

async function loadCategories(){
  // cat-scroll removed — no-op
}

function renderCategoryTabs(type){
  // cat-scroll removed — no-op
}

let activeType = '';

function selectType(type, btn){
  activeType = type;
  document.querySelectorAll('.type-tab').forEach(b=>b.classList.remove('active'));
  btn.classList.add('active');
  renderCategoryTabs(type);
  filterProducts();
}

function selectCat(catId, btn){
  activeCat = catId;
  document.querySelectorAll('.cat-btn').forEach(b=>b.classList.remove('active'));
  btn.classList.add('active');
  filterProducts();
}

function filterProducts(){
  const q = $('prod-search').value.toLowerCase().trim();
  filteredProds = allProducts.filter(p=>{
    const matchType = !activeType || (p.category_type||p.type||'') === activeType;
    const matchCat  = !activeCat  || p.category_id == activeCat;
    const matchQ    = !q || p.name.toLowerCase().includes(q)
                        || (p.category_name||'').toLowerCase().includes(q)
                        || (p.description||'').toLowerCase().includes(q);
    return matchType && matchCat && matchQ;
  });
  renderProductGrid(filteredProds);
}

function renderProductGrid(products){
  const grid = $('products-grid');
  if(!products.length){
    grid.innerHTML=`<div class="empty-products">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V8z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
      <p>No products found</p></div>`;
    return;
  }
  grid.innerHTML = products.map(p=>{
    const inCart  = cart[p.id]?.qty || 0;
    const outStock = p.stock_status === 'out_of_stock';
    const lowStock = p.stock_status === 'low_stock';
    const stockLabel = outStock ? 'Out of stock' : lowStock ? `Only ${p.stock_qty} left` : '';
    const stockCls   = outStock ? 'out' : lowStock ? 'low' : '';
    return `<div class="prod-card${outStock?' unavailable':''}" onclick="${outStock?'showToast(\'Out of stock\',\'warn\')':'addToCart('+p.id+')'}">
      <div class="prod-img">
        ${p.img_url?`<img src="${(p.img_url&&!p.img_url.startsWith('http')?'http://localhost/hakunamatata_v3'+p.img_url:p.img_url)}" alt="${esc(p.name)}" onerror="this.style.display='none'">`:
          p.emoji?`<div class="prod-img-placeholder" style="font-size:32px">${esc(p.emoji)}</div>`:
          `<div class="prod-img-placeholder">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
          </div>`}
        ${p.badge?`<span class="prod-badge-tag">${esc(p.badge)}</span>`:''}
        ${inCart>0?`<span class="prod-badge-tag" style="background:var(--dark);color:white;right:6px;left:auto">×${inCart}</span>`:''}
      </div>
      <div class="prod-info">
        <div class="prod-name">${esc(p.name)}</div>
        <div class="prod-price">${E(p.price)}${p.unit?` <span style="font-size:10px;font-weight:600;color:var(--muted)">/${esc(p.unit)}</span>`:''}</div>
        ${stockLabel?`<div class="prod-stock ${stockCls}">${stockLabel}</div>`:''}
      </div>
      <button class="prod-add-btn${outStock?' hidden':''}" onclick="event.stopPropagation();addToCart(${p.id})">+</button>
    </div>`;
  }).join('');
}

// ── CART ─────────────────────────────────────────────────────────────
function addToCart(productId){
  productId = +productId; // coerce to number
  const prod = allProducts.find(p=>p.id===productId);
  if(!prod) return;
  if(prod.stock_status === 'out_of_stock'){ showToast('Out of stock','warn'); return; }
  if(cart[productId]){
    // check stock limit (skip for unlimited items: stock_qty < 0)
    if(prod.stock_status !== 'unlimited' && prod.stock_qty !== null && cart[productId].qty >= prod.stock_qty){
      showToast('Max stock reached','warn'); return;
    }
    cart[productId].qty++;
  }else{
    cart[productId] = { product: prod, qty: 1 };
  }
  updateCart();
  renderProductGrid(filteredProds.length?filteredProds:allProducts);
}

function changeQty(productId, delta){
  productId = +productId; // coerce to number
  if(!cart[productId]) return;
  cart[productId].qty += delta;
  if(cart[productId].qty <= 0) delete cart[productId];
  updateCart();
  renderProductGrid(filteredProds.length?filteredProds:allProducts);
}

function clearCart(){
  if(!Object.keys(cart).length) return;
  if(!confirm('Clear the entire cart?')) return;
  cart = {};
  discount = { code:'', amount:0, id:null };
  $('promo-code').value = '';
  $('promo-msg').textContent = '';
  $('promo-msg').className = 'promo-msg';
  $('discount-row').style.display = 'none';
  updateCart();
  renderProductGrid(filteredProds.length?filteredProds:allProducts);
}

function updateCart(){
  const items  = Object.values(cart);
  const count  = items.reduce((s,i)=>s+i.qty,0);
  const subtotal = items.reduce((s,i)=>s+(i.product.price*i.qty),0);
  const discAmt  = Math.min(discount.amount, subtotal);
  grandTotal = Math.max(0, subtotal - discAmt);

  // Cart count
  $('cart-count').textContent = count;

  // Cart items list
  const itemsEl = $('cart-items');
  if(!items.length){
    itemsEl.innerHTML=`<div class="cart-empty">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 001.99 1.61h9.72a2 2 0 001.99-1.61L23 6H6"/></svg>
      <p>Cart is empty</p><small>Click a product to add it</small></div>`;
  }else{
    itemsEl.innerHTML = items.map(({product:p,qty})=>`
      <div class="cart-item">
        <div class="ci-name">${esc(p.name)}<br><small style="color:var(--muted);font-weight:400">${E(p.price)} each</small></div>
        <div class="ci-qty-controls">
          <button class="ci-qty-btn minus" onclick="changeQty(${p.id},-1)">−</button>
          <span class="ci-qty">${qty}</span>
          <button class="ci-qty-btn" onclick="changeQty(${p.id},1)">+</button>
        </div>
        <span class="ci-price">${E(p.price*qty)}</span>
      </div>`).join('');
  }

  // Totals
  $('subtotal-display').textContent   = E(subtotal);
  $('grand-total-display').textContent = E(grandTotal);
  if(discAmt>0){
    $('discount-row').style.display = '';
    $('discount-display').textContent = '- ' + E(discAmt);
  }else{
    $('discount-row').style.display = 'none';
  }

  // Pay button
  $('pay-btn').disabled = !items.length;
}

// ── PROMO CODE ───────────────────────────────────────────────────────
async function applyPromo(){
  const code = $('promo-code').value.trim().toUpperCase();
  if(!code){ showToast('Enter a promo code','warn'); return; }
  const subtotal = Object.values(cart).reduce((s,i)=>s+(i.product.price*i.qty),0);
  const r = await authFetch('/discounts/validate',{method:'POST',body:JSON.stringify({code,subtotal})});
  if(!r) return;
  const d = await r.json();
  const msg = $('promo-msg');
  if(d.success && d.data){
    const dc = d.data;
    const amt = dc.type==='percentage' ? (subtotal*dc.value/100) : parseFloat(dc.value);
    discount = { code, amount:amt, id:dc.discount_id };
    msg.textContent = `Applied: ${dc.value}${dc.type==='percentage'?'%':' E'} off`;
    msg.className = 'promo-msg ok';
    updateCart();
    showToast('Promo applied!','success');
  }else{
    discount = { code:'', amount:0, id:null };
    msg.textContent = d.error || 'Invalid or expired code';
    msg.className = 'promo-msg err';
    updateCart();
  }
}

// ── PAYMENT METHOD SWITCH ────────────────────────────────────────────
function switchPayTab(method){
  activePayMethod = method;
  const isCash = method === 'cash';
  $('tab-cash').className = 'pay-tab' + (isCash ? ' active' : '');
  $('tab-nfc').className  = 'pay-tab nfc-tab' + (!isCash ? ' active' : '');
  $('cash-section').className = 'cash-section' + (isCash ? '' : ' hidden');
  $('nfc-section').className  = 'nfc-section' + (!isCash ? ' visible' : '');
  // NFC: confirm immediately enabled (no cash tendering needed); Cash: depends on input
  if(!isCash){
    $('confirm-btn').disabled = false;
  } else {
    calcChange();
  }
}

// ── PAYMENT MODAL ────────────────────────────────────────────────────
function openPayment(){
  if(!Object.keys(cart).length) return;
  const items = Object.values(cart);
  const count = items.reduce((s,i)=>s+i.qty,0);
  const discAmt = Math.min(discount.amount, Object.values(cart).reduce((s,i)=>s+(i.product.price*i.qty),0));

  // Generate optimistic dine_in order number
  const num = 'A' + String(Date.now()).slice(-3).padStart(3,'0');
  $('modal-order-num').textContent = num;
  $('modal-grand-total').textContent = E(grandTotal);
  // Populate receipt item list
  $('receipt-item-list').innerHTML = items.map(({product:p,qty})=>`
    <div class="rcp-row">
      <span class="rcp-name">${esc(p.name)}</span>
      <span class="rcp-qty">×${qty}</span>
      <span class="rcp-price">${E(p.price*qty)}</span>
    </div>`).join('');
  if(discAmt>0){ $('modal-discount-row').style.display=''; $('modal-discount').textContent='- '+E(discAmt); }
  else $('modal-discount-row').style.display='none';

  // Quick amount buttons (rounded up to nearest 10/20/50/100)
  const amounts = [grandTotal];
  [10,20,50,100,200,500].forEach(r=>{ const a=Math.ceil(grandTotal/r)*r; if(!amounts.includes(a)&&a>=grandTotal) amounts.push(a); });
  amounts.sort((a,b)=>a-b);
  $('quick-amounts').innerHTML = amounts.slice(0,6).map(a=>`<button class="quick-btn" onclick="setTendered(${a})">${E(a)}</button>`).join('');

  // Pre-fill tendered with exact total so confirm is immediately enabled
  // Cashier can still change the amount if customer pays with different amount
  $('tendered-input').value = grandTotal.toFixed(2);
  calcChange();  // this enables the confirm button and shows "Exact amount"

  // Always start on Cash tab
  switchPayTab('cash');

  $('payment-modal').classList.add('open');
  setTimeout(()=>{ $('tendered-input').focus(); $('tendered-input').select(); }, 100);
}

function setTendered(amount){
  $('tendered-input').value = amount.toFixed(2);
  calcChange();
}

function calcChange(){
  const tendered = parseFloat($('tendered-input').value)||0;
  const change   = tendered - grandTotal;
  const display  = $('change-display');
  const label    = display.querySelector('.change-label');
  const amount   = $('change-amount');

  if(tendered<=0){
    resetChangeDisplay(); $('confirm-btn').disabled=true; return;
  }
  if(change<0){
    display.className='change-display negative';
    label.className='change-label negative'; label.textContent='Short by';
    amount.className='change-amount negative'; amount.textContent=E(Math.abs(change));
    $('confirm-btn').disabled=true;
  }else if(change===0){
    display.className='change-display zero';
    label.className='change-label zero'; label.textContent='Exact amount';
    amount.className='change-amount zero'; amount.textContent='E 0.00';
    $('confirm-btn').disabled=false;
  }else{
    display.className='change-display positive';
    label.className='change-label positive'; label.textContent='Change to give';
    amount.className='change-amount positive'; amount.textContent=E(change);
    $('confirm-btn').disabled=false;
  }
}

function resetChangeDisplay(){
  const d=$('change-display');
  d.className='change-display zero';
  d.querySelector('.change-label').className='change-label zero';
  d.querySelector('.change-label').textContent='Change';
  $('change-amount').className='change-amount zero';
  $('change-amount').textContent='E 0.00';
}

function closePayment(){
  $('payment-modal').classList.remove('open');
}

// ── CONFIRM PAYMENT & PLACE ORDER ────────────────────────────────────
async function confirmPayment(){
  const isNFC  = activePayMethod === 'nfc';
  const tendered = isNFC ? grandTotal : (parseFloat($('tendered-input').value)||0);
  if(!isNFC && tendered<grandTotal){ showToast('Insufficient cash amount','error'); return; }

  $('confirm-btn').disabled=true;
  $('confirm-btn').innerHTML='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;animation:spin 1s linear infinite"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg> Processing...';

  // Build order payload
  const subtotal = Object.values(cart).reduce((s,i)=>s+(i.product.price*i.qty),0);
  const items = Object.values(cart).map(({product:p,qty})=>({
    product_id: p.id,
    name: p.name,
    unit_price: parseFloat(p.price),
    quantity: qty,
    addon_total: 0,
    line_total: parseFloat((p.price*qty).toFixed(2))
  }));

  const payload = {
    order_type:      'dine_in',
    channel:         'desktop',
    payment_method:  isNFC ? 'Credit Card' : 'Cash on Delivery',
    reference_no:    (isNFC ? 'NFC-' : 'CASH-') + Date.now(),
    subtotal:        parseFloat(subtotal.toFixed(2)),
    delivery_fee:    0,
    discount_code:   discount.code || null,
    tendered_amount: tendered,
    change_amount:   isNFC ? 0 : parseFloat((tendered - grandTotal).toFixed(2)),
    items
  };

  const r = await authFetch('/orders',{method:'POST',body:JSON.stringify(payload)});
  if(!r){
    $('confirm-btn').disabled=false;
    $('confirm-btn').innerHTML='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Confirm &amp; Print Receipt';
    return;
  }
  const d = await r.json();
  if(!d.success){
    showToast(d.error||'Order failed','error');
    $('confirm-btn').disabled=false;
    $('confirm-btn').innerHTML='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Confirm &amp; Print Receipt';
    return;
  }

  const order = d.data;
  const change = isNFC ? 0 : (tendered - grandTotal);
  // Print receipt then reset — ready for next customer
  printReceipt(order, tendered, change, isNFC ? 'NFC / Tap to Pay' : 'Cash');

  closePayment();
  cart     = {};
  discount = { code:'', amount:0, id:null };
  $('promo-code').value='';
  $('promo-msg').textContent='';
  $('promo-msg').className='promo-msg';
  $('discount-row').style.display='none';
  updateCart();
  renderProductGrid(filteredProds.length?filteredProds:allProducts);

  $('confirm-btn').disabled=false;
  $('confirm-btn').innerHTML='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Confirm &amp; Print Receipt';
  showToast('✓ Order complete — ready for next customer','success');
}

// ── LOGO BASE64 CACHE ────────────────────────────────────────────────
let _logoBase64 = null;
async function getLogoBase64(){
  if(_logoBase64) return _logoBase64;
  try {
    const res = await fetch('hakuna_matata.png');
    if(!res.ok) return null;
    const blob = await res.blob();
    return await new Promise(resolve=>{
      const reader = new FileReader();
      reader.onloadend = ()=>{ _logoBase64 = reader.result; resolve(_logoBase64); };
      reader.onerror  = ()=>resolve(null);
      reader.readAsDataURL(blob);
    });
  } catch(e){ return null; }
}

// ── RECEIPT PRINTING ─────────────────────────────────────────────────
async function printReceipt(order, tendered, change, payMethod='Cash'){
  const isNFC = payMethod.toLowerCase().includes('nfc');
  const now = new Date();
  const dateStr = now.toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'});
  const timeStr = now.toLocaleTimeString('en-GB',{hour:'2-digit',minute:'2-digit'});

  const itemsHtml = (order.items||Object.values(cart).map(({product:p,qty})=>({
    name:p.name, quantity:qty, unit_price:p.price, line_total:p.price*qty
  }))).map(it=>`
    <div class="rpt-item">
      <span>${esc(it.name)} x${it.quantity}</span>
      <span>${E(it.line_total)}</span>
    </div>`).join('');

  const discAmt = Math.min(discount.amount, order.subtotal||0);
  const cashierName = user?.full_name || 'Cashier';

  const subtotalAmt = parseFloat((order.subtotal||grandTotal));
  const grandAmt    = parseFloat((order.grand_total||grandTotal));
  const vatRate     = 0.15; // 15% VAT — adjust if needed
  const vatAmt      = grandAmt * vatRate / (1 + vatRate); // VAT inclusive

  const logoSrc = await getLogoBase64();
  const logoHtml = logoSrc
    ? `<img src="${logoSrc}" alt="Hakuna Matata" class="rpt-logo-img">`
    : '';

  document.getElementById('receipt-frame').innerHTML = `
    <div class="rpt-header">
      ${logoHtml}
      <div class="rpt-logo-text">HAKUNA MATATA</div>
    </div>
    <hr class="rpt-divider">
    <div class="rpt-info">
      <div><span>Order</span><span><strong>${esc(order.order_ref||order.order_number||$('modal-order-num').textContent||'')}</strong></span></div>
      <div><span>Date</span><span>${dateStr}</span></div>
      <div><span>Time</span><span>${timeStr}</span></div>
      <div><span>Cashier</span><span><strong>${esc(cashierName)}</strong></span></div>
      <div><span>Type</span><span>Collection (Walk-in)</span></div>
      <div><span>Payment</span><span>${esc(payMethod)}</span></div>
    </div>
    <hr class="rpt-divider">
    <div class="rpt-items">${itemsHtml}</div>
    <hr class="rpt-divider">
    <div class="rpt-totals">
      <div><span>Subtotal</span><span>${E(subtotalAmt)}</span></div>
      ${discAmt>0?`<div><span>Discount (${discount.code})</span><span>- ${E(discAmt)}</span></div>`:''}
      <div><span>VAT (15% incl.)</span><span>${E(vatAmt)}</span></div>
      <div class="rpt-grand"><span>TOTAL</span><span>${E(grandAmt)}</span></div>
      ${isNFC
        ? `<div><span>NFC / TAP TO PAY</span><span>✓ Approved</span></div>`
        : `<div><span>CASH</span><span>${E(tendered)}</span></div>
           <div><span>CHANGE</span><span><strong>${E(change)}</strong></span></div>`
      }
    </div>
    ${isNFC ? `<div class="rpt-pay-method">Paid via NFC / Contactless</div>` : ''}
    <hr class="rpt-divider">
    <div class="rpt-footer">
      <div>Thank you for your order!</div>
      <div>Please collect at the counter</div>
    </div>`;

  setTimeout(()=>window.print(), 250);
}

// ── KEYBOARD SHORTCUT ─────────────────────────────────────────────────
document.addEventListener('keydown',e=>{
  // F2 = focus search
  if(e.key==='F2'){ e.preventDefault(); $('prod-search').focus(); }
  // F8 = process payment
  if(e.key==='F8'&&!$('pay-btn').disabled){ e.preventDefault(); openPayment(); }
  // ESC = close modal
  if(e.key==='Escape') closePayment();
  // Enter in tendered = confirm
  if(e.key==='Enter'&&$('payment-modal').classList.contains('open')&&!$('confirm-btn').disabled) confirmPayment();
});

// Close modal on overlay click
$('payment-modal').addEventListener('click',e=>{ if(e.target===$('payment-modal')) closePayment(); });

// ── INIT ─────────────────────────────────────────────────────────────
async function init(){
  await Promise.all([loadProducts(), loadCategories()]);
}
init();
</script>

<!-- Print spin animation -->
<style>@keyframes spin{to{transform:rotate(360deg)}}
/* ── TYPE TABS (All / Butchery / Restaurant / Liquor) ── */
.type-tabs{display:flex;gap:6px;padding:10px 14px 8px;flex-shrink:0;overflow-x:auto;background:white;border-bottom:1px solid var(--border)}
.type-tabs::-webkit-scrollbar{height:0}
.type-tab{flex-shrink:0;padding:7px 14px;border-radius:20px;border:1.5px solid var(--border);background:white;font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;color:var(--muted);cursor:pointer;transition:all .2s;white-space:nowrap}
.type-tab:hover{border-color:var(--gold);color:var(--dark)}
.type-tab.active{background:var(--dark);border-color:var(--dark);color:white}

/* ── RECEIPT ITEM LIST in payment modal ── */
.receipt-items{border-top:1px dashed var(--border);border-bottom:1px dashed var(--border);padding:8px 0;margin-bottom:10px;max-height:160px;overflow-y:auto}
.receipt-items::-webkit-scrollbar{width:3px}
.receipt-items::-webkit-scrollbar-thumb{background:var(--border)}
.rcp-row{display:flex;justify-content:space-between;align-items:center;padding:4px 0;font-size:13px;gap:8px}
.rcp-name{flex:1;color:var(--text)}
.rcp-qty{color:var(--muted);font-size:12px;white-space:nowrap}
.rcp-price{font-weight:600;color:var(--dark);white-space:nowrap}
</style>

<!-- Mobile cart FAB -->
<button class="cart-fab" id="cartFab" onclick="toggleMobileCart()" aria-label="Open cart">
  🛒
  <span class="cart-fab-badge" id="cartFabBadge">0</span>
</button>

<script>
function toggleMobileCart(){
  var panel=document.getElementById('cartPanel');
  var fab=document.getElementById('cartFab');
  if(panel){
    panel.classList.toggle('cart-open');
    fab.textContent=panel.classList.contains('cart-open')?'✕':'🛒';
    if(panel.classList.contains('cart-open')){
      var badge=document.getElementById('cartFabBadge');
      if(badge) fab.appendChild(badge);
    }
  }
}
// Keep FAB badge in sync with cart count
function syncCartFab(){
  var count=document.getElementById('cart-count');
  var badge=document.getElementById('cartFabBadge');
  if(count&&badge) badge.textContent=count.textContent||'0';
}
// Observe cart count changes
var cartCountEl=document.getElementById('cart-count');
if(cartCountEl&&window.MutationObserver){
  new MutationObserver(syncCartFab).observe(cartCountEl,{childList:true,characterData:true,subtree:true});
  syncCartFab();
}
</script>
</body>
</html>