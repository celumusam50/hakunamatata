<?php
// driver.php — Hakuna Matata POS — Driver Delivery App
// Mobile-first Android app
// Auto-refreshes every 10s — catches new assignments in real-time
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<title>Driver App — Hakuna Matata</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&family=Nunito:wght@700;800;900&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:'DM Sans',sans-serif;background:#F7F6F3;color:#2E2E2E;min-height:100vh;padding-bottom:24px}

:root{
  --gold:#F5A623;--gold-hover:#E09418;--gold-light:rgba(245,166,35,.1);
  --dark:#252525;--charcoal:#363636;--bg:#F7F6F3;--surface:#fff;
  --border:#E6E5E1;--muted:#9A9993;--text:#2E2E2E;
  --red:#E8394D;--green:#22C55E;--orange:#F97316;--blue:#3B82F6;--purple:#A855F7;
  --radius:14px;--shadow:0 2px 12px rgba(0,0,0,.07);
}

/* ══ HEADER — identical pattern to customer.php / admin.php ══ */
.site-header{position:sticky;top:0;z-index:100;width:100%;background:var(--dark);box-shadow:0 2px 16px rgba(0,0,0,.3);overflow:hidden}
.header-blob{position:absolute;border-radius:50%;background:var(--gold);opacity:.07;pointer-events:none}
.header-blob.b1{width:300px;height:300px;bottom:-150px;left:-80px}
.header-blob.b2{width:220px;height:220px;top:-100px;right:60px}
.header-blob.b3{width:140px;height:140px;top:-40px;left:45%;opacity:.04}
.header-inner{position:relative;z-index:1;display:flex;align-items:center;gap:16px;width:100%;padding:0 20px;height:72px}
.brand{display:flex;align-items:center;gap:14px;text-decoration:none;flex-shrink:0}
.brand-logo{width:52px;height:52px;background:white;border-radius:14px;overflow:hidden;display:flex;align-items:center;justify-content:center;box-shadow:0 6px 20px rgba(0,0,0,.3);flex-shrink:0;animation:popIn .55s cubic-bezier(.34,1.56,.64,1) both}
.brand-logo img{width:100%;height:100%;object-fit:cover}
.brand-name{font-family:'Nunito',sans-serif;font-weight:900;font-size:18px;color:#fff;line-height:1.15;display:block}
.brand-name em{font-style:normal;color:var(--gold)}
.brand-sub{font-size:10px;color:rgba(255,255,255,.4);display:block;margin-top:1px}
.header-right{display:flex;align-items:center;gap:10px;margin-left:auto;flex-shrink:0}
.driver-badge{display:flex;align-items:center;gap:6px;padding:4px 10px;border-radius:16px}
.driver-badge.available{background:rgba(34,197,94,.15);border:1px solid rgba(34,197,94,.3)}
.driver-badge.busy{background:rgba(249,115,22,.15);border:1px solid rgba(249,115,22,.3)}
.status-dot{width:7px;height:7px;border-radius:50%;animation:pulse 1.8s infinite;flex-shrink:0}
.driver-badge.available .status-dot{background:var(--green)}
.driver-badge.busy .status-dot{background:var(--orange)}
.status-txt{font-size:11px;font-weight:700;white-space:nowrap}
.driver-badge.available .status-txt{color:var(--green)}
.driver-badge.busy .status-txt{color:var(--orange)}
.driver-name-lbl{font-size:12px;font-weight:600;color:rgba(255,255,255,.75);white-space:nowrap}
.btn-logout{padding:5px 11px;border-radius:14px;border:1.5px solid rgba(255,255,255,.2);background:transparent;color:rgba(255,255,255,.45);font-size:11px;font-weight:600;cursor:pointer;transition:all .2s;font-family:'DM Sans',sans-serif}
.btn-logout:hover{border-color:var(--red);color:var(--red)}

/* ══ TAB BAR — exact same pattern as customer.php ══ */
.tab-bar{background:#fff;border-bottom:1px solid var(--border);position:sticky;top:72px;z-index:90;box-shadow:0 2px 8px rgba(0,0,0,.05);overflow-x:auto}
.tab-bar::-webkit-scrollbar{display:none}
.tab-bar-inner{display:flex;align-items:center;gap:4px;padding:0 20px;height:52px;min-width:max-content}
.tab-btn{display:flex;align-items:center;gap:7px;padding:8px 16px;border-radius:22px;border:none;background:transparent;color:var(--muted);font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;cursor:pointer;transition:all .2s;white-space:nowrap}
.tab-btn:hover{color:var(--text);background:var(--bg)}
.tab-btn.active{background:var(--dark);color:#fff}
.tab-btn svg{width:15px;height:15px;flex-shrink:0}
.tab-badge{background:var(--gold);color:var(--dark);border-radius:10px;min-width:18px;height:18px;font-size:10px;font-weight:800;padding:0 5px;display:inline-flex;align-items:center;justify-content:center}
.tab-badge.red{background:var(--red);color:white}
.tab-badge.green{background:var(--green);color:white}

/* ══ MAIN ══ */
.main{padding:20px 16px;max-width:520px;margin:0 auto}
.tab-panel{display:none}
.tab-panel.active{display:block;animation:fadeUp .3s ease both}

/* ══ LIVE INDICATOR BAR ══ */
.live-bar{display:flex;align-items:center;justify-content:space-between;padding:8px 16px;background:white;border-bottom:1px solid var(--border);font-size:11px;color:var(--muted)}
.live-indicator{display:flex;align-items:center;gap:6px}
.live-dot{width:6px;height:6px;border-radius:50%;background:var(--green);animation:pulse 1.2s infinite}
.live-text{font-weight:600;color:var(--green)}
.last-refresh{color:var(--muted)}
.countdown-pill{display:flex;align-items:center;gap:4px;font-weight:600;color:var(--dark)}
.cd-num{font-family:'Nunito',sans-serif;font-weight:900;color:var(--gold);min-width:16px}

/* ══ EMPTY STATE ══ */
.empty-state{text-align:center;padding:48px 20px;color:var(--muted)}
.empty-state svg{width:48px;height:48px;opacity:.2;margin:0 auto 14px;display:block}
.empty-state h3{font-family:'Nunito',sans-serif;font-size:18px;font-weight:900;color:var(--text);margin-bottom:6px}
.empty-state p{font-size:13px;line-height:1.5}

/* ══ ACTIVE DELIVERY CARD ══ */
.active-card{background:var(--surface);border-radius:var(--radius);border:1px solid var(--border);box-shadow:0 4px 24px rgba(0,0,0,.1);margin-bottom:16px;overflow:hidden;position:relative}
.active-card::before{content:'';position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,var(--gold),var(--orange))}
.card-header{padding:14px 16px 12px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.order-num{font-family:'Nunito',sans-serif;font-size:24px;font-weight:900;color:var(--dark);letter-spacing:1px}
.status-chip{display:flex;align-items:center;gap:5px;padding:5px 12px;border-radius:20px;font-size:11px;font-weight:700}
.chip-pulse{width:6px;height:6px;border-radius:50%;animation:pulse 1.8s infinite;flex-shrink:0}
.chip-assigned{background:rgba(59,130,246,.1);color:var(--blue);border:1px solid rgba(59,130,246,.3)}
.chip-assigned .chip-pulse{background:var(--blue)}
.chip-en_route{background:rgba(168,85,247,.1);color:var(--purple);border:1px solid rgba(168,85,247,.3)}
.chip-en_route .chip-pulse{background:var(--purple)}
.chip-arrived{background:rgba(34,197,94,.12);color:#15803D;border:1px solid rgba(34,197,94,.4)}
.chip-arrived .chip-pulse{background:var(--green)}

/* Customer info */
.cust-block{padding:14px 16px;border-bottom:1px solid var(--border)}
.info-row{display:flex;align-items:flex-start;gap:12px;margin-bottom:12px}
.info-row:last-child{margin-bottom:0}
.info-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.info-icon svg{width:18px;height:18px}
.icon-addr{background:rgba(245,166,35,.1);color:var(--gold)}
.icon-phone{background:rgba(34,197,94,.1);color:var(--green)}
.icon-notes{background:rgba(59,130,246,.1);color:var(--blue)}
.icon-order{background:rgba(245,166,35,.1);color:var(--gold)}
.info-lbl{font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:2px}
.info-val{font-size:14px;font-weight:600;color:var(--text);line-height:1.4}
.info-val a{color:var(--green);text-decoration:none;font-weight:700}

/* Action buttons */
.action-block{padding:14px 16px}
.action-hint{font-size:12px;color:var(--muted);text-align:center;margin-bottom:10px;line-height:1.5}
.act-btn{width:100%;height:52px;border-radius:13px;border:none;font-family:'Nunito',sans-serif;font-size:15px;font-weight:900;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:9px;transition:all .2s;margin-bottom:9px}
.act-btn:last-child{margin-bottom:0}
.act-btn:active{transform:scale(.97)}
.act-btn svg{width:19px;height:19px}
.btn-onway{background:var(--purple);color:white}
.btn-onway:hover{background:#9333EA}
.btn-arrived{background:var(--blue);color:white}
.btn-arrived:hover{background:#2563EB}
.btn-map{background:var(--bg);border:1.5px solid var(--border)!important;color:var(--text);font-size:13px}
.btn-map:hover{border-color:var(--charcoal)!important}

/* OTP block */
.otp-block{padding:16px;background:linear-gradient(135deg,#F0FDF4,#DCFCE7);border-top:2px solid #BBF7D0}
.otp-title{font-family:'Nunito',sans-serif;font-size:15px;font-weight:900;color:#15803D;display:flex;align-items:center;gap:7px;margin-bottom:4px}
.otp-title svg{width:17px;height:17px}
.otp-desc{font-size:12px;color:#166534;margin-bottom:12px;line-height:1.5}
.otp-row{display:flex;gap:8px;margin-bottom:10px}
.otp-input{flex:1;height:58px;border-radius:12px;border:2px solid #BBF7D0;background:white;text-align:center;font-family:'Nunito',sans-serif;font-size:28px;font-weight:900;color:var(--dark);letter-spacing:6px;outline:none;transition:all .2s;-webkit-text-security:none}
.otp-input:focus{border-color:var(--green);box-shadow:0 0 0 3px rgba(34,197,94,.15)}
.otp-input.shake{animation:shake .3s ease;border-color:var(--red)}
.btn-confirm-otp{width:100%;height:52px;border-radius:13px;border:none;background:var(--green);color:white;font-family:'Nunito',sans-serif;font-size:15px;font-weight:900;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:all .2s}
.btn-confirm-otp:hover:not(:disabled){background:#16A34A}
.btn-confirm-otp:active:not(:disabled){transform:scale(.97)}
.btn-confirm-otp:disabled{opacity:.45;cursor:not-allowed}
.btn-confirm-otp svg{width:18px;height:18px}

/* Success card */
.success-card{background:linear-gradient(135deg,#F0FDF4,#DCFCE7);border:1px solid #BBF7D0;border-radius:var(--radius);padding:32px 20px;text-align:center;margin-bottom:16px;box-shadow:var(--shadow)}
.success-circle{width:68px;height:68px;background:var(--green);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;box-shadow:0 8px 24px rgba(34,197,94,.3)}
.success-circle svg{width:34px;height:34px;color:white}
.success-order{font-family:'Nunito',sans-serif;font-size:30px;font-weight:900;color:var(--dark);margin-bottom:4px}
.success-title{font-family:'Nunito',sans-serif;font-size:19px;font-weight:900;color:#15803D;margin-bottom:6px}
.success-sub{font-size:13px;color:#166534}

/* ══ ASSIGNED ORDERS LIST ══ */
.order-card{background:var(--surface);border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow);margin-bottom:10px;overflow:hidden}
.oc-inner{padding:14px 16px;display:flex;align-items:center;gap:12px}
.oc-num{font-family:'Nunito',sans-serif;font-size:20px;font-weight:900;color:var(--dark);min-width:66px}
.oc-info{flex:1;min-width:0}
.oc-customer{font-size:13px;font-weight:700;margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.oc-address{font-size:12px;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.oc-badge{display:flex;align-items:center;gap:4px;padding:3px 9px;border-radius:10px;font-size:10px;font-weight:700;flex-shrink:0}
.oc-badge.assigned{background:rgba(59,130,246,.1);color:var(--blue);border:1px solid rgba(59,130,246,.2)}
.oc-badge.waiting{background:rgba(249,115,22,.1);color:var(--orange);border:1px solid rgba(249,115,22,.2)}
.oc-items{font-size:11px;color:var(--muted);padding:0 16px 10px;border-top:1px solid #F5F4F0;padding-top:8px}

/* ══ DELIVERED LIST ══ */
.del-card{background:var(--surface);border-radius:12px;border:1px solid var(--border);padding:12px 16px;margin-bottom:8px;display:flex;align-items:center;gap:12px;box-shadow:0 1px 4px rgba(0,0,0,.04)}
.del-icon{width:36px;height:36px;background:#F0FDF4;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.del-icon svg{width:18px;height:18px;color:var(--green)}
.del-num{font-family:'Nunito',sans-serif;font-size:16px;font-weight:900;color:var(--dark)}
.del-cust{font-size:12px;color:var(--muted);margin-top:1px}
.del-right{margin-left:auto;text-align:right;flex-shrink:0}
.del-time{font-size:11px;color:var(--muted)}
.del-badge{font-size:10px;font-weight:700;background:#F0FDF4;color:#15803D;padding:2px 8px;border-radius:8px;display:block;margin-top:3px}
.del-summary{display:flex;align-items:center;gap:10px;padding:12px 16px;background:white;border-radius:var(--radius);border:1px solid var(--border);margin-bottom:12px;box-shadow:var(--shadow)}
.ds-stat{text-align:center;flex:1}
.ds-num{font-family:'Nunito',sans-serif;font-size:22px;font-weight:900;color:var(--dark)}
.ds-lbl{font-size:10px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.3px}
.ds-div{width:1px;height:32px;background:var(--border)}

/* ══ PROFILE TAB ══ */
.profile-card{background:var(--surface);border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow);padding:24px;text-align:center;margin-bottom:16px}
.profile-avatar{width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,var(--dark),var(--charcoal));display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-family:'Nunito',sans-serif;font-size:28px;font-weight:900;color:white}
.profile-name{font-family:'Nunito',sans-serif;font-size:20px;font-weight:900;color:var(--text);margin-bottom:4px}
.profile-role{font-size:12px;color:var(--muted);margin-bottom:16px}
.profile-info-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;text-align:left}
.pi-item{background:var(--bg);border-radius:10px;padding:12px;border:1px solid var(--border)}
.pi-lbl{font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:3px}
.pi-val{font-size:13px;font-weight:600;color:var(--text)}
.change-pass-btn{width:100%;height:44px;border-radius:12px;border:1.5px solid var(--border);background:transparent;color:var(--text);font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;cursor:pointer;margin-top:14px;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:6px}
.change-pass-btn:hover{border-color:var(--dark);background:var(--dark);color:white}
.change-pass-btn svg{width:14px;height:14px}

/* ══ TOAST ══ */
.toast{position:fixed;top:136px;left:50%;transform:translateX(-50%) translateY(-16px);z-index:9999;padding:10px 20px;border-radius:12px;font-size:13px;font-weight:600;background:var(--dark);color:white;opacity:0;transition:all .3s;pointer-events:none;white-space:nowrap;max-width:90vw;text-align:center}
.toast.show{transform:translateX(-50%) translateY(0);opacity:1}
.toast.success{background:#166534}
.toast.error{background:var(--red)}
.toast.warn{background:#B45309}

/* ══ ANIMATIONS ══ */
@keyframes popIn{0%{transform:scale(.6);opacity:0}100%{transform:scale(1);opacity:1}}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.35}}
@keyframes shake{0%,100%{transform:translateX(0)}25%{transform:translateX(-6px)}75%{transform:translateX(6px)}}
@keyframes fadeUp{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
@keyframes spin{to{transform:rotate(360deg)}}
@keyframes newOrder{0%{transform:scale(1)}30%{transform:scale(1.03)}60%{transform:scale(.99)}100%{transform:scale(1)}}
.new-order-flash{animation:newOrder .5s ease}
@media(max-width:400px){
  .header-inner{padding:0 12px;height:60px}
  .brand-logo{width:40px;height:40px}
  .brand-name{font-size:16px}
  .main{padding:14px 10px}
  .tab-bar-inner{padding:0 12px;gap:2px}
  .tab-btn{padding:7px 10px;font-size:12px}
  .act-btn{font-size:14px}
}
</style>
</head>
<body>

<!-- ══ HEADER ══ -->
<header class="site-header">
  <div class="header-blob b1"></div>
  <div class="header-blob b2"></div>
  <div class="header-blob b3"></div>
  <div class="header-inner">
    <a href="index.php" class="brand">
      <div class="brand-logo">
        <img src="hakuna matata.png" alt="Hakuna Matata Logo">
      </div>
      <div>
        <span class="brand-name">Hakuna <em>Matata</em></span>
        <span class="brand-sub">Driver App</span>
      </div>
    </a>
    <div class="header-right">
      <div class="driver-badge" id="driver-badge">
        <div class="status-dot"></div>
        <span class="status-txt" id="driver-status-txt">Loading</span>
      </div>
      <span class="driver-name-lbl" id="driver-name-lbl"></span>
      <button class="btn-logout" onclick="logout()">Logout</button>
    </div>
  </div>
</header>

<!-- ══ TAB BAR — same style as customer.php ══ -->
<div class="tab-bar">
  <div class="tab-bar-inner">

    <button class="tab-btn active" id="tb-active" onclick="switchTab('active',this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
      Active Delivery
      <span class="tab-badge" id="active-badge" style="display:none">1</span>
    </button>

    <button class="tab-btn" id="tb-assigned" onclick="switchTab('assigned',this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
      Assigned
      <span class="tab-badge" id="assigned-badge" style="display:none">0</span>
    </button>

    <button class="tab-btn" id="tb-delivered" onclick="switchTab('delivered',this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
      Delivered
      <span class="tab-badge green" id="delivered-badge" style="display:none">0</span>
    </button>

    <button class="tab-btn" id="tb-profile" onclick="switchTab('profile',this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      Profile
    </button>

  </div>
</div>

<!-- ══ LIVE BAR ══ -->
<div class="live-bar">
  <div class="live-indicator">
    <div class="live-dot"></div>
    <span class="live-text">LIVE</span>
    <span class="last-refresh" id="last-refresh">Refreshing...</span>
  </div>
  <div class="countdown-pill">
    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    Next in <span class="cd-num" id="cd-num">10</span>s
  </div>
</div>

<!-- ══ TAB PANELS ══ -->

<!-- ACTIVE DELIVERY TAB -->
<div id="panel-active" class="tab-panel active">
  <div class="main" id="active-content">
    <div class="empty-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      <h3>Loading...</h3>
    </div>
  </div>
</div>

<!-- ASSIGNED ORDERS TAB -->
<div id="panel-assigned" class="tab-panel">
  <div class="main" id="assigned-content">
    <div class="empty-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      <h3>Loading...</h3>
    </div>
  </div>
</div>

<!-- DELIVERED TAB -->
<div id="panel-delivered" class="tab-panel">
  <div class="main" id="delivered-content">
    <div class="empty-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
      <h3>Loading...</h3>
    </div>
  </div>
</div>

<!-- PROFILE TAB -->
<div id="panel-profile" class="tab-panel">
  <div class="main" id="profile-content">
    <div class="empty-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      <h3>Loading profile...</h3>
    </div>
  </div>
</div>

<!-- ══ TOAST ══ -->
<div class="toast" id="toast"></div>

<script>
// ── Config ────────────────────────────────────────────────────────────
const API = window.location.origin + '/api';
const REFRESH_INTERVAL = 10; // seconds — fast enough to catch assignments without hammering server

// ── Auth ──────────────────────────────────────────────────────────────
const token = localStorage.getItem('access_token');
const user  = JSON.parse(localStorage.getItem('user') || 'null');
if (!token || !user) { window.location.href = 'login.php'; }
if (user && !['driver','manager','super_admin'].includes(user.role)) {
  window.location.href = 'index.php';
}

// ── Header ────────────────────────────────────────────────────────────
document.getElementById('driver-name-lbl').textContent = user ? user.full_name.split(' ')[0] : 'Driver';

// ── State ─────────────────────────────────────────────────────────────
let allDeliveries = [];
let countdownVal  = REFRESH_INTERVAL;
let refreshTimer  = null;
let activeTab     = 'active';
let previousAssignedCount = 0;
let isFetching  = false;   // guards against concurrent loadDeliveries calls
let fetchVersion = 0;      // incremented on every status update to discard stale in-flight fetches
let isUpdating   = false;  // true while a PATCH is in-flight — blocks auto-refresh from wiping the active card

// ── Helpers ───────────────────────────────────────────────────────────
function esc(s){ const d=document.createElement('div');d.textContent=s||'';return d.innerHTML; }
function $(id) { return document.getElementById(id); }
function fmtTime(iso){
  if(!iso) return '';
  return new Date(iso).toLocaleTimeString('en-GB',{hour:'2-digit',minute:'2-digit',hour12:false});
}
function showToast(msg, type=''){
  const el=$('toast'); el.textContent=msg;
  el.className='toast show'+(type?' '+type:'');
  clearTimeout(el._t); el._t=setTimeout(()=>el.className='toast',3500);
}
let _liveToken = localStorage.getItem('access_token') || '';

async function refreshAccessToken() {
  const rt = localStorage.getItem('refresh_token');
  if (!rt) return false;
  try {
    const r = await fetch(API + '/auth/refresh', {
      method:'POST', headers:{'Content-Type':'application/json'},
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

async function authFetch(path, opts={}, _isRetry=false){
  const tok = _liveToken || localStorage.getItem('access_token') || '';
  opts.headers=Object.assign({'Content-Type':'application/json','Authorization':'Bearer '+tok},opts.headers||{});
  try{
    const r=await fetch(API+path,opts);
    if(r.status===401 && !_isRetry){
      const ok = await refreshAccessToken();
      if(ok){ delete opts.headers['Authorization']; return authFetch(path, opts, true); }
      logout(); return null;
    }
    return r;
  }catch(e){ return null; }
}
function logout(){
  clearInterval(refreshTimer);
  isFetching = true;
  const rt=localStorage.getItem('refresh_token');
  if(rt) fetch(API+'/auth/logout',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({refresh_token:rt})}).catch(()=>{});
  ['access_token','refresh_token','user'].forEach(k=>localStorage.removeItem(k));
  window.location.href='login.php';
}

// ── TAB SWITCHING ─────────────────────────────────────────────────────
function switchTab(tab, btn){
  document.querySelectorAll('.tab-panel').forEach(p=>p.classList.remove('active'));
  document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
  $('panel-'+tab).classList.add('active');
  if(btn) btn.classList.add('active');
  activeTab = tab;
  if(tab === 'profile') renderProfile();
}

// ── LOAD DELIVERIES ───────────────────────────────────────────────────
async function loadDeliveries(silent=false){
  if(isFetching) return;
  isFetching = true;
  const myVersion = ++fetchVersion;  // snapshot version so stale responses can be discarded
  const r = await authFetch('/deliveries');
  if(!r){
    isFetching = false;
    if(!silent) updateLastRefresh('Error');
    return;
  }
  let resp;
  try { resp = await r.json(); } catch(e) { isFetching = false; if(!silent) updateLastRefresh('Error'); return; }
  if(!resp || !resp.success){ isFetching = false; return; }
  // Discard if a newer fetch was initiated while this one was in-flight
  if(myVersion !== fetchVersion){ isFetching = false; return; }

  allDeliveries = (resp.data || []).map(item => ({
    ...item,
    status:       item.delivery_status,
    order_number: item.order_ref,
  }));

  const nowAssigned = allDeliveries.filter(x=>x.status==='assigned').length;
  if(nowAssigned > previousAssignedCount && !silent){
    showToast('New delivery assigned to you!','success');
    vibrate();
    const tb = $('tb-assigned');
    tb.classList.add('new-order-flash');
    setTimeout(()=>tb.classList.remove('new-order-flash'),600);
  }
  previousAssignedCount = nowAssigned;

  isFetching = false;
  updateBadgesAndStatus();
  renderActiveTab();
  renderAssignedTab();
  renderDeliveredTab();
  updateLastRefresh();
  resetCountdown();
}

function vibrate(){
  if(navigator.vibrate) navigator.vibrate([200,100,200]);
}

function updateLastRefresh(msg){
  const el = $('last-refresh');
  if(msg){ el.textContent = msg; return; }
  const now = new Date();
  el.textContent = 'Updated '+now.toLocaleTimeString('en-GB',{hour:'2-digit',minute:'2-digit',second:'2-digit'});
}

function updateBadgesAndStatus(){
  const active    = allDeliveries.find(d=>['arrived','en_route','out_for_delivery'].includes(d.status))
                  || allDeliveries.find(d=>d.status==='assigned');
  const assigned  = allDeliveries.filter(d=>d.status==='assigned');
  const delivered = allDeliveries.filter(d=>['confirmed','delivered'].includes(d.status));
  const isBusy    = !!active;

  // Header status
  const badge = $('driver-badge');
  const txt   = $('driver-status-txt');
  badge.className = 'driver-badge ' + (isBusy?'busy':'available');
  txt.textContent = isBusy ? 'On Delivery' : 'Available';

  // Tab badges
  const ab = $('active-badge');
  const qb = $('assigned-badge');
  const db = $('delivered-badge');
  if(active){ ab.textContent=1; ab.style.display='inline-flex'; } else { ab.style.display='none'; }
  if(assigned.length){ qb.textContent=assigned.length; qb.style.display='inline-flex'; } else { qb.style.display='none'; }
  if(delivered.length){ db.textContent=delivered.length; db.style.display='inline-flex'; } else { db.style.display='none'; }
}

// ── RENDER: ACTIVE TAB ────────────────────────────────────────────────
function renderActiveTab(){
  // Don't overwrite the active card while a status PATCH is in progress
  if(isUpdating) return;
  const el = $('active-content');
  const active = allDeliveries.find(d=>d.status==='arrived')
    || allDeliveries.find(d=>['en_route','out_for_delivery'].includes(d.status))
    || allDeliveries.find(d=>d.status==='assigned');

  if(!active){
    el.innerHTML=`<div class="empty-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      <h3>No Active Delivery</h3>
      <p>You'll be notified when a new order is assigned. Stay available!</p>
    </div>`;
    return;
  }
  el.innerHTML = renderActiveCard(active);
}

function renderActiveCard(d){
  const chipMap={
    assigned:'chip-assigned',
    out_for_delivery:'chip-en_route',
    en_route:'chip-en_route',
    arrived:'chip-arrived',
  };
  const labelMap={assigned:'Assigned',out_for_delivery:'On the Way',en_route:'On the Way',arrived:'Arrived'};
  const chip  = chipMap[d.status]||'chip-assigned';
  const label = labelMap[d.status]||d.status;
  const mapsUrl=`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(`${d.street||''} ${d.city||''} ${d.region||''}`)}`;

  return `<div class="active-card" id="active-card-${d.order_id}">
    <div class="card-header">
      <span class="order-num">${esc(d.order_number||'')}</span>
      <span class="status-chip ${chip}">
        <span class="chip-pulse"></span>
        ${label}
      </span>
    </div>
    <div class="cust-block">
      <div class="info-row">
        <div class="info-icon icon-addr"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
        <div>
          <span class="info-lbl">Delivery Address</span>
          <span class="info-val">${esc(d.street||'—')}<br>${esc(d.city||'')}${d.region?', '+esc(d.region):''}</span>
        </div>
      </div>
      <div class="info-row">
        <div class="info-icon icon-phone"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.7A2 2 0 012 .99h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 8.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg></div>
        <div>
          <span class="info-lbl">Customer Phone</span>
          <span class="info-val"><a href="tel:${esc(d.customer_phone||d.phone||'')}">${esc(d.customer_phone||d.phone||'Not provided')}</a></span>
        </div>
      </div>
      ${d.special_notes?`<div class="info-row">
        <div class="info-icon icon-notes"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="16" y1="13" x2="8" y2="13"/></svg></div>
        <div><span class="info-lbl">Instructions</span><span class="info-val">${esc(d.special_notes)}</span></div>
      </div>`:''}
    </div>
    ${renderActionBlock(d, mapsUrl)}
    ${(d.status==='arrived')?renderOtpBlock(d.order_id):''}
  </div>`;
}

function renderActionBlock(d, mapsUrl){
  let btn='';
  if(d.status==='assigned'){
    btn=`<button class="act-btn btn-onway" onclick="updateStatus(${d.order_id},'en_route')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
      I'm On The Way
    </button>
    <p class="action-hint">Tap once you have picked up the order and are heading to the customer</p>`;
  } else if(d.status==='out_for_delivery'||d.status==='en_route'){
    btn=`<button class="act-btn btn-arrived" onclick="updateStatus(${d.order_id},'arrived')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
      I've Arrived
    </button>
    <p class="action-hint">Tap when you reach the customer's location</p>`;
  }
  // Always show map button alongside the action button (or alone when arrived)
  const mapBtn=`<button class="act-btn btn-map" onclick="window.open('${mapsUrl}','_blank')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>Open in Maps</button>`;
  return `<div class="action-block">${btn}${mapBtn}</div>`;
}

function renderOtpBlock(orderId){
  return `<div class="otp-block">
    <div class="otp-title">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
      Enter Customer OTP
    </div>
    <p class="otp-desc">Ask the customer for the OTP shown on their tracking screen, then type it below to confirm delivery.</p>
    <div class="otp-row">
      <input type="number" class="otp-input" id="otp-inp" placeholder="_ _ _ _"
             inputmode="numeric" pattern="[0-9]*" maxlength="6"
             oninput="onOtpInput(this)" onkeydown="if(event.key==='Enter')confirmOtp(${orderId})">
    </div>
    <button class="btn-confirm-otp" id="otp-btn" onclick="confirmOtp(${orderId})" disabled>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
      Confirm Delivery
    </button>
  </div>`;
}

// ── RENDER: ASSIGNED TAB ──────────────────────────────────────────────
function renderAssignedTab(){
  const el = $('assigned-content');
  const items = allDeliveries.filter(d=>d.status==='assigned');
  if(!items.length){
    el.innerHTML=`<div class="empty-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      <h3>No Assigned Orders</h3>
      <p>New orders will appear here automatically when assigned to you.</p>
    </div>`;
    return;
  }
  el.innerHTML = items.map(d=>`<div class="order-card">
    <div class="oc-inner">
      <span class="oc-num">${esc(d.order_number||'')}</span>
      <div class="oc-info">
        <div class="oc-customer">${esc(d.customer_name||'Customer')}</div>
        <div class="oc-address">${esc(d.street||'')} ${esc(d.city||'')}</div>
      </div>
      <span class="oc-badge assigned">Assigned</span>
    </div>
    <div class="oc-items">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07"/></svg>
      ${esc(d.customer_phone||d.phone||'No phone')} &nbsp;·&nbsp;
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
      ${esc(d.city||'')}${d.region?', '+esc(d.region):''}
    </div>
  </div>`).join('');
}

// ── RENDER: DELIVERED TAB ─────────────────────────────────────────────
function renderDeliveredTab(){
  const el = $('delivered-content');
  const items = allDeliveries.filter(d=>['confirmed','delivered'].includes(d.status));
  if(!items.length){
    el.innerHTML=`<div class="empty-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
      <h3>No Deliveries Yet Today</h3>
      <p>Your completed deliveries for today will appear here.</p>
    </div>`;
    return;
  }
  const total = items.length;
  const html  = `<div class="del-summary">
    <div class="ds-stat"><div class="ds-num">${total}</div><div class="ds-lbl">Delivered</div></div>
    <div class="ds-div"></div>
    <div class="ds-stat"><div class="ds-num">${(total * 100 / Math.max(total,1)).toFixed(0)}%</div><div class="ds-lbl">Success Rate</div></div>
    <div class="ds-div"></div>
    <div class="ds-stat"><div class="ds-num" id="del-hours">-</div><div class="ds-lbl">Today</div></div>
  </div>`
  + items.map(d=>`<div class="del-card">
    <div class="del-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div>
    <div>
      <div class="del-num">${esc(d.order_number||'')}</div>
      <div class="del-cust">${esc(d.customer_name||'Customer')}</div>
    </div>
    <div class="del-right">
      <div class="del-time">${fmtTime(d.updated_at||'')}</div>
      <span class="del-badge">Delivered</span>
    </div>
  </div>`).join('');
  el.innerHTML = html;
  // Set today's date
  const dh = $('del-hours');
  if(dh) dh.textContent = new Date().toLocaleDateString('en-GB',{day:'numeric',month:'short'});
}

// ── RENDER: PROFILE TAB ───────────────────────────────────────────────
function renderProfile(){
  const el = $('profile-content');
  const initials = user ? user.full_name.split(' ').map(n=>n[0]).join('').slice(0,2).toUpperCase() : '?';
  const delivered = allDeliveries.filter(d=>['confirmed','delivered'].includes(d.status)).length;
  el.innerHTML = `<div class="profile-card">
    <div class="profile-avatar">${initials}</div>
    <div class="profile-name">${esc(user?.full_name||'Driver')}</div>
    <div class="profile-role">Driver · Hakuna Matata POS</div>
    <div class="profile-info-grid">
      <div class="pi-item"><span class="pi-lbl">Email</span><span class="pi-val">${esc(user?.email||'—')}</span></div>
      <div class="pi-item"><span class="pi-lbl">Phone</span><span class="pi-val">${esc(user?.phone||'—')}</span></div>
      <div class="pi-item"><span class="pi-lbl">Today's Deliveries</span><span class="pi-val" style="font-family:'Nunito',sans-serif;font-size:20px;font-weight:900;color:var(--green)">${delivered}</span></div>
      <div class="pi-item"><span class="pi-lbl">Status</span><span class="pi-val" style="color:var(--green);font-weight:700">Active</span></div>
    </div>
    <button class="change-pass-btn" onclick="alert('Password change coming soon')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
      Change Password
    </button>
  </div>`;
}

// ── OTP ───────────────────────────────────────────────────────────────
function onOtpInput(inp){
  inp.value = inp.value.replace(/\D/g,'').slice(0,6);
  inp.classList.remove('shake');
  const btn=$('otp-btn');
  if(btn) btn.disabled = inp.value.length < 4;
}
async function updateStatus(orderId, newStatus){
  // ── Lock: disable buttons + block auto-refresh from re-rendering card ──
  isUpdating = true;
  document.querySelectorAll('.btn-onway,.btn-arrived').forEach(b=>{
    b.disabled=true; b.style.opacity='0.55';
  });

  const r = await authFetch('/deliveries/'+orderId+'/status',{method:'PATCH',body:JSON.stringify({delivery_status:newStatus})});
  if(!r){
    isUpdating = false;
    document.querySelectorAll('.btn-onway,.btn-arrived').forEach(b=>{
      b.disabled=false; b.style.opacity='';
    });
    showToast('Connection error — please try again.','error');
    return;
  }

  let result;
  try {
    result = await r.json();
  } catch(e) {
    // Server returned non-JSON (PHP error page, debug output, etc.)
    isUpdating = false;
    document.querySelectorAll('.btn-onway,.btn-arrived').forEach(b=>{
      b.disabled=false; b.style.opacity='';
    });
    showToast('Server error (HTTP '+r.status+') — please try again.','error');
    return;
  }

  if(result.success){
    // ── Optimistic update ─────────────────────────────────────────────
    // Use == (not ===) — PHP returns numeric IDs as strings in JSON
    const idx = allDeliveries.findIndex(x => x.order_id == orderId);
    if(idx !== -1){
      allDeliveries[idx] = { ...allDeliveries[idx], status: newStatus, delivery_status: newStatus };
    }
    // Clear isUpdating BEFORE rendering so renderActiveTab() is no longer blocked
    isUpdating = false;
    updateBadgesAndStatus();
    renderActiveTab();
    const msgs={out_for_delivery:'On the way! Customer has been notified 🚗',en_route:'On the way! Customer has been notified 🚗',arrived:'Arrived! Ask the customer for their OTP.'};
    showToast(msgs[newStatus]||'Status updated','success');
    if(navigator.vibrate) navigator.vibrate(100);
    // Discard any concurrent in-flight auto-refresh and restart the cycle
    fetchVersion++;
    resetCountdown();
  }else{
    isUpdating = false;
    document.querySelectorAll('.btn-onway,.btn-arrived').forEach(b=>{
      b.disabled=false; b.style.opacity='';
    });
    showToast(result.error||'Update failed','error');
  }
}
async function confirmOtp(orderId){
  const inp=$('otp-inp'); if(!inp) return;
  const otp=inp.value.trim();
  if(!otp||otp.length<4){ inp.classList.add('shake'); showToast('Enter the OTP first','warn'); return; }
  const btn=$('otp-btn');
  if(btn){ btn.disabled=true; btn.innerHTML='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;animation:spin 1s linear infinite"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg> Verifying...'; }
  const r=await authFetch('/deliveries/'+orderId+'/confirm',{method:'PATCH',body:JSON.stringify({otp})});
  if(!r){ if(btn){btn.disabled=false;btn.innerHTML='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Confirm Delivery';} return; }
  const d=await r.json();
  if(d.success){
    if(navigator.vibrate) navigator.vibrate([100,50,100,50,300]);
    showToast('Delivery confirmed! Well done.','success');
    const order=allDeliveries.find(dl=>dl.order_id===orderId)||{};
    $('active-content').innerHTML=`<div class="success-card">
      <div class="success-circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div>
      <div class="success-order">${esc(order.order_number||'')}</div>
      <div class="success-title">Delivery Confirmed!</div>
      <p class="success-sub">Order marked as delivered.<br>You are now available for the next delivery.</p>
    </div>`;
    setTimeout(()=>loadDeliveries(),2500);
  }else{
    if(inp){ inp.value=''; void inp.offsetWidth; inp.classList.add('shake'); inp.focus(); }
    showToast(d.error||'Wrong OTP — please try again','error');
    if(btn){ btn.disabled=true; btn.innerHTML='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Confirm Delivery'; }
  }
}

// ── AUTO REFRESH COUNTDOWN ────────────────────────────────────────────
function resetCountdown(){
  clearInterval(refreshTimer);
  countdownVal = REFRESH_INTERVAL;
  $('cd-num').textContent = countdownVal;
  refreshTimer = setInterval(()=>{
    countdownVal--;
    const el=$('cd-num'); if(el) el.textContent=countdownVal;
    if(countdownVal<=0){ loadDeliveries(true); }
  },1000);
}

// ── INIT ─────────────────────────────────────────────────────────────
loadDeliveries();
</script>
</body>
</html>
