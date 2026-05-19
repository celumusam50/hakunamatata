<?php
// ================================================================
//  kitchen.php — Hakuna Matata Kitchen Display
//  NO LOGIN REQUIRED — queries DB directly via PHP
//  Auto-refreshes every 10s.
//
//  When kitchen marks "Ready":
//    1. Updates sale_order.order_status = 'ready'
//    2. Logs to order_status_log
//    3. Pushes notification to customer (notification table)
//    → Customer tracking screen picks this up automatically
//    → If a driver was auto-assigned, delivery flips to 'assigned' so driver app sees it
// ================================================================
require_once __DIR__ . '/api/config.php';

// ── DB connection ─────────────────────────────────────────────────
$pdo = null; $dbErr = null;
try {
    $pdo = new PDO(
        'mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME.';charset='.DB_CHARSET,
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (Throwable $e) { $dbErr = $e->getMessage(); }

// ── Handle AJAX status updates ────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $pdo) {
    header('Content-Type: application/json');
    $orderId   = (int)($_POST['order_id'] ?? 0);
    $newStatus = $_GET['action'] === 'ready' ? 'preparing' : '';

    if ($orderId > 0 && $newStatus) {
        try {
            // 1. Get current order details
            $orderStmt = $pdo->prepare(
                "SELECT so.order_id, so.order_ref, so.order_status, so.user_id, so.order_type
                 FROM sale_order so WHERE so.order_id=? LIMIT 1"
            );
            $orderStmt->execute([$orderId]);
            $order = $orderStmt->fetch();

            if (!$order) {
                echo json_encode(['success'=>false,'error'=>'Order not found']);
                exit;
            }

            $oldStatus = $order['order_status'];

            // 2. Update order status
            $pdo->prepare("UPDATE sale_order SET order_status=? WHERE order_id=?")
                ->execute([$newStatus, $orderId]);

            // 2b. When marked ready (preparing), auto-assign an available driver
            if ($newStatus === 'preparing' && $order['order_type'] === 'delivery') {
                $driverStmt = $pdo->prepare(
                    "SELECT u.user_id
                     FROM user u
                     WHERE u.role = 'driver'
                       AND u.status = 'active'
                       AND u.user_id NOT IN (
                           SELECT d.driver_id FROM delivery d
                           WHERE d.driver_id IS NOT NULL
                             AND d.delivery_status NOT IN ('delivered','confirmed','failed','cancelled')
                       )
                     LIMIT 1"
                );
                $driverStmt->execute();
                $driver = $driverStmt->fetch();

                if ($driver) {
                    $driverId = $driver['user_id'];
                    $pdo->prepare(
                        "UPDATE delivery SET driver_id=?, delivery_status='assigned' WHERE order_id=?"
                    )->execute([$driverId, $orderId]);

                    $pdo->prepare(
                        "INSERT INTO notification (user_id, type, title, body, data)
                         VALUES (?, 'delivery', '📦 New Delivery Assigned', ?, ?)"
                    )->execute([
                        $driverId,
                        "Order {$order['order_ref']} is ready for pickup. Head to the restaurant.",
                        json_encode(['order_id' => $orderId])
                    ]);
                }
            }

            // 3. Log the status change
            $pdo->prepare(
                "INSERT INTO order_status_log (order_id, changed_by, from_status, to_status, notes)
                 VALUES (?, NULL, ?, ?, 'Kitchen display')"
            )->execute([$orderId, $oldStatus, $newStatus]);

            // 4. Push notification to customer
            $msgs = [
                'preparing' => ['✅ Order Ready',
                    $order['order_type'] === 'delivery'
                        ? "Your order {$order['order_ref']} is ready — a driver is being assigned!"
                        : "Your order {$order['order_ref']} is ready for collection at the counter!"
                ],
            ];
            if (isset($msgs[$newStatus])) {
                $pdo->prepare(
                    "INSERT INTO notification (user_id, type, title, body, data)
                     VALUES (?, 'order_update', ?, ?, ?)"
                )->execute([
                    $order['user_id'],
                    $msgs[$newStatus][0],
                    $msgs[$newStatus][1],
                    json_encode(['order_id' => $orderId])
                ]);
            }

            echo json_encode(['success'=>true, 'status'=>$newStatus, 'order_ref'=>$order['order_ref']]);
        } catch (Throwable $e) {
            echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
        }
    } else {
        echo json_encode(['success'=>false,'error'=>'Invalid request']);
    }
    exit;
}

// ── Fetch active kitchen orders ───────────────────────────────────
$orders = [];
if ($pdo) {
    try {
        $stmt = $pdo->query(
            "SELECT so.order_id, so.order_ref, so.collection_no, so.order_type,
                    so.order_status, so.total_amount, so.created_at, so.special_notes,
                    u.full_name AS customer_name, u.phone AS customer_phone,
                    GROUP_CONCAT(
                        CONCAT(oi.quantity, '× ', oi.product_name_snap)
                        ORDER BY oi.item_id SEPARATOR '|'
                    ) AS items_text
             FROM sale_order so
             JOIN user u ON u.user_id = so.user_id
             LEFT JOIN order_item oi ON oi.order_id = so.order_id
             WHERE so.order_status IN ('pending')
             GROUP BY so.order_id
             ORDER BY so.created_at ASC"
        );
        $orders = $stmt->fetchAll();
    } catch (Throwable $e) { $dbErr = $e->getMessage(); }
}
$ordersJson = json_encode($orders, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Kitchen Display — Hakuna Matata</title>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@700;800;900&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --gold:#F5A623;--charcoal:#252525;--green:#22C55E;--red:#E8394D;
  --bg:#F0EEE9;--card:#fff;--border:#E2E0DB;--muted:#8A8880;--text:#2E2E2E;
}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);min-height:100vh}
.header{background:var(--charcoal);padding:0 24px;height:64px;display:flex;align-items:center;gap:12px;position:sticky;top:0;z-index:100;box-shadow:0 2px 12px rgba(0,0,0,.3)}
.brand-logo{width:42px;height:42px;background:#fff;border-radius:9px;overflow:hidden;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.brand-logo img{width:100%;height:100%;object-fit:cover}
.brand-name{font-family:'Nunito',sans-serif;font-weight:900;font-size:17px;color:#fff}
.brand-name em{font-style:normal;color:var(--gold)}
.kitchen-badge{background:var(--gold);color:var(--charcoal);font-size:10px;font-weight:800;padding:3px 9px;border-radius:20px;text-transform:uppercase;letter-spacing:.06em}
.header-right{margin-left:auto;display:flex;align-items:center;gap:10px}
.live-dot{width:7px;height:7px;border-radius:50%;background:var(--green);animation:pulse 2s infinite;flex-shrink:0}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.35}}
.hdr-text{font-size:11px;color:rgba(255,255,255,.55);font-weight:600}
.status-bar{background:#fff;border-bottom:1px solid var(--border);padding:9px 24px;display:flex;align-items:center;gap:18px;font-size:11px;font-weight:600;color:var(--muted)}
.stat{display:flex;align-items:center;gap:4px}
.sd{width:6px;height:6px;border-radius:50%}
.main{padding:18px 24px}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px}
.card{background:var(--card);border-radius:12px;border:1px solid var(--border);box-shadow:0 2px 8px rgba(0,0,0,.05);overflow:hidden}
.card[data-status="pending"]  {border-top:4px solid #F59E0B}
.card[data-status="confirmed"]{border-top:4px solid #3B82F6}
.card[data-status="preparing"]{border-top:4px solid #F97316}
.card[data-status="ready"]    {border-top:4px solid var(--green)}
.ch{padding:12px 14px 8px;display:flex;justify-content:space-between;align-items:flex-start}
.onum{font-family:'Nunito',sans-serif;font-size:18px;font-weight:900;color:var(--charcoal)}
.colno{font-size:13px;color:var(--gold);font-weight:700;margin-top:2px}
.sbadge{font-size:10px;font-weight:700;padding:3px 8px;border-radius:10px}
.type-badge{font-size:9px;font-weight:700;padding:2px 7px;border-radius:8px;margin-top:4px}
.st-pending  {background:#FEF9C3;color:#713F12}
.st-confirmed{background:#DBEAFE;color:#1E40AF}
.st-preparing{background:#FFEDD5;color:#9A3412}
.st-ready    {background:#DCFCE7;color:#166534}
.tb-delivery  {background:#EFF6FF;color:#1D4ED8}
.tb-collection{background:#FFFBEB;color:#92400E}
.cm{padding:0 14px 8px;font-size:11px;color:var(--muted);font-weight:600}
.ci{padding:0 14px 10px}
.ir{display:flex;align-items:center;gap:6px;padding:5px 0;border-bottom:1px solid var(--border);font-size:12px}
.ir:last-child{border-bottom:none}
.id{width:4px;height:4px;border-radius:50%;background:var(--gold);flex-shrink:0}
.notes{margin:0 14px 10px;padding:7px 9px;background:#FFFBEB;border-radius:7px;font-size:11px;color:#92400E;border:1px solid #FDE68A}
.timer{padding:0 14px 6px;font-size:10px;color:var(--muted);text-align:right;font-weight:600}
.cf{padding:10px 14px;border-top:1px solid var(--border);display:flex;gap:7px}
.ba{flex:1;padding:9px;border-radius:8px;font-family:'DM Sans',sans-serif;font-size:12px;font-weight:700;border:none;cursor:pointer;transition:all .15s}
.ba:hover{opacity:.85}
.bp{background:#FFEDD5;color:#C2410C}
.br{background:var(--green);color:#fff}
.bd{background:#F3F4F6;color:var(--muted);cursor:default;font-size:11px}
.empty{text-align:center;padding:70px 20px;color:var(--muted)}
.empty svg{display:block;margin:0 auto 14px}
.empty h2{font-family:'Nunito',sans-serif;font-size:20px;font-weight:900;margin-bottom:6px}
.empty p{font-size:13px}
.dberr{background:#FEF2F2;border:1px solid #FECACA;color:#991B1B;padding:14px;border-radius:9px;margin:18px 24px;font-size:12px}
.toast{position:fixed;bottom:22px;left:50%;transform:translateX(-50%) translateY(70px);background:var(--charcoal);color:#fff;padding:11px 22px;border-radius:9px;font-size:13px;font-weight:600;opacity:0;transition:all .25s;z-index:999;pointer-events:none;white-space:nowrap}
.toast.show{opacity:1;transform:translateX(-50%) translateY(0)}
.toast.ok{background:#166534}
.toast.err{background:var(--red)}
@media(max-width:600px){.main{padding:12px}.grid{grid-template-columns:1fr}.status-bar{padding:9px 12px}}
</style>
</head>
<body>

<div class="header">
  <div class="brand-logo"><img src="hakuna matata.png" alt="HM" onerror="this.style.display='none';this.parentNode.style.background='#F5A623'"/></div>
  <span class="brand-name">Hakuna <em>Matata</em></span>
  <span class="kitchen-badge">🍳 Kitchen</span>
  <div class="header-right">
    <div class="live-dot"></div>
    <span class="hdr-text">Live: <b id="cnt-all">—</b> orders</span>
    <span class="hdr-text" id="last-update"></span>
  </div>
</div>

<div class="status-bar">
  <div class="stat"><div class="sd" style="background:#F59E0B"></div><span id="cnt-p">0</span> Pending</div>
  <span style="margin-left:auto;font-size:10px">Auto-refresh: <span id="secs">10</span>s</span>
</div>

<div class="main">
  <?php if ($dbErr): ?>
  <div class="dberr">⚠️ DB Error: <?= htmlspecialchars($dbErr) ?></div>
  <?php endif; ?>
  <div id="grid" class="grid"></div>
  <div id="empty" class="empty" style="display:none">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" width="56" height="56"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
    <h2>No active orders</h2>
    <p>New orders will appear here automatically every 10 seconds.</p>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
let orders = <?= $ordersJson ?>;
const SHOW = ['pending'];

function esc(s){const d=document.createElement('div');d.textContent=s||'';return d.innerHTML;}
function timeSince(dt){
  const s=Math.floor((Date.now()-new Date(dt).getTime())/1000);
  if(s<60) return s+'s ago';
  if(s<3600) return Math.floor(s/60)+'m ago';
  return Math.floor(s/3600)+'h '+Math.floor((s%3600)/60)+'m ago';
}
function showToast(msg,type){
  const t=document.getElementById('toast');
  t.textContent=msg; t.className='toast show '+(type||'');
  clearTimeout(t._t); t._t=setTimeout(()=>{t.className='toast'},2600);
}

function buildCard(o){
  const items=(o.items_text||'').split('|').filter(Boolean);
  const isReady=o.order_status==='ready';
  const isDel=o.order_type==='delivery';
  const colPart=o.collection_no?`<div class="colno">Collection: ${esc(o.collection_no)}</div>`:'';
  return `
  <div class="card" id="card-${o.order_id}" data-status="${esc(o.order_status)}" data-created="${esc(o.created_at)}">
    <div class="ch">
      <div>
        <div class="onum">${esc(o.order_ref||('#'+o.order_id))}</div>
        ${colPart}
        <span class="sbadge st-${esc(o.order_status)}">${{pending:'New Order'}[o.order_status]||o.order_status}</span>
      </div>
      <div style="text-align:right">
        <div class="type-badge ${isDel?'tb-delivery':'tb-collection'}">${isDel?'🚚 Delivery':'🏪 Collection'}</div>
      </div>
    </div>
    <div class="cm">👤 ${esc(o.customer_name||'Customer')}${o.customer_phone?' · '+esc(o.customer_phone):''}</div>
    <div class="ci">${items.map(i=>`<div class="ir"><div class="id"></div>${esc(i)}</div>`).join('')}</div>
    ${o.special_notes?`<div class="notes">📝 ${esc(o.special_notes)}</div>`:''}
    <div class="timer" id="t-${o.order_id}">${timeSince(o.created_at)}</div>
    <div class="cf">
      <button class="ba br" onclick="mark(${o.order_id},'ready')">✅ Mark Ready</button>
    </div>
  </div>`;
}

function render(){
  const active=orders.filter(o=>SHOW.includes(o.order_status)).sort((a,b)=>new Date(a.created_at)-new Date(b.created_at));
  document.getElementById('cnt-all').textContent=active.length;
  document.getElementById('cnt-p').textContent=active.filter(o=>o.order_status==='pending').length;
  const grid=document.getElementById('grid');
  const empty=document.getElementById('empty');
  if(!active.length){grid.innerHTML='';empty.style.display='';return;}
  empty.style.display='none';
  grid.innerHTML=active.map(buildCard).join('');
}

async function mark(orderId, status){
  const btn=document.querySelector(`#card-${orderId} .ba.br, #card-${orderId} .ba.bp`);
  if(btn){btn.disabled=true;btn.textContent='Updating…';}
  const fd=new FormData();
  fd.append('order_id',orderId);
  try{
    const res=await fetch('?action='+status,{method:'POST',body:fd});
    const d=await res.json();
    if(d.success){
      const o=orders.find(x=>x.order_id==orderId);
      if(o) o.order_status=status;
      render();
      showToast(`✅ Order ${d.order_ref||''} marked Ready — driver being assigned!`,'ok');
    } else {
      showToast(d.error||'Update failed','err');
      if(btn){btn.disabled=false;btn.textContent=status==='ready'?'✅ Mark Ready':'🍳 Preparing';}
    }
  }catch(e){
    showToast('Network error — will retry on next refresh','err');
    if(btn){btn.disabled=false;}
  }
}

// Timers
setInterval(()=>orders.forEach(o=>{const e=document.getElementById('t-'+o.order_id);if(e)e.textContent=timeSince(o.created_at);}),1000);

// Countdown & auto-refresh
let secs=10;
setInterval(()=>{
  secs--;
  document.getElementById('secs').textContent=secs;
  if(secs<=0) window.location.reload();
},1000);

document.getElementById('last-update').textContent='Updated: '+new Date().toLocaleTimeString();

render();
</script>
</body>
</html>
