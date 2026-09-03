<?php
/**
 * Falcon Chemicals CMS – Enrollment Passkey Manager
 * Place at: /admin/views/enrollment-passkeys.php
 * Accessible only to admin role users.
 *
 * Call from your router in index.php:
 *   if ($uri === '/enrollment-passkeys') { require ADMIN_PATH . '/views/enrollment-passkeys.php'; exit; }
 */

if (!isLoggedIn() || !isAdmin()) {
    redirect('/admin/login');
}

define('TOKENS_FILE_EP', DATA_PATH . '/enrollment_tokens.json');

/* ── Helpers ── */
function ep_read(): array {
    if (!file_exists(TOKENS_FILE_EP)) return [];
    return json_decode(file_get_contents(TOKENS_FILE_EP), true) ?? [];
}

function ep_write(array $data): void {
    if (!is_dir(DATA_PATH)) mkdir(DATA_PATH, 0755, true);
    file_put_contents(TOKENS_FILE_EP, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function generate_passkey(): string {
    $seg = fn() => strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));
    return 'FALC-' . $seg() . '-' . $seg() . '-' . $seg();
}

/* ── Handle POST actions (AJAX) ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    !empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {

    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';

    /* Generate new passkey */
    if ($action === 'generate') {
        $role     = in_array($_POST['role'] ?? '', ['admin','editor','viewer']) ? $_POST['role'] : 'editor';
        $division = trim($_POST['division'] ?? '');
        $note     = trim($_POST['note'] ?? '');
        $expires  = !empty($_POST['expires']) ? $_POST['expires'] : null;

        $tokens   = ep_read();
        $passkey  = generate_passkey();

        /* ensure uniqueness */
        $existing = array_column($tokens, 'passkey');
        while (in_array($passkey, $existing)) $passkey = generate_passkey();

        $tokens[] = [
            'passkey'    => $passkey,
            'role'       => $role,
            'division'   => $division,
            'note'       => $note,
            'expires_at' => $expires ? date('Y-m-d 23:59:59', strtotime($expires)) : null,
            'used'       => false,
            'used_by'    => null,
            'used_at'    => null,
            'created_by' => currentUser()['username'],
            'created_at' => date('Y-m-d H:i:s'),
        ];
        ep_write($tokens);
        echo json_encode(['ok' => true, 'passkey' => $passkey]);
        exit;
    }

    /* Revoke passkey */
    if ($action === 'revoke') {
        $passkey = $_POST['passkey'] ?? '';
        $tokens  = ep_read();
        foreach ($tokens as &$t) {
            if ($t['passkey'] === $passkey && !$t['used']) {
                $t['revoked']    = true;
                $t['used']       = true;
                $t['revoked_by'] = currentUser()['username'];
                $t['revoked_at'] = date('Y-m-d H:i:s');
            }
        }
        ep_write($tokens);
        echo json_encode(['ok' => true]);
        exit;
    }

    echo json_encode(['ok' => false, 'msg' => 'Unknown action']); exit;
}

/* ── Load tokens for display ── */
$tokens   = array_reverse(ep_read()); // newest first
$active   = array_filter($tokens, fn($t) => !$t['used'] && empty($t['revoked']) &&
                (empty($t['expires_at']) || strtotime($t['expires_at']) > time()));
$used_rev = array_filter($tokens, fn($t) => $t['used'] || !empty($t['revoked']));

/* ── Roles list & divisions ── */
$roles     = ['admin' => 'Admin', 'editor' => 'Editor', 'viewer' => 'Viewer'];
$divisions = ['', 'plastics', 'automotive-fluids', 'adhesives', 'detergents', 'construction', 'sulphuric-acid', 'bitumen'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Enrollment Passkeys | Falcon CMS</title>
<link rel="stylesheet" href="../frontend/css/libraries.css">
<link rel="stylesheet" href="../frontend/css/style.css">
<style>
/* ─ Layout ─ */
.ep-wrap{padding:2rem;max-width:960px}
.ep-heading{font-size:22px;font-weight:700;color:#8B1A1A;margin-bottom:4px}
.ep-sub{font-size:13px;color:#777;margin-bottom:2rem}

/* ─ Card ─ */
.ep-card{background:#fff;border:1px solid #e8e0e0;border-radius:12px;
  padding:1.5rem;margin-bottom:1.5rem;box-shadow:0 2px 8px rgba(0,0,0,.06)}
.ep-card-title{font-size:15px;font-weight:600;color:#333;margin-bottom:1rem;
  display:flex;align-items:center;gap:8px}

/* ─ Form grid ─ */
.ep-form{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.ep-form .full{grid-column:1/-1}
.ep-label{font-size:11px;font-weight:600;color:#8B1A1A;letter-spacing:.4px;
  text-transform:uppercase;display:block;margin-bottom:4px}
.ep-input,.ep-select{width:100%;padding:9px 12px;border:1px solid #ddd;border-radius:6px;
  font-size:13px;color:#333;outline:none;font-family:inherit}
.ep-input:focus,.ep-select:focus{border-color:#8B1A1A}

/* ─ Buttons ─ */
.btn-gen{background:#8B1A1A;color:#fff;border:none;border-radius:6px;padding:10px 20px;
  font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;
  display:flex;align-items:center;gap:6px;transition:background .2s}
.btn-gen:hover{background:#6e1414}
.btn-gen:disabled{background:#c88;cursor:not-allowed}
.btn-revoke{background:none;border:1px solid #fca5a5;color:#b91c1c;border-radius:5px;
  padding:5px 12px;font-size:12px;cursor:pointer;font-family:inherit;transition:all .2s}
.btn-revoke:hover{background:#fee2e2}

/* ─ Passkey display ─ */
.passkey-result{display:none;margin-top:1.25rem;background:#f0fdf4;border:1px solid #86efac;
  border-radius:8px;padding:1rem 1.25rem}
.passkey-result.show{display:block}
.passkey-label{font-size:11px;color:#166534;font-weight:600;text-transform:uppercase;
  letter-spacing:.4px;margin-bottom:6px}
.passkey-value{font-size:20px;font-weight:700;color:#15803d;letter-spacing:3px;
  font-family:monospace;margin-bottom:10px}
.passkey-actions{display:flex;gap:8px;flex-wrap:wrap}
.btn-copy{background:#166534;color:#fff;border:none;border-radius:5px;padding:7px 16px;
  font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;
  display:flex;align-items:center;gap:5px;transition:background .2s}
.btn-copy:hover{background:#14532d}
.passkey-warn{font-size:11px;color:#15803d;margin-top:8px;
  display:flex;align-items:center;gap:5px}

/* ─ Table ─ */
.ep-table{width:100%;border-collapse:collapse;font-size:13px}
.ep-table th{background:#f9f5f5;color:#8B1A1A;font-size:11px;font-weight:600;
  text-transform:uppercase;letter-spacing:.4px;padding:9px 12px;text-align:left;
  border-bottom:2px solid #f0e8e8}
.ep-table td{padding:9px 12px;border-bottom:1px solid #f5f0f0;color:#444;vertical-align:middle}
.ep-table tr:last-child td{border-bottom:none}
.ep-table tr:hover td{background:#fdf9f9}

/* ─ Badges ─ */
.badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;
  border-radius:20px;font-size:11px;font-weight:600}
.badge-active{background:#dcfce7;color:#166534}
.badge-used  {background:#f3f4f6;color:#6b7280}
.badge-revoked{background:#fee2e2;color:#991b1b}
.badge-expired{background:#fef3c7;color:#92400e}
.role-admin {background:#fde8e8;color:#8B1A1A}
.role-editor{background:#e0e7ff;color:#3730a3}
.role-viewer{background:#f0fdf4;color:#166534}

/* ─ Empty state ─ */
.empty-state{text-align:center;padding:2rem;color:#bbb;font-size:13px}

/* ─ Alert ─ */
.ep-alert{border-radius:6px;padding:10px 14px;font-size:13px;margin-bottom:1rem;
  display:none;align-items:center;gap:8px}
.ep-alert.show{display:flex}
.ep-alert-err{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5}

/* ─ Responsive ─ */
@media(max-width:600px){
  .ep-form{grid-template-columns:1fr}
  .ep-form .full{grid-column:1}
  .ep-wrap{padding:1rem}
  .ep-table{display:block;overflow-x:auto}
}

/* ─ Spinner ─ */
.ep-spin{width:14px;height:14px;border:2px solid rgba(255,255,255,.35);
  border-top-color:#fff;border-radius:50%;animation:sp .7s linear infinite;display:none}
@keyframes sp{to{transform:rotate(360deg)}}

/* Toast */
.toast{position:fixed;bottom:24px;right:24px;background:#1a1a1a;color:#fff;
  padding:10px 18px;border-radius:8px;font-size:13px;z-index:9999;
  opacity:0;transform:translateY(8px);transition:all .3s;pointer-events:none}
.toast.show{opacity:1;transform:translateY(0)}
</style>
</head>
<body>

<?php /* ── include your existing admin layout header here ── */ ?>
<?php /* require ADMIN_PATH . '/views/partials/header.php'; */ ?>

<div class="ep-wrap">
  <div class="ep-heading">&#128273; Enrollment Passkeys</div>
  <div class="ep-sub">Generate single-use passkeys for new users. Share the passkey with the user — they enter it during enrollment to create their account.</div>

  <!-- ── Generate new passkey ── -->
  <div class="ep-card">
    <div class="ep-card-title">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#8B1A1A" stroke-width="2">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/>
        <path d="M12 16h.01"/>
      </svg>
      Generate New Passkey
    </div>

    <div class="ep-alert ep-alert-err" id="gen-err">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/>
      </svg>
      <span id="gen-err-msg"></span>
    </div>

    <div class="ep-form">
      <div>
        <label class="ep-label">Role to Grant</label>
        <select id="gen-role" class="ep-select">
          <option value="editor">Editor</option>
          <option value="viewer">Viewer</option>
          <option value="admin">Admin</option>
        </select>
      </div>

      <div>
        <label class="ep-label">Division (optional)</label>
        <select id="gen-division" class="ep-select">
          <option value="">— All Divisions —</option>
          <option value="plastics">Manufacturing Plastic Packaging</option>
          <option value="automotive-fluids">Manufacturing Engine Coolants</option>
          <option value="adhesives">Adhesives &amp; Polymer Emulsions</option>
          <option value="detergents">Detergents &amp; Disinfectant</option>
          <option value="construction">Construction Chemicals</option>
          <option value="sulphuric-acid">Sulphuric Acid</option>
          <option value="bitumen">Bitumen Products</option>
        </select>
      </div>

      <div>
        <label class="ep-label">Expires On (optional)</label>
        <input type="date" id="gen-expires" class="ep-input"
               min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
      </div>

      <div>
        <label class="ep-label">Note / Recipient</label>
        <input type="text" id="gen-note" class="ep-input"
               placeholder="e.g. For John Smith (Marketing)">
      </div>

      <div class="full">
        <button class="btn-gen" id="btn-generate" onclick="generatePasskey()">
          <span class="ep-spin" id="gen-spin"></span>
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="8" y1="12" x2="16" y2="12"/>
          </svg>
          Generate Passkey
        </button>
      </div>
    </div>

    <!-- Result box -->
    <div class="passkey-result" id="passkey-result">
      <div class="passkey-label">&#10003; Passkey Generated — Share this with the user</div>
      <div class="passkey-value" id="passkey-display"></div>
      <div class="passkey-actions">
        <button class="btn-copy" onclick="copyPasskey()">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="9" y="9" width="13" height="13" rx="2"/>
            <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>
          </svg>
          <span id="copy-label">Copy Passkey</span>
        </button>
      </div>
      <div class="passkey-warn">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
        </svg>
        This passkey is single-use and will be invalidated after the user enrolls.
      </div>
    </div>
  </div>

  <!-- ── Active passkeys ── -->
  <div class="ep-card">
    <div class="ep-card-title">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#8B1A1A" stroke-width="2">
        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
      </svg>
      Active Passkeys
      <span style="margin-left:auto;background:#dcfce7;color:#166534;font-size:11px;
        font-weight:600;padding:2px 10px;border-radius:20px"><?= count($active) ?> active</span>
    </div>

    <?php if (empty($active)): ?>
      <div class="empty-state">No active passkeys. Generate one above to invite a new user.</div>
    <?php else: ?>
      <div style="overflow-x:auto">
        <table class="ep-table">
          <thead>
            <tr>
              <th>Passkey</th>
              <th>Role</th>
              <th>Division</th>
              <th>Note</th>
              <th>Expires</th>
              <th>Created</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($active as $t): ?>
            <tr id="row-<?= htmlspecialchars($t['passkey']) ?>">
              <td>
                <code style="font-size:13px;font-weight:600;letter-spacing:1px;
                  color:#8B1A1A;background:#fdf0f0;padding:3px 8px;border-radius:4px">
                  <?= e($t['passkey']) ?>
                </code>
              </td>
              <td><span class="badge role-<?= e($t['role']) ?>"><?= e(ucfirst($t['role'])) ?></span></td>
              <td style="color:#777;font-size:12px"><?= $t['division'] ? e($t['division']) : '—' ?></td>
              <td style="color:#666;font-size:12px"><?= $t['note'] ? e($t['note']) : '—' ?></td>
              <td style="font-size:12px;color:#888">
                <?php if ($t['expires_at']): ?>
                  <?= date('d M Y', strtotime($t['expires_at'])) ?>
                <?php else: ?>
                  <span style="color:#bbb">Never</span>
                <?php endif; ?>
              </td>
              <td style="font-size:12px;color:#aaa"><?= date('d M Y H:i', strtotime($t['created_at'])) ?></td>
              <td>
                <button class="btn-revoke"
                        onclick="revokePasskey('<?= e($t['passkey']) ?>', this)">
                  Revoke
                </button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

  <!-- ── History ── -->
  <?php if (!empty($used_rev)): ?>
  <div class="ep-card">
    <div class="ep-card-title" style="cursor:pointer" onclick="toggleHistory()">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="2">
        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
      </svg>
      <span style="color:#888">Usage History</span>
      <span style="margin-left:auto;font-size:11px;color:#bbb"><?= count($used_rev) ?> records</span>
      <svg id="hist-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#bbb" stroke-width="2"
           style="margin-left:6px;transition:transform .2s">
        <polyline points="6 9 12 15 18 9"/>
      </svg>
    </div>

    <div id="history-table" style="display:none;overflow-x:auto">
      <table class="ep-table">
        <thead>
          <tr>
            <th>Passkey</th>
            <th>Status</th>
            <th>Role</th>
            <th>Note</th>
            <th>Used / Revoked By</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($used_rev as $t):
            $status = !empty($t['revoked']) ? 'revoked' : 'used';
            $by_user = $t['used_by'] ?? $t['revoked_by'] ?? '—';
            $at = $t['used_at']   ?? $t['revoked_at'] ?? $t['created_at'];
          ?>
          <tr>
            <td>
              <code style="font-size:12px;color:#aaa;letter-spacing:1px">
                <?= e($t['passkey']) ?>
              </code>
            </td>
            <td>
              <span class="badge badge-<?= $status ?>">
                <?= ucfirst($status) ?>
              </span>
            </td>
            <td><span class="badge role-<?= e($t['role']) ?>"><?= e(ucfirst($t['role'])) ?></span></td>
            <td style="font-size:12px;color:#888"><?= $t['note'] ? e($t['note']) : '—' ?></td>
            <td style="font-size:12px;color:#555"><?= e($by_user) ?></td>
            <td style="font-size:12px;color:#aaa"><?= date('d M Y H:i', strtotime($at)) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>

  <!-- ── Lost Access Requests ── -->
  <?php
  $lost_file = DATA_PATH . '/lost_access_requests.json';
  $lost_reqs = [];
  if (file_exists($lost_file)) {
      $all_lost = json_decode(file_get_contents($lost_file), true) ?? [];
      $lost_reqs = array_filter($all_lost, fn($r) => !$r['handled']);
  }
  if (!empty($lost_reqs)):
  ?>
  <div class="ep-card" style="border-color:#fca5a5">
    <div class="ep-card-title" style="color:#991b1b">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#991b1b" stroke-width="2">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
      Lost Access Requests
      <span style="margin-left:auto;background:#fee2e2;color:#991b1b;font-size:11px;
        font-weight:600;padding:2px 10px;border-radius:20px"><?= count($lost_reqs) ?> pending</span>
    </div>
    <div style="overflow-x:auto">
      <table class="ep-table">
        <thead>
          <tr><th>Email</th><th>IP Address</th><th>Requested At</th><th>Action</th></tr>
        </thead>
        <tbody>
          <?php foreach ($lost_reqs as $req): ?>
          <tr>
            <td><?= e($req['email']) ?></td>
            <td style="font-size:12px;color:#aaa"><?= e($req['ip']) ?></td>
            <td style="font-size:12px;color:#aaa"><?= date('d M Y H:i', strtotime($req['at'])) ?></td>
            <td>
              <a href="mailto:<?= e($req['email']) ?>?subject=Falcon+CMS+Access+Restoration"
                 style="font-size:12px;color:#8B1A1A;font-weight:600;text-decoration:none">
                &#9993; Reply
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>

</div><!-- .ep-wrap -->

<!-- Toast -->
<div class="toast" id="toast"></div>

<script>
let lastPasskey = '';

async function generatePasskey() {
  document.getElementById('gen-err').classList.remove('show');
  const role     = document.getElementById('gen-role').value;
  const division = document.getElementById('gen-division').value;
  const expires  = document.getElementById('gen-expires').value;
  const note     = document.getElementById('gen-note').value.trim();

  const btn = document.getElementById('btn-generate');
  const sp  = document.getElementById('gen-spin');
  btn.disabled = true; sp.style.display = 'inline-block';

  try {
    const fd = new FormData();
    fd.append('action',   'generate');
    fd.append('role',     role);
    fd.append('division', division);
    fd.append('expires',  expires);
    fd.append('note',     note);

    const r   = await fetch(window.location.href, {
      method: 'POST',
      headers: {'X-Requested-With': 'XMLHttpRequest'},
      body: fd
    });
    const res = await r.json();

    if (res.ok) {
      lastPasskey = res.passkey;
      document.getElementById('passkey-display').textContent = res.passkey;
      document.getElementById('passkey-result').classList.add('show');
      document.getElementById('copy-label').textContent = 'Copy Passkey';
      /* refresh active table row without full reload */
      setTimeout(() => location.reload(), 300);
    } else {
      document.getElementById('gen-err-msg').textContent = res.msg || 'Generation failed.';
      document.getElementById('gen-err').classList.add('show');
    }
  } catch(e) {
    document.getElementById('gen-err-msg').textContent = 'Network error.';
    document.getElementById('gen-err').classList.add('show');
  }

  btn.disabled = false; sp.style.display = 'none';
}

function copyPasskey() {
  if (!lastPasskey) return;
  navigator.clipboard.writeText(lastPasskey).then(() => {
    document.getElementById('copy-label').textContent = '✓ Copied!';
    showToast('Passkey copied to clipboard');
    setTimeout(() => document.getElementById('copy-label').textContent = 'Copy Passkey', 2500);
  });
}

async function revokePasskey(passkey, btn) {
  if (!confirm('Revoke passkey ' + passkey + '? This cannot be undone.')) return;
  btn.disabled = true; btn.textContent = '...';

  try {
    const fd = new FormData();
    fd.append('action',  'revoke');
    fd.append('passkey', passkey);

    const r   = await fetch(window.location.href, {
      method: 'POST',
      headers: {'X-Requested-With': 'XMLHttpRequest'},
      body: fd
    });
    const res = await r.json();

    if (res.ok) {
      const row = document.getElementById('row-' + passkey);
      if (row) {
        row.style.opacity = '.4';
        row.style.transition = 'opacity .4s';
        setTimeout(() => row.remove(), 450);
      }
      showToast('Passkey revoked');
    }
  } catch(e) { btn.disabled = false; btn.textContent = 'Revoke'; }
}

function toggleHistory() {
  const tbl = document.getElementById('history-table');
  const chv = document.getElementById('hist-chevron');
  const open = tbl.style.display === 'none';
  tbl.style.display = open ? 'block' : 'none';
  chv.style.transform = open ? 'rotate(180deg)' : '';
}

function showToast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg; t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 2500);
}
</script>

</body>
</html>
