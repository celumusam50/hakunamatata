<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Hakuna Matata</title>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@700;800;900&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html, body { height: 100%; overflow: hidden; }

body {
  font-family: 'DM Sans', sans-serif;
  background: #252525;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
}

/* Background blobs */
.blob {
  position: fixed;
  border-radius: 50%;
  filter: blur(80px);
  opacity: 0.18;
  pointer-events: none;
  z-index: 0;
}
.blob-1 { width: 420px; height: 420px; background: #F5A623; top: -100px; right: -100px; }
.blob-2 { width: 320px; height: 320px; background: #F5A623; bottom: -80px; left: -80px; opacity: 0.12; }

.splash-content {
  position: relative;
  z-index: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0;
  animation: fadeIn 0.8s ease both;
}

.logo-wrap {
  width: 110px;
  height: 110px;
  background: white;
  border-radius: 26px;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 20px 60px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.08);
  margin-bottom: 28px;
  animation: popIn 0.65s cubic-bezier(.34,1.56,.64,1) 0.2s both;
}
.logo-wrap img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.logo-wrap .logo-fallback {
  font-family: 'Nunito', sans-serif;
  font-weight: 900;
  font-size: 36px;
  color: #F5A623;
  letter-spacing: -1px;
}

.brand-name {
  font-family: 'Nunito', sans-serif;
  font-weight: 900;
  font-size: 34px;
  color: #ffffff;
  letter-spacing: -0.5px;
  text-align: center;
  line-height: 1.1;
  margin-bottom: 8px;
  animation: slideUp 0.5s ease 0.4s both;
}
.brand-name span { color: #F5A623; }

.tagline {
  font-size: 14px;
  color: rgba(255,255,255,0.45);
  font-weight: 500;
  text-align: center;
  margin-bottom: 52px;
  animation: slideUp 0.5s ease 0.5s both;
}

/* Progress bar */
.progress-wrap {
  width: 180px;
  height: 3px;
  background: rgba(255,255,255,0.1);
  border-radius: 99px;
  overflow: hidden;
  animation: slideUp 0.4s ease 0.6s both;
}
.progress-bar {
  height: 100%;
  width: 0%;
  background: #F5A623;
  border-radius: 99px;
  transition: width 0.1s linear;
}

/* Redirect message */
.redirect-msg {
  margin-top: 20px;
  font-size: 12px;
  color: rgba(255,255,255,0.3);
  animation: slideUp 0.4s ease 0.7s both;
  min-height: 18px;
  text-align: center;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to   { opacity: 1; }
}
@keyframes popIn {
  from { opacity: 0; transform: scale(0.7); }
  to   { opacity: 1; transform: scale(1); }
}
@keyframes slideUp {
  from { opacity: 0; transform: translateY(14px); }
  to   { opacity: 1; transform: translateY(0); }
}
</style>
</head>
<body>
<div class="blob blob-1"></div>
<div class="blob blob-2"></div>

<div class="splash-content">
  <div class="logo-wrap">
    <img src="hakuna matata.png" alt="Hakuna Matata" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
    <div class="logo-fallback" style="display:none">HM</div>
  </div>
  <div class="brand-name">Hakuna<br><span>Matata</span></div>
  <div class="tagline">Restaurant Management System</div>
  <div class="progress-wrap">
    <div class="progress-bar" id="progressBar"></div>
  </div>
  <div class="redirect-msg" id="redirectMsg">Loading&hellip;</div>
</div>

<script>
(function() {
  var DURATION = 10000; // 10 seconds
  var bar = document.getElementById('progressBar');
  var msg = document.getElementById('redirectMsg');
  var start = Date.now();
  var raf;

  // Detect platform: Android app or Electron/desktop → login.php; browser web → shop.php
  function getRedirectTarget() {
    var ua = navigator.userAgent || '';
    // Check for Android WebView (no Chrome or with wv flag), or standalone PWA on non-web
    var isAndroidWebView = /Android/i.test(ua) && (/wv\)/.test(ua) || !/Chrome/i.test(ua));
    var isElectron = /Electron/i.test(ua);
    var isStandalone = window.navigator.standalone === true ||
                       window.matchMedia('(display-mode: standalone)').matches;

    if (isAndroidWebView || isElectron) {
      return 'login.php';
    }
    // Regular browser (desktop or mobile web) → shop
    return 'shop.php';
  }

  function tick() {
    var elapsed = Date.now() - start;
    var pct = Math.min(100, (elapsed / DURATION) * 100);
    bar.style.width = pct + '%';

    if (elapsed >= DURATION) {
      var target = getRedirectTarget();
      msg.textContent = 'Redirecting\u2026';
      window.location.href = target;
      return;
    }

    var remaining = Math.ceil((DURATION - elapsed) / 1000);
    msg.textContent = 'Ready in ' + remaining + 's\u2026';
    raf = requestAnimationFrame(tick);
  }

  raf = requestAnimationFrame(tick);
})();
</script>
</body>
</html>
