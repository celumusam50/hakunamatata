<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Hakuna Matata — Sign In</title>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@700;800;900&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
<style>
/* ── Reset ── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html, body { height: 100%; }

/* ── Tokens ── */
:root {
  --gold:         #F5A623;
  --gold-hover:   #E09418;
  --gold-glow:    rgba(245,166,35,.18);
  --charcoal:     #363636;
  --charcoal-2:   #252525;
  --border:       #E6E5E1;
  --muted:        #9A9993;
  --text:         #2E2E2E;
  --input-bg:     #F7F6F3;
  --error-bg:     #FFF2F2;
  --error-border: #FFCACA;
  --error-text:   #C0392B;
  --radius-sm:    10px;
  --radius-md:    14px;
}

/* ── Page: column, single white background ── */
body {
  font-family: 'DM Sans', sans-serif;
  background: #ffffff;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

/* ── TOP PANEL (horizontal, logo left-aligned) ── */
.panel {
  flex: none;
  width: 100%;
  background: var(--charcoal-2);
  display: flex;
  align-items: center;
  justify-content: flex-start;
  padding: 0 40px;
  min-height: 100px;
  position: relative;
  overflow: hidden;
}

.panel-blob {
  position: absolute;
  border-radius: 50%;
  background: var(--gold);
  opacity: .07;
  pointer-events: none;
}
.panel-blob.b1 { width: 300px; height: 300px; bottom: -150px; left: -80px; }
.panel-blob.b2 { width: 220px; height: 220px; top: -100px;   right: 60px; }
.panel-blob.b3 { width: 140px; height: 140px; top: -40px;    left: 45%; opacity: .04; }

/* Brand: logo + text, left-aligned */
.brand-group {
  position: relative;
  z-index: 1;
  display: flex;
  align-items: center;
  gap: 14px;
}

.logo-box {
  width: 68px;
  height: 68px;
  background: white;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 6px 20px rgba(0,0,0,.3);
  overflow: hidden;
  flex-shrink: 0;
  animation: popIn .55s cubic-bezier(.34,1.56,.64,1) both;
}
.logo-box img { width: 100%; height: 100%; object-fit: cover; }

.panel-title {
  font-family: 'Nunito', sans-serif;
  font-size: 22px;
  font-weight: 900;
  color: #fff;
  line-height: 1.15;
  animation: fadeUp .5s ease both .1s;
}
.panel-title em { font-style: normal; color: var(--gold); }

.panel-tagline {
  font-size: 11px;
  color: rgba(255,255,255,.4);
  white-space: nowrap;
  margin-top: 2px;
  animation: fadeUp .5s ease both .18s;
}

/* ── FORM AREA: full remaining height, white ── */
.form-area {
  flex: 1;
  background: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 48px 24px;
}

/* Card: no border, no shadow — blends into white page */
.card { width: 100%; max-width: 440px; }

.card-heading {
  font-family: 'Nunito', sans-serif;
  font-size: 26px;
  font-weight: 900;
  color: var(--text);
  margin-bottom: 6px;
  animation: fadeUp .45s ease both .05s;
}
.card-sub {
  font-size: 13.5px;
  color: var(--muted);
  margin-bottom: 32px;
  animation: fadeUp .45s ease both .1s;
}

/* ── Alert ── */
.alert {
  display: none;
  align-items: flex-start;
  gap: 9px;
  padding: 11px 14px;
  border-radius: var(--radius-sm);
  font-size: 13px;
  font-weight: 500;
  margin-bottom: 20px;
  line-height: 1.4;
}
.alert.show   { display: flex; }
.alert.error  { background: var(--error-bg);  color: var(--error-text); border: 1px solid var(--error-border); }
.alert.success{ background: #F0FDF4; color: #166534; border: 1px solid #BBF7D0; }
.alert svg    { flex-shrink: 0; margin-top: 1px; }

/* ── Form fields ── */
.form-group { margin-bottom: 18px; }
.form-label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: var(--text);
  margin-bottom: 7px;
}
.input-wrap { position: relative; }
.input-icon {
  position: absolute;
  left: 13px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--muted);
  pointer-events: none;
  display: flex;
  transition: color .2s;
}
.input-icon svg { width: 17px; height: 17px; }

.form-input {
  width: 100%;
  padding: 12px 14px 12px 40px;
  border: 1.5px solid var(--border);
  border-radius: var(--radius-sm);
  font-size: 14px;
  font-family: 'DM Sans', sans-serif;
  color: var(--text);
  background: var(--input-bg);
  outline: none;
  transition: border-color .2s, background .2s, box-shadow .2s;
  -webkit-appearance: none;
}
.form-input::placeholder { color: #C2C1BC; }
.form-input:focus {
  border-color: var(--gold);
  background: #fff;
  box-shadow: 0 0 0 3px var(--gold-glow);
}
.input-wrap:focus-within .input-icon { color: var(--gold-hover); }
.form-input.is-invalid {
  border-color: var(--error-text) !important;
  box-shadow: none !important;
  background: var(--error-bg) !important;
}

.field-err { font-size: 12px; color: var(--error-text); margin-top: 5px; display: none; }
.field-err.show { display: block; }

/* Password error list */
.pwd-error-list { list-style: none; margin-top: 6px; display: none; }
.pwd-error-list.show { display: block; }
.pwd-error-list li {
  font-size: 12px;
  color: var(--error-text);
  display: flex;
  align-items: center;
  gap: 5px;
  margin-top: 3px;
}
.pwd-error-list li::before { content: '✕'; font-size: 10px; font-weight: 800; }

/* Eye toggle */
.eye-btn {
  position: absolute;
  right: 13px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  color: var(--muted);
  display: flex;
  padding: 2px;
  transition: color .2s;
}
.eye-btn:hover { color: var(--text); }
.eye-btn svg   { width: 17px; height: 17px; }

/* Forgot link */
.forgot-row { text-align: right; margin-top: -10px; margin-bottom: 24px; }
.forgot-row a { font-size: 13px; color: var(--gold-hover); font-weight: 600; text-decoration: none; }
.forgot-row a:hover { text-decoration: underline; }

/* Submit button */
.btn {
  width: 100%;
  padding: 14px;
  border-radius: var(--radius-sm);
  font-size: 15px;
  font-family: 'Nunito', sans-serif;
  font-weight: 800;
  border: none;
  cursor: pointer;
  transition: background .2s, transform .1s, box-shadow .2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}
.btn-primary {
  background: var(--gold);
  color: var(--charcoal-2);
  box-shadow: 0 4px 18px rgba(245,166,35,.38);
}
.btn-primary:hover  { background: var(--gold-hover); box-shadow: 0 6px 22px rgba(245,166,35,.45); }
.btn-primary:active { transform: scale(.98); }
.btn-primary:disabled { opacity: .65; cursor: not-allowed; transform: none; }

/* Spinner */
.spinner {
  width: 18px; height: 18px;
  border: 2.5px solid rgba(0,0,0,.2);
  border-top-color: var(--charcoal);
  border-radius: 50%;
  animation: spin .7s linear infinite;
  display: none;
}
.btn.loading .spinner  { display: block; }
.btn.loading .btn-text { display: none; }

/* Divider */
.divider {
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 24px 0;
  font-size: 12px;
  color: var(--muted);
}
.divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--border); }

/* Register row */
.register-row { text-align: center; font-size: 13.5px; color: var(--muted); margin-top: 20px; }
.register-row a { color: var(--gold-hover); font-weight: 600; text-decoration: none; }
.register-row a:hover { text-decoration: underline; }

/* Reset steps */
.reset-step        { display: none; }
.reset-step.active { display: block; }

/* Back link */
.back-link {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  color: var(--muted);
  text-decoration: none;
  font-weight: 500;
  margin-bottom: 20px;
  cursor: pointer;
  transition: color .2s;
}
.back-link:hover { color: var(--text); }
.back-link svg   { width: 15px; height: 15px; }

/* ── Blink animations ── */
@keyframes blinkReject {
  0%   { border-color: var(--error-text); background: var(--error-bg); box-shadow: 0 0 0 3px rgba(192,57,43,.15); }
  65%  { border-color: var(--error-text); background: var(--error-bg); box-shadow: 0 0 0 3px rgba(192,57,43,.15); }
  100% { border-color: var(--border);     background: var(--input-bg); box-shadow: none; }
}
@keyframes blinkError {
  0%   { border-color: var(--border);     background: var(--input-bg); }
  20%  { border-color: var(--error-text); background: var(--error-bg); box-shadow: 0 0 0 3px rgba(192,57,43,.15); }
  40%  { border-color: var(--border);     background: var(--input-bg); }
  60%  { border-color: var(--error-text); background: var(--error-bg); box-shadow: 0 0 0 3px rgba(192,57,43,.15); }
  100% { border-color: var(--error-text); background: var(--error-bg); box-shadow: 0 0 0 2px rgba(192,57,43,.1); }
}
.blink-reject { animation: blinkReject 0.55s ease forwards !important; }
.blink-error  { animation: blinkError  0.55s ease both    !important; }

/* ── Core animations ── */
@keyframes fadeUp { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }
@keyframes popIn  { from { opacity: 0; transform: scale(.7); } to { opacity: 1; transform: scale(1); } }
@keyframes spin   { to { transform: rotate(360deg); } }

/* ── Responsive ── */
@media (max-width: 600px) {
  .panel    { padding: 0 20px; min-height: 80px; }
  .logo-box { width: 54px; height: 54px; }
  .panel-title { font-size: 18px; }
  .form-area{ padding: 32px 20px; }
}
</style>
</head>
<body>

<!-- ══ TOP PANEL: logo + title only, left-aligned ════════════════════ -->
<div class="panel">
  <div class="panel-blob b1"></div>
  <div class="panel-blob b2"></div>
  <div class="panel-blob b3"></div>

  <div class="brand-group">
    <div class="logo-box">
      <img src="hakuna matata.png" alt="Hakuna Matata Logo"/>
    </div>
    <div>
      <h1 class="panel-title">Hakuna <em>Matata</em></h1>
      <p class="panel-tagline">Online Point of Sale </p>
    </div>
  </div>
</div>

<!-- ══ FORM AREA ════════════════════════════════════════════════════ -->
<div class="form-area">
  <div class="card">

    <!-- ── LOGIN VIEW ──────────────────────────────── -->
    <div id="view-login">
      <h2 class="card-heading">Welcome! </h2>
      <p class="card-sub">Sign in with your phone number and password</p>

      <div class="alert error" id="login-alert">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span id="login-alert-msg"></span>
      </div>

      <form id="login-form" novalidate>

        <!-- Phone (digits only, exactly 11) -->
        <div class="form-group">
          <label class="form-label" for="login-phone">Phone Number</label>
          <div class="input-wrap">
            <input class="form-input" type="text" id="login-phone" name="phone"
                   placeholder="e.g. 26879090901"
                   inputmode="numeric" maxlength="11" autocomplete="tel"/>
            <span class="input-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.8 19.79 19.79 0 01.01 1.17 2 2 0 012 0h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
            </span>
          </div>
          <div class="field-err" id="err-phone">Phone number must be exactly 11 digits.</div>
        </div>

        <!-- Password (min 8 + 4 strength rules) -->
        <div class="form-group">
          <label class="form-label" for="login-password">Password</label>
          <div class="input-wrap">
            <input class="form-input" type="password" id="login-password" name="password"
                   placeholder="Min. 8 characters" autocomplete="current-password"/>
            <span class="input-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
            </span>
            <button type="button" class="eye-btn" onclick="togglePwd('login-password',this)" aria-label="Show password">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
          <ul class="pwd-error-list" id="pwd-errors"></ul>
        </div>

        <div class="forgot-row">
          <a href="#" onclick="showView('view-forgot'); return false;">Forgot password?</a>
        </div>

        <button type="submit" class="btn btn-primary" id="login-btn">
          <span class="btn-text">Sign in</span>
          <span class="spinner"></span>
        </button>
      </form>

      <div class="divider">or</div>
      <div class="register-row">
        Don't have an account? <a href="register.php">Create one</a>
      </div>
    </div><!-- /view-login -->


    <!-- ── FORGOT PASSWORD VIEW ─────────────────────────────────────
         NOTE: The API's request-reset / verify-otp / reset-password
         all use EMAIL (not phone). So the reset flow uses email.
    ──────────────────────────────────────────────────────────────── -->
    <div id="view-forgot" style="display:none">
      <a class="back-link" onclick="showView('view-login')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Back to sign in
      </a>

      <!-- Step 1: Enter email address -->
      <div class="reset-step active" id="reset-step-1">
        <h2 class="card-heading">Reset password</h2>
        <p class="card-sub">Enter your email address to receive a reset code.</p>

        <div class="alert error" id="forgot-alert">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <span id="forgot-alert-msg"></span>
        </div>

        <div class="form-group">
          <label class="form-label" for="reset-email">Email Address</label>
          <div class="input-wrap">
            <input class="form-input" type="text" id="reset-email" style="display:none"
                   placeholder="you@example.com" autocomplete="email"/>
            <span class="input-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </span>
          </div>
          <div class="field-err" id="err-reset-email">Please enter a valid email address.</div>
        </div>

        <button type="button" class="btn btn-primary" id="reset-req-btn" onclick="requestReset()">
          <span class="btn-text">Send reset code</span>
          <span class="spinner"></span>
        </button>
      </div>

      <!-- Step 2: Enter OTP -->
      <div class="reset-step" id="reset-step-2">
        <h2 class="card-heading">Enter reset code</h2>
        <p class="card-sub" id="otp-sub">We sent a 6-digit code to your email.</p>

        <div class="alert error" id="otp-alert">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <span id="otp-alert-msg"></span>
        </div>

        <div class="form-group">
          <label class="form-label" for="reset-otp">6-digit code</label>
          <div class="input-wrap">
            <input class="form-input" type="text" id="reset-otp" placeholder="000000"
                   maxlength="6" inputmode="numeric" autocomplete="one-time-code"
                   style="letter-spacing:.2em; font-size:18px; text-align:center; padding-left:14px;"/>
          </div>
        </div>

        <button type="button" class="btn btn-primary" id="otp-verify-btn" onclick="verifyOtp()">
          <span class="btn-text">Verify code</span>
          <span class="spinner"></span>
        </button>

        <p style="text-align:center; margin-top:14px; font-size:13px; color:var(--muted)">
          Didn't receive it? <a href="#" style="color:var(--gold-hover); font-weight:600" onclick="requestReset(true); return false;">Resend</a>
        </p>
      </div>

      <!-- Step 3: Set new password -->
      <div class="reset-step" id="reset-step-3">
        <h2 class="card-heading">New password</h2>
        <p class="card-sub">Choose a strong new password.</p>

        <div class="alert error" id="newpwd-alert">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <span id="newpwd-alert-msg"></span>
        </div>

        <div class="form-group">
          <label class="form-label" for="new-password">New password</label>
          <div class="input-wrap">
            <input class="form-input" type="password" id="new-password"
                   placeholder="Min. 8 characters" autocomplete="new-password"/>
            <span class="input-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
            </span>
            <button type="button" class="eye-btn" onclick="togglePwd('new-password',this)" aria-label="Show password">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
          <ul class="pwd-error-list" id="new-pwd-errors"></ul>
        </div>

        <div class="form-group">
          <label class="form-label" for="confirm-password">Confirm password</label>
          <div class="input-wrap">
            <input class="form-input" type="password" id="confirm-password"
                   placeholder="Repeat password" autocomplete="new-password"/>
            <span class="input-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
            </span>
          </div>
        </div>

        <button type="button" class="btn btn-primary" id="reset-pwd-btn" onclick="resetPassword()">
          <span class="btn-text">Reset password</span>
          <span class="spinner"></span>
        </button>
      </div>

    </div><!-- /view-forgot -->

  </div><!-- /card -->
</div><!-- /form-area -->

<script>
// ── Config ─────────────────────────────────────────────────────────
const API = window.location.origin + '/api';

// Already logged in? Redirect to correct dashboard
(function(){
  var t=localStorage.getItem('access_token');
  var u=JSON.parse(localStorage.getItem('user')||'null');
  if(t&&u){
    var m={super_admin:'admin.php',manager:'admin.php',
           cashier:'pos.php',driver:'driver.php',customer:'shop.php'};
    window.location.replace(m[u.role]||'shop.php');
  }
})();


// ── State ──────────────────────────────────────────────────────────
let resetEmail = '';   // carried across reset steps (email-based flow)

// ── Utility ────────────────────────────────────────────────────────
function $(id) { return document.getElementById(id); }

function showAlert(id, msgId, msg, type = 'error') {
  const el = $(id);
  el.className = 'alert ' + type + ' show';
  $(msgId).textContent = msg;
  el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
function hideAlert(id) { const el = $(id); if (el) el.className = 'alert'; }

function setLoading(btnId, on) {
  const btn = $(btnId);
  if (on) { btn.classList.add('loading');    btn.disabled = true;  }
  else    { btn.classList.remove('loading'); btn.disabled = false; }
}

function showView(viewId) {
  ['view-login', 'view-forgot'].forEach(id => {
    $(id).style.display = id === viewId ? 'block' : 'none';
  });
  hideAlert('login-alert');
  hideAlert('forgot-alert');
  if (viewId === 'view-forgot') showResetStep(1);
}

function showResetStep(n) {
  document.querySelectorAll('.reset-step').forEach((el, i) => {
    el.classList.toggle('active', i + 1 === n);
  });
}

// ── Blink helpers ──────────────────────────────────────────────────
function blinkReject(el) {
  el.classList.remove('blink-reject');
  void el.offsetWidth;
  el.classList.add('blink-reject');
  el.addEventListener('animationend', () => el.classList.remove('blink-reject'), { once: true });
}

function blinkError(el) {
  el.classList.remove('blink-error', 'is-invalid');
  void el.offsetWidth;
  el.classList.add('blink-error', 'is-invalid');
  el.addEventListener('animationend', () => el.classList.remove('blink-error'), { once: true });
}

// ── Password: min 8 chars + uppercase + lowercase + number + special ──
function getPasswordErrors(pass) {
  const errors = [];
  if (pass.length < 8)             errors.push('Minimum 8 characters required');
  if (!/[A-Z]/.test(pass))         errors.push('At least one uppercase letter (A\u2013Z) required');
  if (!/[a-z]/.test(pass))         errors.push('At least one lowercase letter (a\u2013z) required');
  if (!/[0-9]/.test(pass))         errors.push('At least one number (0\u20139) required');
  if (!/[^A-Za-z0-9]/.test(pass))  errors.push('At least one special character (!@#$%...) required');
  return errors;
}

function showPwdErrors(listId, errors) {
  const list = $(listId);
  if (!errors.length) { list.innerHTML = ''; list.classList.remove('show'); return; }
  list.innerHTML = errors.map(e => '<li>' + e + '</li>').join('');
  list.classList.add('show');
}

// ── Password visibility ─────────────────────────────────────────────
function togglePwd(inputId, btn) {
  const inp  = $(inputId);
  const show = inp.type === 'password';
  inp.type   = show ? 'text' : 'password';
  btn.innerHTML = show
    ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="17" height="17"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>'
    : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="17" height="17"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
}

// ── Role → redirect (matches DB ENUM: super_admin, manager, cashier, driver, customer) ──
function redirectForRole(role) {
  const map = {
    super_admin: 'admin.php',
    manager: 'admin.php',
    cashier:     'pos.php',
    driver:      'driver.php',
    customer:    'shop.php',
  };
  return map[role] || 'shop.php';
}

// ── Phone field guard: block non-digits with red blink ─────────────
function attachPhoneGuard(el) {
  el.addEventListener('keydown', function(e) {
    const ctrl = ['Backspace','Delete','ArrowLeft','ArrowRight','ArrowUp','ArrowDown','Tab','Home','End','Enter'];
    if (!ctrl.includes(e.key) && !/^\d$/.test(e.key)) {
      e.preventDefault();
      blinkReject(this);
    }
  });
  el.addEventListener('paste', function(e) {
    e.preventDefault();
    const raw    = (e.clipboardData || window.clipboardData).getData('text');
    const digits = raw.replace(/\D/g, '').slice(0, 11);
    this.value   = digits;
    if (raw !== digits) blinkReject(this);
  });
}

attachPhoneGuard($('login-phone'));

// ── LOGIN (POST /auth/login with phone) ────────────────────────────
$('login-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  hideAlert('login-alert');

  const phoneVal = $('login-phone').value.trim();
  const passVal  = $('login-password').value;
  let hasError   = false;

  // Phone: exactly 11 digits
  if (!/^\d{11}$/.test(phoneVal)) {
    blinkError($('login-phone'));
    $('err-phone').classList.add('show');
    hasError = true;
  } else {
    $('login-phone').classList.remove('is-invalid');
    $('err-phone').classList.remove('show');
  }

  // Password: server validates credentials — only block empty submit
  if (!passVal) {
    blinkError($('login-password'));
    showPwdErrors('pwd-errors', ['Password is required.']);
    hasError = true;
  } else {
    $('login-password').classList.remove('is-invalid');
    showPwdErrors('pwd-errors', []);
  }

  if (hasError) return;

  setLoading('login-btn', true);
  try {
    const res  = await fetch(API + '/auth/login', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body:    JSON.stringify({ phone: phoneVal, password: passVal }),
    });
    const data = await res.json();

    if (!res.ok) {
      showAlert('login-alert', 'login-alert-msg', data.error || 'Login failed. Please try again.');
      setLoading('login-btn', false);
      return;
    }

    // Response structure: { success, message, data: { access_token, refresh_token, user: { role, ... } } }
    localStorage.setItem('access_token',  data.data.access_token);
    localStorage.setItem('refresh_token', data.data.refresh_token);
    localStorage.setItem('user',          JSON.stringify(data.data.user));
    window.location.href = redirectForRole(data.data.user.role);

  } catch (err) {
    showAlert('login-alert', 'login-alert-msg', 'Could not connect to server. Please check your connection.');
    setLoading('login-btn', false);
  }
});

// Live-clear phone error on input
$('login-phone').addEventListener('input', function() {
  this.classList.remove('is-invalid');
  $('err-phone').classList.remove('show');
  hideAlert('login-alert');
});

// Clear password error as soon as user types again
$('login-password').addEventListener('input', function() {
  if (this.value) {
    this.classList.remove('is-invalid');
    showPwdErrors('pwd-errors', []);
    hideAlert('login-alert');
  }
});

// ── FORGOT: request reset via EMAIL (POST /auth/request-reset { email }) ──
async function requestReset(silent = false) {
  const email = $('reset-email').value.trim();
  hideAlert('forgot-alert');

  if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    if (!silent) {
      blinkError($('reset-email'));
      $('err-reset-email').classList.add('show');
    }
    return;
  }
  $('err-reset-email').classList.remove('show');
  $('reset-email').classList.remove('is-invalid');

  resetEmail = email;
  setLoading('reset-req-btn', true);
  try {
    const res  = await fetch(API + '/auth/request-reset', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body:    JSON.stringify({ email }),
    });
    const data = await res.json();

    // API always responds 200 (hides whether email exists for security)
    $('otp-sub').textContent = 'We sent a 6-digit code to ' + email + '. Check your inbox.';
    // Dev mode: API returns OTP in response
    if (data.data && data.data.otp) {
      $('otp-sub').textContent += ' (Dev code: ' + data.data.otp + ')';
    }
    showResetStep(2);
  } catch (err) {
    showAlert('forgot-alert', 'forgot-alert-msg', 'Server error. Please try again.');
  } finally {
    setLoading('reset-req-btn', false);
  }
}

// ── FORGOT: verify OTP (POST /auth/verify-otp { email, otp }) ──────
async function verifyOtp() {
  const otp = $('reset-otp').value.trim();
  hideAlert('otp-alert');

  if (!/^\d{6}$/.test(otp)) {
    showAlert('otp-alert', 'otp-alert-msg', 'Please enter the 6-digit code from your email.');
    return;
  }

  setLoading('otp-verify-btn', true);
  try {
    const res  = await fetch(API + '/auth/verify-otp', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body:    JSON.stringify({ email: resetEmail, otp }),
    });
    const data = await res.json();

    if (!res.ok) {
      showAlert('otp-alert', 'otp-alert-msg', data.error || 'Invalid or expired code.');
      setLoading('otp-verify-btn', false);
      return;
    }
    showResetStep(3);
  } catch (err) {
    showAlert('otp-alert', 'otp-alert-msg', 'Server error. Please try again.');
    setLoading('otp-verify-btn', false);
  }
}

// ── FORGOT: reset password (POST /auth/reset-password { email, otp, new_password }) ──
async function resetPassword() {
  const newPwd  = $('new-password').value;
  const confirm = $('confirm-password').value;
  hideAlert('newpwd-alert');

  const pwdErrs = getPasswordErrors(newPwd);
  if (pwdErrs.length) {
    blinkError($('new-password'));
    showPwdErrors('new-pwd-errors', pwdErrs);
    return;
  }
  showPwdErrors('new-pwd-errors', []);

  if (newPwd !== confirm) {
    showAlert('newpwd-alert', 'newpwd-alert-msg', 'Passwords do not match.');
    blinkError($('confirm-password'));
    return;
  }

  const otp = $('reset-otp').value.trim();
  setLoading('reset-pwd-btn', true);
  try {
    const res  = await fetch(API + '/auth/reset-password', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body:    JSON.stringify({ email: resetEmail, otp, new_password: newPwd }),
    });
    const data = await res.json();

    if (!res.ok) {
      showAlert('newpwd-alert', 'newpwd-alert-msg', data.error || 'Reset failed. Please try again.');
      setLoading('reset-pwd-btn', false);
      return;
    }
    showView('view-login');
    showAlert('login-alert', 'login-alert-msg', 'Password reset successfully. Please sign in.', 'success');
  } catch (err) {
    showAlert('newpwd-alert', 'newpwd-alert-msg', 'Server error. Please try again.');
    setLoading('reset-pwd-btn', false);
  }
}

// Live-update errors while correcting new password
$('new-password').addEventListener('input', function() {
  if (!this.classList.contains('is-invalid')) return;
  const errs = getPasswordErrors(this.value);
  showPwdErrors('new-pwd-errors', errs);
  if (!errs.length) this.classList.remove('is-invalid');
});
</script>
</body>
</html>
