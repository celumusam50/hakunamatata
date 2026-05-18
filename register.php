<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Create Account — Hakuna Matata</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&family=Nunito:wght@700;800;900&display=swap" rel="stylesheet">
<style>
/* ── Reset ── */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%}

/* ── Tokens ── */
:root{
  --gold:#F5A623;--gold-hover:#E09418;--gold-glow:rgba(245,166,35,.18);
  --charcoal:#363636;--charcoal-2:#252525;
  --border:#E6E5E1;--muted:#9A9993;--text:#2E2E2E;
  --input-bg:#F7F6F3;
  --error-bg:#FFF2F2;--error-border:#FFCACA;--error-text:#C0392B;
  --success-bg:#F0FDF4;--success-border:#BBF7D0;--success-text:#15803D;
  --radius-sm:10px;--radius-md:14px;
}

body{font-family:'DM Sans',sans-serif;background:#fff;display:flex;flex-direction:column;min-height:100vh}

/* ══ TOP PANEL — identical to login.php ══ */
.panel{
  flex:none;width:100%;background:var(--charcoal-2);
  display:flex;align-items:center;justify-content:flex-start;
  padding:0 40px;min-height:100px;position:relative;overflow:hidden;
}
.panel-blob{position:absolute;border-radius:50%;background:var(--gold);opacity:.07;pointer-events:none}
.panel-blob.b1{width:300px;height:300px;bottom:-150px;left:-80px}
.panel-blob.b2{width:220px;height:220px;top:-100px;right:60px}
.panel-blob.b3{width:140px;height:140px;top:-40px;left:45%;opacity:.04}

.brand-group{position:relative;z-index:1;display:flex;align-items:center;gap:14px}
.logo-box{
  width:68px;height:68px;background:white;border-radius:14px;
  display:flex;align-items:center;justify-content:center;
  box-shadow:0 6px 20px rgba(0,0,0,.3);overflow:hidden;flex-shrink:0;
  animation:popIn .55s cubic-bezier(.34,1.56,.64,1) both;
}
.logo-box img{width:100%;height:100%;object-fit:cover}
.panel-title{font-family:'Nunito',sans-serif;font-size:22px;font-weight:900;color:#fff;line-height:1.15;animation:fadeUp .5s ease both .1s}
.panel-title em{font-style:normal;color:var(--gold)}
.panel-tagline{font-size:11px;color:rgba(255,255,255,.4);white-space:nowrap;margin-top:2px;animation:fadeUp .5s ease both .18s}

/* ══ FORM AREA ══ */
.form-area{flex:1;background:#fff;display:flex;align-items:center;justify-content:center;padding:40px 24px}
.card{width:100%;max-width:500px}

.card-heading{font-family:'Nunito',sans-serif;font-size:26px;font-weight:900;color:var(--text);margin-bottom:4px;animation:fadeUp .45s ease both .05s}
.card-sub{font-size:13.5px;color:var(--muted);margin-bottom:28px;animation:fadeUp .45s ease both .1s}
.card-sub a{color:var(--gold);text-decoration:none;font-weight:600}
.card-sub a:hover{text-decoration:underline}

/* ── Alert ── */
.alert{display:none;align-items:flex-start;gap:9px;padding:11px 14px;border-radius:var(--radius-sm);font-size:13px;margin-bottom:18px;animation:fadeUp .3s ease both}
.alert.show{display:flex}
.alert svg{width:16px;height:16px;flex-shrink:0;margin-top:1px}
.alert-error{background:var(--error-bg);border:1px solid var(--error-border);color:var(--error-text)}
.alert-success{background:var(--success-bg);border:1px solid var(--success-border);color:var(--success-text)}

/* ── Form rows ── */
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.form-group{margin-bottom:16px;position:relative;animation:fadeUp .4s ease both}
.form-group:nth-child(1){animation-delay:.12s}
.form-group:nth-child(2){animation-delay:.15s}
.form-group:nth-child(3){animation-delay:.18s}
.form-group:nth-child(4){animation-delay:.21s}
.form-group:nth-child(5){animation-delay:.24s}
.form-group:nth-child(6){animation-delay:.27s}

.form-label{display:flex;align-items:center;justify-content:space-between;font-size:12px;font-weight:600;color:var(--text);margin-bottom:6px}
.form-label .req{color:var(--gold)}

.input-wrap{position:relative}
.form-input{
  width:100%;height:46px;border-radius:var(--radius-sm);
  border:1.5px solid var(--border);background:var(--input-bg);
  padding:0 42px 0 14px;font-size:14px;font-family:'DM Sans',sans-serif;
  color:var(--text);outline:none;transition:border-color .2s,background .2s;
}
.form-input:focus{border-color:var(--gold);background:#fff;box-shadow:0 0 0 3px var(--gold-glow)}
.form-input.valid{border-color:#22C55E;background:#fff}
.form-input.invalid{border-color:var(--error-text);background:var(--error-bg)}

/* validation icon in input */
.input-icon{position:absolute;right:13px;top:50%;transform:translateY(-50%);pointer-events:none;opacity:0;transition:opacity .2s}
.input-icon svg{width:16px;height:16px;display:block}
.input-icon.ok{opacity:1;color:#22C55E}
.input-icon.err{opacity:1;color:var(--error-text)}

/* field error message */
.field-err{font-size:11px;color:var(--error-text);margin-top:4px;min-height:16px;display:flex;align-items:center;gap:4px}
.field-err svg{width:11px;height:11px;flex-shrink:0}

/* ── Password toggle ── */
.pwd-toggle{position:absolute;right:13px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted);padding:0;display:flex;align-items:center}
.pwd-toggle:hover{color:var(--text)}
.pwd-toggle svg{width:16px;height:16px}
/* pwd fields need extra right padding for toggle+icon */
#password,#confirm-password{padding-right:70px}
/* validation icon sits left of the toggle (right:38px) */
#icon-pwd,#icon-cpwd{right:38px}

/* ── Password strength meter ── */
.strength-wrap{margin-top:6px}
.strength-bar{display:flex;gap:4px;margin-bottom:4px}
.strength-seg{flex:1;height:4px;border-radius:2px;background:var(--border);transition:background .3s}
.strength-seg.weak{background:#E8394D}
.strength-seg.fair{background:#F97316}
.strength-seg.good{background:#F5A623}
.strength-seg.strong{background:#22C55E}
.strength-label{font-size:11px;color:var(--muted);font-weight:600}
.strength-label.weak{color:#E8394D}
.strength-label.fair{color:#F97316}
.strength-label.good{color:#F5A623}
.strength-label.strong{color:#22C55E}

/* password rules list */
.pwd-rules{display:flex;flex-wrap:wrap;gap:5px;margin-top:6px}
.pwd-rule{display:flex;align-items:center;gap:4px;font-size:11px;color:var(--muted);padding:3px 8px;border-radius:6px;background:var(--input-bg);border:1px solid var(--border);transition:all .2s}
.pwd-rule.met{background:#F0FDF4;border-color:#BBF7D0;color:#15803D}
.pwd-rule svg{width:11px;height:11px;flex-shrink:0}

/* ── Submit button ── */
.btn-submit{
  width:100%;height:50px;border-radius:var(--radius-md);border:none;
  background:var(--gold);color:var(--charcoal-2);
  font-family:'Nunito',sans-serif;font-size:17px;font-weight:900;
  cursor:pointer;transition:all .2s;display:flex;align-items:center;
  justify-content:center;gap:8px;margin-top:8px;
  animation:fadeUp .4s ease both .3s;
}
.btn-submit:hover:not(:disabled){background:var(--gold-hover);transform:translateY(-1px);box-shadow:0 6px 20px var(--gold-glow)}
.btn-submit:active:not(:disabled){transform:translateY(0)}
.btn-submit:disabled{opacity:.55;cursor:not-allowed;transform:none!important}
.btn-submit svg{width:18px;height:18px}

/* loading spinner */
.spinner{width:18px;height:18px;border:2.5px solid rgba(255,255,255,.3);border-top-color:var(--charcoal-2);border-radius:50%;animation:spin .7s linear infinite;display:none}

/* ── Login link ── */
.login-link{text-align:center;margin-top:18px;font-size:13px;color:var(--muted);animation:fadeUp .4s ease both .35s}
.login-link a{color:var(--gold);font-weight:700;text-decoration:none}
.login-link a:hover{text-decoration:underline}

/* ── Animations ── */
@keyframes popIn{0%{transform:scale(.6);opacity:0}100%{transform:scale(1);opacity:1}}
@keyframes fadeUp{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
@keyframes spin{to{transform:rotate(360deg)}}
@keyframes shake{0%,100%{transform:translateX(0)}25%{transform:translateX(-6px)}75%{transform:translateX(6px)}}

/* ── Responsive ── */
@media(max-width:520px){
  .panel{padding:0 20px;min-height:82px}
  .logo-box{width:52px;height:52px}
  .panel-title{font-size:18px}
  .form-area{padding:28px 18px}
  .form-row{grid-template-columns:1fr}
}

/* ── Success Overlay ── */
.success-overlay{
  display:none;position:fixed;inset:0;z-index:9999;
  background:rgba(0,0,0,.45);backdrop-filter:blur(4px);
  align-items:center;justify-content:center;animation:fadeIn .3s ease both;
}
.success-overlay.show{display:flex}
.success-box{
  background:#fff;border-radius:20px;padding:40px 36px;text-align:center;
  max-width:380px;width:90%;box-shadow:0 24px 60px rgba(0,0,0,.18);
  animation:popIn .45s cubic-bezier(.34,1.56,.64,1) both;
}
.success-icon{
  width:72px;height:72px;border-radius:50%;background:#F0FDF4;
  display:flex;align-items:center;justify-content:center;margin:0 auto 18px;
}
.success-icon svg{width:36px;height:36px;color:#22C55E}
.success-title{font-family:'Nunito',sans-serif;font-size:22px;font-weight:900;color:var(--text);margin-bottom:8px}
.success-msg{font-size:14px;color:var(--muted);margin-bottom:20px;line-height:1.5}
.success-countdown{font-size:13px;color:var(--muted);font-weight:600}
.success-countdown span{color:var(--gold);font-size:16px;font-weight:900}
.success-bar-wrap{height:4px;background:var(--border);border-radius:2px;margin-top:16px;overflow:hidden}
.success-bar{height:100%;background:var(--gold);border-radius:2px;width:100%;transition:width linear}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
</style>
</head>
<body>

<!-- ══ PANEL / HEADER ══ -->
<div class="panel">
  <div class="panel-blob b1"></div>
  <div class="panel-blob b2"></div>
  <div class="panel-blob b3"></div>
  <div class="brand-group">
    <div class="logo-box">
      <img src="hakuna matata.png" alt="Hakuna Matata Logo">
    </div>
    <div>
      <div class="panel-title">Hakuna <em>Matata</em></div>
      <div class="panel-tagline">Online Point of Sale</div>
    </div>
  </div>
</div>

<!-- ══ FORM AREA ══ -->
<div class="form-area">
  <div class="card">

    <h1 class="card-heading">Create Account</h1>
    <p class="card-sub">Already have an account? <a href="login.php">Sign in</a></p>

    <!-- Alert -->
    <div class="alert alert-error" id="reg-alert" role="alert">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <span id="reg-alert-msg"></span>
    </div>

    <form id="reg-form"  method="post" action="">

      <!-- First Name + Last Name -->
      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="first-name">First Name <span class="req">*</span></label>
          <div class="input-wrap">
            <input type="text" class="form-input" id="first-name" name="first_name"
              autocomplete="given-name" tabindex="1"
              oninput="sanitizeName(this);validateName(this)" onblur="validateName(this,true)" maxlength="75">
            <div class="input-icon" id="icon-first-name"></div>
          </div>
          <div class="field-err" id="err-first-name"></div>
        </div>
        <div class="form-group">
          <label class="form-label" for="last-name">Last Name <span class="req">*</span></label>
          <div class="input-wrap">
            <input type="text" class="form-input" id="last-name" name="last_name"
              autocomplete="family-name" tabindex="2"
              oninput="sanitizeName(this);validateName(this)" onblur="if(!_guardRedirecting)validateName(this,true)" maxlength="75">
            <div class="input-icon" id="icon-last-name"></div>
          </div>
          <div class="field-err" id="err-last-name"></div>
        </div>
      </div>



      <!-- Phone -->
      <div class="form-group">
        <label class="form-label" for="phone">
          Phone Number <span class="req">*</span>
          <span style="font-size:10px;color:var(--muted);font-weight:500">exactly 11 digits, e.g. 26876000001</span>
        </label>
        <div class="input-wrap">
          <input type="tel" class="form-input" id="phone" name="phone"
            autocomplete="tel" tabindex="4" maxlength="11" inputmode="numeric"
            onfocus="guardFocus(this,['first-name','last-name'])"
            oninput="sanitizePhone(this);validatePhone(this)" onblur="if(!_guardRedirecting)validatePhone(this,true)">
          <div class="input-icon" id="icon-phone"></div>
        </div>
        <div class="field-err" id="err-phone"></div>
      </div>

      <!-- Password -->
      <div class="form-group">
        <label class="form-label" for="password">Password <span class="req">*</span></label>
        <div class="input-wrap">
          <input type="password" class="form-input" id="password" name="password"
            autocomplete="new-password" tabindex="5"
            onfocus="guardFocus(this,['first-name','last-name','phone'])"
            oninput="validatePassword(this)" onblur="if(!_guardRedirecting)validatePassword(this,true)">
          <div class="input-icon" id="icon-pwd"></div>
          <button type="button" class="pwd-toggle" id="toggle-pwd" onclick="togglePwd('password','toggle-pwd')" aria-label="Show/hide password">
            <svg id="eye-pwd" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
        <!-- Strength meter -->
        <div class="strength-wrap" id="strength-wrap" style="display:none">
          <div class="strength-bar">
            <div class="strength-seg" id="seg1"></div>
            <div class="strength-seg" id="seg2"></div>
            <div class="strength-seg" id="seg3"></div>
            <div class="strength-seg" id="seg4"></div>
          </div>
          <div class="strength-label" id="strength-label">Too weak</div>
        </div>
        <!-- Rules -->
        <div class="pwd-rules" id="pwd-rules">
          <div class="pwd-rule" id="rule-len">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            8+ chars
          </div>
          <div class="pwd-rule" id="rule-upper">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            Uppercase
          </div>
          <div class="pwd-rule" id="rule-num">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            Number
          </div>
          <div class="pwd-rule" id="rule-special">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            Special (!@#)
          </div>
        </div>
        <div class="field-err" id="err-password"></div>
      </div>

      <!-- Confirm Password -->
      <div class="form-group">
        <label class="form-label" for="confirm-password">Confirm Password <span class="req">*</span></label>
        <div class="input-wrap">
          <input type="password" class="form-input" id="confirm-password" name="confirm_password"
            autocomplete="new-password" tabindex="6"
            onfocus="guardFocus(this,['first-name','last-name','phone','password'])"
            oninput="validateConfirm(this)" onblur="if(!_guardRedirecting)validateConfirm(this,true)">
          <div class="input-icon" id="icon-cpwd"></div>
          <button type="button" class="pwd-toggle" id="toggle-cpwd" onclick="togglePwd('confirm-password','toggle-cpwd')" aria-label="Show/hide confirm password">
            <svg id="eye-cpwd" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
        <div class="field-err" id="err-confirm"></div>
      </div>

      <!-- Submit -->
      <button type="submit" class="btn-submit" id="reg-btn" name="create" tabindex="7">
        <div class="spinner" id="reg-spinner"></div>
        <span id="reg-btn-label">Create Account</span>
        <svg id="reg-btn-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
      </button>

    </form>

    <p class="login-link">Already registered? <a href="login.php">Sign in to your account</a></p>
  </div>
</div>


<!-- ══ SUCCESS OVERLAY ══ -->
<div class="success-overlay" id="success-overlay">
  <div class="success-box">
    <div class="success-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
    </div>
    <div class="success-title">Account Created! 🎉</div>
    <div class="success-msg">Your Hakuna Matata account has been created successfully. Redirecting you to the login page…</div>
    <div class="success-countdown">Redirecting in <span id="countdown">5</span>s</div>
    <div class="success-bar-wrap"><div class="success-bar" id="success-bar"></div></div>
  </div>
</div>

<script>
const API = window.location.origin + '/hakunamatata_v3/api';

// Redirect if already logged in
const token = localStorage.getItem('access_token');
const existingUser = JSON.parse(localStorage.getItem('user') || 'null');
if (token && existingUser) {
  window.location.href = existingUser.role === 'customer' ? 'customer.php' : 'index.php';
}

// ── Helpers ──────────────────────────────────────────────────────────
function $(id) { return document.getElementById(id); }

const CHECK_SVG = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>`;
const CROSS_SVG = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>`;
const ERR_SVG   = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>`;

function setFieldState(input, iconId, errId, ok, msg) {
  const icon = $(iconId);
  const err  = $(errId);
  input.classList.remove('valid','invalid');
  if (ok === null) { icon.className = 'input-icon'; err.innerHTML = ''; return; }
  if (ok) {
    input.classList.add('valid');
    icon.className = 'input-icon ok';
    icon.innerHTML = CHECK_SVG;
    err.innerHTML  = '';
  } else {
    input.classList.add('invalid');
    icon.className = 'input-icon err';
    icon.innerHTML = CROSS_SVG;
    err.innerHTML  = ERR_SVG + ' ' + msg;
  }
}

// ── Validators ───────────────────────────────────────────────────────
// Re-focus the field when user tries to leave it invalid
// (keeps cursor locked on the failing field until it's corrected)
function trapFocus(inp, blur) {
  if (blur) {
    setTimeout(function() { inp.focus(); inp.select && inp.select(); }, 30);
  }
}

// Flag set while guardFocus is redirecting focus — suppresses blur validation
// on the field being left so it doesn't flash red mid-redirect.
var _guardRedirecting = false;

// Guard: when a field gets focus, check every required predecessor.
// Highlights ONLY the first incomplete field, moves cursor there,
// then clears the highlight the moment user starts typing/focusing it.
function guardFocus(inp, requiredIds) {
  for (var i = 0; i < requiredIds.length; i++) {
    var prev = $(requiredIds[i]);
    if (!prev) continue;
    if (!prev.value.trim() || prev.classList.contains('invalid')) {
      // Show red border + message on just this one field
      var ico = 'icon-' + prev.id;
      var err = 'err-'  + prev.id;
      if (prev.id === 'password') { ico = 'icon-pwd'; err = 'err-password'; }
      setFieldState(prev, ico, err, false, 'Please fill this in first.');
      // Clear the highlight the instant the user actually focuses/types in it
      var clearOnce = function(p, ic, er) {
        return function() {
          setFieldState(p, ic, er, null, '');
          p.removeEventListener('focus', clearOnce);
          p.removeEventListener('input', clearOnce);
        };
      }(prev, ico, err);
      prev.addEventListener('focus', clearOnce);
      prev.addEventListener('input', clearOnce);
      // Signal that we're redirecting so the blur on `inp` is suppressed
      _guardRedirecting = true;
      setTimeout(function(p) {
        p.focus();
        // Clear flag after redirect settles
        setTimeout(function() { _guardRedirecting = false; }, 50);
      }, 0, prev);
      return;
    }
  }
}

function sanitizeName(inp) {
  const cur = inp.selectionStart; // remember cursor position
  let v = inp.value;
  // 1) Strip everything that is NOT an ASCII letter (A-Z a-z)
  v = v.replace(/[^A-Za-z]/g, '');
  // 2) Collapse 3+ consecutive identical letters down to 2 max
  //    e.g. "ttt" → "tt", "aaaa" → "aa"
  v = v.replace(/([A-Za-z])\1{2,}/g, '$1$1');
  if (inp.value !== v) {
    inp.value = v;
    // Restore cursor (clamp to new length)
    inp.setSelectionRange(Math.min(cur, v.length), Math.min(cur, v.length));
  }
}

function validateName(inp, blur=false) {
  const v   = inp.value.trim();
  const id  = inp.id;
  const ico = 'icon-' + id;
  const err = 'err-' + id;
  if (!v) {
    if (blur) setFieldState(inp, ico, err, false, 'This field is required.');
    else      setFieldState(inp, ico, err, null, '');
    return false;
  }
  // Letters only — no numbers, spaces, hyphens or symbols
  if (!/^[A-Za-z]+$/.test(v)) {
    setFieldState(inp, ico, err, false, 'Letters only — no numbers, spaces or symbols.');
    return false;
  }
  // No 3+ consecutive identical letters (sanitizeName strips these on input)
  if (/([A-Za-z])\1{2,}/.test(v)) {
    setFieldState(inp, ico, err, false, 'Same letter cannot repeat 3 times in a row (e.g. "ttt" is not allowed).');
    return false;
  }
  if (v.length < 2) {
    setFieldState(inp, ico, err, false, 'Must be at least 2 characters.');
    return false;
  }
  setFieldState(inp, ico, err, true, '');
  return true;
}

function sanitizePhone(inp) {
  // Strip everything except digits — cap at exactly 11
  let v = inp.value.replace(/\D/g, '');
  if (v.length > 11) v = v.slice(0, 11);
  inp.value = v;
}

function validatePhone(inp, blur=false) {
  const v = inp.value.trim();
  if (!v) {
    if (blur) setFieldState(inp, 'icon-phone', 'err-phone', false, 'Phone number is required.');
    else      setFieldState(inp, 'icon-phone', 'err-phone', null, '');
    return false;
  }
  // Exactly 11 digits — no + prefix, no spaces, no dashes
  const digits = v.replace(/\D/g, '');
  const ok = /^\d{11}$/.test(digits);
  if (!ok) {
    setFieldState(inp, 'icon-phone', 'err-phone', false, 'Phone must be exactly 11 digits (e.g. 26876000001).');
    return false;
  }
  setFieldState(inp, 'icon-phone', 'err-phone', true, '');
  return true;
}

function validatePassword(inp, blur=false) {
  const v   = inp.value;
  const len = v.length >= 8;
  const upp = /[A-Z]/.test(v);
  const num = /[0-9]/.test(v);
  const spc = /[!@#$%^&*()_+\-=\[\]{}|;':",.<>?/\\`~]/.test(v);

  // Show strength wrap
  const sw = $('strength-wrap');
  if (v.length > 0) {
    sw.style.display = 'block';
    $('pwd-rules').style.display = 'flex';
  } else {
    sw.style.display = 'none';
    $('pwd-rules').style.display = 'flex';
  }

  // Update rule pills
  $('rule-len').classList.toggle('met', len);
  $('rule-upper').classList.toggle('met', upp);
  $('rule-num').classList.toggle('met', num);
  $('rule-special').classList.toggle('met', spc);

  // Strength score
  const score = [len, upp, num, spc].filter(Boolean).length;
  const segs  = ['seg1','seg2','seg3','seg4'];
  const cls   = ['','weak','fair','good','strong'];
  const labels= ['','Too weak','Fair','Good','Strong'];
  segs.forEach((s,i)=>{ $(s).className = 'strength-seg' + (i < score ? ' ' + cls[score] : ''); });
  $('strength-label').className = 'strength-label ' + cls[score];
  $('strength-label').textContent = labels[score];

  // Validate confirm if already typed
  const cp = $('confirm-password');
  if (cp.value) validateConfirm(cp, false);

  if (!v) {
    if (blur) setFieldState(inp, 'icon-pwd', 'err-password', false, 'Password is required.');
    else      setFieldState(inp, 'icon-pwd', 'err-password', null, '');
    return false;
  }
  const allMet = len && upp && num && spc;
  if (!allMet) {
    if (blur) setFieldState(inp, 'icon-pwd', 'err-password', false, 'Password does not meet all requirements.');
    else setFieldState(inp, 'icon-pwd', 'err-password', null, '');
    return false;
  }
  setFieldState(inp, 'icon-pwd', 'err-password', true, '');
  return true;
}

function validateConfirm(inp, blur=false) {
  const v  = inp.value;
  const pw = $('password').value;
  if (!v) {
    if (blur) setFieldState(inp, 'icon-cpwd', 'err-confirm', false, 'Please confirm your password.');
    else      setFieldState(inp, 'icon-cpwd', 'err-confirm', null, '');
    return false;
  }
  if (v !== pw) {
    setFieldState(inp, 'icon-cpwd', 'err-confirm', false, 'Passwords do not match.');
    return false;
  }
  setFieldState(inp, 'icon-cpwd', 'err-confirm', true, '');
  return true;
}

// ── Password toggle ───────────────────────────────────────────────────
function togglePwd(inputId, btnId) {
  const inp  = $(inputId);
  const show = inp.type === 'password';
  inp.type = show ? 'text' : 'password';
  $(btnId).innerHTML = show
    ? `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>`
    : `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`;
}

// ── Alert ─────────────────────────────────────────────────────────────
function showAlert(msg, type='error') {
  const el  = $('reg-alert');
  const txt = $('reg-alert-msg');
  el.className = 'alert show alert-' + type;
  txt.textContent = msg;
  el.scrollIntoView({ behavior:'smooth', block:'nearest' });
}
function hideAlert() { $('reg-alert').className = 'alert'; }

// ── Set loading ────────────────────────────────────────────────────────
function setLoading(on) {
  $('reg-btn').disabled   = on;
  $('reg-spinner').style.display  = on ? 'block' : 'none';
  $('reg-btn-label').textContent  = on ? 'Creating account...' : 'Create Account';
  $('reg-btn-arrow').style.display = on ? 'none' : 'block';
}


// ── Success Overlay ──────────────────────────────────────────────────
function showSuccessOverlay() {
  const overlay = $('success-overlay');
  const bar     = $('success-bar');
  const cd      = $('countdown');
  overlay.classList.add('show');

  // Animate the progress bar shrinking over 5s
  bar.style.transition = 'none';
  bar.style.width = '100%';
  requestAnimationFrame(() => {
    requestAnimationFrame(() => {
      bar.style.transition = 'width 5s linear';
      bar.style.width = '0%';
    });
  });

  // Countdown
  let secs = 5;
  cd.textContent = secs;
  const interval = setInterval(() => {
    secs--;
    cd.textContent = secs;
    if (secs <= 0) {
      clearInterval(interval);
      sessionStorage.setItem('reg_success', '1');
      window.location.href = 'login.php';
    }
  }, 1000);
}

// ── Form submit ───────────────────────────────────────────────────────
$('reg-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  hideAlert();

  // Run all validations
  const fnOk   = validateName($('first-name'), true);
  const lnOk   = validateName($('last-name'), true);

  const phOk   = validatePhone($('phone'), true);
  const pwOk   = validatePassword($('password'), true);
  const cpOk   = validateConfirm($('confirm-password'), true);

  if (!fnOk || !lnOk || !phOk || !pwOk || !cpOk) {
    showAlert('Please fix the errors above before submitting.');
    // Scroll to AND focus the first invalid field so user can correct it
    const first = document.querySelector('.form-input.invalid');
    if (first) {
      first.scrollIntoView({ behavior:'smooth', block:'center' });
      setTimeout(() => first.focus(), 300); // wait for scroll to complete
    }
    return;
  }

  setLoading(true);

  const first_name = $('first-name').value.trim();
  const last_name  = $('last-name').value.trim();
  const payload = {
    first_name,
    last_name,
    full_name: first_name + ' ' + last_name, // included for backward compat
    
    phone:     $('phone').value.trim(),
    password:  $('password').value,
  };

  try {
    const res = await fetch(API + '/auth/register', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(payload),
    });
    const data = await res.json();

    if (!res.ok) {
      showAlert(data.error || 'Registration failed. Please try again.');
      setLoading(false);
      // Highlight specific field errors from API
      if (data.errors && Array.isArray(data.errors)) {
        data.errors.forEach(err => {
          const low = err.toLowerCase();
          if      (low.includes('name'))     setFieldState($('first-name'), 'icon-first-name', 'err-first-name', false, err);
          else if (low.includes('phone'))    setFieldState($('phone'), 'icon-phone', 'err-phone', false, err);
          else if (low.includes('password')) setFieldState($('password'), 'icon-pwd', 'err-password', false, err);
        });
      }
      return;
    }

    // API response: { success, message, data: { access_token, refresh_token, user } }
    const tokenData = data.data || {};
    if (tokenData.access_token)  localStorage.setItem('access_token',  tokenData.access_token);
    if (tokenData.refresh_token) localStorage.setItem('refresh_token', tokenData.refresh_token);
    if (tokenData.user)          localStorage.setItem('user', JSON.stringify(tokenData.user));

    // Success — show success overlay for 5 seconds then redirect
    setLoading(false);
    showSuccessOverlay();

  } catch (err) {
    const msg = err?.message ?? String(err);
    // Network / fetch failure (XAMPP not running, wrong URL, etc.)
    if (msg.includes('fetch') || msg.includes('Network') || msg.includes('Failed')) {
      showAlert('Cannot reach the server — make sure XAMPP Apache & MySQL are both running, ' +
                'then try again.');
    // JSON parse failure (server returned HTML/PHP error page instead of JSON)
    } else if (msg.includes('JSON') || msg.includes('token') || msg.includes('Unexpected')) {
      showAlert('Server responded but not with valid JSON. ' +
                'Check that mod_rewrite is enabled and the API folder is in htdocs.');
    } else {
      showAlert('Unexpected error: ' + msg);
    }
    setLoading(false);
  }
});

// Real-time confirm check when password changes
$('password').addEventListener('input', () => {
  if ($('confirm-password').value) validateConfirm($('confirm-password'));
});
</script>
</body>
</html>
