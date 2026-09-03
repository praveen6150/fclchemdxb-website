<?php
/**
 * Falcon Chemicals CMS – Login View
 * Place at: /admin/views/pages/login.php
 *
 * This view is rendered by AuthController::login().
 * AJAX form posts go to /admin/login (same URL) where AuthController handles them.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<meta name="robots" content="noindex,nofollow"/>
<link href="../frontend/images/favicon/fav-icon.png" rel="icon">
<title>Falcon Chemical | Admin Login</title>
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Roboto',Arial,sans-serif;background:#6b1313;min-height:100vh;
  display:flex;align-items:center;justify-content:center;padding:1.5rem}

.card{background:#fff;border-radius:14px;padding:2rem 2rem 1.5rem;
  width:100%;max-width:430px;box-shadow:0 10px 40px rgba(0,0,0,.3)}

.brand{text-align:center;margin-bottom:1.4rem}
.brand img{width:130px;margin-bottom:8px}
.brand-name{font-size:17px;font-weight:700;color:#8B1A1A}
.brand-cert{font-size:11px;color:#777;margin-top:2px}
.brand-sub{font-size:12px;color:#555;margin-top:7px}

.tabs{display:flex;border:1px solid #e0e0e0;border-radius:8px;overflow:hidden;margin-bottom:1.25rem}
.tab-btn{flex:1;padding:10px 0;font-size:13px;font-weight:500;border:none;cursor:pointer;
  background:#fff;color:#8B1A1A;transition:background .2s,color .2s;font-family:inherit}
.tab-btn.active{background:#8B1A1A;color:#fff}

.panel{display:none}.panel.active{display:block}

.fl{display:block;font-size:11px;font-weight:600;color:#8B1A1A;
   letter-spacing:.5px;text-transform:uppercase;margin-bottom:5px}
.fli{display:flex;align-items:center;gap:6px;font-size:12px;font-weight:500;color:#444;margin-bottom:5px}
.fg{margin-bottom:13px}
.fr{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.fi{width:100%;padding:10px 12px;border:1px solid #ddd;border-radius:6px;
  font-size:13px;color:#333;outline:none;font-family:inherit;transition:border-color .2s}
.fi:focus{border-color:#8B1A1A}
.fi.tok{letter-spacing:6px;font-size:15px;text-align:center}
.fi.up{text-transform:uppercase;letter-spacing:2px}
.pw-w{position:relative}
.pw-w .fi{padding-right:38px}
.eye{position:absolute;right:10px;top:50%;transform:translateY(-50%);
  background:none;border:none;cursor:pointer;color:#aaa;padding:0}
.eye:hover{color:#8B1A1A}
.cf-w{margin-bottom:13px}

.btn{width:100%;padding:12px;background:#8B1A1A;color:#fff;border:none;
  border-radius:6px;font-size:14px;font-weight:600;cursor:pointer;
  letter-spacing:.5px;transition:background .2s;font-family:inherit;
  display:flex;align-items:center;justify-content:center;gap:8px}
.btn:hover:not(:disabled){background:#6e1414}
.btn:disabled{opacity:.5;cursor:not-allowed}
.sp{display:none;width:15px;height:15px;border:2px solid rgba(255,255,255,.3);
  border-top-color:#fff;border-radius:50%;animation:rot .7s linear infinite;flex-shrink:0}
@keyframes rot{to{transform:rotate(360deg)}}

.al{border-radius:6px;padding:10px 14px;font-size:13px;margin-bottom:13px;
  display:none;align-items:flex-start;gap:8px}
.al.on{display:flex}
.al-e{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5}
.al svg{flex-shrink:0;margin-top:1px}

.tab-links{text-align:center;margin-top:11px;font-size:12px;color:#666}
.tab-links a{color:#8B1A1A;text-decoration:none;font-weight:500}
.tab-links a:hover{text-decoration:underline}
.sec-note{text-align:center;margin-top:15px;font-size:11px;color:#999;
  display:flex;align-items:center;justify-content:center;gap:4px}

.ov{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);
  z-index:1000;align-items:flex-start;justify-content:center;
  padding:1.5rem 1rem;overflow-y:auto}
.ov.open{display:flex}

.mb{background:#fff;border-radius:14px;padding:2rem;
  width:100%;max-width:560px;
  position:relative;box-shadow:0 16px 48px rgba(0,0,0,.35);margin:auto}
.mc{position:absolute;top:12px;right:16px;background:none;border:none;
  cursor:pointer;font-size:22px;color:#aaa;line-height:1}
.mc:hover{color:#8B1A1A}
.mt{font-size:18px;font-weight:700;color:#8B1A1A;margin-bottom:3px}
.ms{font-size:12px;color:#777;margin-bottom:1.2rem;line-height:1.5}
.div{border:none;border-top:1px solid #eee;margin:13px 0}
.pk-hint{background:#fff8f0;border:1px solid #f5c28a;border-radius:6px;
  padding:10px 12px;font-size:12px;color:#7c4a00;margin-bottom:13px;
  display:flex;gap:8px;align-items:flex-start;line-height:1.5}
.pk-hint svg{flex-shrink:0;margin-top:1px}

.m-ok{display:none;text-align:center;padding:.5rem 0}
.m-ok.on{display:block}
.mf.hide{display:none}
.ok-ico{width:56px;height:56px;border-radius:50%;background:#dcfce7;
  display:flex;align-items:center;justify-content:center;margin:0 auto 12px}

#m-lost .mb{max-width:420px}

@media(max-width:600px){
  .fr{grid-template-columns:1fr}
  .card{padding:1.5rem 1.25rem}
  .mb{padding:1.5rem 1.25rem}
  .ov{padding:1rem .5rem}
}
</style>
</head>
<body>

<!-- LOGIN CARD -->
<div class="card">
  <div class="brand">
    <img src="../frontend/images/logo/fcl-logo.png" alt="Falcon Chemicals">
    <div class="brand-name">Falcon Chemicals L.L.C</div>
    <div class="brand-cert">An ISO 9001 and ISO 14001 Certified Company</div>
    <div class="brand-sub">CMS Admin Panel &mdash; Sign in to continue</div>
  </div>

  <div class="tabs">
    <button class="tab-btn active" id="btn-cred" onclick="switchTab('credentials')">Credentials</button>
    <button class="tab-btn"        id="btn-auth" onclick="switchTab('authenticator')">Authenticator</button>
  </div>

  <!-- Credentials Panel (username + password -> direct login) -->
  <div class="panel active" id="panel-credentials">
    <div class="al al-e" id="a-cred">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
      <span id="m-cred"></span>
    </div>
    <div class="fg">
      <label class="fli">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#8B1A1A" stroke-width="2">
          <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
        </svg>Username
      </label>
      <input type="text" id="u" class="fi" placeholder="Enter your username" autocomplete="username">
    </div>
    <div class="fg">
      <label class="fli">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#8B1A1A" stroke-width="2">
          <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
        </svg>Password
      </label>
      <div class="pw-w">
        <input type="password" id="pw" class="fi" placeholder="Enter your password" autocomplete="current-password">
        <button type="button" class="eye" onclick="togglePw('pw',this)">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
          </svg>
        </button>
      </div>
    </div>
    <div class="cf-w">
      <div class="cf-turnstile" data-sitekey="<?= CF_SITE_KEY ?>" data-theme="light"
           data-callback="ts_cred_ok" data-expired-callback="ts_cred_exp"></div>
    </div>
    <button class="btn" id="btn-login" onclick="doLogin()" disabled>
      <span class="sp" id="sp-l"></span>&#10145; SIGN IN
    </button>
    <div class="tab-links" style="margin-top:11px">
      New? <a href="#" onclick="openM('enroll');return false">Enroll Here</a>
      &nbsp;|&nbsp;
      <a href="#" onclick="openM('lost');return false">Lost Access?</a>
    </div>
  </div>

  <!-- Authenticator Panel (email + TOTP -> direct login, alternative method) -->
  <div class="panel" id="panel-authenticator">
    <div class="al al-e" id="a-auth">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
      <span id="m-auth"></span>
    </div>
    <div class="fg">
      <label class="fl">Registered Email / Identity</label>
      <input type="email" id="ae" class="fi" placeholder="name@company.com" autocomplete="email">
    </div>
    <div class="fg">
      <label class="fl">Authenticator Token</label>
      <input type="text" id="at" class="fi tok" placeholder="000000" maxlength="6"
             inputmode="numeric" autocomplete="one-time-code">
    </div>
    <div class="cf-w">
      <div class="cf-turnstile" data-sitekey="<?= CF_SITE_KEY ?>" data-theme="light"
           data-callback="ts_auth_ok" data-expired-callback="ts_auth_exp"></div>
    </div>
    <button class="btn" id="btn-verify" onclick="doMFA()" disabled>
      <span class="sp" id="sp-v"></span>Verify Identity
    </button>
    <div class="tab-links">
      New? <a href="#" onclick="openM('enroll');return false">Enroll Here</a>
      &nbsp;|&nbsp;
      <a href="#" onclick="openM('lost');return false">Lost Access?</a>
    </div>
  </div>

  <div class="sec-note">
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="2">
      <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
    </svg>Secured Admin Access Only
  </div>
</div>


<!-- MODAL: ENROLL -->
<div class="ov" id="m-enroll">
  <div class="mb">
    <button class="mc" onclick="closeM('enroll')">&times;</button>
    <div class="mf" id="enroll-form">
      <div class="mt">Enrollment</div>
      <div class="ms">Enter the passkey given to you by your administrator, then fill in your details to create your account.</div>
      <div class="pk-hint">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2">
          <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/>
          <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        Your administrator generates a one-time passkey from the admin panel and shares it with you. Each passkey can only be used once.
      </div>
      <div class="al al-e" id="a-enr">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"/>
        </svg>
        <span id="m-enr"></span>
      </div>
      <div class="fg">
        <label class="fl">Admin Passkey</label>
        <input type="text" id="e-pk" class="fi up" placeholder="e.g. FALC-A1B2-C3D4" maxlength="20"
               oninput="this.value=this.value.toUpperCase()">
      </div>
      <hr class="div">
      <div class="fr">
        <div class="fg"><label class="fl">First Name</label><input type="text" id="e-fn" class="fi" placeholder="John"></div>
        <div class="fg"><label class="fl">Last Name</label><input type="text" id="e-ln" class="fi" placeholder="Smith"></div>
      </div>
      <div class="fr">
        <div class="fg"><label class="fl">Email Address</label><input type="email" id="e-em" class="fi" placeholder="john@company.com"></div>
        <div class="fg"><label class="fl">Username</label><input type="text" id="e-un" class="fi" placeholder="johnsmith"></div>
      </div>
      <div class="fg">
        <label class="fl">Password</label>
        <div class="pw-w">
          <input type="password" id="e-pw" class="fi" placeholder="Min. 8 characters">
          <button type="button" class="eye" onclick="togglePw('e-pw',this)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
            </svg>
          </button>
        </div>
        <div style="font-size:11px;color:#aaa;margin-top:4px">Min. 8 characters</div>
      </div>
      <div class="cf-w">
        <div class="cf-turnstile" data-sitekey="<?= CF_SITE_KEY ?>" data-theme="light"
             data-callback="ts_enr_ok" data-expired-callback="ts_enr_exp"></div>
      </div>
      <button class="btn" id="btn-enr" onclick="doEnroll()" disabled>
        <span class="sp" id="sp-e"></span>Sign Up
      </button>
      <div style="text-align:center;margin-top:11px;font-size:12px">
        <a href="#" onclick="closeM('enroll');return false" style="color:#8B1A1A;text-decoration:none">
          &#8592; Back to Log in
        </a>
      </div>
    </div>
    <div class="m-ok" id="enr-ok">
      <div class="ok-ico">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5">
          <path d="M20 6L9 17l-5-5"/>
        </svg>
      </div>
      <div style="font-size:16px;font-weight:700;color:#166534;margin-bottom:6px">Account Created!</div>
      <div style="font-size:13px;color:#555;margin-bottom:16px">You can now sign in with your credentials.</div>
      <button class="btn" onclick="closeM('enroll');switchTab('credentials')"
              style="max-width:180px;margin:0 auto">Go to Sign In</button>
    </div>
  </div>
</div>


<!-- MODAL: LOST ACCESS -->
<div class="ov" id="m-lost">
  <div class="mb">
    <button class="mc" onclick="closeM('lost')">&times;</button>
    <div class="mf" id="lost-form">
      <div class="mt">Lost Access?</div>
      <div class="ms">Enter your registered email. Your administrator will review the request and issue a new passkey or reset your credentials.</div>
      <div class="al al-e" id="a-lost">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"/>
        </svg>
        <span id="m-lost-msg"></span>
      </div>
      <div class="fg">
        <label class="fl">Registered Email Address</label>
        <input type="email" id="lost-em" class="fi" placeholder="name@company.com">
      </div>
      <div class="cf-w">
        <div class="cf-turnstile" data-sitekey="<?= CF_SITE_KEY ?>" data-theme="light"
             data-callback="ts_lost_ok" data-expired-callback="ts_lost_exp"></div>
      </div>
      <button class="btn" id="btn-lost" onclick="doLost()" disabled>
        <span class="sp" id="sp-ls"></span>Submit Request
      </button>
      <div style="text-align:center;margin-top:11px;font-size:12px">
        <a href="#" onclick="closeM('lost');return false" style="color:#8B1A1A;text-decoration:none">
          &#8592; Back to Log in
        </a>
      </div>
    </div>
    <div class="m-ok" id="lost-ok">
      <div class="ok-ico">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5">
          <path d="M20 6L9 17l-5-5"/>
        </svg>
      </div>
      <div style="font-size:16px;font-weight:700;color:#166534;margin-bottom:6px">Request Submitted</div>
      <div style="font-size:13px;color:#555;margin-bottom:10px">Your administrator will contact you shortly to restore access.</div>
      <div style="font-size:12px;color:#888">Or contact directly:<br>
        <a href="mailto:inquiry@falconchemicals.com" style="color:#8B1A1A">inquiry@falconchemicals.com</a>
      </div>
      <button class="btn" onclick="closeM('lost')" style="max-width:140px;margin:16px auto 0">Close</button>
    </div>
  </div>
</div>


<script>
/* Turnstile callbacks */
const TS={cred:null,auth:null,enr:null,lost:null};
function ts_cred_ok(t){TS.cred=t;en('btn-login',true)}   function ts_cred_exp(){TS.cred=null;en('btn-login',false)}
function ts_auth_ok(t){TS.auth=t;en('btn-verify',true)}  function ts_auth_exp(){TS.auth=null;en('btn-verify',false)}
function ts_enr_ok(t) {TS.enr=t; en('btn-enr',true)}     function ts_enr_exp() {TS.enr=null; en('btn-enr',false)}
function ts_lost_ok(t){TS.lost=t;en('btn-lost',true)}    function ts_lost_exp(){TS.lost=null;en('btn-lost',false)}
function en(id,v){document.getElementById(id).disabled=!v}

/* Tabs */
function switchTab(tab){
  ['credentials','authenticator'].forEach(t=>{
    document.getElementById('panel-'+t).classList.toggle('active',t===tab);
    document.getElementById('btn-'+(t==='credentials'?'cred':'auth')).classList.toggle('active',t===tab);
  });
}

/* Modals */
function openM(id){
  document.getElementById('m-'+id).classList.add('open');
  document.body.style.overflow='hidden';
}
function closeM(id){
  document.getElementById('m-'+id).classList.remove('open');
  document.body.style.overflow='';
}
document.querySelectorAll('.ov').forEach(el=>
  el.addEventListener('click',e=>{ if(e.target===el) closeM(el.id.slice(2)) })
);

function loading(b,s,on){
  document.getElementById(b).disabled=on;
  document.getElementById(s).style.display=on?'block':'none';
}
function showErr(a,m,msg){document.getElementById(m).textContent=msg;document.getElementById(a).classList.add('on')}
function hideErr(a){document.getElementById(a).classList.remove('on')}

function togglePw(id,btn){
  const el=document.getElementById(id);
  const show=el.type==='password';
  el.type=show?'text':'password';
  btn.style.color=show?'#8B1A1A':'#aaa';
}

/* AJAX to /admin/login (AuthController handles it) */
async function api(data){
  const fd=new FormData();
  Object.entries(data).forEach(([k,v])=>fd.append(k,v));
  const r=await fetch(window.location.href,{
    method:'POST',
    headers:{'X-Requested-With':'XMLHttpRequest'},
    body:fd
  });
  return r.json();
}
function resetTS(key,btn){
  TS[key]=null; en(btn,false);
  if(window.turnstile) turnstile.reset();
}

/* Credentials tab ? direct login */
async function doLogin(){
  hideErr('a-cred');
  const u=document.getElementById('u').value.trim();
  const p=document.getElementById('pw').value;
  if(!u||!p){showErr('a-cred','m-cred','Please enter your username and password.');return}
  loading('btn-login','sp-l',true);
  try{
    const r=await api({action:'login',username:u,password:p,cf_token:TS.cred??''});
    if(r.ok && r.redirect){ window.location.href=r.redirect; return; }
    showErr('a-cred','m-cred',r.msg||'Login failed. Please try again.');
    resetTS('cred','btn-login');
  }catch(e){showErr('a-cred','m-cred','Network error. Please try again.')}
  loading('btn-login','sp-l',false);
}

/* Authenticator tab ? direct login (independent method) */
async function doMFA(){
  hideErr('a-auth');
  const em=document.getElementById('ae').value.trim();
  const tok=document.getElementById('at').value.replace(/\D/g,'');
  if(!em){showErr('a-auth','m-auth','Enter your registered email.');return}
  if(tok.length!==6){showErr('a-auth','m-auth','Enter the 6-digit code from your authenticator app.');return}
  loading('btn-verify','sp-v',true);
  try{
    const r=await api({action:'verify_mfa',email:em,totp_token:tok,cf_token:TS.auth??''});
    if(r.ok && r.redirect){ window.location.href=r.redirect; return; }
    showErr('a-auth','m-auth',r.msg||'Login failed. Please try again.');
    resetTS('auth','btn-verify');
  }catch(e){showErr('a-auth','m-auth','Network error. Please try again.')}
  loading('btn-verify','sp-v',false);
}

/* Enroll */
async function doEnroll(){
  hideErr('a-enr');
  const d={
    action:'enroll',
    passkey:document.getElementById('e-pk').value.trim(),
    first_name:document.getElementById('e-fn').value.trim(),
    last_name:document.getElementById('e-ln').value.trim(),
    email:document.getElementById('e-em').value.trim(),
    new_username:document.getElementById('e-un').value.trim(),
    new_password:document.getElementById('e-pw').value,
    cf_token:TS.enr??''
  };
  if(!d.passkey||!d.first_name||!d.last_name||!d.email||!d.new_username||!d.new_password){
    showErr('a-enr','m-enr','All fields are required.');return;
  }
  loading('btn-enr','sp-e',true);
  try{
    const r=await api(d);
    if(r.ok){
      document.getElementById('enroll-form').classList.add('hide');
      document.getElementById('enr-ok').classList.add('on');
    }else{showErr('a-enr','m-enr',r.msg);resetTS('enr','btn-enr')}
  }catch(e){showErr('a-enr','m-enr','Network error. Please try again.')}
  loading('btn-enr','sp-e',false);
}

/* Lost access */
async function doLost(){
  hideErr('a-lost');
  const email=document.getElementById('lost-em').value.trim();
  if(!email){showErr('a-lost','m-lost-msg','Please enter your email address.');return}
  loading('btn-lost','sp-ls',true);
  try{
    const r=await api({action:'lost_access',email,cf_token:TS.lost??''});
    if(r.ok){
      document.getElementById('lost-form').classList.add('hide');
      document.getElementById('lost-ok').classList.add('on');
    }else{showErr('a-lost','m-lost-msg',r.msg)}
  }catch(e){showErr('a-lost','m-lost-msg','Network error. Please try again.')}
  loading('btn-lost','sp-ls',false);
}

/* Enter key */
document.addEventListener('keydown',e=>{
  if(e.key!=='Enter') return;
  const act=document.querySelector('.panel.active'); if(!act) return;
  if(act.id==='panel-credentials' && !document.getElementById('btn-login').disabled) doLogin();
  if(act.id==='panel-authenticator' && !document.getElementById('btn-verify').disabled) doMFA();
});
</script>
</body>
</html>
