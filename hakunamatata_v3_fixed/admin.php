<?php
// admin.php — Hakuna Matata POS — Manager / Super Admin Dashboard
// Auth is handled fully by JavaScript via localStorage (same pattern as customer.php)
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Manager Dashboard — Hakuna Matata POS</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&family=Nunito:wght@700;800;900&display=swap" rel="stylesheet">
<style>
/* ── Reset ── */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:'DM Sans',sans-serif;background:#F7F6F3;color:#2E2E2E;min-height:100vh}

/* ── Tokens ── */
:root{
  --gold:#F5A623;--gold-hover:#E09418;--gold-light:rgba(245,166,35,.1);
  --charcoal:#363636;--dark:#252525;--bg:#F7F6F3;--surface:#FFFFFF;
  --border:#E6E5E1;--muted:#9A9993;--text:#2E2E2E;
  --red:#E8394D;--green:#22C55E;--orange:#F97316;--blue:#3B82F6;
  --radius:14px;--shadow:0 2px 12px rgba(0,0,0,.07),0 1px 3px rgba(0,0,0,.04);
}

/* ══ HEADER ══ */
.site-header{position:sticky;top:0;z-index:100;width:100%;background:var(--dark);box-shadow:0 2px 16px rgba(0,0,0,.3);overflow:hidden}
.header-blob{position:absolute;border-radius:50%;background:var(--gold);opacity:.07;pointer-events:none}
.header-blob.b1{width:300px;height:300px;bottom:-150px;left:-80px}
.header-blob.b2{width:220px;height:220px;top:-100px;right:60px}
.header-blob.b3{width:140px;height:140px;top:-40px;left:45%;opacity:.04}
.header-inner{position:relative;z-index:1;display:flex;align-items:center;gap:16px;width:100%;padding:0 40px;height:100px}
.brand{display:flex;align-items:center;gap:14px;text-decoration:none;flex-shrink:0}
.brand-logo{width:68px;height:68px;background:white;border-radius:14px;overflow:hidden;display:flex;align-items:center;justify-content:center;box-shadow:0 6px 20px rgba(0,0,0,.3);flex-shrink:0;animation:popIn .55s cubic-bezier(.34,1.56,.64,1) both}
.brand-logo img{width:100%;height:100%;object-fit:cover}
.brand-name{font-family:'Nunito',sans-serif;font-weight:900;font-size:22px;color:#fff;line-height:1.15;display:block}
.brand-name em{font-style:normal;color:var(--gold)}
.brand-sub{font-size:11px;color:rgba(255,255,255,.4);display:block;margin-top:2px}
.header-right{display:flex;align-items:center;gap:12px;margin-left:auto;flex-shrink:0}
.admin-badge{padding:4px 10px;border-radius:20px;background:rgba(245,166,35,.15);color:var(--gold);font-size:11px;font-weight:700;letter-spacing:.5px;text-transform:uppercase}
.user-name{font-size:13px;font-weight:600;color:rgba(255,255,255,.75);white-space:nowrap}
.btn-logout{padding:6px 13px;border-radius:16px;border:1.5px solid rgba(255,255,255,.2);background:transparent;color:rgba(255,255,255,.5);font-size:12px;font-weight:600;cursor:pointer;transition:all .2s;font-family:'DM Sans',sans-serif}
.btn-logout:hover{border-color:var(--red);color:var(--red)}

/* ══ TAB BAR ══ */
.tab-bar{background:#fff;border-bottom:1px solid var(--border);position:sticky;top:100px;z-index:90;box-shadow:0 2px 8px rgba(0,0,0,.05);overflow-x:auto}
.tab-bar::-webkit-scrollbar{display:none}
.tab-bar-inner{display:flex;align-items:center;gap:4px;padding:0 40px;height:52px;min-width:max-content}
.tab-btn{display:flex;align-items:center;gap:7px;padding:8px 16px;border-radius:22px;border:none;background:transparent;color:var(--muted);font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;cursor:pointer;transition:all .2s;white-space:nowrap}
.tab-btn:hover{color:var(--text);background:var(--bg)}
.tab-btn.active{background:var(--dark);color:#fff}
.tab-btn svg{width:15px;height:15px;flex-shrink:0}
.tab-badge{background:var(--gold);color:var(--dark);border-radius:10px;min-width:18px;height:18px;font-size:10px;font-weight:800;padding:0 5px;display:inline-flex;align-items:center;justify-content:center}

/* ══ MAIN ══ */
.main-content{padding:32px 40px;max-width:1400px;margin:0 auto}
.tab-panel{display:none}
.tab-panel.active{display:block;animation:fadeUp .3s ease both}

/* ══ PAGE HEADER ROW ══ */
.page-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.page-hdr h2{font-family:'Nunito',sans-serif;font-size:22px;font-weight:900;color:var(--text)}
.page-hdr p{font-size:13px;color:var(--muted);margin-top:2px}

/* ══ KPI GRID ══ */
.kpi-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:16px;margin-bottom:28px}
.kpi-card{background:var(--surface);border-radius:var(--radius);padding:20px 22px;box-shadow:var(--shadow);border:1px solid var(--border);position:relative;overflow:hidden;animation:fadeUp .4s ease both}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px}
.kpi-card.gold::before{background:var(--gold)}
.kpi-card.green::before{background:var(--green)}
.kpi-card.orange::before{background:var(--orange)}
.kpi-card.blue::before{background:var(--blue)}
.kpi-card.red::before{background:var(--red)}
.kpi-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:12px}
.kpi-icon svg{width:20px;height:20px}
.kpi-card.gold .kpi-icon{background:rgba(245,166,35,.12);color:var(--gold)}
.kpi-card.green .kpi-icon{background:rgba(34,197,94,.12);color:var(--green)}
.kpi-card.orange .kpi-icon{background:rgba(249,115,22,.12);color:var(--orange)}
.kpi-card.blue .kpi-icon{background:rgba(59,130,246,.12);color:var(--blue)}
.kpi-card.red .kpi-icon{background:rgba(232,57,77,.12);color:var(--red)}
.kpi-value{font-family:'Nunito',sans-serif;font-size:26px;font-weight:900;color:var(--text);line-height:1;margin-bottom:4px}
.kpi-label{font-size:12px;color:var(--muted);font-weight:500}
.kpi-sub{font-size:11px;color:var(--muted);margin-top:6px}

/* ══ DASHBOARD GRID ══ */
.dash-grid{display:grid;grid-template-columns:1fr 340px;gap:20px}
@media(max-width:1024px){.dash-grid{grid-template-columns:1fr}}

/* ══ SECTION CARD ══ */
.section-card{background:var(--surface);border-radius:var(--radius);box-shadow:var(--shadow);border:1px solid var(--border);margin-bottom:20px}
.sc-header{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--border)}
.sc-title{font-family:'Nunito',sans-serif;font-size:15px;font-weight:800;color:var(--text);display:flex;align-items:center;gap:8px}
.sc-title svg{width:16px;height:16px;color:var(--gold)}

/* ══ TABLE ══ */
.data-table{width:100%;border-collapse:collapse;font-size:13px}
.data-table th{padding:10px 16px;background:#F7F6F3;color:var(--muted);font-weight:600;font-size:11px;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid var(--border);text-align:left;white-space:nowrap}
.data-table td{padding:11px 16px;border-bottom:1px solid #F0EFEB;color:var(--text);vertical-align:middle}
.data-table tr:last-child td{border-bottom:none}
.data-table tbody tr:hover{background:#FAFAF8}
.td-order{font-family:'Nunito',sans-serif;font-weight:800;color:var(--dark);font-size:13px}
.td-mono{font-family:monospace;font-size:12px}

/* ══ BADGES ══ */
.badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:700;text-transform:capitalize;white-space:nowrap}
.b-pending{background:#FFF7ED;color:#C2410C}
.b-confirmed{background:#EFF6FF;color:#1D4ED8}
.b-preparing{background:#FFFBEB;color:#B45309}
.b-ready{background:#F0FDF4;color:#15803D}
.b-dispatched{background:#EDE9FE;color:#6D28D9}
.b-delivered,.b-completed{background:#F0FDF4;color:#166534}
.b-cancelled{background:#FFF1F2;color:#BE123C}
.b-delivery{background:#EFF6FF;color:#1D4ED8}
.b-collection{background:#FFF7ED;color:#B45309}
.b-active{background:#F0FDF4;color:#15803D}
.b-inactive{background:#F1F5F9;color:#64748B}
.b-suspended{background:#FFF1F2;color:#BE123C}
.b-available{background:#F0FDF4;color:#15803D}
.b-low{background:#FFF7ED;color:#C2410C}
.b-out{background:#FFF1F2;color:#BE123C}
.b-super_admin{background:rgba(245,166,35,.15);color:#B45309}
.b-manager{background:#EDE9FE;color:#6D28D9}
.b-cashier{background:#EFF6FF;color:#1D4ED8}
.b-driver{background:#F0FDF4;color:#15803D}
.b-customer{background:#F1F5F9;color:#64748B}

/* ══ FILTER BAR ══ */
.filter-bar{display:flex;align-items:center;gap:10px;flex-wrap:wrap;padding:14px 20px;background:var(--bg);border-bottom:1px solid var(--border)}
.fi{height:36px;border-radius:8px;border:1.5px solid var(--border);background:white;padding:0 12px;font-size:13px;font-family:'DM Sans',sans-serif;color:var(--text);outline:none;transition:border-color .2s}
.fi:focus{border-color:var(--gold)}
.fi-search{min-width:180px}
.fi-select{min-width:130px;cursor:pointer}
.filter-bar label{font-size:12px;font-weight:600;color:var(--muted);white-space:nowrap}

/* ══ BUTTONS ══ */
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:22px;border:none;font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;cursor:pointer;transition:all .2s;white-space:nowrap}
.btn-gold{background:var(--gold);color:var(--dark)}
.btn-gold:hover{background:var(--gold-hover)}
.btn-dark{background:var(--dark);color:white}
.btn-dark:hover{background:var(--charcoal)}
.btn-outline{background:transparent;border:1.5px solid var(--border);color:var(--text)}
.btn-outline:hover{border-color:var(--charcoal);color:var(--dark)}
.btn-red{background:transparent;border:1.5px solid #FFCACA;color:var(--red)}
.btn-red:hover{background:var(--red);color:white;border-color:var(--red)}
.btn-green{background:transparent;border:1.5px solid #BBF7D0;color:#15803D}
.btn-green:hover{background:var(--green);color:white;border-color:var(--green)}
.btn-sm{padding:5px 10px;font-size:12px;border-radius:16px}
.btn svg{width:14px;height:14px}
.action-btns{display:flex;gap:6px;align-items:center}

/* ══ PRODUCT CARD GRID ══ */
.product-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;padding:20px}
.prod-card{border:1.5px solid var(--border);border-radius:var(--radius);overflow:hidden;background:white;transition:box-shadow .2s}
.prod-card:hover{box-shadow:0 4px 20px rgba(0,0,0,.1)}
.prod-img{height:120px;background:#F7F6F3;display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:32px;overflow:hidden}
.prod-img img{width:100%;height:100%;object-fit:cover}
.prod-info{padding:12px}
.prod-name{font-weight:700;font-size:13px;margin-bottom:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.prod-price{font-family:'Nunito',sans-serif;font-weight:900;font-size:16px;color:var(--gold)}
.prod-meta{display:flex;align-items:center;justify-content:space-between;margin-top:8px}
.prod-stock{font-size:11px;color:var(--muted)}

/* ══ STATS ROW ══ */
.stats-row{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;padding:20px}
.stat-item{text-align:center;padding:16px;background:var(--bg);border-radius:10px;border:1px solid var(--border)}
.stat-value{font-family:'Nunito',sans-serif;font-size:24px;font-weight:900;color:var(--text)}
.stat-label{font-size:12px;color:var(--muted);margin-top:4px}

/* ══ MODAL ══ */
.modal-overlay{display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.45);align-items:center;justify-content:center;padding:24px}
.modal-overlay.open{display:flex}
.modal{background:white;border-radius:18px;width:100%;max-width:560px;box-shadow:0 24px 64px rgba(0,0,0,.2);max-height:90vh;overflow-y:auto;animation:slideUp .25s ease both}
.modal-wide{max-width:760px}
.modal-hdr{display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid var(--border);position:sticky;top:0;background:white;z-index:1}
.modal-hdr h3{font-family:'Nunito',sans-serif;font-size:18px;font-weight:900}
.modal-close{width:32px;height:32px;border-radius:50%;border:none;background:var(--bg);color:var(--muted);font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1}
.modal-close:hover{background:var(--border);color:var(--text)}
.modal-body{padding:24px}
.modal-footer{padding:16px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;position:sticky;bottom:0;background:white}

/* ══ FORM ══ */
.fg{margin-bottom:16px}
.fl{display:block;font-size:12px;font-weight:600;color:var(--text);margin-bottom:5px}
.fl .req{color:var(--red);margin-left:2px}
.fi-full{width:100%;height:40px;border-radius:10px;border:1.5px solid var(--border);background:var(--bg);padding:0 14px;font-size:14px;font-family:'DM Sans',sans-serif;color:var(--text);outline:none;transition:border-color .2s}
.fi-full:focus{border-color:var(--gold);background:white}
.fi-ta{width:100%;border-radius:10px;border:1.5px solid var(--border);background:var(--bg);padding:10px 14px;font-size:14px;font-family:'DM Sans',sans-serif;color:var(--text);outline:none;resize:vertical;min-height:80px;transition:border-color .2s}
.fi-ta:focus{border-color:var(--gold);background:white}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.form-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px}

/* ══ TOGGLE ══ */
.tgl{position:relative;display:inline-flex;align-items:center;cursor:pointer}
.tgl input{opacity:0;width:0;height:0}
.tgl-slider{width:38px;height:21px;background:#D1D5DB;border-radius:11px;transition:background .2s;flex-shrink:0}
.tgl-slider::after{content:'';position:absolute;top:3px;left:3px;width:15px;height:15px;border-radius:50%;background:white;transition:transform .2s;box-shadow:0 1px 3px rgba(0,0,0,.2)}
.tgl input:checked+.tgl-slider{background:var(--green)}
.tgl input:checked+.tgl-slider::after{transform:translateX(17px)}

/* ══ SUB TABS ══ */
.sub-tabs{display:flex;gap:6px;padding:14px 20px;border-bottom:1px solid var(--border);flex-wrap:wrap;background:white}
.stab{padding:5px 14px;border-radius:16px;border:1.5px solid var(--border);background:transparent;color:var(--muted);font-size:12px;font-weight:600;cursor:pointer;transition:all .2s;font-family:'DM Sans',sans-serif}
.stab.active{background:var(--dark);color:white;border-color:var(--dark)}
.stab:hover:not(.active){border-color:var(--charcoal);color:var(--text)}

/* ══ SMART SEARCH / LOV ══ */
.search-wrap{position:relative;display:inline-block;min-width:220px}
.search-wrap .fi{width:100%;padding-right:32px}
.search-clear{position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);font-size:16px;cursor:pointer;line-height:1;padding:2px;display:none}
.search-wrap.has-value .search-clear{display:block}
.lov-dropdown{position:absolute;top:calc(100% + 4px);left:0;right:0;background:white;border:1.5px solid var(--gold);border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:500;max-height:220px;overflow-y:auto;display:none}
.lov-dropdown.open{display:block;animation:fadeUp .15s ease both}
.lov-item{display:flex;align-items:center;gap:10px;padding:9px 14px;cursor:pointer;font-size:13px;border-bottom:1px solid #F0EFEB;transition:background .15s}
.lov-item:last-child{border-bottom:none}
.lov-item:hover,.lov-item.focused{background:var(--gold-light)}
.lov-item-avatar{width:28px;height:28px;border-radius:50%;background:var(--gold-light);display:flex;align-items:center;justify-content:center;font-weight:800;color:var(--gold);font-size:11px;flex-shrink:0;overflow:hidden}
.lov-item-avatar img{width:100%;height:100%;object-fit:cover}
.lov-item-main{flex:1;min-width:0}
.lov-item-name{font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.lov-item-sub{font-size:11px;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.lov-item-badge{flex-shrink:0}
.lov-empty{padding:16px;text-align:center;font-size:13px;color:var(--muted)}
.lov-loading{padding:16px;text-align:center;font-size:13px;color:var(--muted)}
/* ══ STAFF TAB TOOLTIP ══ */
.tab-btn{position:relative}
.staff-tip{
  position:absolute;top:calc(100% + 8px);left:50%;transform:translateX(-50%);
  background:var(--dark);color:#fff;border-radius:10px;padding:8px 12px;
  display:flex;flex-direction:column;gap:5px;min-width:120px;
  opacity:0;pointer-events:none;transition:opacity .18s,transform .18s;
  transform:translateX(-50%) translateY(-4px);
  box-shadow:0 6px 20px rgba(0,0,0,.25);z-index:200;white-space:nowrap
}
.staff-tip::before{
  content:'';position:absolute;top:-5px;left:50%;transform:translateX(-50%);
  border:5px solid transparent;border-bottom-color:var(--dark);border-top:none
}
.tab-btn:hover .staff-tip{opacity:1;transform:translateX(-50%) translateY(0);pointer-events:none}
.staff-tip-row{display:flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:rgba(255,255,255,.85)}

.sa-item{display:flex;align-items:center;gap:10px;padding:10px 20px;border-bottom:1px solid var(--border)}
.sa-item:last-child{border-bottom:none}
.sa-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}
.sa-dot.low{background:var(--orange)}
.sa-dot.out{background:var(--red)}
.sa-name{font-size:13px;font-weight:600;flex:1}
.sa-qty{font-size:12px;font-weight:700}
.sa-qty.low{color:var(--orange)}
.sa-qty.out{color:var(--red)}

/* ══ PAGINATION ══ */
.pager{display:flex;align-items:center;justify-content:center;gap:6px;padding:16px}
.pg{width:32px;height:32px;border-radius:8px;border:1.5px solid var(--border);background:white;color:var(--text);font-size:13px;font-weight:600;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center}
.pg:hover{border-color:var(--dark)}
.pg.active{background:var(--dark);color:white;border-color:var(--dark)}
.pg:disabled{opacity:.4;cursor:not-allowed}

/* ══ EMPTY STATE ══ */
.empty{text-align:center;padding:48px 24px;color:var(--muted)}
.empty svg{width:48px;height:48px;opacity:.25;margin-bottom:12px;display:block;margin-left:auto;margin-right:auto}
.empty p{font-size:14px}

/* ══ TOAST ══ */
.toast{position:fixed;bottom:24px;right:24px;z-index:9999;padding:12px 20px;border-radius:12px;font-size:14px;font-weight:600;background:var(--dark);color:white;transform:translateY(20px);opacity:0;transition:all .3s;pointer-events:none;max-width:320px}
.toast.show{transform:translateY(0);opacity:1}
.toast.success{background:#166534}
.toast.error{background:var(--red)}
.toast.warn{background:#B45309}

/* ══ ANIMATIONS ══ */
@keyframes popIn{0%{transform:scale(.6);opacity:0}100%{transform:scale(1);opacity:1}}
@keyframes slideUp{0%{transform:translateY(20px);opacity:0}100%{transform:translateY(0);opacity:1}}
@keyframes fadeUp{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}

.superadmin-only{display:none}
.role-superadmin .superadmin-only{display:block}

/* ══ IMAGE UPLOAD ══ */
.img-upload-box{border:2px dashed var(--border);border-radius:12px;padding:18px;text-align:center;cursor:pointer;transition:border-color .2s;position:relative;background:var(--bg)}
.img-upload-box:hover{border-color:var(--gold)}
.img-upload-box input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%}
.img-preview{width:100%;max-height:160px;object-fit:cover;border-radius:8px;display:none;margin-bottom:8px}
.img-preview.show{display:block}
.img-upload-icon{color:var(--muted);margin-bottom:6px}
.img-upload-icon svg{width:32px;height:32px}
.img-upload-hint{font-size:12px;color:var(--muted)}
.img-upload-hint strong{color:var(--gold)}
.img-clear{position:absolute;top:8px;right:8px;width:24px;height:24px;border-radius:50%;background:var(--red);border:none;color:white;font-size:14px;line-height:1;cursor:pointer;display:none;align-items:center;justify-content:center;z-index:2}
.img-clear.show{display:flex}

/* ══ ORDER DETAIL ══ */
.od-section{margin-bottom:20px}
.od-section h4{font-family:'Nunito',sans-serif;font-size:14px;font-weight:800;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px}
.od-row{display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid #F0EFEB;font-size:13px}
.od-row:last-child{border-bottom:none}
.od-label{color:var(--muted);font-weight:500}
.od-value{font-weight:600}
.od-total{font-family:'Nunito',sans-serif;font-size:18px;font-weight:900;color:var(--text)}

/* ══ RESPONSIVE ══ */
@media(max-width:1100px){.kpi-grid{grid-template-columns:repeat(3,1fr)}}
/* table scroll wrapper — section-card has overflow:hidden so tables need their own wrapper */
.table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch}
@media(max-width:768px){
  .header-inner{padding:0 20px;height:80px}
  .brand-logo{width:52px;height:52px}
  .brand-name{font-size:18px}
  .main-content{padding:20px}
  .kpi-grid{grid-template-columns:repeat(2,1fr)}
  .tab-bar-inner{padding:0 16px}
  .form-row,.form-row-3{grid-template-columns:1fr}
  .fi-search{min-width:120px}
  .fi-select{min-width:100px}
  .product-grid{grid-template-columns:repeat(auto-fill,minmax(160px,1fr))}
  .sc-header{flex-wrap:wrap;gap:8px}
}
@media(max-width:480px){
  .kpi-grid{grid-template-columns:1fr}
  .main-content{padding:14px}
  .section-header{flex-wrap:wrap;gap:8px}
  .filter-row{flex-wrap:wrap}
  .fi-search,.fi-select{min-width:0;width:100%}
  .product-grid{grid-template-columns:repeat(2,1fr)}
  .tab-btn .tab-label{display:none}
  .tab-btn{padding:8px 10px}
}
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
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
        <span class="brand-sub">Online Point of Sale</span>
      </div>
    </a>
    <div class="header-right">
      <span class="admin-badge" id="role-badge">Admin</span>
      <span class="user-name" id="user-name"></span>
      <button class="btn-logout" onclick="logout()">Logout</button>
    </div>
  </div>
</header>

<!-- ══ TAB BAR ══ -->
<div class="tab-bar">
  <div class="tab-bar-inner">
    <button class="tab-btn active" id="tb-dashboard" onclick="switchTab('dashboard',this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>
      Dashboard
    </button>
    <button class="tab-btn" id="tb-orders" onclick="switchTab('orders',this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
      Orders
      <span class="tab-badge" id="pending-badge" style="display:none">0</span>
    </button>
    <button class="tab-btn" id="tb-products" onclick="switchTab('products',this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
      Products
    </button>
    <button class="tab-btn" id="tb-customers" onclick="switchTab('customers',this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      Customers
    </button>
    <button class="tab-btn" id="tb-staff" onclick="switchTab('staff',this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
      Staff
      <span class="staff-tip">
        <span class="staff-tip-row">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/><circle cx="12" cy="10" r="2"/></svg>
          Cashiers
        </span>
        <span class="staff-tip-row">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
          Drivers
        </span>
      </span>
    </button>

    <button class="tab-btn" id="tb-payment" onclick="switchTab('payment',this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
      Payment
    </button>
    <button class="tab-btn" id="tb-reports" onclick="switchTab('reports',this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
      Reports
    </button>

  </div>
</div>

<!-- ══ MAIN CONTENT ══ -->
<div class="main-content">

  <!-- ─── DASHBOARD ─── -->
  <div id="panel-dashboard" class="tab-panel active">
    <div class="page-hdr">
      <div>
        <h2>Dashboard</h2>
        <p id="dash-date">Today's overview</p>
      </div>
      <button class="btn btn-outline btn-sm" onclick="loadDashboard()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>
        Refresh
      </button>
    </div>
    <!-- KPI row -->
    <div class="kpi-grid" id="kpi-grid">
      <div class="kpi-card gold"><div class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div><div class="kpi-value" id="kpi-revenue">E 0.00</div><div class="kpi-label">Today's Revenue</div></div>
      <div class="kpi-card blue"><div class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div><div class="kpi-value" id="kpi-orders">0</div><div class="kpi-label">Orders Today</div></div>
      <div class="kpi-card orange"><div class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div><div class="kpi-value" id="kpi-pending">0</div><div class="kpi-label">Pending Orders</div></div>
      <div class="kpi-card green"><div class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div><div class="kpi-value" id="kpi-ready">0</div><div class="kpi-label">Ready Orders</div></div>
      <div class="kpi-card red"><div class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M6 20v-2a4 4 0 018 0v2"/><path d="M22 20v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.74"/></svg></div><div class="kpi-value" id="kpi-alerts">0</div><div class="kpi-label">Stock Alerts</div></div>
    </div>
    <!-- Dash grid: recent orders + stock alerts -->
    <div class="dash-grid">
      <div class="section-card">
        <div class="sc-header">
          <span class="sc-title"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Recent Orders</span>
          <button class="btn btn-outline btn-sm" onclick="switchTab('orders',document.getElementById('tb-orders'))">View all</button>
        </div>
        <div id="recent-orders-body">
          <div class="table-wrap">
          <table class="data-table"><thead><tr><th>Order #</th><th>Customer</th><th>Type</th><th>Total</th><th>Status</th><th>Time</th></tr></thead>
          <tbody id="recent-orders-rows"><tr><td colspan="6" class="empty"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg><p>Loading orders...</p></td></tr></tbody></table>
          </div><!-- /.table-wrap -->
        </div>
      </div>
      <div class="section-card">
        <div class="sc-header">
          <span class="sc-title"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg> Stock Alerts</span>
        </div>
        <div id="stock-alerts-body"><p style="padding:20px;color:var(--muted);font-size:13px">Loading...</p></div>
      </div>
    </div>
  </div>

  <!-- ─── ORDERS ─── -->
  <div id="panel-orders" class="tab-panel">
    <div class="section-card">
      <div class="sc-header">
        <span class="sc-title"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Orders</span>
        <button class="btn btn-outline btn-sm" onclick="loadOrders()">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>
          Refresh
        </button>
      </div>
      <div class="filter-bar">
        <select class="fi fi-select" id="ord-status" onchange="loadOrders()">
          <option value="">All Statuses</option>
          <option value="pending">Pending</option>
          <option value="delivered">Delivered</option>
        </select>
        <select class="fi fi-select" id="ord-type" onchange="loadOrders()">
          <option value="">All Types</option>
          <option value="delivery">Delivery</option>
          <option value="collection">Collection</option>
        </select>
        <input type="date" class="fi" id="ord-from" onchange="loadOrders()">
        <label style="font-size:12px;color:var(--muted)">to</label>
        <input type="date" class="fi" id="ord-to" onchange="loadOrders()">
      </div>
      <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th>Order #</th><th>Customer</th><th>Channel</th><th>Type</th><th>Total</th><th>Payment</th><th>Status</th><th>Time</th><th>Actions</th></tr></thead>
        <tbody id="orders-tbody"><tr><td colspan="9"><div class="empty"><p>Loading...</p></div></td></tr></tbody>
      </table>
      </div><!-- /.table-wrap -->
      <div class="pager" id="orders-pager"></div>
    </div>
  </div>

  <!-- ─── PRODUCTS ─── -->
  <div id="panel-products" class="tab-panel">
    <div class="section-card">
      <div class="sc-header">
        <span class="sc-title"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg> Products</span>
        <button class="btn btn-gold btn-sm" onclick="openProductModal()">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Add Product
        </button>
      </div>
      <div class="filter-bar">
        <select class="fi fi-select" id="prod-cat" onchange="loadProducts()"><option value="">All Categories</option></select>
        <select class="fi fi-select" id="prod-stock" onchange="loadProducts()">
          <option value="">All Stock</option>
          <option value="1">Low Stock</option>
        </select>
        <div class="search-wrap" id="sw-products">
          <input type="search" class="fi fi-search" id="prod-search" placeholder="Search products..." autocomplete="off" oninput="smartSearch('products',this)">
          <button class="search-clear" onclick="clearSmartSearch('products')" tabindex="-1">&#x2715;</button>
          <div class="lov-dropdown" id="lov-products"></div>
        </div>
      </div>
      <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th>Name</th><th>Category</th><th>Price (E)</th><th>Stock</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody id="products-tbody"><tr><td colspan="6"><div class="empty"><p>Loading...</p></div></td></tr></tbody>
      </table>
      </div><!-- /.table-wrap -->
      <div class="pager" id="products-pager"></div>
    </div>
  </div>

  <!-- ─── USERS ─── -->
  <div id="panel-users" class="tab-panel">
    <div class="section-card">
      <div class="sc-header">
        <span class="sc-title"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg> Users</span>
        <button class="btn btn-gold btn-sm" onclick="openUserModal()">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Add User
        </button>
      </div>
      <div class="sub-tabs">
        <button class="stab active" onclick="filterUsers('staff',this)">All Staff</button>
        <button class="stab" onclick="filterUsers('cashier',this)">Cashiers</button>
        <button class="stab" onclick="filterUsers('driver',this)">Drivers</button>
        <button class="stab" onclick="filterUsers('manager',this)">Managers</button>
        <button class="stab superadmin-only" onclick="filterUsers('super_admin',this)">Super Admins</button>
      </div>
      <div class="filter-bar">
        <input type="search" class="fi fi-search" id="user-search" placeholder="Search name / email..." oninput="debounce(loadUsers,500)()">
      </div>
      <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th>Last Login</th><th>Actions</th></tr></thead>
        <tbody id="users-tbody"><tr><td colspan="7"><div class="empty"><p>Loading...</p></div></td></tr></tbody>
      </table>
      </div><!-- /.table-wrap -->
      <div class="pager" id="users-pager"></div>
    </div>
  </div>

  <!-- ─── CUSTOMERS ─── -->
  <div id="panel-customers" class="tab-panel">
    <div class="section-card">
      <div class="sc-header">
        <span class="sc-title"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Customers</span>

      </div>
      <div class="filter-bar">
        <select class="fi fi-select" id="cust-status" onchange="_allCustomers=[];loadCustomers()">
          <option value="">All</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
        <div class="search-wrap" id="sw-customers">
          <input type="search" class="fi fi-search" id="cust-search" placeholder="Search name / email / phone..." autocomplete="off" oninput="smartSearch('customers',this)">
          <button class="search-clear" onclick="clearSmartSearch('customers')" tabindex="-1">&#x2715;</button>
          <div class="lov-dropdown" id="lov-customers"></div>
        </div>
      </div>
      <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th>Name</th><th>Phone</th><th>Orders</th><th>Total Spent</th><th>Status</th><th>Joined</th></tr></thead>
        <tbody id="customers-tbody"><tr><td colspan="8"><div class="empty"><p>Loading...</p></div></td></tr></tbody>
      </table>
      </div><!-- /.table-wrap -->
      <div class="pager" id="customers-pager"></div>
    </div>
  </div>

  <!-- ─── STAFF (Cashiers + Drivers) ─── -->
  <div id="panel-staff" class="tab-panel">
    <div class="section-card">
      <div class="sc-header">
        <span class="sc-title">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
          Staff
        </span>
        <button class="btn btn-gold btn-sm" id="add-staff-btn" style="display:none" onclick="openStaffModal(staffRole==='driver'?'driver':'cashier')">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          <span id="add-staff-label">Add Cashier</span>
        </button>
      </div>
      <!-- sub-tabs: All / Cashiers / Drivers -->
      <div class="sub-tabs">
        <button class="stab active" onclick="filterStaff('',this)">All Staff</button>
        <button class="stab" onclick="filterStaff('cashier',this)">Cashiers</button>
        <button class="stab" onclick="filterStaff('driver',this)">Drivers</button>
      </div>
      <div class="filter-bar">
        <select class="fi fi-select" id="staff-status" onchange="loadStaff()">
          <option value="">All Statuses</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
          <option value="suspended">Suspended</option>
        </select>
        <div class="search-wrap" id="sw-staff">
          <input type="search" class="fi fi-search" id="staff-search" placeholder="Search name / email..." autocomplete="off" oninput="smartSearch('staff',this)">
          <button class="search-clear" onclick="clearSmartSearch('staff')" tabindex="-1">&#x2715;</button>
          <div class="lov-dropdown" id="lov-staff"></div>
        </div>
      </div>
      <div class="table-wrap">
      <table class="data-table">
        <thead>
          <tr>
            <th>Photo</th><th>Name</th><th>Phone</th>
            <th>Role</th><th>Activity</th><th>Performance</th>
            <th>Status</th><th>Last Login</th><th>Actions</th>
          </tr>
        </thead>
        <tbody id="staff-tbody"><tr><td colspan="9"><div class="empty"><p>Loading...</p></div></td></tr></tbody>
      </table>
      </div><!-- /.table-wrap -->
      <div class="pager" id="staff-pager"></div>
    </div>
  </div>

  <!-- ─── PAYMENT ─── -->
  <div id="panel-payment" class="tab-panel">
    <div class="kpi-grid" id="pay-kpi-grid" style="margin-bottom:24px">
      <div class="kpi-card gold"><div class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></div><div class="kpi-value" id="pay-kpi-total">E 0.00</div><div class="kpi-label">Total Collected</div></div>
      <div class="kpi-card green"><div class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div><div class="kpi-value" id="pay-kpi-paid">0</div><div class="kpi-label">Paid Transactions</div></div>
      <div class="kpi-card orange"><div class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/></svg></div><div class="kpi-value" id="pay-kpi-momo">E 0.00</div><div class="kpi-label">Mobile Money</div></div>
      <div class="kpi-card blue"><div class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div><div class="kpi-value" id="pay-kpi-cash">E 0.00</div><div class="kpi-label">Cash (POS)</div></div>
      <div class="kpi-card red"><div class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><path d="M7 15h3l2-6 2 6h3"/></svg></div><div class="kpi-value" id="pay-kpi-card">E 0.00</div><div class="kpi-label">Bank / Card</div></div>
    </div>
    <div class="section-card">
      <div class="sc-header">
        <span class="sc-title"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg> Payment Transactions</span>
        <button class="btn btn-outline btn-sm" onclick="loadPayments()">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>
          Refresh
        </button>
      </div>
      <div class="filter-bar">
        <select class="fi fi-select" id="pay-status" onchange="loadPayments()">
          <option value="">All Statuses</option>
          <option value="paid">Paid</option>
        </select>
        <select class="fi fi-select" id="pay-method" onchange="loadPayments()">
          <option value="">All Methods</option>
          <option value="cash">Cash</option>
          <option value="mobile_money">Mobile Money</option>
          <option value="card">Bank / Card</option>
        </select>
        <input type="date" class="fi" id="pay-from" onchange="loadPayments()">
        <label style="font-size:12px;color:var(--muted)">to</label>
        <input type="date" class="fi" id="pay-to" onchange="loadPayments()">
        <div class="search-wrap" id="sw-payments">
          <input type="search" class="fi fi-search" id="pay-search" placeholder="Search order / ref..." autocomplete="off" oninput="smartSearch('payments',this)">
          <button class="search-clear" onclick="clearSmartSearch('payments')" tabindex="-1">&#x2715;</button>
          <div class="lov-dropdown" id="lov-payments"></div>
        </div>
      </div>
      <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th>Order #</th><th>Customer</th><th>Amount</th><th>Method</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody id="payments-tbody"><tr><td colspan="7"><div class="empty"><p>Loading...</p></div></td></tr></tbody>
      </table>
      </div><!-- /.table-wrap -->
      <div class="pager" id="payments-pager"></div>
    </div>
  </div>

  <!-- ─── REPORTS ─── -->
  <div id="panel-reports" class="tab-panel">

    <!-- ── KPI SUMMARY CARDS ── -->
    <div id="rep-kpi-row" class="kpi-grid" style="display:none;margin-bottom:20px">
      <div class="kpi-card gold">
        <div class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div>
        <div class="kpi-value" id="rep-kpi-revenue">E 0.00</div>
        <div class="kpi-label">Net Revenue</div>
        <div class="kpi-sub" id="rep-kpi-aov"></div>
      </div>
      <div class="kpi-card green">
        <div class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
        <div class="kpi-value" id="rep-kpi-orders">0</div>
        <div class="kpi-label">Total Orders</div>
        <div class="kpi-sub" id="rep-kpi-completed"></div>
      </div>
      <div class="kpi-card orange">
        <div class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg></div>
        <div class="kpi-value" id="rep-kpi-discounts">E 0.00</div>
        <div class="kpi-label">Discounts Given</div>
        <div class="kpi-sub" id="rep-kpi-delivery-rev"></div>
      </div>
      <div class="kpi-card blue">
        <div class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
        <div class="kpi-value" id="rep-kpi-customers">0</div>
        <div class="kpi-label">New Customers</div>
        <div class="kpi-sub" id="rep-kpi-top-product"></div>
      </div>
      <div class="kpi-card red">
        <div class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
        <div class="kpi-value" id="rep-kpi-cancelled">0</div>
        <div class="kpi-label">Cancelled Orders</div>
        <div class="kpi-sub" id="rep-kpi-order-types"></div>
      </div>
    </div>

    <!-- ── MAIN REPORT CARD ── -->
    <div class="section-card">
      <div class="sc-header">
        <span class="sc-title">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
          Reports
        </span>
        <div style="display:flex;gap:8px;align-items:center">
          <button class="btn btn-outline btn-sm" onclick="exportReportCSV()" id="rep-export-btn" style="display:none">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Export CSV
          </button>
          <button class="btn btn-outline btn-sm" onclick="printReport()" id="rep-print-btn" style="display:none">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Print / PDF
          </button>
          <button class="btn btn-dark btn-sm" onclick="loadReports()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            Run Report
          </button>
        </div>
      </div>

      <!-- Tab row + filters -->
      <div class="sub-tabs" style="flex-direction:column;gap:12px;align-items:stretch;padding:14px 20px">
        <!-- Report type tabs -->
        <div style="display:flex;gap:6px;flex-wrap:wrap">
          <button class="stab active" id="rtype-sales"     onclick="switchReport('sales',this)">📅 Sales</button>
          <button class="stab"        id="rtype-products"  onclick="switchReport('products',this)">🛒 Products</button>
          <button class="stab"        id="rtype-customers" onclick="switchReport('customers',this)">👥 Customers</button>
          <button class="stab"        id="rtype-drivers"   onclick="switchReport('drivers',this)">🚗 Drivers</button>
          <button class="stab"        id="rtype-cashiers"  onclick="switchReport('cashiers',this)">🧾 Cashiers</button>
          <button class="stab"        id="rtype-payment"   onclick="switchReport('payment',this)">💳 Payments</button>
        </div>
        <!-- Filter bar -->
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
          <label style="font-size:12px;font-weight:600;color:var(--muted);white-space:nowrap">From</label>
          <input type="date" class="fi" id="rep-from" style="height:34px">
          <label style="font-size:12px;font-weight:600;color:var(--muted);white-space:nowrap">To</label>
          <input type="date" class="fi" id="rep-to"   style="height:34px">

          <select class="fi fi-select" id="rep-channel" style="height:34px" title="Channel">
            <option value="">All Channels</option>
            <option value="web">Web</option>
            <option value="android">Android</option>
            <option value="desktop">Desktop / POS</option>
          </select>

          <select class="fi fi-select" id="rep-order-type" style="height:34px" title="Order Type">
            <option value="">All Types</option>
            <option value="delivery">Delivery</option>
            <option value="pickup">Pickup</option>
            <option value="dine_in">Dine-in</option>
          </select>

          <!-- Quick range buttons -->
          <div style="display:flex;gap:4px;margin-left:auto">
            <button class="btn btn-outline btn-sm" onclick="setRepRange('today')">Today</button>
            <button class="btn btn-outline btn-sm" onclick="setRepRange('week')">7 Days</button>
            <button class="btn btn-outline btn-sm" onclick="setRepRange('month')">This Month</button>
            <button class="btn btn-outline btn-sm" onclick="setRepRange('last_month')">Last Month</button>
          </div>

          <!-- Search / filter -->
          <div class="search-wrap" id="sw-report" style="min-width:180px;max-width:240px">
            <input type="search" class="fi fi-search" id="rep-search" placeholder="Filter results..." autocomplete="off"
              oninput="reportSmartSearch(this)" style="width:100%;padding-right:32px;height:34px">
            <button class="search-clear" onclick="clearReportSearch()" tabindex="-1">&#x2715;</button>
            <div class="lov-dropdown" id="lov-report"></div>
          </div>
        </div>
      </div>

      <!-- Chart area (shown for Sales report) -->
      <div id="rep-chart-area" style="display:none;padding:20px 20px 0">
        <canvas id="rep-chart" height="90"></canvas>
      </div>

      <div id="report-body" style="padding:20px">
        <div class="empty"><p>Select a date range and click <strong>Run Report</strong></p></div>
      </div>
    </div>
  </div>

</div><!-- /main-content -->

<!-- ══ ORDER DETAIL MODAL ══ -->
<div class="modal-overlay" id="order-modal">
  <div class="modal modal-wide">
    <div class="modal-hdr">
      <h3 id="order-modal-title">Order Details</h3>
      <button class="modal-close" onclick="closeModal('order-modal')">&#x2715;</button>
    </div>
    <div class="modal-body" id="order-modal-body"></div>
    <div class="modal-footer">
      <button class="btn btn-outline btn-sm" onclick="closeModal('order-modal')">Close</button>
    </div>
  </div>
</div>

<!-- ══ PRODUCT MODAL ══ -->
<div class="modal-overlay" id="product-modal">
  <div class="modal">
    <div class="modal-hdr">
      <h3 id="prod-modal-title">Add Product</h3>
      <button class="modal-close" onclick="closeModal('product-modal')">&#x2715;</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="prod-id">
      <input type="hidden" id="prod-category">
      <div class="fg"><label class="fl">Name <span class="req">*</span></label><input type="text" class="fi-full" id="prod-name" placeholder="e.g. Ribeye Steak"></div>
      <div class="form-row">
        <div class="fg">
          <label class="fl">Type <span class="req">*</span></label>
          <select class="fi-full" id="prod-type" onchange="onProdTypeChange()">
            <option value="">Select type</option>
            <option value="Butchery">&#x1F969; Butchery</option>
            <option value="Restaurant">&#x1F37D;&#xFE0F; Restaurant</option>
            <option value="Liquor">&#x1F37A; Liquor</option>
          </select>
        </div>
        <div class="fg"><label class="fl">Price (E) <span class="req">*</span></label><input type="number" class="fi-full" id="prod-price-inp" placeholder="0.00" min="0" step="0.01"></div>
      </div>
      <div class="form-row">
        <div class="fg"><label class="fl">Stock Qty</label><input type="number" class="fi-full" id="prod-qty" placeholder="0" min="0"></div>
        <div class="fg"><label class="fl">Low Stock Threshold</label><input type="number" class="fi-full" id="prod-low-stock" placeholder="5" min="0"></div>
      </div>
      <div class="fg"><label class="fl">Description</label><textarea class="fi-ta" id="prod-desc" placeholder="Short product description..."></textarea></div>
      <div class="fg"><label class="fl">Product Image</label>
        <div class="img-upload-box" id="prod-img-box">
          <button type="button" class="img-clear" id="prod-img-clear" onclick="clearProdImg(event)">&#x2715;</button>
          <img class="img-preview" id="prod-img-preview" alt="Preview">
          <input type="file" id="prod-image" accept="image/*" onchange="previewImg('prod-image','prod-img-preview','prod-img-clear','prod-img-hint')">
          <div class="img-upload-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div>
          <div class="img-upload-hint" id="prod-img-hint">Click or drag to upload · <strong>JPG, PNG, WEBP</strong></div>
        </div>
      </div>
      <div class="fg"><label class="fl">Badge (optional)</label>
        <select class="fi-full" id="prod-badge">
          <option value="">None</option>
          <option value="new">New</option>
          <option value="popular">Popular</option>
          <option value="fresh">Fresh</option>
          <option value="premium">Premium</option>
          <option value="offer">Offer</option>
          <option value="seasonal">Seasonal</option>
        </select>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline btn-sm" onclick="closeModal('product-modal')">Cancel</button>
      <button class="btn btn-gold btn-sm" onclick="saveProduct()">Save Product</button>
    </div>
  </div>
</div>

<!-- ══ STOCK MODAL ══ -->
<div class="modal-overlay" id="stock-modal">
  <div class="modal">
    <div class="modal-hdr">
      <h3 id="stock-modal-title">Adjust Stock</h3>
      <button class="modal-close" onclick="closeModal('stock-modal')">&#x2715;</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="stock-prod-id">
      <div class="fg">
        <label class="fl">Current Stock</label>
        <div id="stock-current" style="font-family:'Nunito',sans-serif;font-size:22px;font-weight:900;color:var(--text);padding:6px 0"></div>
      </div>
      <div class="fg">
        <label class="fl">Adjustment <span class="req">*</span></label>
        <input type="number" class="fi-full" id="stock-qty-change" placeholder="e.g. +10 to add, -5 to remove">
        <span style="font-size:11px;color:var(--muted);margin-top:4px;display:block">Enter a positive number to add stock, negative to remove.</span>
      </div>
      <div class="fg">
        <label class="fl">Notes</label>
        <input type="text" class="fi-full" id="stock-notes" placeholder="e.g. Restock from supplier">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline btn-sm" onclick="closeModal('stock-modal')">Cancel</button>
      <button class="btn btn-gold btn-sm" onclick="saveStockAdjustment()">Save</button>
    </div>
  </div>
</div>

<!-- ══ USER MODAL ══ -->
<div class="modal-overlay" id="user-modal">
  <div class="modal">
    <div class="modal-hdr">
      <h3 id="user-modal-title">Add User</h3>
      <button class="modal-close" onclick="closeModal('user-modal')">&#x2715;</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="user-id">
      <div class="fg"><label class="fl">Full Name <span class="req">*</span></label><input type="text" class="fi-full" id="user-fullname" placeholder="John Doe"></div>
      <div class="form-row">
        <div class="fg"><label class="fl">Email <span class="req">*</span></label><input type="email" class="fi-full" id="user-email" placeholder="user@email.com"></div>
        <div class="fg"><label class="fl">Phone</label><input type="tel" class="fi-full" id="user-phone" placeholder="+268 7600 0001"></div>
      </div>
      <div class="form-row">
        <div class="fg"><label class="fl">Role <span class="req">*</span></label>
          <select class="fi-full" id="user-role" onchange="toggleUserPhotoField()">
            <option value="cashier">Cashier</option>
            <option value="driver">Driver</option>
            <option value="manager" class="superadmin-only">Manager</option>
            <option value="super_admin" class="superadmin-only">Super Admin</option>
          </select>
        </div>
        <div class="fg" id="user-pass-group"><label class="fl">Password <span class="req">*</span></label><input type="password" class="fi-full" id="user-password" placeholder="Min 6 characters"></div>
      </div>
      <div class="fg" id="user-photo-group" style="display:none">
        <label class="fl">Profile Photo <span style="color:var(--muted);font-weight:400">(optional)</span></label>
        <div class="img-upload-box" id="user-img-box">
          <button type="button" class="img-clear" id="user-img-clear" onclick="clearUserImg(event)">&#x2715;</button>
          <img class="img-preview" id="user-img-preview" alt="Preview">
          <input type="file" id="user-photo" accept="image/*" onchange="previewImg('user-photo','user-img-preview','user-img-clear','user-img-hint')">
          <div class="img-upload-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg></div>
          <div class="img-upload-hint" id="user-img-hint">Click or drag · <strong>JPG, PNG, WEBP</strong></div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline btn-sm" onclick="closeModal('user-modal')">Cancel</button>
      <button class="btn btn-gold btn-sm" onclick="saveUser()">Save User</button>
    </div>
  </div>
</div>

<!-- ══ CUSTOMER MODAL ══ -->
<div class="modal-overlay" id="customer-modal">
  <div class="modal">
    <div class="modal-hdr">
      <h3 id="cust-modal-title">Add Customer</h3>
      <button class="modal-close" onclick="closeModal('customer-modal')">&#x2715;</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="cust-id">
      <div class="form-row">
        <div class="fg"><label class="fl">First Name <span class="req">*</span></label><input type="text" class="fi-full" id="cust-firstname" placeholder="Jane"></div>
        <div class="fg"><label class="fl">Last Name <span class="req">*</span></label><input type="text" class="fi-full" id="cust-lastname" placeholder="Doe"></div>
      </div>
      <div class="fg"><label class="fl">Phone <span class="req">*</span></label><input type="tel" class="fi-full" id="cust-phone" placeholder="26876000001" maxlength="11" inputmode="numeric"></div>
      <div class="fg" id="cust-pass-group"><label class="fl">Password <span class="req">*</span></label><input type="password" class="fi-full" id="cust-password" placeholder="Min 6 characters"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline btn-sm" onclick="closeModal('customer-modal')">Cancel</button>
      <button class="btn btn-gold btn-sm" onclick="saveCustomer()">Save Customer</button>
    </div>
  </div>
</div>

<!-- ══ STAFF MODAL (Cashier / Driver) ══ -->
<div class="modal-overlay" id="staff-modal">
  <div class="modal">
    <div class="modal-hdr">
      <h3 id="staff-modal-title">Add Staff</h3>
      <button class="modal-close" onclick="closeModal('staff-modal')">&#x2715;</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="staff-id">
      <input type="hidden" id="staff-role-val">
      <div class="form-row">
        <div class="fg"><label class="fl">First Name <span class="req">*</span></label><input type="text" class="fi-full" id="staff-firstname" placeholder="John"></div>
        <div class="fg"><label class="fl">Last Name <span class="req">*</span></label><input type="text" class="fi-full" id="staff-lastname" placeholder="Smith"></div>
      </div>
      <div class="fg">
        <label class="fl">Phone <span class="req">*</span></label>
        <input type="tel" class="fi-full" id="staff-phone" placeholder="26876000001" maxlength="11" inputmode="numeric">
        <span class="field-err" id="err-staff-phone">Enter 11-digit phone number e.g. 26876000001</span>
      </div>
      <div class="fg" id="staff-pass-group">
        <label class="fl">Password <span class="req">*</span></label>
        <input type="password" class="fi-full" id="staff-password" placeholder="Min 8 chars, uppercase, number, special">
        <span class="field-err" id="err-staff-pass"></span>
      </div>
      <div class="fg">
        <label class="fl">Profile Photo <span style="color:var(--muted);font-weight:400">(optional)</span></label>
        <div class="img-upload-box" id="staff-img-box">
          <button type="button" class="img-clear" id="staff-img-clear" onclick="clearStaffImg(event)">&#x2715;</button>
          <img class="img-preview" id="staff-img-preview" alt="Preview">
          <input type="file" id="staff-photo" accept="image/*" onchange="previewImg('staff-photo','staff-img-preview','staff-img-clear','staff-img-hint')">
          <div class="img-upload-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg></div>
          <div class="img-upload-hint" id="staff-img-hint">Click or drag · <strong>JPG, PNG, WEBP</strong></div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline btn-sm" onclick="closeModal('staff-modal')">Cancel</button>
      <button class="btn btn-gold btn-sm" onclick="saveStaff()">Save</button>
    </div>
  </div>
</div>

<!-- ══ PAYMENT DETAIL MODAL ══ -->
<div class="modal-overlay" id="payment-modal">
  <div class="modal">
    <div class="modal-hdr">
      <h3 id="pay-modal-title">Payment Details</h3>
      <button class="modal-close" onclick="closeModal('payment-modal')">&#x2715;</button>
    </div>
    <div class="modal-body" id="pay-modal-body"></div>
    <div class="modal-footer" id="pay-modal-footer">
      <button class="btn btn-outline btn-sm" onclick="closeModal('payment-modal')">Close</button>
    </div>
  </div>
</div>

<!-- ══ TOAST ══ -->

<!-- ══ DELETE CONFIRM MODAL ══ -->
<div class="modal-overlay" id="delete-confirm-modal">
  <div class="modal" style="max-width:420px">
    <div class="modal-hdr">
      <h3 style="color:var(--red);display:flex;align-items:center;gap:8px">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;flex-shrink:0"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
        Delete Product
      </h3>
      <button class="modal-close" onclick="closeModal('delete-confirm-modal')">&#x2715;</button>
    </div>
    <div class="modal-body" style="text-align:center;padding:28px 24px">
      <p style="font-size:15px;color:var(--text);margin-bottom:6px">Are you sure you want to delete</p>
      <p id="delete-confirm-name" style="font-family:'Nunito',sans-serif;font-size:18px;font-weight:900;color:var(--dark);margin-bottom:8px"></p>
      <p style="font-size:12px;color:var(--muted)">This action cannot be undone.</p>
    </div>
    <div class="modal-footer" style="justify-content:center;gap:12px">
      <button class="btn btn-outline btn-sm" style="min-width:90px" onclick="closeModal('delete-confirm-modal')">No, cancel</button>
      <button class="btn btn-red btn-sm" style="min-width:90px;background:var(--red);color:white;border-color:var(--red)" id="delete-confirm-btn" onclick="confirmDeleteProduct()">Yes, delete</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
// ── Config ───────────────────────────────────────────────────
const API = window.location.origin + '/api';

// ── Auth (localStorage — same pattern as customer.php) ────────
const token = localStorage.getItem('access_token'); // kept for legacy; authFetch uses _liveToken
const user  = JSON.parse(localStorage.getItem('user') || 'null');

if (!token || !user) { window.location.href = 'login.php'; throw 'redirect'; }
if (user && user.role !== 'manager' && user.role !== 'super_admin') {
  window.location.href = 'shop.php'; throw 'redirect';
}

// ── Set header info ───────────────────────────────────────────
document.getElementById('user-name').textContent = 'Hi, ' + (user ? user.full_name.split(' ')[0] : '');
const roleBadge = document.getElementById('role-badge');
if (user && user.role === 'super_admin') {
  roleBadge.textContent = 'Super Admin';
  document.body.classList.add('role-superadmin');
  document.querySelectorAll('.superadmin-only').forEach(el => el.style.display = 'flex');
  document.querySelectorAll('button.superadmin-only').forEach(el => el.style.display = 'flex');
} else {
  roleBadge.textContent = 'Shop Manager';
}

// ── Helpers ──────────────────────────────────────────────────
function esc(s){ const d=document.createElement('div');d.textContent=s||'';return d.innerHTML; }
function E(n)  { return 'E ' + parseFloat(n||0).toFixed(2); }

// ── Image upload helpers ──────────────────────────────────────
function previewImg(inputId, previewId, clearId, hintId){
  const file=$(''+inputId).files[0]; if(!file) return;
  const reader=new FileReader();
  reader.onload=e=>{
    const img=$(previewId); img.src=e.target.result; img.classList.add('show');
    $(clearId).classList.add('show');
    $(hintId).style.display='none';
  };
  reader.readAsDataURL(file);
}
function clearProdImg(e){
  e.stopPropagation();
  $('prod-image').value=''; $('prod-img-preview').src=''; $('prod-img-preview').classList.remove('show');
  $('prod-img-clear').classList.remove('show'); $('prod-img-hint').style.display='';
}
function clearUserImg(e){
  e.stopPropagation();
  $('user-photo').value=''; $('user-img-preview').src=''; $('user-img-preview').classList.remove('show');
  $('user-img-clear').classList.remove('show'); $('user-img-hint').style.display='';
}
function toggleUserPhotoField(){
  const role=$('user-role').value;
  const show=(role==='driver'||role==='cashier');
  $('user-photo-group').style.display=show?'block':'none';
  if(!show){ clearUserImg({stopPropagation:()=>{}}); }
}
function $(id) { return document.getElementById(id); }
function fmtDate(iso){
  if(!iso) return '—';
  return new Date(iso).toLocaleString('en-GB',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit',hour12:false}).replace(',','');
}
function fmtDateShort(iso){
  if(!iso) return '—';
  return new Date(iso).toLocaleString('en-GB',{day:'2-digit',month:'short',hour:'2-digit',minute:'2-digit',hour12:false}).replace(',','');
}
function showToast(msg, type=''){
  const el=$('toast'); el.textContent=msg;
  el.className='toast show'+(type?' '+type:'');
  clearTimeout(el._t); el._t=setTimeout(()=>{el.className='toast';},3000);
}
function badgeHtml(status, prefix='b'){
  const s=(status||'').toLowerCase().replace(' ','_');
  return `<span class="badge ${prefix}-${s}">${esc(status)}</span>`;
}
function openModal(id){ $(id).classList.add('open'); }
function closeModal(id){ $(id).classList.remove('open'); }

// Close modal on overlay click
document.querySelectorAll('.modal-overlay').forEach(o=>{
  o.addEventListener('click', e=>{ if(e.target===o) o.classList.remove('open'); });
});

// Debounce
function debounce(fn, delay){
  let t; return function(...args){ clearTimeout(t); t=setTimeout(()=>fn.apply(this,args),delay); };
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

// Auth fetch — silently refreshes token on 401 instead of logging out
async function authFetch(path, opts={}, _isRetry=false){
  const tok = _liveToken || localStorage.getItem('access_token') || '';
  opts.headers = Object.assign({'Content-Type':'application/json','Authorization':'Bearer '+tok}, opts.headers||{});
  try{
    const r = await fetch(API+path, opts);
    if(r.status===401 && !_isRetry){
      const ok = await refreshAccessToken();
      if(ok){ delete opts.headers['Authorization']; return authFetch(path, opts, true); }
      logout(); return null;
    }
    return r;
  }catch(e){
    showToast('Network error','error'); return null;
  }
}

function logout(){
  const rt = localStorage.getItem('refresh_token');
  if(rt){ fetch(API+'/auth/logout',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({refresh_token:rt})}).catch(()=>{}); }
  localStorage.removeItem('access_token');
  localStorage.removeItem('refresh_token');
  localStorage.removeItem('user');
  window.location.href='login.php';
}

// ── Tab switching ─────────────────────────────────────────────
let activeTab = 'dashboard';
function switchTab(tab, btn){
  document.querySelectorAll('.tab-panel').forEach(p=>p.classList.remove('active'));
  document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
  $('panel-'+tab).classList.add('active');
  if(btn) btn.classList.add('active');
  activeTab = tab;
  const loaders={dashboard:loadDashboard,orders:loadOrders,products:loadProducts,customers:loadCustomers,staff:loadStaff,payment:loadPayments,reports:initReports};
  if(loaders[tab]) loaders[tab]();
}

// ── DASHBOARD ────────────────────────────────────────────────
async function loadDashboard(){
  $('dash-date').textContent = new Date().toLocaleDateString('en-GB',{weekday:'long',day:'numeric',month:'long',year:'numeric'});
  const r = await authFetch('/reports/dashboard');
  if(!r) return;
  const d = await r.json();
  if(!d.success) return;
  const kpi = d.data || {};
  $('kpi-revenue').textContent = E(kpi.today_revenue || 0);
  $('kpi-orders').textContent  = kpi.today_orders || 0;
  $('kpi-pending').textContent = kpi.pending_orders || 0;
  $('kpi-ready').textContent   = kpi.ready_orders || 0;
  $('kpi-alerts').textContent  = (kpi.stock_alerts||[]).length;
  // Pending badge on orders tab
  const pb = $('pending-badge');
  if(kpi.pending_orders>0){ pb.textContent=kpi.pending_orders; pb.style.display='inline-flex'; } else { pb.style.display='none'; }
  renderRecentOrders(kpi.recent_orders||[]);
  renderStockAlerts(kpi.stock_alerts||[]);
}
function renderRecentOrders(orders){
  const tbody = $('recent-orders-rows');
  if(!orders.length){ tbody.innerHTML='<tr><td colspan="6"><div class="empty"><p>No recent orders</p></div></td></tr>'; return; }
  tbody.innerHTML = orders.slice(0,10).map(o=>`
    <tr>
      <td><span class="td-order">${esc(o.order_ref||'')}</span></td>
      <td>${esc(o.customer_name||'Walk-in')}</td>
      <td>${badgeHtml(o.order_type)}</td>
      <td style="font-family:'Nunito',sans-serif;font-weight:800">${E(o.total_amount)}</td>
      <td>${badgeHtml(o.order_status)}</td>
      <td style="color:var(--muted);font-size:12px">${fmtDateShort(o.created_at)}</td>
    </tr>`).join('');
}
function renderStockAlerts(alerts){
  const el = $('stock-alerts-body');
  if(!alerts.length){ el.innerHTML='<div class="empty"><p style="color:var(--green)">All stock levels OK</p></div>'; return; }
  el.innerHTML = alerts.map(a=>{
    const cls = a.stock_qty===0 ? 'out' : 'low';
    return `<div class="sa-item"><div class="sa-dot ${cls}"></div><span class="sa-name">${esc(a.name)}</span><span class="sa-qty ${cls}">${a.stock_qty===0?'Out of stock':'Qty: '+a.stock_qty}</span></div>`;
  }).join('');
}

// ── ORDERS ───────────────────────────────────────────────────
// Status logic:
//   "Pending"  = payment confirmed but NOT yet marked ready
//               (statuses: pending, confirmed, preparing, dispatched)
//   "Ready"    = A-prefix orders marked ready (collection ready for pickup)
//   "Delivered"= D-prefix orders marked delivered OR A-prefix orders marked completed
//
// Type filter logic (by order_ref prefix):
//   Collection = order_ref starts with 'A'
//   Delivery   = order_ref starts with 'D'

let ordersPage=1;
let _ordersAllRows=[]; // cache for client-side type filtering

async function loadOrders(page=1){
  ordersPage=page;
  const statusFilter=$('ord-status').value;
  const typeFilter=$('ord-type').value;
  const from=$('ord-from').value, to=$('ord-to').value;

  // Map simplified statuses to actual API statuses to fetch
  let apiStatuses=[];
  if(!statusFilter){
    // All: fetch all relevant statuses (payment confirmed onward, excluding cancelled)
    apiStatuses=['pending','confirmed','preparing','dispatched','delivered','completed'];
  } else if(statusFilter==='pending'){
    apiStatuses=['pending','confirmed','preparing','dispatched'];
  } else if(statusFilter==='delivered'){
    apiStatuses=['delivered','completed'];
  }

  // Fetch all needed statuses (parallel if multiple)
  $('orders-tbody').innerHTML='<tr><td colspan="9"><div class="empty"><p>Loading...</p></div></td></tr>';

  let allRows=[];
  try{
    const fetches = apiStatuses.map(s=>{
      let qs=`?page=1&per_page=200&status=${s}`;
      if(from) qs+=`&date_from=${from}`;
      if(to)   qs+=`&date_to=${to}`;
      return authFetch('/orders'+qs).then(r=>r?r.json():null);
    });
    const results = await Promise.all(fetches);
    results.forEach(d=>{ if(!d) return; const rows=Array.isArray(d.data&&d.data.data?d.data.data:null)?d.data.data:Array.isArray(d.data)?d.data:[]; allRows=allRows.concat(rows); });
  }catch(e){
    $('orders-tbody').innerHTML='<tr><td colspan="9"><div class="empty"><p>Failed to load orders</p></div></td></tr>'; return;
  }

  // Sort by created_at descending
  allRows.sort((a,b)=>new Date(b.created_at)-new Date(a.created_at));

  // Client-side type filter by order_ref prefix
  if(typeFilter==='collection'){
    allRows=allRows.filter(o=>(o.order_ref||'').toUpperCase().startsWith('A'));
  } else if(typeFilter==='delivery'){
    allRows=allRows.filter(o=>(o.order_ref||'').toUpperCase().startsWith('D'));
  }

  if(!allRows.length){ $('orders-tbody').innerHTML='<tr><td colspan="9"><div class="empty"><p>No orders found</p></div></td></tr>'; renderPager('orders-pager',{page:1,total_pages:1},loadOrders); return; }

  // Client-side pagination
  const perPage=20, totalRows=allRows.length;
  const totalPages=Math.ceil(totalRows/perPage);
  const pageRows=allRows.slice((page-1)*perPage, page*perPage);

  $('orders-tbody').innerHTML = pageRows.map(o=>{
    const ref=(o.order_ref||'');
    const isCollection=ref.toUpperCase().startsWith('A');
    const orderType=isCollection?'Collection':'Delivery';
    const typeBadge=isCollection
      ?`<span class="badge b-collection">Collection</span>`
      :`<span class="badge b-delivery">Delivery</span>`;

    // Display status: map to simplified label
    // Ready only means something for A-prefix (collection) orders.
    // D-prefix orders marked "ready" in kitchen are still awaiting dispatch → show as Pending.
    const rawStatus=(o.order_status||'').toLowerCase();
    let displayBadge;
    if(['pending','confirmed','preparing','dispatched','ready'].includes(rawStatus)){
      displayBadge=`<span class="badge b-pending">Pending</span>`;
    } else if(['delivered','completed'].includes(rawStatus)){
      displayBadge=`<span class="badge b-delivered">Delivered</span>`;
    } else {
      displayBadge=badgeHtml(o.order_status);
    }

    return `<tr>
      <td><span class="td-order">${esc(ref)}</span></td>
      <td>${esc(o.customer_name||'Walk-in')}</td>
      <td><span class="badge" style="background:#F1F5F9;color:#64748B">${esc(o.channel||'')}</span></td>
      <td>${typeBadge}</td>
      <td style="font-family:'Nunito',sans-serif;font-weight:800">${E(o.total_amount)}</td>
      <td style="font-size:12px;color:var(--muted)">${esc(o.payment_method||'')}</td>
      <td>${displayBadge}</td>
      <td style="color:var(--muted);font-size:12px;white-space:nowrap">${fmtDateShort(o.created_at)}</td>
      <td><button class="btn btn-outline btn-sm" onclick="openOrderDetail(${o.order_id})">View</button></td>
    </tr>`;
  }).join('');

  renderPager('orders-pager',{page,total_pages:totalPages},loadOrders);
}
async function openOrderDetail(id){
  openModal('order-modal');
  $('order-modal-body').innerHTML='<div class="empty"><p>Loading...</p></div>';
  const r = await authFetch('/orders/'+id);
  if(!r){ $('order-modal-body').innerHTML='<div class="empty"><p>Failed to load</p></div>'; return; }
  const d = await r.json();
  if(!d.success){ $('order-modal-body').innerHTML='<div class="empty"><p>Error loading order</p></div>'; return; }
  const o = d.data;
  $('order-modal-title').textContent = 'Order ' + (o.order_ref||'#'+id);
  $('order-modal-body').innerHTML = `
    <div class="od-section">
      <h4>Order Info</h4>
      <div class="od-row"><span class="od-label">Order #</span><span class="od-value td-order">${esc(o.order_ref)}</span></div>
      <div class="od-row"><span class="od-label">Status</span><span>${badgeHtml(o.order_status)}</span></div>
      <div class="od-row"><span class="od-label">Type</span><span>${badgeHtml(o.order_type)}</span></div>
      <div class="od-row"><span class="od-label">Channel</span><span class="od-value">${esc(o.channel)}</span></div>
      <div class="od-row"><span class="od-label">Customer</span><span class="od-value">${esc(o.customer_name||'Walk-in')}</span></div>
      <div class="od-row"><span class="od-label">Payment</span><span class="od-value">${esc(o.payment_method||'')}</span></div>
      <div class="od-row"><span class="od-label">Time</span><span class="od-value">${fmtDate(o.created_at)}</span></div>
      ${o.special_notes ? `<div class="od-row"><span class="od-label">Notes</span><span class="od-value">${esc(o.special_notes)}</span></div>` : ''}
    </div>
    ${o.order_type==='delivery' ? `<div class="od-section"><h4>Delivery Address</h4>
      <div class="od-row"><span class="od-label">Street</span><span class="od-value">${esc(o.street||'')}</span></div>
      <div class="od-row"><span class="od-label">City</span><span class="od-value">${esc(o.city||'')} ${esc(o.region||'')}</span></div>
    </div>` : ''}
    <div class="od-section"><h4>Items</h4>
      <div class="table-wrap">
      <table class="data-table" style="margin:0">
        <thead><tr><th style="color:#333;font-weight:700">Item</th><th style="color:#333;font-weight:700">Qty</th><th style="color:#333;font-weight:700">Unit Price</th><th style="color:#333;font-weight:700">Total</th></tr></thead>
        <tbody>${(o.items||[]).map(it=>`<tr>
          <td style="font-weight:600">${esc(it.product_name_snap||it.name||'—')}</td>
          <td>${it.quantity}</td>
          <td>${E(it.unit_price||it.unit_price_snap||0)}</td>
          <td style="font-weight:700">${E(it.line_total)}</td>
        </tr>`).join('')}</tbody>
      </table>
      </div><!-- /.table-wrap -->
    </div>
    <div class="od-section">
      <div class="od-row"><span class="od-label">Subtotal</span><span class="od-value">${E(o.subtotal)}</span></div>
      ${o.discount_amount>0?`<div class="od-row"><span class="od-label">Discount</span><span class="od-value" style="color:var(--green)">- ${E(o.discount_amount)}</span></div>`:''}
      ${o.delivery_fee>0?`<div class="od-row"><span class="od-label">Delivery Fee</span><span class="od-value">${E(o.delivery_fee)}</span></div>`:''}
    </div>`;
}
async function updateOrderStatus(){
  const sel=$('order-status-select'), id=sel.dataset.orderId, status=sel.value;
  const r=await authFetch('/orders/'+id+'/status',{method:'PATCH',body:JSON.stringify({status})});
  if(!r) return;
  const d=await r.json();
  if(d.success){ showToast('Status updated','success'); closeModal('order-modal'); loadOrders(ordersPage); }
  else showToast(d.error||'Update failed','error');
}

// ── PRODUCTS ─────────────────────────────────────────────────
let productsPage=1;
async function loadProducts(page=1){
  productsPage=page;
  const cat=$('prod-cat').value,
        stock=$('prod-stock').value, search=$('prod-search').value;
  let qs=`?page=${page}&per_page=20`;
  if(cat)    qs+=`&category_id=${cat}`;
  if(stock)  qs+=`&low_stock=1`;
  if(search) qs+=`&search=${encodeURIComponent(search)}`;
  $('products-tbody').innerHTML='<tr><td colspan="6"><div class="empty"><p>Loading...</p></div></td></tr>';
  const r=await authFetch('/products'+qs);
  if(!r) return;
  const d=await r.json();
  if(!d.success) return;
  const rows=d.data||[];
  if(!rows.length){ $('products-tbody').innerHTML='<tr><td colspan="6"><div class="empty"><p>No products found</p></div></td></tr>'; return; }
  $('products-tbody').innerHTML=rows.map(p=>{
    const stockCls = p.stock_qty===0?'b-out':p.stock_qty<=p.low_stock_threshold?'b-low':'b-active';
    const stockLabel = p.stock_qty===0?'Out of stock':p.stock_qty<=p.low_stock_threshold?'Low ('+p.stock_qty+')':p.stock_qty;
    return `<tr>
      <td><strong>${esc(p.name)}</strong>${p.badge?` <span style="background:var(--gold-light);color:var(--gold);font-size:10px;font-weight:700;padding:2px 6px;border-radius:4px">${esc(p.badge)}</span>`:''}</td>
      <td style="color:var(--muted)">${esc(p.category_name||'')}</td>
      <td style="font-family:'Nunito',sans-serif;font-weight:900">${E(p.price)}</td>
      <td><span class="badge ${stockCls}">${stockLabel}</span></td>
      <td>
        <label class="tgl"><input type="checkbox" ${p.is_available?'checked':''} onchange="toggleProduct(${p.id},this.checked)"><div class="tgl-slider"></div></label>
      </td>
      <td class="action-btns">
        <button class="btn btn-outline btn-sm" onclick="openProductModal(${p.id})">Edit</button>
        <button class="btn btn-outline btn-sm" onclick="openStockModal(${p.id},'${esc(p.name)}',${p.stock_qty||0})">Stock</button>
        <button class="btn btn-red btn-sm" onclick="deleteProduct(${p.id},'${esc(p.name)}')">Delete</button>
      </td>
    </tr>`;
  }).join('');
  renderPager('products-pager',{page:1,total_pages:1},loadProducts);
}
async function toggleProduct(id,val){
  const r=await authFetch('/products/'+id+'/toggle',{method:'PATCH',body:JSON.stringify({is_available:val?1:0})});
  if(r){ const d=await r.json(); if(!d.success) showToast('Update failed','error'); }
}
function openProductModal(id=null){
  $('prod-modal-title').textContent = id ? 'Edit Product' : 'Add Product';
  $('prod-id').value=''; $('prod-name').value=''; $('prod-price-inp').value=''; $('prod-qty').value=''; $('prod-low-stock').value='5'; $('prod-desc').value=''; $('prod-badge').value='';
  $('prod-type').value=''; $('prod-category').value='';
  clearProdImg({stopPropagation:()=>{}});
  // Always reload categories so allCategories is up to date
  loadCategories().then(()=>{ if(id) loadProductForEdit(id); });
  openModal('product-modal');
}
async function loadProductForEdit(id){
  const r=await authFetch('/products/'+id); if(!r) return;
  const d=await r.json(); if(!d.success) return;
  const p=d.data;
  $('prod-id').value=p.id||id;  // API aliases product_id as 'id'
  $('prod-name').value=p.name||'';
  $('prod-price-inp').value=p.price||'';
  $('prod-qty').value=p.stock_qty!==undefined?p.stock_qty:'';
  $('prod-low-stock').value=p.low_stock_threshold||5;
  $('prod-desc').value=p.description||'';
  $('prod-badge').value=p.badge||'';
  // Set category_id directly (most reliable)
  $('prod-category').value=p.category_id||'';
  // Set type dropdown to match category type
  if(p.category_type){ $('prod-type').value=p.category_type; }
  else if(p.category_id){
    const cat=allCategories.find(c=>c.category_id==p.category_id);
    if(cat) $('prod-type').value=cat.type||'';
  }
  // Image preview
  const BASE_URL=window.location.origin+'/api';
  const rawImg=p.img_url||p.image_url||'';
  const imgSrc=rawImg?(rawImg.startsWith('http')?rawImg:BASE_URL+rawImg):'';
  if(imgSrc){ const img=$('prod-img-preview'); img.src=imgSrc; img.classList.add('show'); $('prod-img-clear').classList.add('show'); $('prod-img-hint').style.display='none'; }
}
async function saveProduct(){
  const id=$('prod-id').value;
  const name=$('prod-name').value.trim(), price=parseFloat($('prod-price-inp').value)||0;
  const catId=parseInt($('prod-category').value)||0;
  const typeVal=$('prod-type').value;

  // ── Validation ───────────────────────────────────────────────
  if(!name){    showToast('Product name is required','error'); return; }
  if(!typeVal){ showToast('Please select a type — Butchery, Restaurant or Liquor','error'); return; }
  if(!price){   showToast('Price is required','error'); return; }
  if(!catId){   showToast('Category not found — make sure you ran the SQL insert in phpMyAdmin','error'); return; }

  // ── Image: convert to base64 if selected ─────────────────────
  const imgFile=$('prod-image').files[0];
  let imgBase64=null, imgMime=null;
  if(imgFile){
    imgBase64 = await new Promise(res=>{
      const r=new FileReader();
      r.onload=e=>res(e.target.result.split(',')[1]);
      r.readAsDataURL(imgFile);
    });
    imgMime=imgFile.type;
  }

  // ── Send as JSON (API body() reads php://input as JSON) ───────
  const payload={
    category_id: catId,
    name:        name,
    price:       price,
    unit:        'each',
    description: $('prod-desc').value,
    badge:       $('prod-badge').value||null,
    stock_qty:   parseInt($('prod-qty').value)||0,
    low_stock_threshold: parseInt($('prod-low-stock').value)||5,
  };
  if(imgBase64){ payload.img_base64=imgBase64; payload.img_mime=imgMime; }

  const tok=localStorage.getItem('access_token');
  const opts={
    method: id?'PUT':'POST',
    headers:{'Content-Type':'application/json','Authorization':'Bearer '+tok},
    body: JSON.stringify(payload)
  };
  const r=await fetch(API+(id?'/products/'+id:'/products'),opts);
  if(!r){ showToast('Request failed','error'); return; }
  const d=await r.json();
  if(d.success){ showToast(id?'Product updated':'Product created','success'); closeModal('product-modal'); loadProducts(productsPage); }
  else showToast(d.error||'Save failed','error');
}
async function openStockModal(id, name, currentQty){
  $('stock-prod-id').value = id;
  $('stock-modal-title').textContent = 'Adjust Stock — ' + name;
  $('stock-current').textContent = currentQty !== undefined ? currentQty : '…';
  $('stock-qty-change').value = '';
  $('stock-notes').value = '';
  openModal('stock-modal');
}
async function saveStockAdjustment(){
  const id = $('stock-prod-id').value;
  const n = parseInt($('stock-qty-change').value);
  if(isNaN(n) || n === 0){ showToast('Enter a non-zero adjustment amount','error'); return; }
  const notes = $('stock-notes').value || 'Admin adjustment';
  const type = n > 0 ? 'restock' : 'adjustment';
  const r = await authFetch('/products/'+id+'/stock', {method:'PATCH', body:JSON.stringify({qty_change:n, change_type:type, notes})});
  if(!r) return;
  const d = await r.json();
  if(d.success){ showToast('Stock updated','success'); closeModal('stock-modal'); loadProducts(productsPage); }
  else showToast(d.error||'Failed','error');
}
let _deleteId=null;
function deleteProduct(id, name){
  _deleteId=id;
  document.getElementById('delete-confirm-name').textContent='“'+name+'”';
  openModal('delete-confirm-modal');
}
async function confirmDeleteProduct(){
  if(!_deleteId) return;
  const id=_deleteId;
  const btn=document.getElementById('delete-confirm-btn');
  btn.disabled=true; btn.textContent='Deleting...';
  const r=await authFetch('/products/'+id,{method:'DELETE'});
  btn.disabled=false; btn.textContent='Yes, delete';
  closeModal('delete-confirm-modal');
  _deleteId=null;
  if(!r) return;
  const d=await r.json();
  if(d.success){ showToast('Product deleted','success'); loadProducts(productsPage); }
  else showToast(d.error||'Delete failed','error');
}

// ── CATEGORIES (internal — populates product form dropdowns) ─
let allCategories = [];
async function loadCategories(){
  const r=await authFetch('/categories'); if(!r) return;
  const d=await r.json(); if(!d.success) return;
  allCategories=d.data||[];
  // Populate filter bar dropdown (all categories)
  $('prod-cat').innerHTML='<option value="">All Categories</option>'+allCategories.map(c=>`<option value="${c.category_id}">${esc(c.name)}</option>`).join('');
}

// When type is selected, auto-set the hidden category_id to the matching category
// The API may return the type field as c.type or c.category_type — check both
function onProdTypeChange(){
  const type=$('prod-type').value;
  if(!type){ $('prod-category').value=''; return; }
  // Match on c.type OR c.name (case-insensitive) in case API field name differs
  const match=allCategories.find(c=>
    (c.type && c.type===type) ||
    (c.name && c.name.toLowerCase()===type.toLowerCase())
  );
  if(match){
    $('prod-category').value=match.category_id;
  } else {
    $('prod-category').value='';
    showToast('Category "'+type+'" not found in database. Make sure you ran the SQL insert.','error');
  }
}

// Legacy alias
function filterCategoriesByType(preselectId=null){ onProdTypeChange(); }

// ── USERS ────────────────────────────────────────────────────
let usersPage=1, usersRole='staff';
function filterUsers(role, btn){
  document.querySelectorAll('.sub-tabs .stab').forEach(b=>b.classList.remove('active'));
  btn.classList.add('active'); usersRole=role; loadUsers();
}
async function loadUsers(page=1){
  usersPage=page;
  const search=$('user-search').value;
  $('users-tbody').innerHTML='<tr><td colspan="7"><div class="empty"><p>Loading...</p></div></td></tr>';

  let rows=[];

  if(usersRole==='staff'){
    // Fetch cashiers + drivers in parallel, merge results
    const searchQs = search ? `&search=${encodeURIComponent(search)}` : '';
    const [rC, rD] = await Promise.all([
      authFetch(`/users?role=cashier&per_page=100${searchQs}`),
      authFetch(`/users?role=driver&per_page=100${searchQs}`)
    ]);
    if(!rC || !rD) return;
    const [dC, dD] = await Promise.all([rC.json(), rD.json()]);
    const cashiers = dC.success ? (dC.data.data||dC.data||[]) : [];
    const drivers  = dD.success ? (dD.data.data||dD.data||[]) : [];
    // Merge and sort by created_at descending
    rows = [...cashiers, ...drivers].sort((a,b)=>new Date(b.created_at)-new Date(a.created_at));
    $('users-pager').innerHTML=''; // no pagination for merged results
  } else {
    // Single role or no filter — normal paginated request
    let qs=`?page=${page}&per_page=20`;
    if(usersRole) qs+=`&role=${usersRole}`;
    if(search)    qs+=`&search=${encodeURIComponent(search)}`;
    const r=await authFetch('/users'+qs); if(!r) return;
    const d=await r.json(); if(!d.success) return;
    rows=(d.data.data||d.data||[]);
    const _up=d.data.pagination||{}; renderPager('users-pager',{page:_up.current_page||1,total_pages:_up.last_page||1},loadUsers);
  }

  if(!rows.length){ $('users-tbody').innerHTML='<tr><td colspan="7"><div class="empty"><p>No users found</p></div></td></tr>'; return; }
  $('users-tbody').innerHTML=rows.map(u=>`<tr>
    <td><strong>${esc(u.full_name)}</strong></td>
    
    <td style="font-size:12px;color:var(--muted)">${esc(u.phone||'—')}</td>
    <td>${badgeHtml(u.role)}</td>
    <td>${badgeHtml(u.status)}</td>
    <td style="font-size:12px;color:var(--muted)">${u.last_login_at?fmtDateShort(u.last_login_at):'Never'}</td>
    <td class="action-btns">
      ${(user.role==='super_admin' || (u.role!=='manager' && u.role!=='super_admin'))?`<button class="btn btn-outline btn-sm" onclick="openUserModal(${u.user_id})">Edit</button>`:''}
      ${(user.role==='super_admin' && u.role!=='super_admin')?`<button class="btn btn-${u.status==='suspended'?'green':'red'} btn-sm" onclick="toggleUserStatus(${u.user_id},'${u.status==='suspended'?'active':'suspended'}')">${u.status==='suspended'?'Activate':'Suspend'}</button>`:''}
    </td>
  </tr>`).join('');
}
function openUserModal(id=null){
  $('user-modal-title').textContent=id?'Edit User':'Add User';
  $('user-id').value=''; $('user-fullname').value=''; $('user-email').value=''; $('user-phone').value=''; $('user-password').value=''; $('user-role').value='cashier';
  clearUserImg({stopPropagation:()=>{}});
  toggleUserPhotoField();
  $('user-pass-group').querySelector('label .req') && ($('user-pass-group').querySelector('label .req').style.display=id?'none':'inline');
  if(id) loadUserForEdit(id);
  openModal('user-modal');
}
async function loadUserForEdit(id){
  const r=await authFetch('/users/'+id); if(!r) return;
  const d=await r.json(); if(!d.success) return;
  $('user-id').value=id; $('user-fullname').value=d.data.full_name||''; $('user-email').value=d.data.email||''; $('user-phone').value=d.data.phone||''; $('user-role').value=d.data.role||'cashier';
  toggleUserPhotoField();
  if(d.data.avatar_url){ const img=$('user-img-preview'); img.src=d.data.avatar_url; img.classList.add('show'); $('user-img-clear').classList.add('show'); $('user-img-hint').style.display='none'; }
}
async function saveUser(){
  const id=$('user-id').value;
  const selectedRole=$('user-role').value;
  // Managers can only create/edit cashiers and drivers
  if(user.role!=='super_admin' && (selectedRole==='manager'||selectedRole==='super_admin')){
    showToast('You cannot assign Manager or Super Admin roles','error'); return;
  }
  const fullName=($('user-fullname').value||'').trim(), email=$('user-email').value.trim();
  if(!fullName||!email){ showToast('Name and email are required','error'); return; }
  const pass=$('user-password').value;
  if(!id && !pass){ showToast('Password required for new users','error'); return; }
  // Split "First Last" into first_name / last_name for the API
  const spaceIdx=fullName.indexOf(' ');
  const firstName=spaceIdx>0?fullName.slice(0,spaceIdx).trim():fullName;
  const lastName =spaceIdx>0?fullName.slice(spaceIdx+1).trim():'';
  if(!lastName){
    showToast('Please enter both a first and last name (e.g. John Smith)','error'); return;
  }
  const payload={first_name:firstName,last_name:lastName,email,phone:$('user-phone').value.trim(),role:selectedRole};
  if(pass) payload.password=pass;
  const tok=localStorage.getItem('access_token');
  const opts={method:id?'PUT':'POST',headers:{'Content-Type':'application/json','Authorization':'Bearer '+tok},body:JSON.stringify(payload)};
  const r=await fetch(API+(id?'/users/'+id:'/users'),opts);
  if(!r){ showToast('Request failed','error'); return; }
  const d=await r.json();
  if(d.success){ showToast(id?'User updated':'User created','success'); closeModal('user-modal'); loadUsers(usersPage); }
  else showToast(d.error||'Save failed','error');
}
async function toggleUserStatus(id,newStatus){
  const r=await authFetch('/users/'+id+'/status',{method:'PATCH',body:JSON.stringify({status:newStatus})});
  if(!r) return;
  const d=await r.json();
  if(d.success){
    showToast('User '+newStatus,'success');
    // Reload whichever tab triggered the action
    const activeTab=document.querySelector('.tab-btn.active')?.id||'';
    if(activeTab==='tb-staff') loadStaff();
    else loadUsers(usersPage);
  } else showToast(d.error||'Failed','error');
}

// ── REPORTS ─────────────────────────────────────────────────
let reportType='sales';
let reportDataCache=[];   // full fetched data kept for filtering

function initReports(){
  // Set default date range to current month on first open
  if(!$('rep-from').value){
    const today=new Date().toISOString().split('T')[0];
    const first=today.substring(0,8)+'01';
    $('rep-from').value=first;
    $('rep-to').value=today;
  }
  loadReports();
}

function setRepRange(range){
  const now=new Date();
  const pad=n=>String(n).padStart(2,'0');
  const fmt=d=>`${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
  let from,to=fmt(now);
  if(range==='today')      { from=to; }
  else if(range==='week')  { const d=new Date(now); d.setDate(d.getDate()-6); from=fmt(d); }
  else if(range==='month') { from=`${now.getFullYear()}-${pad(now.getMonth()+1)}-01`; }
  else if(range==='last_month'){
    const f=new Date(now.getFullYear(),now.getMonth()-1,1);
    const l=new Date(now.getFullYear(),now.getMonth(),0);
    from=fmt(f); to=fmt(l);
  }
  $('rep-from').value=from; $('rep-to').value=to;
  loadReports();
}

function switchReport(type,btn){
  document.querySelectorAll('#panel-reports .stab').forEach(b=>b.classList.remove('active'));
  btn.classList.add('active'); reportType=type;
  clearReportSearch();
  loadReports();
}

// Chart.js instance
let _repChart=null;

async function loadReports(){
  const el=$('report-body');
  el.innerHTML='<div class="empty"><p>Loading report...</p></div>';
  $('rep-kpi-row').style.display='none';
  $('rep-chart-area').style.display='none';
  $('rep-export-btn').style.display='none';
  if($('rep-print-btn')) $('rep-print-btn').style.display='none';

  const from=$('rep-from').value||'';
  const to=$('rep-to').value||'';
  const channel=$('rep-channel').value||'';
  const orderType=$('rep-order-type').value||'';
  const qs=(from?`&date_from=${from}`:'')+
           (to?`&date_to=${to}`:'')+
           (channel?`&channel=${channel}`:'')+
           (orderType?`&order_type=${orderType}`:'');

  // Load summary KPIs alongside the report data
  const summaryProm=authFetch(`/reports/summary?${qs.replace(/^&/,'')}`);

  let path='/reports/sales';
  if(reportType==='products')  path='/reports/products';
  if(reportType==='customers') path='/reports/customers';
  if(reportType==='drivers')   path='/reports/drivers';
  if(reportType==='cashiers')  path='/reports/cashiers';
  if(reportType==='payment')   path='/reports/payments';

  const [r, rSummary]=await Promise.all([authFetch(path+(qs?'?'+qs.replace(/^&/,''):'')), summaryProm]);
  if(!r){ el.innerHTML='<div class="empty"><p>Failed to load report</p></div>'; return; }
  const d=await r.json();
  if(!d.success){ el.innerHTML=`<div class="empty"><p>${esc(d.error||'Failed to load report')}</p></div>`; return; }
  reportDataCache=d.data||[];

  // Render KPI summary
  if(rSummary){
    const ds=await rSummary.json();
    if(ds.success) renderReportKPIs(ds.data);
  }

  $('rep-export-btn').style.display='inline-flex';
  if($('rep-print-btn')) $('rep-print-btn').style.display='inline-flex';
  clearReportSearch();
  renderReport(reportDataCache, reportType, el);

  // Draw chart for sales report
  if(reportType==='sales' && reportDataCache.length){
    drawSalesChart(reportDataCache);
  }
}

function renderReportKPIs(s){
  $('rep-kpi-row').style.display='grid';
  $('rep-kpi-revenue').textContent=E(s.net_revenue||0);
  $('rep-kpi-aov').textContent='Avg order: '+E(s.avg_order_value||0);
  $('rep-kpi-orders').textContent=s.total_orders||0;
  const compRate=s.total_orders>0?Math.round((s.completed_orders/s.total_orders)*100):0;
  $('rep-kpi-completed').textContent=`${s.completed_orders||0} completed (${compRate}%)`;
  $('rep-kpi-discounts').textContent=E(s.total_discounts||0);
  $('rep-kpi-delivery-rev').textContent='Delivery fees: '+E(s.delivery_revenue||0);
  $('rep-kpi-customers').textContent=s.new_customers||0;
  $('rep-kpi-top-product').textContent=s.top_product?'Top: '+s.top_product.name:'';
  $('rep-kpi-cancelled').textContent=s.cancelled_orders||0;
  const types=[];
  if(s.delivery_orders>0) types.push(s.delivery_orders+' delivery');
  if(s.pickup_orders>0)   types.push(s.pickup_orders+' pickup');
  if(s.dine_in_orders>0)  types.push(s.dine_in_orders+' dine-in');
  $('rep-kpi-order-types').textContent=types.join(' · ')||'';
}

function drawSalesChart(data){
  $('rep-chart-area').style.display='block';
  const labels=[...data].reverse().map(r=>r.sale_date||r.date||'');
  const revenues=[...data].reverse().map(r=>parseFloat(r.net_revenue||r.total_revenue||0));
  const orders=[...data].reverse().map(r=>parseInt(r.total_orders||r.order_count||0));
  const ctx=$('rep-chart').getContext('2d');
  if(_repChart) _repChart.destroy();
  _repChart=new Chart(ctx,{
    type:'bar',
    data:{
      labels,
      datasets:[
        {
          label:'Revenue (E)',
          data:revenues,
          backgroundColor:'rgba(245,166,35,0.75)',
          borderColor:'#F5A623',
          borderWidth:1.5,
          borderRadius:4,
          yAxisID:'y',
        },
        {
          label:'Orders',
          data:orders,
          type:'line',
          borderColor:'#3B82F6',
          backgroundColor:'rgba(59,130,246,0.1)',
          fill:true,
          tension:0.35,
          pointRadius:3,
          yAxisID:'y1',
        }
      ]
    },
    options:{
      responsive:true,
      interaction:{mode:'index',intersect:false},
      plugins:{legend:{position:'top',labels:{font:{family:'DM Sans',size:12},usePointStyle:true}}},
      scales:{
        y:{position:'left',beginAtZero:true,ticks:{callback:v=>'E'+v.toLocaleString(),font:{size:11}},grid:{color:'rgba(0,0,0,.05)'}},
        y1:{position:'right',beginAtZero:true,ticks:{font:{size:11}},grid:{display:false}},
        x:{ticks:{font:{size:11}},grid:{display:false}}
      }
    }
  });
}

// ── Report smart search (client-side filter + LOV) ───────────
let repSearchTimer=null;
function reportSmartSearch(inputEl){
  const wrap=$('sw-report');
  const q=(inputEl.value||'').trim().toLowerCase();
  if(q) wrap.classList.add('has-value'); else wrap.classList.remove('has-value');
  clearTimeout(repSearchTimer);
  closeLov('report');
  if(!q){
    renderReport(reportDataCache, reportType, $('report-body'));
    return;
  }
  repSearchTimer=setTimeout(()=>{
    const filtered=reportDataCache.filter(row=>{
      const haystack=Object.values(row).join(' ').toLowerCase();
      return haystack.includes(q);
    });
    renderReport(filtered, reportType, $('report-body'));
    const lov=$('lov-report');
    if(!filtered.length){ lov.innerHTML='<div class="lov-empty">No matches</div>'; lov.classList.add('open'); return; }
    const suggestions=filtered.slice(0,8).map((row,i)=>{
      const label=reportRowLabel(row);
      return `<div class="lov-item" onclick="repLovPick(${i})">
        <div class="lov-item-avatar" style="border-radius:6px;background:var(--gold-light);color:var(--gold);font-size:11px;font-weight:800;width:28px;height:28px;display:flex;align-items:center;justify-content:center">${label.icon}</div>
        <div class="lov-item-main">
          <div class="lov-item-name">${esc(label.name)}</div>
          ${label.sub?`<div class="lov-item-sub">${esc(label.sub)}</div>`:''}
        </div>
        <div class="lov-item-badge" style="font-family:'Nunito',sans-serif;font-weight:900;font-size:12px;color:var(--text)">${label.value}</div>
      </div>`;
    }).join('');
    lov.innerHTML=suggestions;
    lov._filtered=filtered;
    lov.classList.add('open');
  }, 250);
}

function reportRowLabel(row){
  switch(reportType){
    case 'sales':     return {icon:'📅', name: row.sale_date||row.date||'—', sub: (row.total_orders||row.order_count||0)+' orders', value: E(row.net_revenue||row.total_revenue||0)};
    case 'products':  return {icon:'🛒', name: row.product_name||row.name||'—', sub: row.category_name||'', value: E(row.total_revenue||0)};
    case 'customers': return {icon:'👤', name: row.full_name||row.customer_name||'—', sub: row.phone||'', value: E(row.total_spent||0)};
    case 'drivers':   return {icon:'🚗', name: row.driver_name||row.full_name||'—', sub: (row.total_assigned||0)+' assigned', value: (row.completed||0)+' done'};
    case 'cashiers':  return {icon:'🧾', name: row.cashier_name||row.full_name||'—', sub: (row.orders_processed||row.order_count||0)+' orders', value: E(row.total_sales||0)};
    case 'payment':   return {icon:'💳', name: row.order_ref||row.order_number||'—', sub: row.customer_name||'Walk-in', value: E(row.amount||row.grand_total||0)};
    default:          return {icon:'📦', name: row.name||'—', sub:'', value: String(row.stock_qty||0)};
  }
}

function repLovPick(index){
  const lov=$('lov-report');
  const filtered=lov._filtered||[];
  if(filtered[index]){
    const label=reportRowLabel(filtered[index]);
    $('rep-search').value=label.name;
    $('sw-report').classList.add('has-value');
    renderReport([filtered[index]], reportType, $('report-body'));
  }
  closeLov('report');
}

function clearReportSearch(){
  const inp=$('rep-search');
  if(inp) inp.value='';
  const wrap=$('sw-report');
  if(wrap) wrap.classList.remove('has-value');
  closeLov('report');
  if(reportDataCache.length) renderReport(reportDataCache, reportType, $('report-body'));
}

function renderReport(data,type,el){
  if(!data||!data.length){ el.innerHTML='<div class="empty"><p>No data for the selected period</p></div>'; return; }
  let html='<div style="overflow-x:auto">';

  if(type==='sales'){
    const totRev=data.reduce((s,r)=>s+parseFloat(r.net_revenue||r.total_revenue||0),0);
    const totOrd=data.reduce((s,r)=>s+parseInt(r.total_orders||r.order_count||0),0);
    const totDisc=data.reduce((s,r)=>s+parseFloat(r.total_discounts||0),0);
    html+=`<div class="stats-row">
      <div class="stat-item"><div class="stat-value">${E(totRev)}</div><div class="stat-label">Net Revenue</div></div>
      <div class="stat-item"><div class="stat-value">${totOrd}</div><div class="stat-label">Total Orders</div></div>
      <div class="stat-item"><div class="stat-value">${totOrd?E(totRev/totOrd):'—'}</div><div class="stat-label">Avg Order Value</div></div>
      <div class="stat-item"><div class="stat-value">${E(totDisc)}</div><div class="stat-label">Discounts</div></div>
      <div class="stat-item"><div class="stat-value">${data.length}</div><div class="stat-label">Active Days</div></div>
    </div>`;
    html+=`<table class="data-table"><thead><tr>
      <th>Date</th><th>Orders</th><th>Completed</th><th>Cancelled</th>
      <th>Gross Sales</th><th>Discounts</th><th>Delivery Fees</th><th>Net Revenue</th><th>Channel</th><th>Type</th>
    </tr></thead><tbody>`;
    html+=data.map(r=>`<tr>
      <td style="font-weight:600">${esc(r.sale_date||r.date||'')}</td>
      <td>${r.total_orders||r.order_count||0}</td>
      <td style="color:var(--green);font-weight:600">${r.completed_orders||0}</td>
      <td style="color:var(--red)">${r.cancelled_orders||0}</td>
      <td>${E(r.gross_sales||0)}</td>
      <td style="color:var(--orange)">${E(r.total_discounts||0)}</td>
      <td>${E(r.delivery_revenue||0)}</td>
      <td style="font-family:'Nunito',sans-serif;font-weight:900">${E(r.net_revenue||r.total_revenue||0)}</td>
      <td style="color:var(--muted);font-size:12px">${esc(r.channel||'—')}</td>
      <td style="color:var(--muted);font-size:12px">${esc(r.order_type||'—')}</td>
    </tr>`).join('');

  }else if(type==='products'){
    const totUnits=data.reduce((s,r)=>s+parseInt(r.units_sold||r.total_qty||0),0);
    const totRev=data.reduce((s,r)=>s+parseFloat(r.total_revenue||0),0);
    html+=`<div class="stats-row">
      <div class="stat-item"><div class="stat-value">${data.length}</div><div class="stat-label">Products Sold</div></div>
      <div class="stat-item"><div class="stat-value">${totUnits}</div><div class="stat-label">Units Sold</div></div>
      <div class="stat-item"><div class="stat-value">${E(totRev)}</div><div class="stat-label">Total Revenue</div></div>
    </div>`;
    html+=`<table class="data-table"><thead><tr>
      <th>#</th><th>Product</th><th>Category</th><th>Units Sold</th><th>Orders</th><th>Avg Price</th><th>Revenue</th><th>Revenue %</th>
    </tr></thead><tbody>`;
    html+=data.map((r,i)=>{
      const pct=totRev>0?((parseFloat(r.total_revenue||0)/totRev)*100).toFixed(1):0;
      return `<tr>
        <td style="color:var(--muted);font-size:11px">${i+1}</td>
        <td><strong>${esc(r.product_name||r.name||'')}</strong></td>
        <td><span class="badge b-${(r.category_type||'').toLowerCase()}" style="font-size:10px">${esc(r.category_name||'')}</span></td>
        <td style="font-weight:600">${r.units_sold||r.total_qty||0}</td>
        <td>${r.order_count||0}</td>
        <td style="color:var(--muted)">${E(r.avg_price||0)}</td>
        <td style="font-family:'Nunito',sans-serif;font-weight:900">${E(r.total_revenue||0)}</td>
        <td>
          <div style="display:flex;align-items:center;gap:6px">
            <div style="width:60px;height:6px;background:#F0EFEB;border-radius:3px;overflow:hidden">
              <div style="width:${pct}%;height:100%;background:var(--gold);border-radius:3px"></div>
            </div>
            <span style="font-size:11px;color:var(--muted)">${pct}%</span>
          </div>
        </td>
      </tr>`;
    }).join('');

  }else if(type==='customers'){
    const totSpent=data.reduce((s,r)=>s+parseFloat(r.total_spent||0),0);
    const active=data.filter(r=>r.order_count>0).length;
    html+=`<div class="stats-row">
      <div class="stat-item"><div class="stat-value">${data.length}</div><div class="stat-label">Total Customers</div></div>
      <div class="stat-item"><div class="stat-value">${active}</div><div class="stat-label">Active Buyers</div></div>
      <div class="stat-item"><div class="stat-value">${E(totSpent)}</div><div class="stat-label">Total Spent</div></div>
    </div>`;
    html+=`<table class="data-table"><thead><tr>
      <th>#</th><th>Customer</th><th>Phone</th><th>Orders</th><th>Total Spent</th><th>Avg Order</th><th>Last Order</th><th>Member Since</th><th>Status</th>
    </tr></thead><tbody>`;
    html+=data.map((r,i)=>`<tr>
      <td style="color:var(--muted);font-size:11px">${i+1}</td>
      <td><strong>${esc(r.full_name||'')}</strong></td>
      <td style="font-size:12px;color:var(--muted)">${esc(r.phone||'—')}</td>
      <td>${r.order_count||0}</td>
      <td style="font-family:'Nunito',sans-serif;font-weight:900">${E(r.total_spent||0)}</td>
      <td style="color:var(--muted)">${r.order_count>0?E((r.total_spent||0)/r.order_count):'—'}</td>
      <td style="font-size:12px;color:var(--muted)">${r.last_order_at?fmtDateShort(r.last_order_at):'Never'}</td>
      <td style="font-size:12px;color:var(--muted)">${r.member_since?fmtDateShort(r.member_since):'—'}</td>
      <td>${badgeHtml(r.status||'active')}</td>
    </tr>`).join('');

  }else if(type==='drivers'){
    html+=`<div class="stats-row">
      <div class="stat-item"><div class="stat-value">${data.length}</div><div class="stat-label">Active Drivers</div></div>
      <div class="stat-item"><div class="stat-value">${data.reduce((s,r)=>s+parseInt(r.completed||0),0)}</div><div class="stat-label">Completed Deliveries</div></div>
      <div class="stat-item"><div class="stat-value">${data.reduce((s,r)=>s+parseInt(r.failed||0),0)}</div><div class="stat-label">Failed</div></div>
    </div>`;
    html+=`<table class="data-table"><thead><tr>
      <th>Driver</th><th>Phone</th><th>Assigned</th><th>Completed</th><th>Failed</th><th>Completion Rate</th><th>Avg Delivery Time</th>
    </tr></thead><tbody>`;
    html+=data.map(r=>{
      const rate=r.total_assigned>0?Math.round((r.completed/r.total_assigned)*100):0;
      const mins=r.avg_delivery_min?Math.round(r.avg_delivery_min):null;
      return `<tr>
        <td><strong>${esc(r.driver_name||r.full_name||'')}</strong></td>
        <td style="font-size:12px;color:var(--muted)">${esc(r.driver_phone||r.phone||'—')}</td>
        <td>${r.total_assigned||0}</td>
        <td style="color:var(--green);font-weight:600">${r.completed||0}</td>
        <td style="color:var(--red)">${r.failed||0}</td>
        <td>
          <div style="display:flex;align-items:center;gap:6px">
            <div style="width:60px;height:6px;background:#F0EFEB;border-radius:3px;overflow:hidden">
              <div style="width:${rate}%;height:100%;background:var(--green);border-radius:3px"></div>
            </div>
            <span style="font-size:12px;font-weight:600;color:${rate>=80?'var(--green)':rate>=50?'var(--orange)':'var(--red)'}">${rate}%</span>
          </div>
        </td>
        <td style="color:var(--muted)">${mins!==null?mins+' min':'—'}</td>
      </tr>`;
    }).join('');

  }else if(type==='cashiers'){
    html+=`<div class="stats-row">
      <div class="stat-item"><div class="stat-value">${data.length}</div><div class="stat-label">Records</div></div>
      <div class="stat-item"><div class="stat-value">${data.reduce((s,r)=>s+parseInt(r.orders_processed||r.order_count||0),0)}</div><div class="stat-label">Total Orders</div></div>
      <div class="stat-item"><div class="stat-value">${E(data.reduce((s,r)=>s+parseFloat(r.total_sales||0),0))}</div><div class="stat-label">Total Sales</div></div>
    </div>`;
    html+=`<table class="data-table"><thead><tr>
      <th>Cashier</th><th>Work Date</th><th>Orders Processed</th><th>Cash Orders</th><th>Total Sales</th><th>Avg per Order</th>
    </tr></thead><tbody>`;
    html+=data.map(r=>{
      const orders=parseInt(r.orders_processed||r.order_count||0);
      const sales=parseFloat(r.total_sales||0);
      return `<tr>
        <td><strong>${esc(r.cashier_name||r.full_name||'')}</strong></td>
        <td style="font-size:12px;color:var(--muted)">${esc(r.work_date||'—')}</td>
        <td>${orders}</td>
        <td style="color:var(--muted)">${r.cash_orders||0}</td>
        <td style="font-family:'Nunito',sans-serif;font-weight:900">${E(sales)}</td>
        <td style="color:var(--muted)">${orders>0?E(sales/orders):'—'}</td>
      </tr>`;
    }).join('');

  }else if(type==='payment'){
    // Method breakdown mini-summary
    const methods={};
    data.forEach(r=>{
      const m=r.method||r.payment_method||'Unknown';
      if(!methods[m]) methods[m]={count:0,total:0};
      methods[m].count++; methods[m].total+=parseFloat(r.amount||0);
    });
    const paid=data.filter(r=>(r.payment_status||r.status)==='paid').length;
    const totAmt=data.reduce((s,r)=>s+parseFloat(r.amount||0),0);
    html+=`<div class="stats-row">
      <div class="stat-item"><div class="stat-value">${E(totAmt)}</div><div class="stat-label">Total Collected</div></div>
      <div class="stat-item"><div class="stat-value">${paid}</div><div class="stat-label">Paid Transactions</div></div>
      <div class="stat-item"><div class="stat-value">${data.length-paid}</div><div class="stat-label">Pending / Other</div></div>
    </div>`;
    // Method breakdown pills
    html+=`<div style="display:flex;gap:10px;flex-wrap:wrap;padding:0 0 16px">`;
    Object.entries(methods).forEach(([m,v])=>{
      html+=`<div style="background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:8px 14px">
        <div style="font-size:11px;color:var(--muted);font-weight:600">${esc(m)}</div>
        <div style="font-family:'Nunito',sans-serif;font-weight:900;font-size:15px">${E(v.total)}</div>
        <div style="font-size:11px;color:var(--muted)">${v.count} txn</div>
      </div>`;
    });
    html+=`</div>`;
    html+=`<table class="data-table"><thead><tr>
      <th>Order #</th><th>Customer</th><th>Method</th><th>Amount</th><th>Reference</th><th>Receipt</th><th>Status</th><th>Date</th>
    </tr></thead><tbody>`;
    html+=data.map(r=>`<tr>
      <td class="td-order">${esc(r.order_ref||'—')}</td>
      <td>${esc(r.customer_name||'Walk-in')}</td>
      <td><span class="badge" style="background:#F1F5F9;color:#475569">${esc(r.method||'—')}</span></td>
      <td style="font-family:'Nunito',sans-serif;font-weight:900">${E(r.amount||0)}</td>
      <td class="td-mono" style="font-size:11px;color:var(--muted)">${esc(r.reference_no||'—')}</td>
      <td class="td-mono" style="font-size:11px;color:var(--muted)">${esc(r.receipt_no||'—')}</td>
      <td>${badgeHtml(r.payment_status||'pending')}</td>
      <td style="font-size:12px;color:var(--muted)">${fmtDateShort(r.paid_at||r.created_at)}</td>
    </tr>`).join('');

  }else{
    html+=`<table class="data-table"><thead><tr><th>Product</th><th>Category</th><th>Stock</th><th>Threshold</th><th>Status</th></tr></thead><tbody>`;
    html+=data.map(r=>`<tr>
      <td><strong>${esc(r.name||'')}</strong></td>
      <td style="color:var(--muted);font-size:12px">${esc(r.category_name||'')}</td>
      <td style="font-weight:700;color:var(--${r.stock_qty===0?'red':'orange'})">${r.stock_qty}</td>
      <td style="color:var(--muted)">${r.low_stock_threshold||0}</td>
      <td>${badgeHtml(r.stock_qty===0?'out':'low')}</td>
    </tr>`).join('');
  }

  html+='</tbody></table></div>';
  el.innerHTML=html;
}

// ── CSV Export ────────────────────────────────────────────────
function exportReportCSV(){
  if(!reportDataCache.length) return;
  const rows=reportDataCache;
  const headers=Object.keys(rows[0]);
  const csv=[headers.join(','),...rows.map(r=>headers.map(h=>{
    const v=r[h]===null||r[h]===undefined?'':String(r[h]);
    return v.includes(',')||v.includes('"')||v.includes('\n')?`"${v.replace(/"/g,'""')}"`:v;
  }).join(','))].join('\n');
  const a=document.createElement('a');
  a.href='data:text/csv;charset=utf-8,'+encodeURIComponent(csv);
  const from=$('rep-from').value||'';
  const to=$('rep-to').value||'';
  a.download=`report_${reportType}_${from}_to_${to}.csv`;
  a.click();
}

// ── Print / Save as PDF ───────────────────────────────────────
function printReport(){
  const from=$('rep-from').value||'';
  const to=$('rep-to').value||'';
  const typeLabel={
    sales:'Sales Report', products:'Products Report', customers:'Customers Report',
    drivers:'Driver Performance', cashiers:'Cashier Performance', payment:'Payments Report'
  }[reportType]||'Report';
  const dateLabel=from&&to?`${from}  →  ${to}`:(from||to||'All dates');

  // Grab the KPI cards HTML (if visible)
  const kpiEl=$('rep-kpi-row');
  const kpiHtml=(kpiEl&&kpiEl.style.display!=='none')?kpiEl.outerHTML:'';

  // Grab the chart as a static image (if visible)
  const chartArea=$('rep-chart-area');
  let chartImgHtml='';
  if(chartArea&&chartArea.style.display!=='none'){
    const canvas=$('rep-chart');
    if(canvas) chartImgHtml=`<img src="${canvas.toDataURL('image/png')}" style="width:100%;max-height:260px;object-fit:contain;margin-bottom:20px;border-radius:8px">`;
  }

  // Grab the table
  const bodyEl=$('report-body');
  const bodyHtml=bodyEl?bodyEl.innerHTML:'';

  const win=window.open('','_blank','width=1000,height=800');
  win.document.write(`<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>${typeLabel} — Hakuna Matata</title>
<style>
  *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
  body{font-family:'Segoe UI',Arial,sans-serif;background:#fff;color:#2E2E2E;font-size:13px}
  /* ── Print header ── */
  .print-header{display:flex;align-items:center;justify-content:space-between;
    padding:18px 32px;background:#252525;color:#fff;margin-bottom:24px}
  .print-header-left{display:flex;align-items:center;gap:14px}
  .print-logo{width:48px;height:48px;background:#fff;border-radius:10px;
    display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0}
  .print-logo img{width:100%;height:100%;object-fit:cover}
  .print-brand{font-size:20px;font-weight:900;letter-spacing:-.3px}
  .print-brand em{font-style:normal;color:#F5A623}
  .print-sub{font-size:11px;color:rgba(255,255,255,.5);margin-top:2px}
  .print-meta{text-align:right}
  .print-report-type{font-size:16px;font-weight:700;color:#F5A623}
  .print-date-range{font-size:11px;color:rgba(255,255,255,.6);margin-top:3px}
  .print-generated{font-size:10px;color:rgba(255,255,255,.35);margin-top:2px}
  /* ── KPI cards ── */
  .kpi-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:12px;
    padding:0 32px 20px;page-break-inside:avoid}
  .kpi-card{border:1px solid #E6E5E1;border-radius:10px;padding:14px 16px;position:relative;overflow:hidden}
  .kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px}
  .kpi-card.gold::before{background:#F5A623}.kpi-card.green::before{background:#22C55E}
  .kpi-card.orange::before{background:#F97316}.kpi-card.blue::before{background:#3B82F6}
  .kpi-card.red::before{background:#E8394D}
  .kpi-icon{display:none}
  .kpi-value{font-size:20px;font-weight:900;line-height:1.1;margin-bottom:3px}
  .kpi-label{font-size:10px;color:#9A9993;font-weight:500;text-transform:uppercase;letter-spacing:.4px}
  .kpi-sub{font-size:10px;color:#9A9993;margin-top:4px}
  /* ── Body ── */
  .print-body{padding:0 32px 32px}
  /* ── Stats row ── */
  .stats-row{display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:20px;page-break-inside:avoid}
  .stat-item{text-align:center;padding:12px;background:#F7F6F3;border-radius:8px;border:1px solid #E6E5E1}
  .stat-value{font-size:18px;font-weight:900}
  .stat-label{font-size:10px;color:#9A9993;margin-top:3px;text-transform:uppercase;letter-spacing:.4px}
  /* ── Table ── */
  .data-table{width:100%;border-collapse:collapse;font-size:11px;margin-top:4px}
  .data-table th{padding:7px 10px;background:#F7F6F3;color:#9A9993;font-weight:700;font-size:10px;
    text-transform:uppercase;letter-spacing:.4px;border-bottom:1.5px solid #E6E5E1;text-align:left;white-space:nowrap}
  .data-table td{padding:8px 10px;border-bottom:1px solid #F0EFEB;vertical-align:middle}
  .data-table tbody tr:nth-child(even) td{background:#FAFAF8}
  .td-order{font-weight:800;font-size:12px}
  .badge{display:inline-flex;align-items:center;padding:2px 8px;border-radius:10px;
    font-size:10px;font-weight:700;text-transform:capitalize}
  .b-paid,.b-completed,.b-delivered,.b-active{background:#F0FDF4;color:#166534}
  .b-pending{background:#FFF7ED;color:#C2410C}
  .b-cancelled,.b-failed,.b-out{background:#FFF1F2;color:#BE123C}
  .b-low{background:#FFFBEB;color:#B45309}
  .b-confirmed{background:#EFF6FF;color:#1D4ED8}
  /* method breakdown pills */
  .method-pills{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px}
  .method-pill{background:#F7F6F3;border:1px solid #E6E5E1;border-radius:8px;padding:6px 12px}
  .method-pill-label{font-size:10px;color:#9A9993;font-weight:600}
  .method-pill-value{font-size:14px;font-weight:900}
  .method-pill-sub{font-size:10px;color:#9A9993}
  /* revenue bar */
  .rev-bar-wrap{display:flex;align-items:center;gap:6px}
  .rev-bar{width:60px;height:5px;background:#E6E5E1;border-radius:3px;overflow:hidden;display:inline-block}
  .rev-bar-fill{height:100%;background:#F5A623;border-radius:3px}
  /* completion bar */
  .comp-bar-wrap{display:flex;align-items:center;gap:6px}
  .comp-bar{width:50px;height:5px;background:#E6E5E1;border-radius:3px;overflow:hidden;display:inline-block}
  /* footer ── */
  .print-footer{margin-top:32px;padding:14px 32px;border-top:1px solid #E6E5E1;
    display:flex;justify-content:space-between;font-size:10px;color:#9A9993}
  /* print media ── */
  @media print{
    body{-webkit-print-color-adjust:exact;print-color-adjust:exact}
    .no-print{display:none!important}
    .print-header{-webkit-print-color-adjust:exact;print-color-adjust:exact}
    .kpi-card::before{-webkit-print-color-adjust:exact;print-color-adjust:exact}
    @page{margin:0;size:A4 landscape}
  }
  /* ── print button (screen only) ── */
  .print-action-bar{
    position:sticky;top:0;z-index:999;background:#F7F6F3;
    border-bottom:1px solid #E6E5E1;padding:10px 32px;
    display:flex;align-items:center;justify-content:flex-end;gap:8px
  }
  .btn-print{
    display:inline-flex;align-items:center;gap:6px;padding:8px 18px;
    border-radius:20px;border:none;background:#252525;color:#fff;
    font-size:13px;font-weight:600;cursor:pointer;font-family:inherit
  }
  .btn-print:hover{background:#363636}
  .btn-close{
    display:inline-flex;align-items:center;gap:6px;padding:8px 14px;
    border-radius:20px;border:1.5px solid #E6E5E1;background:transparent;
    color:#2E2E2E;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit
  }
</style>
</head>
<body>
<div class="print-action-bar no-print">
  <button class="btn-close" onclick="window.close()">✕ Close</button>
  <button class="btn-print" onclick="window.print()">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
    Print / Save as PDF
  </button>
</div>

<div class="print-header">
  <div class="print-header-left">
    <div class="print-logo"><img src="hakuna matata.png" onerror="this.style.display='none'"></div>
    <div>
      <div class="print-brand">Hakuna <em>Matata</em></div>
      <div class="print-sub">Online Point of Sale</div>
    </div>
  </div>
  <div class="print-meta">
    <div class="print-report-type">${typeLabel}</div>
    <div class="print-date-range">${dateLabel}</div>
    <div class="print-generated">Generated: ${new Date().toLocaleString()}</div>
  </div>
</div>

${kpiHtml}

<div class="print-body">
${chartImgHtml}
${bodyHtml}
</div>

<div class="print-footer">
  <span>Hakuna Matata POS — Confidential</span>
  <span>${typeLabel} · ${dateLabel}</span>
  <span>Printed: ${new Date().toLocaleString()}</span>
</div>
</body>
</html>`);
  win.document.close();
  win.focus();
}



// ── CUSTOMERS ────────────────────────────────────────────────
let customersPage=1;
let _allCustomers=[];

async function loadCustomers(page=1){
  const filter=$('cust-status').value;
  const search=$('cust-search').value.toLowerCase().trim();

  // Fetch fresh from API on page 1 or when cache is empty
  if(page===1 || !_allCustomers.length){
    $('customers-tbody').innerHTML='<tr><td colspan="8"><div class="empty"><p>Loading...</p></div></td></tr>';
    // Build API URL — pass status filter directly to the server
    const statusQs=filter?`&status=${filter}`:'';
    const r1=await authFetch(`/users?page=1&per_page=100&role=customer${statusQs}`);
    if(!r1){ $('customers-tbody').innerHTML='<tr><td colspan="8"><div class="empty"><p>Network error — check console</p></div></td></tr>'; return; }
    let d1;
    try{ d1=await r1.json(); } catch(e){ $('customers-tbody').innerHTML='<tr><td colspan="8"><div class="empty"><p>Invalid JSON from API — check console</p></div></td></tr>'; console.error('JSON parse error',e); return; }
    if(!d1.success){ $('customers-tbody').innerHTML='<tr><td colspan="8"><div class="empty"><p>API error: '+esc(d1.error||JSON.stringify(d1))+'</p></div></td></tr>'; return; }
    const page1Rows=(d1.data&&d1.data.data)||[];
    const totalPages=((d1.data&&d1.data.pagination)||{}).last_page||1;
    let allRows=[...page1Rows];
    if(totalPages>1){
      const extra=[];
      for(let p2=2;p2<=Math.min(totalPages,10);p2++) extra.push(authFetch(`/users?page=${p2}&per_page=100&role=customer${statusQs}`));
      const results=await Promise.all(extra);
      for(const rx of results){
        if(!rx) continue;
        const dx=await rx.json();
        if(dx.success) allRows=allRows.concat(dx.data.data||[]);
      }
    }
    _allCustomers=allRows;
    customersPage=1; page=1;
  }

  let rows=_allCustomers;

  // Search filter (client-side)
  if(search) rows=rows.filter(u=>(u.full_name+' '+(u.phone||'')).toLowerCase().includes(search));

  if(!rows.length){
    $('customers-tbody').innerHTML='<tr><td colspan="8"><div class="empty"><p>No customers found</p></div></td></tr>';
    $('customers-pager').innerHTML='';
    return;
  }

  // Client-side pagination
  const perPage=20, total=rows.length, totalPages=Math.ceil(total/perPage);
  const pageRows=rows.slice((page-1)*perPage, page*perPage);

  $('customers-tbody').innerHTML=pageRows.map(u=>`<tr>
    <td><strong>${esc(u.full_name)}</strong></td>
    <td style="font-size:12px;color:var(--muted)">${esc(u.phone||'—')}</td>
    <td style="font-weight:700">${parseInt(u.order_count||0)}</td>
    <td style="font-family:'Nunito',sans-serif;font-weight:900">${E(u.total_spent||0)}</td>
    <td>${badgeHtml(u.status)}</td>
    <td style="font-size:12px;color:var(--muted)">${u.created_at?fmtDateShort(u.created_at):'—'}</td>
  </tr>`).join('');
  renderPager('customers-pager',{page,total_pages:totalPages},loadCustomers);
}

function openCustomerModal(id=null){
  $('cust-modal-title').textContent=id?'Edit Customer':'Add Customer';
  $('cust-id').value=''; $('cust-firstname').value=''; $('cust-lastname').value=''; $('cust-phone').value=''; $('cust-password').value='';
  $('cust-pass-group').style.display=id?'none':'block';
  if(id) loadCustomerForEdit(id);
  openModal('customer-modal');
}
async function loadCustomerForEdit(id){
  const r=await authFetch('/users/'+id); if(!r) return;
  const d=await r.json(); if(!d.success) return;
  $('cust-id').value=id; $('cust-firstname').value=d.data.first_name||''; $('cust-lastname').value=d.data.last_name||''; $('cust-phone').value=d.data.phone||''; $('cust-pass-group').style.display='none';
}
async function saveCustomer(){
  const id=$('cust-id').value;
  const firstName=$('cust-firstname').value.trim();
  const lastName=$('cust-lastname').value.trim();
  const phone=$('cust-phone').value.trim();
  const pass=$('cust-password').value;
  if(!firstName||!lastName){ showToast('First and last name are required','error'); return; }
  if(!/^\d{11}$/.test(phone)){ showToast('Enter 11-digit phone e.g. 26876000001','error'); return; }
  if(!id&&!pass){ showToast('Password required for new customers','error'); return; }
  const payload={first_name:firstName, last_name:lastName, phone:phone, role:'customer'};
  if(pass) payload.password=pass;
  const tok=localStorage.getItem('access_token');
  const opts={method:id?'PUT':'POST',headers:{'Content-Type':'application/json','Authorization':'Bearer '+tok},body:JSON.stringify(payload)};
  const r=await fetch(API+(id?'/users/'+id:'/users'),opts);
  if(!r){ showToast('Request failed','error'); return; }
  const d=await r.json();
  if(d.success){ showToast(id?'Customer updated':'Customer created','success'); closeModal('customer-modal'); loadCustomers(customersPage); }
  else showToast(d.error||'Save failed','error');
}

// ── STAFF (Cashiers + Drivers unified) ───────────────────────
let staffPage=1, staffRole='';

function filterStaff(role, btn){
  document.querySelectorAll('#panel-staff .stab').forEach(b=>b.classList.remove('active'));
  btn.classList.add('active');
  staffRole=role;
  // Update add-button label
  const lbl=$('add-staff-label');
  if(lbl) lbl.textContent = role==='driver'?'Add Driver':'Add Cashier';
  const addBtn=$('add-staff-btn');
  if(addBtn) addBtn.style.display = role?'':'none';
  loadStaff();
}

async function loadStaff(page=1){
  staffPage=page;
  const status=$('staff-status').value, search=$('staff-search').value;
  $('staff-tbody').innerHTML='<tr><td colspan="9"><div class="empty"><p>Loading...</p></div></td></tr>';
  let rows=[];

  if(staffRole===''){
    // All Staff: fetch cashiers + drivers separately (API only accepts single role)
    const statusQs = status ? `&status=${status}` : '';
    const searchQs = search ? `&search=${encodeURIComponent(search)}` : '';
    const [rC, rD] = await Promise.all([
      authFetch(`/users?role=cashier&per_page=100${statusQs}${searchQs}`),
      authFetch(`/users?role=driver&per_page=100${statusQs}${searchQs}`)
    ]);
    if(!rC || !rD) return;
    const [dC, dD] = await Promise.all([rC.json(), rD.json()]);
    const cashiers = dC.success ? (dC.data.data||dC.data||[]) : [];
    const drivers  = dD.success ? (dD.data.data||dD.data||[]) : [];
    rows = [...cashiers, ...drivers].sort((a,b)=>new Date(b.created_at)-new Date(a.created_at));
    $('staff-pager').innerHTML='';
  } else {
    // Single role: cashier or driver
    let qs=`?page=${page}&per_page=20&role=${staffRole}`;
    if(status) qs+=`&status=${status}`;
    if(search) qs+=`&search=${encodeURIComponent(search)}`;
    const r=await authFetch('/users'+qs); if(!r) return;
    const d=await r.json(); if(!d.success) return;
    rows=(d.data.data||d.data||[]);
    const _sp=d.data.pagination||{}; renderPager('staff-pager',{page:_sp.current_page||1,total_pages:_sp.last_page||1},loadStaff);
  }

  if(!rows.length){ $('staff-tbody').innerHTML='<tr><td colspan="9"><div class="empty"><p>No staff found</p></div></td></tr>'; return; }
  $('staff-tbody').innerHTML=rows.map(u=>{
    const isCashier=u.role==='cashier';
    const avatarBg = isCashier?'var(--gold-light)':'rgba(34,197,94,.1)';
    const avatarColor = isCashier?'var(--gold)':'var(--green)';
    const avatar = u.avatar_url
      ? `<img src="${esc(u.avatar_url)}" style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:2px solid var(--border)">`
      : `<div style="width:36px;height:36px;border-radius:50%;background:${avatarBg};display:flex;align-items:center;justify-content:center;font-weight:800;color:${avatarColor};font-size:13px">${esc(u.full_name.charAt(0))}</div>`;
    // Activity column: orders for cashier, deliveries for driver
    const activity = isCashier
      ? `<span style="font-weight:700">${u.order_count||0}</span> <span style="font-size:11px;color:var(--muted)">orders</span>`
      : `<span style="font-weight:700">${u.total_deliveries||0}</span> <span style="font-size:11px;color:var(--muted)">trips</span>`;
    // Performance: total sales for cashier, completed deliveries for driver
    const perf = isCashier
      ? `<span style="font-family:'Nunito',sans-serif;font-weight:900">${E(u.total_sales||0)}</span>`
      : `<span class="badge b-${u.completed_deliveries>0?'active':'inactive'}">${u.completed_deliveries||0} done</span>`;
    return `<tr>
      <td>${avatar}</td>
      <td><strong>${esc(u.full_name)}</strong></td>
      
      <td style="font-size:12px;color:var(--muted)">${esc(u.phone||'—')}</td>
      <td>${badgeHtml(u.role)}</td>
      <td>${activity}</td>
      <td>${perf}</td>
      <td>${badgeHtml(u.status)}</td>
      <td style="font-size:12px;color:var(--muted)">${u.last_login_at?fmtDateShort(u.last_login_at):'Never'}</td>
      <td class="action-btns">
        <button class="btn btn-outline btn-sm" onclick="openStaffModal('${u.role}',${u.user_id})">Edit</button>
        <button class="btn btn-${u.status==='suspended'?'green':'red'} btn-sm" onclick="toggleUserStatus(${u.user_id},'${u.status==='suspended'?'active':'suspended'}')">${u.status==='suspended'?'Activate':'Suspend'}</button>
      </td>
    </tr>`;
  }).join('');
}

// Keep legacy loaders as aliases so any other references still work
function loadCashiers(p=1){ staffRole='cashier'; loadStaff(p); }
function loadDrivers(p=1){ staffRole='driver'; loadStaff(p); }



// ── STAFF MODAL (shared cashier/driver) ──────────────────────
function openStaffModal(role='cashier', id=null){
  $('staff-modal-title').textContent=(id?'Edit ':'Add ')+(role==='cashier'?'Cashier':'Driver');
  $('staff-id').value=''; $('staff-role-val').value=role;
  $('staff-firstname').value=''; $('staff-lastname').value='';
  $('staff-phone').value=''; $('staff-password').value='';
  $('err-staff-phone').classList.remove('show');
  $('err-staff-pass').classList.remove('show');
  $('err-staff-pass').textContent='';
  // Show/hide password required asterisk (not required on edit)
  const passReq=$('staff-pass-group').querySelector('label .req');
  if(passReq) passReq.style.display=id?'none':'inline';
  clearStaffImg({stopPropagation:()=>{}});
  $('staff-pass-group').style.display=id?'none':'block';
  if(id) loadStaffForEdit(id);
  openModal('staff-modal');
}
async function loadStaffForEdit(id){
  const r=await authFetch('/users/'+id); if(!r) return;
  const d=await r.json(); if(!d.success) return;
  $('staff-id').value=id;
  $('staff-firstname').value=d.data.first_name||'';
  $('staff-lastname').value=d.data.last_name||'';
  $('staff-phone').value=d.data.phone||'';
  // Hide password field on edit (not required)
  $('staff-pass-group').style.display='none';
  if(d.data.avatar_url){ const img=$('staff-img-preview'); img.src=d.data.avatar_url; img.classList.add('show'); $('staff-img-clear').classList.add('show'); $('staff-img-hint').style.display='none'; }
}
function staffPasswordErrors(pass){
  const e=[];
  if(pass.length<8)            e.push('At least 8 characters');
  if(!/[A-Z]/.test(pass))      e.push('At least one uppercase letter');
  if(!/[a-z]/.test(pass))      e.push('At least one lowercase letter');
  if(!/[0-9]/.test(pass))      e.push('At least one number');
  if(!/[^A-Za-z0-9]/.test(pass)) e.push('At least one special character (!@#$%...)');
  return e;
}
async function saveStaff(){
  const id=$('staff-id').value.trim(), role=$('staff-role-val').value;
  const firstName=$('staff-firstname').value.trim();
  const lastName=$('staff-lastname').value.trim();
  const phone=$('staff-phone').value.trim();
  const pass=$('staff-password').value;
  let hasError=false;

  // Clear previous errors
  $('err-staff-phone').classList.remove('show');
  $('err-staff-pass').classList.remove('show');
  $('err-staff-pass').textContent='';

  if(!firstName){ showToast('First name is required','error'); $('staff-firstname').focus(); return; }
  if(!lastName){  showToast('Last name is required','error');  $('staff-lastname').focus();  return; }

  // Phone: must be exactly 11 digits, no + or spaces
  if(!/^\d{11}$/.test(phone)){
    $('err-staff-phone').classList.add('show');
    $('staff-phone').focus();
    hasError=true;
  }

  // Password: required on new, validated if provided
  if(!id && !pass){
    $('err-staff-pass').textContent='Password is required';
    $('err-staff-pass').classList.add('show');
    hasError=true;
  } else if(pass){
    const pwdErrs=staffPasswordErrors(pass);
    if(pwdErrs.length){
      $('err-staff-pass').textContent=pwdErrs.join(' · ');
      $('err-staff-pass').classList.add('show');
      hasError=true;
    }
  }

  if(hasError) return;

  // Send as JSON to match API body() which reads php://input
  const payload={
    first_name: firstName,
    last_name:  lastName,
    phone:      phone,
    role:       role,
  };
  if(pass) payload.password=pass;

  // Handle photo as base64 if selected
  const photoFile=$('staff-photo').files[0];
  if(photoFile){
    payload.photo_base64 = await new Promise(res=>{
      const r=new FileReader();
      r.onload=e=>res(e.target.result.split(',')[1]);
      r.readAsDataURL(photoFile);
    });
    payload.photo_mime=photoFile.type;
  }

  const tok=localStorage.getItem('access_token');
  const opts={method:id?'PUT':'POST',headers:{'Content-Type':'application/json','Authorization':'Bearer '+tok},body:JSON.stringify(payload)};
  const r=await fetch(API+(id?'/users/'+id:'/users'),opts);
  if(!r){ showToast('Request failed','error'); return; }
  const d=await r.json();
  if(d.success){
    showToast(id?(role==='cashier'?'Cashier':'Driver')+' updated':(role==='cashier'?'Cashier':'Driver')+' created','success');
    closeModal('staff-modal');
    loadStaff(staffPage);
  } else showToast(d.error||'Save failed','error');
}
function clearStaffImg(e){
  e.stopPropagation();
  $('staff-photo').value=''; $('staff-img-preview').src=''; $('staff-img-preview').classList.remove('show');
  $('staff-img-clear').classList.remove('show'); $('staff-img-hint').style.display='';
}

// ── PAYMENT ──────────────────────────────────────────────────
let paymentsPage=1;
async function loadPayments(page=1){
  paymentsPage=page;
  const status=$('pay-status').value, method=$('pay-method').value,
        from=$('pay-from').value, to=$('pay-to').value,
        search=$('pay-search').value;
  let qs=`?page=${page}&per_page=20`;
  if(status) qs+=`&status=${status}`;
  if(method) qs+=`&method=${method}`;
  if(from)   qs+=`&date_from=${from}`;
  if(to)     qs+=`&date_to=${to}`;
  if(search) qs+=`&ref=${encodeURIComponent(search)}`;
  $('payments-tbody').innerHTML='<tr><td colspan="8"><div class="empty"><p>Loading...</p></div></td></tr>';
  // Try dedicated payments endpoint; fall back to orders with payment data
  let r=await authFetch('/payments'+qs);
  if(!r){ $('payments-tbody').innerHTML='<tr><td colspan="8"><div class="empty"><p>Failed to load payments</p></div></td></tr>'; return; }
  const d=await r.json();
  // /payments uses respond(paginate()) — no success field, data+pagination at top level
  if(Array.isArray(d.data)){
    renderPaymentsTable(d.data);
    loadPaymentKPIs(d.data);
    const _pp=d.pagination||{}; renderPager('payments-pager',{page:_pp.current_page||1,total_pages:_pp.last_page||1},loadPayments);
    return;
  }
  // Fallback: load from orders endpoint (also respond(paginate()) — no success)
  r=await authFetch('/orders'+qs.replace('/payments',''));
  if(!r) return;
  const od=await r.json();
  if(!od.data){ $('payments-tbody').innerHTML='<tr><td colspan="8"><div class="empty"><p>No payment data available</p></div></td></tr>'; return; }
  renderPaymentsTable(od.data||[]);
  loadPaymentKPIs(od.data||[]);
  const _op2=od.pagination||{}; renderPager('payments-pager',{page:_op2.current_page||1,total_pages:_op2.last_page||1},loadPayments);
}
function renderPaymentsTable(rows){
  const methodFilter=($('pay-method').value||'').toLowerCase();
  const fromVal=$('pay-from').value;
  const toVal=$('pay-to').value;
  const fromDate=fromVal?new Date(fromVal):null;
  const toDate=toVal?new Date(toVal):null;
  if(toDate) toDate.setHours(23,59,59,999); // include the full to-day
  // Apply client-side method + date filtering — match actual DB enum values:
  // 'Cash', 'Mobile Money', 'Credit Card', 'Bank Transfer'
  let filtered=rows.filter(p=>{
    const m=(p.payment_method||p.method||'').toLowerCase();
    if(!methodFilter) { /* no method filter */ }
    else if(methodFilter==='cash' && m!=='cash') return false;
    else if(methodFilter==='mobile_money' && m!=='mobile money') return false;
    else if(methodFilter==='card' && !['credit card','bank transfer'].includes(m)) return false;
    // Date filter
    const rawDate=p.payment_date||p.created_at;
    if((fromDate||toDate) && rawDate){
      const d=new Date(rawDate);
      if(fromDate && d<fromDate) return false;
      if(toDate   && d>toDate)   return false;
    }
    return true;
  });
  if(!filtered.length){ $('payments-tbody').innerHTML='<tr><td colspan="7"><div class="empty"><p>No payment records found</p></div></td></tr>'; return; }
  $('payments-tbody').innerHTML=filtered.map(p=>{
    const payStatus=p.payment_status||p.status||'pending';
    const method=p.payment_method||p.method||'—';
    const orderRef=p.order_ref||p.order_number||p.ref||'—';
    return `<tr>
      <td><span class="td-order">${esc(orderRef)}</span></td>
      <td>${esc(p.customer_name||p.full_name||'Walk-in')}</td>
      <td style="font-family:'Nunito',sans-serif;font-weight:900">${E(p.grand_total||p.amount||0)}</td>
      <td><span class="badge" style="background:#F1F5F9;color:#475569;text-transform:capitalize">${esc(method)}</span></td>
      <td>${badgeHtml(payStatus)}</td>
      <td style="font-size:12px;color:var(--muted)">${fmtDateShort(p.payment_date||p.created_at)}</td>
      <td class="action-btns">
        <button class="btn btn-outline btn-sm" onclick="openPaymentDetail(${JSON.stringify(p).replace(/"/g,'&quot;')})">View</button>
        ${payStatus==='pending'?`<button class="btn btn-green btn-sm" onclick="markPaymentPaid(${p.payment_id||p.order_id})">Mark Paid</button>`:''}
      </td>
    </tr>`;
  }).join('');
}
function loadPaymentKPIs(rows){
  // Apply the same date filter so KPI totals match the visible table rows
  const fromVal=$('pay-from').value;
  const toVal=$('pay-to').value;
  const fromDate=fromVal?new Date(fromVal):null;
  const toDate=toVal?new Date(toVal):null;
  if(toDate) toDate.setHours(23,59,59,999);
  if(fromDate||toDate){
    rows=rows.filter(p=>{
      const rawDate=p.payment_date||p.created_at;
      if(!rawDate) return true;
      const d=new Date(rawDate);
      if(fromDate && d<fromDate) return false;
      if(toDate   && d>toDate)   return false;
      return true;
    });
  }
  const paid=rows.filter(p=>(p.payment_status||p.status)==='paid'||p.status==='completed'||p.status==='delivered');
  const momo=rows.filter(p=>(p.payment_method||p.method||'').toLowerCase()==='mobile money');
  const cash=rows.filter(p=>(p.payment_method||p.method||'').toLowerCase()==='cash');
  const card=rows.filter(p=>['credit card','bank transfer'].includes((p.payment_method||p.method||'').toLowerCase()));
  const total=rows.reduce((s,p)=>s+parseFloat(p.grand_total||p.amount||0),0);
  const momoTotal=momo.reduce((s,p)=>s+parseFloat(p.grand_total||p.amount||0),0);
  const cashTotal=cash.reduce((s,p)=>s+parseFloat(p.grand_total||p.amount||0),0);
  const cardTotal=card.reduce((s,p)=>s+parseFloat(p.grand_total||p.amount||0),0);
  $('pay-kpi-total').textContent=E(total);
  $('pay-kpi-paid').textContent=paid.length;
  $('pay-kpi-momo').textContent=E(momoTotal);
  $('pay-kpi-cash').textContent=E(cashTotal);
  $('pay-kpi-card').textContent=E(cardTotal);
}
function openPaymentDetail(p){
  if(typeof p==='string') p=JSON.parse(p);
  $('pay-modal-title').textContent='Payment — '+(p.order_ref||p.order_number||p.ref||'Details');
  const payStatus=p.payment_status||p.status||'pending';
  $('pay-modal-body').innerHTML=`
    <div class="od-section">
      <h4>Payment Info</h4>
      <div class="od-row"><span class="od-label">Order #</span><span class="od-value td-order">${esc(p.order_ref||p.order_number||'—')}</span></div>
      <div class="od-row"><span class="od-label">Customer</span><span class="od-value">${esc(p.customer_name||p.full_name||'Walk-in')}</span></div>
      <div class="od-row"><span class="od-label">Method</span><span class="od-value">${esc(p.payment_method||p.method||'—')}</span></div>
      <div class="od-row"><span class="od-label">Reference</span><span class="od-value td-mono">${esc(p.payment_reference||p.transaction_ref||'—')}</span></div>
      <div class="od-row"><span class="od-label">Amount</span><span class="od-total">${E(p.grand_total||p.amount||0)}</span></div>
      <div class="od-row"><span class="od-label">Status</span><span>${badgeHtml(payStatus)}</span></div>
      <div class="od-row"><span class="od-label">Date</span><span class="od-value">${fmtDate(p.payment_date||p.created_at)}</span></div>
    </div>`;
  const footer=$('pay-modal-footer');
  footer.innerHTML=`
    ${payStatus==='pending'?`<button class="btn btn-green btn-sm" onclick="markPaymentPaid(${p.payment_id||p.order_id});closeModal('payment-modal')">Mark as Paid</button>`:''}
    <button class="btn btn-outline btn-sm" onclick="closeModal('payment-modal')">Close</button>`;
  openModal('payment-modal');
}
async function markPaymentPaid(id){
  const r=await authFetch('/payments/'+id+'/mark-paid',{method:'PATCH',body:JSON.stringify({payment_status:'paid'})});
  if(!r) return;
  const d=await r.json();
  if(d.success){ showToast('Payment marked as paid','success'); loadPayments(paymentsPage); }
  else showToast(d.error||'Update failed','error');
}


// ── SMART SEARCH / LOV SYSTEM ───────────────────────────────
const lovDebounceTimers={};
const lovConfigs={
  products:{
    inputId:'prod-search', lovId:'lov-products', wrapperId:'sw-products',
    fetch: async(q)=>{
      // /products → respondOk(fetchProducts()) → success:true, data is flat array
      const r=await authFetch(`/products?per_page=8&search=${encodeURIComponent(q)}`); if(!r) return [];
      const d=await r.json(); return d.success&&Array.isArray(d.data)?d.data:[];
    },
    render:(item)=>{
      const _raw=item.img_url||item.image_url||'';
      const _src=_raw?(_raw.startsWith('http')?_raw:window.location.origin+'/api'+_raw):'';
      return {
        avatar: _src?`<img src="${esc(_src)}">`:`<div style="width:28px;height:28px;border-radius:6px;background:var(--gold-light);display:flex;align-items:center;justify-content:center;font-size:14px">🍽️</div>`,
        name: item.name||'Product',
        sub: (item.category_name||'')+(item.price?' · E '+parseFloat(item.price).toFixed(2):''),
        badge: item.stock_qty===0?`<span class="badge b-out" style="font-size:10px;padding:2px 6px">Out</span>`:item.stock_qty<=(item.low_stock_threshold||5)?`<span class="badge b-low" style="font-size:10px;padding:2px 6px">Low</span>`:''
      };
    },
    select:(item)=>{ $('prod-search').value=item.name||''; closeLov('products'); loadProducts(); }
  },
  customers:{
    inputId:'cust-search', lovId:'lov-customers', wrapperId:'sw-customers',
    fetch: async(q)=>{
      _allCustomers=[];
      const r=await authFetch(`/users?per_page=8&role=customer&search=${encodeURIComponent(q)}`); if(!r) return [];
      const d=await r.json(); if(!d.success) return [];
      return Array.isArray(d.data.data)?d.data.data:(Array.isArray(d.data)?d.data:[]);
    },
    render:(item)=>({
      avatar: item.avatar_url?`<img src="${esc(item.avatar_url)}">`:`<div style="background:var(--gold-light);color:var(--gold);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:11px;width:28px;height:28px;border-radius:50%">${esc((item.full_name||'?').charAt(0))}</div>`,
      name: item.full_name||'Customer',
      sub: item.email||(item.phone||''),
      badge: `<span class="badge b-${(item.status||'active').toLowerCase()}" style="font-size:10px;padding:2px 6px">${esc(item.status||'active')}</span>`
    }),
    select:(item)=>{ $('cust-search').value=item.full_name||''; _allCustomers=[]; closeLov('customers'); loadCustomers(); }
  },
  staff:{
    inputId:'staff-search', lovId:'lov-staff', wrapperId:'sw-staff',
    fetch: async(q)=>{
      const searchQs=`&search=${encodeURIComponent(q)}`;
      // /users → respondOk(paginate()) → success:true, data.data is rows
      if(staffRole){
        const r=await authFetch(`/users?per_page=8&role=${staffRole}${searchQs}`); if(!r) return [];
        const d=await r.json(); if(!d.success) return [];
        return Array.isArray(d.data.data)?d.data.data:(Array.isArray(d.data)?d.data:[]);
      }
      const [rC,rD]=await Promise.all([
        authFetch(`/users?per_page=8&role=cashier${searchQs}`),
        authFetch(`/users?per_page=8&role=driver${searchQs}`)
      ]);
      if(!rC||!rD) return [];
      const [dC,dD]=await Promise.all([rC.json(),rD.json()]);
      const merged=[
        ...(dC.success?(Array.isArray(dC.data.data)?dC.data.data:(Array.isArray(dC.data)?dC.data:[])):[] ),
        ...(dD.success?(Array.isArray(dD.data.data)?dD.data.data:(Array.isArray(dD.data)?dD.data:[])):[] )
      ];
      return merged.sort((a,b)=>new Date(b.created_at)-new Date(a.created_at)).slice(0,8);
    },
    render:(item)=>{
      const isCashier=item.role==='cashier';
      return {
        avatar: item.avatar_url
          ? `<img src="${esc(item.avatar_url)}">`
          : `<div style="width:28px;height:28px;border-radius:50%;background:${isCashier?'var(--gold-light)':'rgba(34,197,94,.1)'};display:flex;align-items:center;justify-content:center;font-weight:800;color:${isCashier?'var(--gold)':'var(--green)'};font-size:11px">${esc((item.full_name||'?').charAt(0))}</div>`,
        name: item.full_name||'Staff',
        sub: item.email||(item.phone||''),
        badge: `<span class="badge b-${item.role}" style="font-size:10px;padding:2px 6px">${esc(item.role)}</span>`
      };
    },
    select:(item)=>{ $('staff-search').value=item.full_name||''; closeLov('staff'); loadStaff(); }
  },
  payments:{
    inputId:'pay-search', lovId:'lov-payments', wrapperId:'sw-payments',
    fetch: async(q)=>{
      // /orders → respond(paginate()) → no success, data at top level
      const r=await authFetch(`/orders?per_page=8&ref=${encodeURIComponent(q)}`); if(!r) return [];
      const d=await r.json(); return Array.isArray(d.data)?d.data:[];
    },
    render:(item)=>({
      avatar: `<div style="width:28px;height:28px;border-radius:6px;background:#F0FDF4;display:flex;align-items:center;justify-content:center;font-size:14px">💳</div>`,
      name: item.order_ref||item.order_number||'Order',
      sub: (item.customer_name||'Walk-in')+' · '+(item.payment_method||'—'),
      badge: `<span style="font-family:'Nunito',sans-serif;font-weight:900;font-size:12px;color:var(--text)">${E(item.total_amount||item.grand_total||0)}</span>`
    }),
    select:(item)=>{ $('pay-search').value=item.order_ref||item.order_number||''; closeLov('payments'); loadPayments(); }
  }
};

function smartSearch(context, inputEl){
  const wrap=$(lovConfigs[context].wrapperId);
  const val=inputEl.value.trim();
  if(val) wrap.classList.add('has-value'); else wrap.classList.remove('has-value');
  clearTimeout(lovDebounceTimers[context]);
  if(!val){ closeLov(context); (({orders:loadOrders,products:loadProducts,customers:loadCustomers,staff:loadStaff,payments:loadPayments})[context]||loadOrders)(); return; }
  const lov=$(lovConfigs[context].lovId);
  lov.innerHTML='<div class="lov-loading">Searching...</div>';
  lov.classList.add('open');
  lovDebounceTimers[context]=setTimeout(async()=>{
    const cfg=lovConfigs[context];
    const items=await cfg.fetch(val);
    if(!items.length){ lov.innerHTML='<div class="lov-empty">No results found</div>'; return; }
    lov.innerHTML=items.map((item,i)=>{
      const r=cfg.render(item);
      return `<div class="lov-item" tabindex="-1" onclick='lovSelect("${context}",${i})'>
        <div class="lov-item-avatar">${r.avatar}</div>
        <div class="lov-item-main">
          <div class="lov-item-name">${r.name}</div>
          ${r.sub?`<div class="lov-item-sub">${r.sub}</div>`:''}
        </div>
        ${r.badge?`<div class="lov-item-badge">${r.badge}</div>`:''}
      </div>`;
    }).join('');
    lov._items=items;
  },320);
}

function lovSelect(context,index){
  const cfg=lovConfigs[context];
  const items=$(cfg.lovId)._items||[];
  if(items[index]) cfg.select(items[index]);
}

function closeLov(context){
  const lovId = lovConfigs[context] ? lovConfigs[context].lovId : 'lov-'+context;
  const lov=$(lovId);
  if(lov){ lov.classList.remove('open'); lov.innerHTML=''; }
}

function clearSmartSearch(context){
  const cfg=lovConfigs[context];
  $(cfg.inputId).value='';
  $(cfg.wrapperId).classList.remove('has-value');
  closeLov(context);
  if(context==='customers') _allCustomers=[];
  (({orders:loadOrders,products:loadProducts,customers:loadCustomers,staff:loadStaff,payments:loadPayments})[context]||loadOrders)();
}

// Close LOV on outside click
document.addEventListener('click', e=>{
  // report LOV
  const repWrap=$('sw-report');
  if(repWrap && !repWrap.contains(e.target)) closeLov('report');
  // other LOVs
  Object.keys(lovConfigs).forEach(ctx=>{
    const wrap=$(lovConfigs[ctx].wrapperId);
    if(wrap && !wrap.contains(e.target)) closeLov(ctx);
  });
});

// ── SET PANEL-USERS HIDDEN (safety) ─────────────────────────
(function(){ const p=$('panel-users'); if(p) p.style.display='none'; })();


function renderPager(id,meta,loader){
  const el=$(id); if(!el) return;
  const {page=1,total_pages=1}=meta;
  if(total_pages<=1){ el.innerHTML=''; return; }
  let h=`<button class="pg" onclick="${loader.name}(${page-1})" ${page<=1?'disabled':''}>&#8249;</button>`;
  const start=Math.max(1,page-2), end=Math.min(total_pages,page+2);
  for(let i=start;i<=end;i++) h+=`<button class="pg ${i===page?'active':''}" onclick="${loader.name}(${i})">${i}</button>`;
  h+=`<button class="pg" onclick="${loader.name}(${page+1})" ${page>=total_pages?'disabled':''}>&#8250;</button>`;
  el.innerHTML=h;
}

// ── INIT ─────────────────────────────────────────────────────
loadDashboard();
loadCategories(); // pre-load for product form

// Set default payment date range to current month
(function(){
  const today=new Date().toISOString().split('T')[0];
  const first=today.substring(0,8)+'01';
  if($('pay-from')) $('pay-from').value=first;
  if($('pay-to'))   $('pay-to').value=today;
  // Also set report date range defaults
  if($('rep-from')) $('rep-from').value=first;
  if($('rep-to'))   $('rep-to').value=today;
})();
</script>
</body>
</html>
