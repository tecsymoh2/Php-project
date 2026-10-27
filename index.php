<?php
// Symoh Storage: the app screen. The whole page is served from this PHP file.
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-cache');
echo <<<'__APP__'
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="theme-color" content="#5b4bff"><link rel="manifest" href="manifest.php"><link rel="icon" href="icon.php"><meta name="apple-mobile-web-app-capable" content="yes">
<title>Symoh Storage</title>
<style>
:root{--bg:#f3f4f8;--cd:#fff;--fg:#14152b;--mu:#6b6f8a;--bd:#e4e6f0;--ac:#5b4bff;--on:#fff;--c1:#4f8cff;--c2:#ff7a59;--c3:#a3abc6;--dg:#d92d20}
@media(prefers-color-scheme:dark){:root{--bg:#0e0f1d;--cd:#171829;--fg:#eceeff;--mu:#8e92b0;--bd:#272944;--ac:#8b7dff;--on:#10102a;--c3:#5a6082;--dg:#f97066}}
*{box-sizing:border-box;-webkit-tap-highlight-color:transparent}
body{margin:0;background:var(--bg);color:var(--fg);font:15px/1.4 system-ui,-apple-system,"Segoe UI",sans-serif;padding:env(safe-area-inset-top) 0 calc(100px + env(safe-area-inset-bottom))}
button,input,select{font:inherit;color:inherit}
button{background:var(--cd);border:1px solid var(--bd);border-radius:999px;padding:8px 14px;cursor:pointer;white-space:nowrap}
button:focus-visible,input:focus-visible,select:focus-visible{outline:2px solid var(--ac);outline-offset:2px}
.pri{background:var(--ac);color:var(--on);border-color:var(--ac);font-weight:600}
.dg{color:var(--dg)}
input,select{background:var(--cd);border:1px solid var(--bd);border-radius:12px;padding:10px 14px;width:100%}
main{max-width:1100px;margin:auto;padding:0 16px}
.top{display:flex;gap:10px;align-items:center;padding:16px 0 8px}
.top h1{font-size:20px;letter-spacing:-.02em;margin:0;white-space:nowrap}
.us{background:var(--cd);border:1px solid var(--bd);border-radius:18px;padding:14px 16px;margin:8px 0 12px}
.us b{font-size:24px;letter-spacing:-.02em}.us>span{color:var(--mu)}
.sg{display:flex;height:10px;border-radius:5px;overflow:hidden;background:var(--bd);margin:10px 0 8px;gap:2px}
.sg i{display:block}
.lg{display:flex;gap:14px;font-size:12px;color:var(--mu);flex-wrap:wrap}
.lg u{display:inline-block;width:8px;height:8px;border-radius:50%;margin-right:5px}
.row{display:flex;gap:6px;overflow-x:auto;padding:4px 0;align-items:center}
.on.nv,.ch.on{background:var(--fg);color:var(--bg);border-color:var(--fg)}
.row select{width:auto;padding:7px 10px;border-radius:999px}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:12px;margin-top:8px}
.t{position:relative;background:var(--cd);border:1px solid var(--bd);border-radius:16px;overflow:hidden}
.th{aspect-ratio:1;background:var(--bd);display:grid;place-items:center;position:relative;cursor:pointer;overflow:hidden}
.th img,.th video{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
.th b{color:var(--mu);font-size:18px}
.pl{position:absolute;color:#fff;background:#0007;border-radius:50%;width:34px;height:34px;display:grid;place-items:center;font-style:normal;font-size:13px}
.bdg{position:absolute;left:8px;top:8px;background:#0008;color:#fff;border-radius:999px;padding:2px 7px;font-size:12px;font-style:normal}
.bdg.f{left:auto;right:8px;top:auto;bottom:8px;color:#fbbf24}
.nm{padding:8px 40px 10px 12px;font-size:13px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis}
.nm small{display:block;color:var(--mu)}
.mo{position:absolute;right:2px;bottom:4px;border:0;background:none;font-size:20px;padding:4px 10px}
.ck{display:none;position:absolute;right:8px;top:8px;width:24px;height:24px;border-radius:50%;border:2px solid #fff;background:#0005}
.sm .ck{display:block}
.sel .ck{background:var(--ac);border-color:var(--ac)}
.sel .ck::after{content:"✓";color:var(--on);display:grid;place-items:center;font-size:14px;font-weight:700;height:100%}
.sel{outline:3px solid var(--ac)}
.list{grid-template-columns:1fr;gap:8px}
.list .t{display:flex;align-items:center}
.list .th{width:60px;flex:none;border-radius:12px;margin:8px}
.list .nm{flex:1;min-width:0}
.list .mo{position:static}
.list .ck{right:auto;left:12px;top:12px}
.empty{grid-column:1/-1;text-align:center;color:var(--mu);padding:56px 0}
.fab{position:fixed;right:18px;bottom:calc(18px + env(safe-area-inset-bottom));width:58px;height:58px;padding:0;font-size:30px;box-shadow:0 8px 24px #5b4bff55;z-index:6}
#bk{position:fixed;left:12px;right:12px;bottom:calc(12px + env(safe-area-inset-bottom));background:var(--fg);color:var(--bg);border-radius:18px;padding:10px;display:none;gap:6px;align-items:center;z-index:7;overflow-x:auto;max-width:640px;margin:auto}
#bk button{background:none;border-color:#8888;color:inherit}
#sh{position:fixed;inset:0;display:none;z-index:20}
#sh.on{display:block}
.bd{position:absolute;inset:0;background:#0007}
.pn{position:absolute;left:0;right:0;bottom:0;max-width:560px;margin:auto;background:var(--cd);border-radius:22px 22px 0 0;padding:18px 18px calc(18px + env(safe-area-inset-bottom));display:grid;gap:10px;max-height:88vh;overflow:auto}
.pn h3{margin:0 0 4px;font-size:17px;word-break:break-all}
.pn .a{text-align:left;border-radius:12px;border:0;background:none;padding:12px 6px;font-size:16px}
.lk{display:flex;align-items:center;border:1px solid var(--bd);border-radius:12px;overflow:hidden}
.lk span{padding-left:12px;color:var(--mu);white-space:nowrap;font-size:13px}
.lk input{border:0;border-radius:0}
#vw{position:fixed;inset:0;background:#05060ff2;display:none;z-index:15;color:#fff}
#vc{position:absolute;inset:64px 0 0;display:grid;place-items:center;padding:8px}
#vc img,#vc video{max-width:100%;max-height:100%}
#vw .h{position:absolute;top:0;left:0;right:0;padding:calc(10px + env(safe-area-inset-top)) 12px 10px;display:flex;gap:6px;align-items:center}
#vw .h b{flex:1;min-width:0;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;font-weight:500}
#vw button{background:#fff2;border:0;color:#fff}
#uq{position:fixed;right:18px;bottom:calc(90px + env(safe-area-inset-bottom));width:min(320px,calc(100% - 36px));background:var(--cd);border:1px solid var(--bd);border-radius:16px;padding:10px 12px;z-index:8;display:none;font-size:13px;box-shadow:0 8px 30px #0003}
#uq div{margin:4px 0}
#uq i{display:block;height:4px;border-radius:2px;background:var(--ac);margin-top:3px}
#ms{position:fixed;left:50%;top:calc(14px + env(safe-area-inset-top));transform:translateX(-50%);background:var(--fg);color:var(--bg);padding:9px 16px;border-radius:999px;display:none;z-index:40;max-width:90vw}
#dz{position:fixed;inset:0;display:none;place-items:center;background:#5b4bffcc;color:#fff;font-size:22px;z-index:50}
.drag #dz{display:grid}
#login{max-width:340px;margin:22vh auto;padding:20px;display:grid;gap:12px;text-align:center}
#ed{position:fixed;inset:0;background:#0b0c18;color:#fff;display:none;flex-direction:column;z-index:60}
#ed button{background:#ffffff1a;border-color:#ffffff22;color:#fff;padding:7px 12px}
#ed button.on,#ed .pri{background:var(--ac);border-color:var(--ac);color:var(--on)}
.eh{display:flex;gap:6px;align-items:center;flex-wrap:wrap;padding:calc(10px + env(safe-area-inset-top)) 12px 8px}
.eh small{opacity:.7;margin-left:6px}.sp{flex:1}
#es{flex:1;min-height:0;display:grid;place-items:center;overflow:hidden}
#ew{position:relative}#ec{display:block}
#cr{position:absolute;border:2px solid #fff;box-shadow:0 0 0 9999px #000a;touch-action:none;cursor:move;display:none}
#cr:not(.ac) i{display:none}
#cr i{position:absolute;width:30px;height:30px;touch-action:none}
#cr i::after{content:"";position:absolute;inset:8px;background:#fff;border-radius:50%}
#cr i[data-h=nw]{left:-15px;top:-15px}#cr i[data-h=ne]{right:-15px;top:-15px}#cr i[data-h=sw]{left:-15px;bottom:-15px}#cr i[data-h=se]{right:-15px;bottom:-15px}
#ets{display:flex;background:#14152b;border-top:1px solid #ffffff1a}
#ets button,#ed #ets button.on{flex:1;border:0;border-radius:0;background:none;padding:12px 4px}
#ed #ets button.on{box-shadow:inset 0 -3px var(--ac)}
#ep{display:flex;gap:8px;flex-wrap:wrap;align-items:center;justify-content:center;background:#14152b;padding:12px 14px calc(12px + env(safe-area-inset-bottom));min-height:116px}
#ep label{display:flex;align-items:center;gap:8px;width:100%;font-size:13px}
#ep label span{width:78px}#ep output{width:42px;text-align:right}
#ep input[type=range]{flex:1;padding:0;border:0;background:none}
#ep input[type=text],#ep select{width:auto;flex:1;background:#ffffff1a;border-color:#ffffff22;color:#fff}
#ep select option{color:#000}
.seg{display:flex;gap:6px;justify-content:center}
.seg button.on{background:var(--fg);color:var(--bg);border-color:var(--fg)}
.lp{display:grid;gap:12px}.lp[hidden]{display:none}
#login{margin:8vh auto}
.dots{display:flex;gap:10px;justify-content:center;align-items:center;min-height:28px}
.dots i{width:14px;height:14px;border-radius:50%;background:var(--fg)}
.kp{display:grid;grid-template-columns:repeat(3,1fr);gap:10px}
.kp button{padding:16px 0;font-size:22px;border-radius:18px}
.pad{width:150px;height:150px;border-radius:50%;margin:6px auto;display:grid;place-items:center;border:2px solid var(--ac);background:color-mix(in srgb,var(--ac) 10%,transparent);color:var(--ac);position:relative;cursor:pointer;padding:0}
.pad.sm{width:104px;height:104px}.pad.sm svg{width:44px;height:44px}
.pad:active{transform:scale(.97)}
.pad.scan::after{content:"";position:absolute;inset:-7px;border-radius:50%;border:3px solid transparent;border-top-color:var(--ac);animation:spin 1s linear infinite}
.pad.ok{border-color:#16a34a;color:#16a34a;background:#16a34a1a}
@keyframes spin{to{transform:rotate(360deg)}}
@media(prefers-reduced-motion:reduce){.pad.scan::after{animation:none}}
</style>
</head>
<body>
<div id="login" hidden>
  <h1>Symoh Storage</h1>
  <div class="seg" id="lm"></div>
  <div id="lp-pw" class="lp"><input id="pw" type="password" placeholder="Password" autocomplete="current-password"><button class="pri" data-act="login">Sign in</button></div>
  <div id="lp-pin" class="lp" hidden><div class="dots" id="dots"></div><div class="kp"><button data-act="pd" data-d="1">1</button><button data-act="pd" data-d="2">2</button><button data-act="pd" data-d="3">3</button><button data-act="pd" data-d="4">4</button><button data-act="pd" data-d="5">5</button><button data-act="pd" data-d="6">6</button><button data-act="pd" data-d="7">7</button><button data-act="pd" data-d="8">8</button><button data-act="pd" data-d="9">9</button><button data-act="pdel" aria-label="Delete">⌫</button><button data-act="pd" data-d="0">0</button><button class="pri" data-act="pgo" aria-label="Sign in">✓</button></div></div>
  <div id="lp-fp" class="lp" hidden><button class="pad" id="fpad" data-act="fplogin" aria-label="Fingerprint pad"><svg viewBox="0 0 64 64" width="64" height="64" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" aria-hidden="true"><path d="M10 42V30a22 22 0 0 1 44 0v6"/><path d="M18 46V30a14 14 0 0 1 28 0v14"/><path d="M26 50V30a6 6 0 0 1 12 0v18"/><path d="M32 30v20"/><path d="M46 44v4"/><path d="M14 52v2"/></svg></button><p id="fpt" style="margin:0;color:var(--mu)">Place your finger on the pad</p></div>
</div>
<main id="app" hidden>
  <div class="top"><h1>Symoh Storage</h1><input id="q" type="search" placeholder="Search files"><button data-act="fpmenu" title="Fingerprint sign-in" aria-label="Fingerprint sign-in">👆</button><button data-act="logout">Log out</button></div>
  <div class="us" id="us"></div>
  <div class="row" id="nav"></div>
  <div class="row" id="fo"></div>
  <div class="row">
    <select id="kd"><option value="all">All types<option value="image">Photos<option value="video">Videos<option value="audio">Audio<option value="other">Documents</select>
    <select id="so"><option value="new">Newest<option value="old">Oldest<option value="name">Name<option value="size">Largest</select>
    <button data-act="lay" id="ly"></button><button data-act="smode" id="sb">Select</button>
  </div>
  <div class="grid" id="grid"></div>
</main>
<button class="pri fab" data-act="pick" aria-label="Upload" hidden id="fab">+</button>
<input type="file" id="file" multiple hidden>
<div id="bk"></div>
<div id="uq"></div>
<div id="vw"><div class="h"><button data-act="vp">‹</button><button data-act="vn">›</button><b id="vt"></b><button data-act="share">🔗</button><button data-act="dl">⬇</button><button data-act="vx">✕</button></div><div id="vc"></div></div>
<div id="sh"></div>
<div id="ed"><div class="eh"><div><b id="et"></b><small id="ei"></small></div><span class="sp"></span><button data-e="skip">Skip</button><button data-e="all" id="ea">All originals</button><button data-e="orig">Original</button><button class="pri" data-e="up">Upload</button></div><div id="es"><div id="ew"><canvas id="ec"></canvas><div id="cr"><i data-h="nw"></i><i data-h="ne"></i><i data-h="sw"></i><i data-h="se"></i></div></div></div><div id="ets"></div><div id="ep"></div></div>
<div id="ms"></div>
<div id="dz">Drop to upload</div>
<script>
const $=s=>document.querySelector(s),CAP=5*2**30;
const S={tab:"files",folder:"*",kind:"all",sort:"new",lay:"grid",q:"",rows:[],ef:[],sel:new Set(),sm:false,cur:null,vi:[]};
const fmt=n=>n<1024?n+" B":n<1048576?(n/1024).toFixed(0)+" KB":n<1073741824?(n/1048576).toFixed(1)+" MB":(n/1073741824).toFixed(2)+" GB";
const esc=s=>String(s??"").replace(/[&<>"]/g,c=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;"}[c]));
const kind=f=>{const t=f.type||"";return t.startsWith("image/")?"image":t.startsWith("video/")?"video":t.startsWith("audio/")?"audio":"other"};
const ext=f=>(f.name.split(".").pop()||"file").slice(0,5).toUpperCase();
const slugify=n=>{const m=/\.([a-z0-9]{1,8})$/i.exec(n);return(n.replace(/\.[^.]*$/,"").toLowerCase().replace(/[^a-z0-9]+/g,"-").replace(/^-|-$/g,"")||"file")+"."+(m?m[1].toLowerCase():"bin")};
const file=id=>S.rows.find(f=>f.id===id);
let mt;const toast=t=>{const m=$("#ms");m.textContent=t;m.style.display="block";clearTimeout(mt);mt=setTimeout(()=>m.style.display="none",3500)};
async function api(b){
  const r=await fetch("api.php",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify(b)});
  const j=await r.json().catch(()=>({}));
  if(r.status===401&&b.a!=="login"){$("#app").hidden=true;$("#fab").hidden=true;$("#login").hidden=false;throw 0}
  if(!r.ok)throw new Error(j.error||"Something went wrong");
  return j;
}
const run=async f=>{try{return await f()}catch(e){if(e)toast(e.message)}};
async function load(){const j=await api({a:"list"});S.rows=j.rows;$("#login").hidden=true;$("#app").hidden=false;$("#fab").hidden=false;render()}
const folders=()=>[...new Set([...S.rows.filter(f=>!f.trashed_at&&f.folder).map(f=>f.folder),...S.ef])].sort();
function vis(){
  let r=S.rows.filter(f=>S.tab==="trash"?f.trashed_at:!f.trashed_at);
  if(S.tab==="fav")r=r.filter(f=>f.favorite);
  if(S.tab==="shared")r=r.filter(f=>f.share_slug);
  if(S.tab==="files"&&S.folder!=="*")r=r.filter(f=>f.folder===S.folder);
  if(S.kind!=="all")r=r.filter(f=>kind(f)===S.kind);
  if(S.q)r=r.filter(f=>f.name.toLowerCase().includes(S.q));
  const o={new:(a,b)=>b.created_at.localeCompare(a.created_at),old:(a,b)=>a.created_at.localeCompare(b.created_at),name:(a,b)=>a.name.localeCompare(b.name),size:(a,b)=>b.size-a.size};
  return r.sort(o[S.sort]);
}
function render(){
  const u={image:0,video:0,other:0};S.rows.forEach(f=>u[kind(f)==="image"||kind(f)==="video"?kind(f):"other"]+=f.size);
  const tot=u.image+u.video+u.other,pc=n=>Math.max(n/CAP*100,n?1:0);
  $("#us").innerHTML=`<b>${fmt(tot)}</b> <span>of 5 GB used</span><div class="sg"><i style="width:${pc(u.image)}%;background:var(--c1)"></i><i style="width:${pc(u.video)}%;background:var(--c2)"></i><i style="width:${pc(u.other)}%;background:var(--c3)"></i></div><div class="lg"><span><u style="background:var(--c1)"></u>Photos ${fmt(u.image)}</span><span><u style="background:var(--c2)"></u>Videos ${fmt(u.video)}</span><span><u style="background:var(--c3)"></u>Other ${fmt(u.other)}</span></div>`;
  $("#nav").innerHTML=[["files","All files"],["fav","Favorites"],["shared","Shared"],["trash","Trash"]].map(([k,n])=>`<button class="nv ${S.tab===k?"on":""}" data-tab="${k}">${n}</button>`).join("");
  const fo=$("#fo");fo.hidden=!(S.tab==="files"||S.tab==="trash");
  fo.innerHTML=S.tab==="trash"?(S.rows.some(f=>f.trashed_at)?`<button class="dg" data-act="empty">Empty trash</button>`:""):["*",...folders()].map(n=>`<button class="ch ${n===S.folder?"on":""}" data-f="${esc(n)}">${n==="*"?"All folders":esc(n)}</button>`).join("")+`<button data-act="newf">+ Folder</button>`+(S.folder!=="*"?`<button data-act="fmenu">Edit folder</button>`:"");
  $("#kd").value=S.kind;$("#so").value=S.sort;$("#ly").textContent=S.lay==="grid"?"List":"Grid";$("#sb").textContent=S.sm?"Done":"Select";
  const v=vis();
  $("#grid").className="grid"+(S.lay==="list"?" list":"")+(S.sm?" sm":"");
  $("#grid").innerHTML=v.length?v.map(tile).join(""):`<div class="empty">${S.q?"No files match your search":S.tab==="trash"?"Trash is empty":S.tab==="shared"?"No public links yet":"Nothing here yet. Tap + or drop files anywhere."}</div>`;
  bar();
}
function tile(f){
  const k=kind(f),th=k==="image"?`<img loading="lazy" src="${f.thumb||f.url}"${f.thumb?` data-full="${f.url}" onerror="this.onerror=null;this.src=this.dataset.full"`:""} alt="">`:k==="video"?`<video src="${f.url}#t=0.1" preload="metadata" muted playsinline></video><i class="pl">▶</i>`:`<b>${esc(ext(f))}</b>`;
  return `<div class="t ${S.sel.has(f.id)?"sel":""}" data-id="${f.id}"><div class="th" data-act="open">${th}${f.share_slug?`<i class="bdg">🔗</i>`:""}${f.favorite?`<i class="bdg f">★</i>`:""}<span class="ck"></span></div><div class="nm">${esc(f.name)}<small>${fmt(f.size)}${f.folder?" · "+esc(f.folder):""}</small></div><button class="mo" data-act="more" aria-label="More">⋯</button></div>`;
}
function bar(){
  const b=$("#bk"),n=S.sel.size;b.style.display=S.sm?"flex":"none";$("#fab").style.display=S.sm?"none":"";
  b.innerHTML=`<b style="padding:0 8px">${n} selected</b>`+(S.tab==="trash"?`<button data-act="b-all">All</button><button data-act="b-restore">Restore</button><button data-act="b-del">Delete</button>`:`<button data-act="b-all">All</button><button data-act="b-fav">★</button><button data-act="b-move">Move</button><button data-act="b-trash">Trash</button>`);
}
const sheet=h=>{$("#sh").innerHTML=`<div class="bd" data-act="sx"></div><div class="pn">${h}</div>`;$("#sh").classList.add("on")};
const shut=()=>$("#sh").classList.remove("on");
const copy=async t=>{try{await navigator.clipboard.writeText(t);toast("Link copied")}catch{prompt("Copy this link",t)}};
function more(f){
  S.cur=f.id;
  sheet(`<h3>${esc(f.name)}</h3>`+(f.trashed_at?`<button class="a" data-act="restore">↩ Restore</button><button class="a dg" data-act="del">Delete forever</button>`:`<button class="a" data-act="fav">${f.favorite?"☆ Remove from favorites":"★ Add to favorites"}</button><button class="a" data-act="share">🔗 ${f.share_slug?"Manage public link":"Create public link"}</button><button class="a" data-act="dl">⬇ Download</button><button class="a" data-act="edit">✎ Rename or move</button><button class="a dg" data-act="trash">🗑 Move to trash</button>`));
}
function share(f){
  S.cur=f.id;
  const d=f.share_expires_at?Math.max(1,Math.round((new Date(f.share_expires_at)-Date.now())/864e5)):0;
  sheet(`<h3>Public link</h3><div class="lk"><span>${esc(location.host)}/</span><input id="sl" value="${esc(f.share_slug||slugify(f.name))}" autocapitalize="off" spellcheck="false"></div><select id="ex"><option value="0">Never expires<option value="1">Expires in 1 day<option value="7">Expires in 7 days<option value="30">Expires in 30 days</select>${f.share_slug?`<small style="color:var(--mu)">${f.share_views} views${d?` · about ${d} day(s) left`:""}</small>`:""}<button class="pri" data-act="pub">${f.share_slug?"Update and copy link":"Create and copy link"}</button>${f.share_slug?`<button data-act="cp">Copy link</button><button data-act="cpd">Copy download link</button>${navigator.share?`<button data-act="ns">Share…</button>`:""}<button class="dg" data-act="unpub">Stop sharing</button>`:""}`);
}
function view(id){
  S.cur=id;S.vi=vis();const f=file(id);if(!f)return;
  $("#vw").style.display="block";$("#vt").textContent=f.name;const k=kind(f);
  const mob=/Android|iPhone|iPad/i.test(navigator.userAgent),pdf=f.type==="application/pdf";
  $("#vc").innerHTML=k==="image"?`<img src="${f.url}" alt="">`:k==="video"?`<video src="${f.url}" controls autoplay playsinline></video>`:k==="audio"?`<audio src="${f.url}" controls autoplay></audio>`:pdf&&!mob?`<iframe src="${f.url}" style="width:100%;height:100%;border:0;background:#fff"></iframe>`:`<div style="text-align:center"><h1>${esc(ext(f))}</h1><p>${fmt(f.size)}</p>${pdf?`<p><a class="pri" style="padding:8px 16px;border-radius:999px;display:inline-block;text-decoration:none" href="${f.url}" target="_blank" rel="noopener">Open</a></p>`:""}<button class="pri" data-act="dl">Download</button></div>`;
}
const vshut=()=>{$("#vw").style.display="none";$("#vc").innerHTML=""};
const step=d=>{const i=S.vi.findIndex(f=>f.id===S.cur),n=S.vi[i+d];if(n)view(n.id)};
const bulk=async(op,extra={})=>{await api({a:"bulk",op,ids:[...S.sel],...extra});S.sel.clear();S.sm=false;await load()};
document.addEventListener("click",e=>{
  const nv=e.target.closest("[data-tab]");if(nv){S.tab=nv.dataset.tab;S.folder="*";return render()}
  const ch=e.target.closest("[data-f]");if(ch){S.folder=ch.dataset.f;return render()}
  const b=e.target.closest("[data-act]");if(!b)return;
  const act=b.dataset.act,tid=b.closest("[data-id]")?.dataset.id,f=file(tid||S.cur);
  run(async()=>{
    switch(act){
      case"login":await api({a:"login",password:$("#pw").value});$("#pw").value="";return load();
      case"logout":await api({a:"logout"});S.rows=[];S.sel.clear();S.sm=false;S.q="";$("#q").value="";$("#grid").innerHTML="";$("#app").hidden=true;$("#fab").hidden=true;$("#bk").style.display="none";$("#login").hidden=false;$("#pw").focus();fpInit();return;
      case"fplogin":{
        const o=S.fpo;if(!o)return;
        const pad=$("#fpad"),t=$("#fpt");pad.classList.add("scan");t.textContent="Scanning… use your phone's fingerprint sensor";
        try{
          const c=await navigator.credentials.get({publicKey:{challenge:b64d(o.token.split(".")[0]),rpId:location.hostname,allowCredentials:o.ids.map(id=>({type:"public-key",id:b64d(id)})),userVerification:"required",timeout:60000}});
          const r=c.response;
          await api({a:"pk_login",token:o.token,id:b64e(c.rawId),authenticatorData:b64e(r.authenticatorData),clientDataJSON:b64e(r.clientDataJSON),signature:b64e(r.signature)});
          pad.classList.replace("scan","ok");t.textContent="Fingerprint recognised";
        }catch(err){pad.classList.remove("scan");t.textContent="Place your finger on the pad";fpInit(true);throw err.name==="NotAllowedError"?new Error("Fingerprint cancelled or not recognised. Try again or use your password."):err}
        return load();
      }
      case"fpmenu":{
        const [l,o]=await Promise.all([api({a:"pk_list"}),api({a:"pk_reg_options"})]);S.fpr=o;S.hasPin=!!l.pin;
        const dt=x=>new Date(x).toLocaleDateString(),can=!!window.PublicKeyCredential;
        sheet(`<h3>Quick sign-in</h3><b>Fingerprint</b><p style="margin:0;color:var(--mu)">${can?"Uses your phone's own fingerprint sensor, so it accepts the fingerprints saved in your phone's settings.":"This browser does not support fingerprint sign-in."}</p>${l.keys.map(k=>`<div style="display:flex;align-items:center;gap:8px"><span style="flex:1;min-width:0">${esc(k.name)}<small style="display:block;color:var(--mu)">Added ${dt(k.created_at)}${k.last_used_at?" · used "+dt(k.last_used_at):""}</small></span><button class="dg" data-act="fpdel" data-k="${esc(k.id)}">Remove</button></div>`).join("")}${can?`<button class="pad sm" id="fpa" data-act="fpadd" aria-label="Add this device">${FP}</button><p id="fpat" style="text-align:center;margin:0;color:var(--mu)">Tap the pad to add this device</p><button data-act="fpadd" data-kind="key">Add a fingerprint security key</button><small style="color:var(--mu)">A security key such as a YubiKey Bio keeps its own fingerprints, separate from your phone.</small>`:""}<b>App PIN</b><p style="margin:0;color:var(--mu)">A 4 to 8 digit code you set here. It works on any device.</p><button class="pri" data-act="pinset">${l.pin?"Change PIN":"Set a PIN"}</button>${l.pin?`<button class="dg" data-act="pindel">Remove PIN</button>`:""}`);
        return;
      }
      case"fpadd":{
        const o=S.fpr;if(!o)return;
        const ua=navigator.userAgent,name=b.dataset.kind==="key"?"Security key":/iPhone/.test(ua)?"iPhone":/iPad/.test(ua)?"iPad":/Android/.test(ua)?"Android phone":/Windows/.test(ua)?"Windows PC":/Mac/.test(ua)?"Mac":"This device",t=$("#fpat");
        b.classList.add("scan");t.textContent="Scanning… use your phone's fingerprint sensor";
        try{
          const c=await navigator.credentials.create({publicKey:{rp:{name:"Symoh Storage",id:location.hostname},user:{id:new TextEncoder().encode("owner"),name:"owner",displayName:"Owner"},challenge:b64d(o.token.split(".")[0]),pubKeyCredParams:[{type:"public-key",alg:-7},{type:"public-key",alg:-257}],authenticatorSelection:{authenticatorAttachment:b.dataset.kind==="key"?"cross-platform":"platform",userVerification:"required",residentKey:"preferred"},excludeCredentials:o.ids.map(id=>({type:"public-key",id:b64d(id)})),timeout:60000,attestation:"none"}});
          const r=c.response;
          if(!r.getPublicKey)throw new Error("This browser can't be used for fingerprint sign-in");
          await api({a:"pk_register",token:o.token,id:b64e(c.rawId),publicKey:b64e(r.getPublicKey()),alg:r.getPublicKeyAlgorithm(),clientDataJSON:b64e(r.clientDataJSON),name});
          b.classList.replace("scan","ok");t.textContent="Fingerprint added";
        }catch(err){b.classList.remove("scan");t.textContent="Tap the pad to add this device";throw err.name==="NotAllowedError"?new Error("Cancelled or not recognised"):err}
        setTimeout(shut,1200);return toast("Fingerprint added. You can now sign in with it.");
      }
      case"fpdel":if(confirm("Remove fingerprint sign-in for this device?")){await api({a:"pk_del",id:b.dataset.k});shut();return toast("Removed")}return;
      case"pinset":sheet(`<h3>${S.hasPin?"Change":"Set"} PIN</h3><input id="p1" type="password" inputmode="numeric" maxlength="8" autocomplete="new-password" placeholder="New PIN (4 to 8 digits)"><input id="p2" type="password" inputmode="numeric" maxlength="8" autocomplete="new-password" placeholder="Repeat PIN"><button class="pri" data-act="pinsave">Save PIN</button>`);return;
      case"pinsave":{const p1=$("#p1").value,p2=$("#p2").value;if(!/^\d{4,8}$/.test(p1))throw new Error("Use 4 to 8 digits");if(p1!==p2)throw new Error("The PINs do not match");await api({a:"pin_set",pin:p1});S.hasPin=true;shut();return toast("PIN saved")}
      case"pindel":if(confirm("Remove the app PIN?")){await api({a:"pin_del"});S.hasPin=false;shut();return toast("PIN removed")}return;
      case"lm":S.lm=b.dataset.m;try{localStorage.lm=S.lm}catch{}renderLogin();return;
      case"pd":if(S.pin.length<8){S.pin+=b.dataset.d;pinDraw()}return;
      case"pdel":S.pin=S.pin.slice(0,-1);pinDraw();return;
      case"pgo":{if(S.pin.length<4)throw new Error("Enter at least 4 digits");try{await api({a:"pin_login",pin:S.pin})}catch(e){S.pin="";pinDraw();throw e}S.pin="";pinDraw();return load()}
      case"lay":S.lay=S.lay==="grid"?"list":"grid";return render();
      case"smode":S.sm=!S.sm;S.sel.clear();return render();
      case"pick":return $("#file").click();
      case"sx":return shut();
      case"open":if(S.sm){S.sel.has(tid)?S.sel.delete(tid):S.sel.add(tid);return render()}return view(tid);
      case"more":return more(file(tid));
      case"vp":return step(-1);case"vn":return step(1);case"vx":return vshut();
      case"newf":sheet(`<h3>New folder</h3><input id="nf" placeholder="Folder name"><button class="pri" data-act="mkf">Create folder</button>`);return;
      case"mkf":{const n=$("#nf").value.trim();if(n){if(!S.ef.includes(n))S.ef.push(n);S.folder=n;shut();render();toast("Upload a file to keep this folder")}return}
      case"fav":await api({a:"fav",id:f.id,v:!f.favorite});shut();return load();
      case"trash":await api({a:"bulk",op:"trash",ids:[f.id]});shut();vshut();return load();
      case"restore":await api({a:"bulk",op:"restore",ids:[f.id]});shut();return load();
      case"del":if(confirm("Delete this file forever?")){await api({a:"bulk",op:"del",ids:[f.id]});shut();return load()}return;
      case"empty":if(confirm("Permanently delete everything in trash?")){await api({a:"empty"});return load()}return;
      case"dl":{const j=await api({a:"dl",id:f.id});shut();location.href=j.url;return}
      case"edit":sheet(`<h3>Rename or move</h3><input id="en" value="${esc(f.name)}"><input id="ef" list="fl" placeholder="Folder (blank for none)" value="${esc(f.folder)}"><datalist id="fl">${folders().map(x=>`<option value="${esc(x)}">`).join("")}</datalist><button class="pri" data-act="saveedit">Save</button>`);return;
      case"saveedit":{const n=$("#en").value.trim();if(!n)return;await api({a:"edit",id:f.id,name:n,folder:$("#ef").value.trim()});shut();return load()}
      case"share":return share(f);
      case"pub":{const j=await api({a:"publish",id:f.id,days:$("#ex").value,slug:$("#sl").value.trim()});shut();await load();return copy(location.origin+"/"+j.slug)}
      case"cp":return copy(location.origin+"/"+f.share_slug);
      case"unpub":await api({a:"unpublish",id:f.id});shut();return load();
      case"b-all":vis().forEach(x=>S.sel.add(x.id));return render();
      case"cpd":return copy(location.origin+"/"+f.share_slug+"?dl=1");
      case"ns":return navigator.share({title:f.name,url:location.origin+"/"+f.share_slug}).catch(()=>{});
      case"fmenu":sheet(`<h3>Folder</h3><input id="fn" value="${esc(S.folder)}"><button class="pri" data-act="frename">Rename folder</button><button class="dg" data-act="fremove">Remove folder (files stay)</button>`);return;
      case"frename":{const n=$("#fn").value.trim(),o=S.folder;if(!n||n===o)return shut();await api({a:"folder",from:o,to:n});S.ef=S.ef.map(x=>x===o?n:x);S.folder=n;shut();return load()}
      case"fremove":if(confirm("Remove this folder? Files stay in All folders.")){const o=S.folder;await api({a:"folder",from:o,to:""});S.ef=S.ef.filter(x=>x!==o);S.folder="*";shut();return load()}return;
      case"b-fav":return bulk("fav");
      case"b-trash":return bulk("trash");
      case"b-restore":return bulk("restore");
      case"b-del":if(confirm("Delete the selected files forever?"))return bulk("del");return;
      case"b-move":sheet(`<h3>Move ${S.sel.size} file(s)</h3><input id="mf" list="fl" placeholder="Folder (blank for none)"><datalist id="fl">${folders().map(x=>`<option value="${esc(x)}">`).join("")}</datalist><button class="pri" data-act="b-go">Move</button>`);return;
      case"b-go":{const fo=$("#mf").value.trim();shut();return bulk("move",{folder:fo})}
    }
  });
});
$("#q").oninput=e=>{S.q=e.target.value.trim().toLowerCase();render()};
$("#kd").onchange=e=>{S.kind=e.target.value;render()};
$("#so").onchange=e=>{S.sort=e.target.value;render()};
$("#pw").onkeydown=e=>{if(e.key==="Enter")document.querySelector('[data-act="login"]').click()};
addEventListener("keydown",e=>{
  if(e.key==="Escape"){shut();vshut()}
  if($("#vw").style.display==="block"&&!$("#sh").classList.contains("on")){if(e.key==="ArrowLeft")step(-1);if(e.key==="ArrowRight")step(1)}
});
let E=null,skipAll=false;
const clamp=(v,a,b)=>Math.min(b,Math.max(a,v)),canEdit=f=>/^image\/(jpeg|png|webp|bmp|avif)$/.test(f.type);
async function intake(fs){
  fs=[...fs];const n=fs.filter(canEdit).length,out=[];let i=0;skipAll=false;
  for(const f of fs){if(!canEdit(f)||skipAll){out.push(f);continue}const r=await openEd(f,++i,n);if(r)out.push(r)}
  if(out.length)upload(out);
}
function openEd(f,n,tot){return new Promise(async res=>{
  let bm;try{bm=await createImageBitmap(f)}catch{toast("Can't edit "+f.name+", uploading as is");return res(f)}
  E={bm,rot:0,fx:0,fy:0,c:{x:0,y:0,w:1,h:1},ar:0,bri:100,con:100,sat:100,gray:0,sc:100,qp:90,tab:"rot",f,res,name:f.name.replace(/\.[^.]*$/,""),fmt:/png$/.test(f.type)?"png":/webp$/.test(f.type)?"webp":"jpeg"};
  $("#et").textContent=`Edit photo ${n} of ${tot}`;$("#ea").style.display=tot>1?"":"none";$("#ed").style.display="flex";
  build();tabs();panel();requestAnimationFrame(draw);
})}
const fin=r=>{const x=E;E=null;$("#ed").style.display="none";x.res(r)};
function build(){
  const b=E.bm,q=E.rot%2,W0=q?b.height:b.width,H0=q?b.width:b.height,k=Math.min(1,4096/Math.max(W0,H0)),W=Math.round(W0*k),H=Math.round(H0*k);
  const c=document.createElement("canvas");c.width=W;c.height=H;const g=c.getContext("2d");
  g.translate(W/2,H/2);g.scale(E.fx?-1:1,E.fy?-1:1);g.rotate(E.rot*Math.PI/2);g.scale(k,k);g.drawImage(b,-b.width/2,-b.height/2);
  E.src=c;E.sw=W;E.sh=H;
}
const outSize=()=>[Math.max(1,Math.round(E.sw*E.c.w*E.sc/100)),Math.max(1,Math.round(E.sh*E.c.h*E.sc/100))];
const info=()=>{const[w,h]=outSize();$("#ei").textContent=w+" × "+h};
const filt=()=>{$("#ec").style.filter=`brightness(${E.bri}%) contrast(${E.con}%) saturate(${E.gray?0:E.sat}%)`};
function draw(){
  if(!E)return;const st=$("#es"),k=Math.min((st.clientWidth-16)/E.sw,(st.clientHeight-16)/E.sh),w=Math.max(1,Math.round(E.sw*k)),h=Math.max(1,Math.round(E.sh*k)),c=$("#ec");
  c.width=w;c.height=h;c.getContext("2d").drawImage(E.src,0,0,w,h);$("#ew").style.width=w+"px";$("#ew").style.height=h+"px";filt();drawCr();
}
function drawCr(){
  const c=$("#cr"),f=E.c,full=f.w>.999&&f.h>.999,on=E.tab==="crop";
  c.style.display=on||!full?"block":"none";c.style.pointerEvents=on?"auto":"none";c.classList.toggle("ac",on);
  c.style.left=f.x*100+"%";c.style.top=f.y*100+"%";c.style.width=f.w*100+"%";c.style.height=f.h*100+"%";info();
}
function setAr(ar){
  E.ar=ar;if(ar){let w=1,h=E.sw/(ar*E.sh);if(h>1){h=1;w=ar*E.sh/E.sw}E.c={x:(1-w)/2,y:(1-h)/2,w,h}}
  panel();drawCr();
}
$("#cr").addEventListener("pointerdown",e=>{
  if(!E||E.tab!=="crop")return;e.preventDefault();
  const t=e.target.dataset.h,W=$("#ec").clientWidth,H=$("#ec").clientHeight,s={...E.c},x0=e.clientX,y0=e.clientY;
  const mv=ev=>{
    const dx=(ev.clientX-x0)/W,dy=(ev.clientY-y0)/H;let c;
    if(!t)c={...s,x:clamp(s.x+dx,0,1-s.w),y:clamp(s.y+dy,0,1-s.h)};
    else{
      let l=s.x,r=s.x+s.w,tp=s.y,b=s.y+s.h;
      if(t[1]==="w")l=clamp(s.x+dx,0,r-.05);else r=clamp(r+dx,l+.05,1);
      if(t[0]==="n")tp=clamp(s.y+dy,0,b-.05);else b=clamp(b+dy,tp+.05,1);
      c={x:l,y:tp,w:r-l,h:b-tp};
      if(E.ar){
        let w=c.w,h=w*E.sw/(E.ar*E.sh);const ax=t[1]==="w"?r:l,ay=t[0]==="n"?b:tp,mh=t[0]==="n"?ay:1-ay,mw=t[1]==="w"?ax:1-ax;
        if(h>mh){h=mh;w=h*E.ar*E.sh/E.sw}if(w>mw){w=mw;h=w*E.sw/(E.ar*E.sh)}
        c={x:t[1]==="w"?ax-w:ax,y:t[0]==="n"?ay-h:ay,w,h};
      }
    }
    E.c=c;drawCr();
  };
  const up=()=>{removeEventListener("pointermove",mv);removeEventListener("pointerup",up)};
  addEventListener("pointermove",mv);addEventListener("pointerup",up);
});
const tabs=()=>{$("#ets").innerHTML=[["rot","Rotate"],["crop","Crop"],["size","Resize"],["adj","Adjust"],["out","Export"]].map(([k,n])=>`<button data-et="${k}" class="${E.tab===k?"on":""}">${n}</button>`).join("")};
const rng=(k,n,a,b)=>`<label><span>${n}</span><input type="range" min="${a}" max="${b}" value="${E[k]}" data-r="${k}"><output>${E[k]}%</output></label>`;
function panel(){
  const T=E.tab,B=(a,t,v,on)=>`<button data-e="${a}"${v!==undefined?` data-v="${v}"`:""} class="${on?"on":""}">${t}</button>`;
  $("#ep").innerHTML=T==="rot"?B("rl","⟲ Rotate left")+B("rr","⟳ Rotate right")+B("fx","⇋ Flip horizontal",undefined,E.fx)+B("fy","⇅ Flip vertical",undefined,E.fy)
  :T==="crop"?[["Free",0],["1:1",1],["4:3",4/3],["3:4",3/4],["16:9",16/9],["9:16",9/16]].map(([n,v])=>B("ar",n,v,E.ar===v)).join("")+B("cr0","Reset")
  :T==="size"?[100,75,50,25].map(n=>B("sc",n+"%",n,E.sc===n)).join("")+[2048,1280,800].map(n=>B("le","Long edge "+n,n)).join("")+rng("sc","Scale",10,100)
  :T==="adj"?rng("bri","Brightness",50,150)+rng("con","Contrast",50,150)+rng("sat","Saturation",0,200)+B("gr","Black and white",undefined,E.gray)+B("rs","Reset")
  :`<select id="xf">${["jpeg","png","webp"].map(f=>`<option value="${f}"${E.fmt===f?" selected":""}>${f.toUpperCase()}</option>`).join("")}</select><input type="text" id="xn" value="${esc(E.name)}" aria-label="File name">`+(E.fmt==="png"?"":rng("qp","Quality",50,100));
}
function adjust(g,w,h){
  if(E.bri==100&&E.con==100&&E.sat==100&&!E.gray)return;
  const d=g.getImageData(0,0,w,h),a=d.data,b=E.bri/100,c=E.con/100,s=E.gray?0:E.sat/100;
  for(let i=0;i<a.length;i+=4){
    const r=(a[i]*b-128)*c+128,gg=(a[i+1]*b-128)*c+128,bl=(a[i+2]*b-128)*c+128,l=.299*r+.587*gg+.114*bl;
    a[i]=l+(r-l)*s;a[i+1]=l+(gg-l)*s;a[i+2]=l+(bl-l)*s;
  }
  g.putImageData(d,0,0);
}
function exportImg(){
  const{x,y,w,h}=E.c,sx=Math.round(E.sw*x),sy=Math.round(E.sh*y),sw=Math.max(1,Math.round(E.sw*w)),sh=Math.max(1,Math.round(E.sh*h)),[ow,oh]=outSize();
  const c=document.createElement("canvas");c.width=ow;c.height=oh;const g=c.getContext("2d");
  if(E.fmt==="jpeg"){g.fillStyle="#fff";g.fillRect(0,0,ow,oh)}
  g.imageSmoothingQuality="high";g.drawImage(E.src,sx,sy,sw,sh,0,0,ow,oh);adjust(g,ow,oh);
  const type="image/"+E.fmt,nm=(E.name.trim()||"photo")+"."+({jpeg:"jpg",png:"png",webp:"webp"})[E.fmt];
  return new Promise((ok,no)=>c.toBlob(b=>b?ok(new File([b],nm,{type})):no(Error("unsupported format")),type,E.qp/100));
}
$("#ed").addEventListener("click",async e=>{
  if(!E)return;
  const t=e.target.closest("[data-et]");if(t){E.tab=t.dataset.et;tabs();panel();return drawCr()}
  const b=e.target.closest("[data-e]");if(!b)return;const a=b.dataset.e,v=+b.dataset.v;
  if(a==="skip")return fin(null);
  if(a==="orig")return fin(E.f);
  if(a==="all"){skipAll=true;return fin(E.f)}
  if(a==="up"){b.disabled=true;try{fin(await exportImg())}catch(err){b.disabled=false;toast("Couldn't export: "+err.message)}return}
  if(a==="rl"||a==="rr"){E.rot=(E.rot+(a==="rr"?1:3))%4;E.c={x:0,y:0,w:1,h:1};E.ar=0;build();return draw()}
  if(a==="fx"||a==="fy"){E[a]=E[a]?0:1;build();panel();return draw()}
  if(a==="ar")return setAr(v);
  if(a==="cr0"){E.ar=0;E.c={x:0,y:0,w:1,h:1};panel();return drawCr()}
  if(a==="sc"){E.sc=v;panel();return info()}
  if(a==="le"){E.sc=Math.min(100,Math.round(v/Math.max(E.sw*E.c.w,E.sh*E.c.h)*100));panel();return info()}
  if(a==="gr"){E.gray=E.gray?0:1;panel();return filt()}
  if(a==="rs"){Object.assign(E,{bri:100,con:100,sat:100,gray:0});panel();return filt()}
});
$("#ep").addEventListener("input",e=>{
  if(!E)return;const k=e.target.dataset.r;
  if(k){E[k]=+e.target.value;e.target.nextElementSibling.textContent=e.target.value+"%";return k==="sc"||k==="qp"?info():filt()}
  if(e.target.id==="xn")E.name=e.target.value;
  if(e.target.id==="xf"){E.fmt=e.target.value;panel()}
});
addEventListener("resize",()=>{if(E)draw()});
const b64e=x=>btoa(String.fromCharCode(...new Uint8Array(x))).replace(/\+/g,"-").replace(/\//g,"_").replace(/=+$/,"");
const b64d=s=>Uint8Array.from(atob(s.replace(/-/g,"+").replace(/_/g,"/")+"=".repeat((4-s.length%4)%4)),c=>c.charCodeAt(0));
const FP='<svg viewBox="0 0 64 64" width="64" height="64" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" aria-hidden="true"><path d="M10 42V30a22 22 0 0 1 44 0v6"/><path d="M18 46V30a14 14 0 0 1 28 0v14"/><path d="M26 50V30a6 6 0 0 1 12 0v18"/><path d="M32 30v20"/><path d="M46 44v4"/><path d="M14 52v2"/></svg>';
const pinDraw=()=>{const d=$("#dots");if(d)d.innerHTML=S.pin?[...S.pin].map(()=>"<i></i>").join(""):"<span style=\"color:var(--mu)\">Enter your PIN</span>"};
function renderLogin(){
  const ms=[["pw","Password"]];if(S.hasPin)ms.push(["pin","PIN"]);if(S.fpo)ms.push(["fp","Fingerprint"]);
  $("#lm").innerHTML=ms.length>1?ms.map(([k,n])=>`<button class="${S.lm===k?"on":""}" data-act="lm" data-m="${k}">${n}</button>`).join(""):"";
  ["pw","pin","fp"].forEach(k=>$("#lp-"+k).hidden=S.lm!==k);
}
async function fpInit(keep){
  S.fpo=null;S.hasPin=false;if(!keep)S.pin="";
  try{const j=await api({a:"pk_options"});S.hasPin=!!j.pin;if(window.PublicKeyCredential&&j.ids.length)S.fpo=j}catch{}
  if(!keep){let last="";try{last=localStorage.lm||""}catch{}const ok={pw:1,pin:S.hasPin,fp:!!S.fpo};S.lm=ok[last]?last:S.fpo?"fp":S.hasPin?"pin":"pw"}
  else if(S.lm==="fp"&&!S.fpo)S.lm=S.hasPin?"pin":"pw";
  renderLogin();pinDraw();
}
addEventListener("keydown",e=>{
  if($("#login").hidden||S.lm!=="pin")return;
  if(/^\d$/.test(e.key)&&S.pin.length<8){S.pin+=e.key;pinDraw()}
  else if(e.key==="Backspace"){S.pin=S.pin.slice(0,-1);pinDraw()}
  else if(e.key==="Enter")document.querySelector('[data-act="pgo"]').click();
});
const Q=[];
function drawQ(){
  const q=$("#uq");q.style.display=Q.length?"block":"none";
  q.innerHTML=Q.map(i=>`<div>${esc(i.f.name)} <span style="color:${i.s==="err"?"var(--dg)":"var(--mu)"}">${i.s==="done"?"✓":i.s==="err"?esc(i.e):Math.round(i.p*100)+"%"}</span><i style="width:${i.p*100}%"></i></div>`).join("");
}
async function mkthumb(f){
  const bm=await createImageBitmap(f,{resizeWidth:480,resizeQuality:"medium"}),c=document.createElement("canvas");
  c.width=bm.width;c.height=bm.height;c.getContext("2d").drawImage(bm,0,0);
  return new Promise(r=>c.toBlob(r,"image/jpeg",.78));
}
async function one(it){
  const f=it.f,type=f.type||"application/octet-stream";
  try{
    const tb=/^image\/(jpeg|png|webp|bmp|avif)$/.test(type)?await mkthumb(f).catch(()=>null):null;
    const {id,url,thumbUrl}=await api({a:"upload",name:f.name,type,size:f.size,thumb:!!tb,folder:S.tab==="files"&&S.folder!=="*"?S.folder:""});it.id=id;
    if(tb&&thumbUrl)await fetch(thumbUrl,{method:"PUT",headers:{"Content-Type":"image/jpeg"},body:tb}).catch(()=>{});
    await new Promise((ok,no)=>{const x=new XMLHttpRequest();x.open("PUT",url);x.setRequestHeader("Content-Type",type);
      x.upload.onprogress=e=>{it.p=e.loaded/e.total;drawQ()};
      x.onload=()=>x.status<300?ok():no(Error("Upload failed ("+x.status+")"));
      x.onerror=()=>no(Error("Upload blocked. Check bucket CORS."));x.send(f)});
    it.s="done";it.p=1;
  }catch(e){it.s="err";it.e=e?.message||"Failed";if(it.id)api({a:"bulk",op:"del",ids:[it.id]}).catch(()=>{})}
  drawQ();
}
async function upload(files){
  const items=[...files].map(f=>({f,p:0,s:"up"}));Q.push(...items);drawQ();let i=0;
  const w=async()=>{while(i<items.length)await one(items[i++])};
  await Promise.all([w(),w(),w()]);
  await run(load);
  setTimeout(()=>{items.filter(x=>x.s==="done").forEach(x=>Q.splice(Q.indexOf(x),1));drawQ()},2500);
  setTimeout(()=>{items.forEach(x=>{const k=Q.indexOf(x);if(k>-1)Q.splice(k,1)});drawQ()},9000);
}
$("#file").onchange=e=>{intake(e.target.files);e.target.value=""};
let dc=0;
addEventListener("dragenter",e=>{e.preventDefault();dc++;document.body.classList.add("drag")});
addEventListener("dragleave",()=>{if(--dc<=0){dc=0;document.body.classList.remove("drag")}});
addEventListener("dragover",e=>e.preventDefault());
addEventListener("drop",e=>{e.preventDefault();dc=0;document.body.classList.remove("drag");if(e.dataTransfer.files.length)intake(e.dataTransfer.files)});
addEventListener("paste",e=>{
  const fs=[...(e.clipboardData?.files||[])].map(f=>/^image\.\w+$/.test(f.name)?new File([f],"screenshot-"+Date.now().toString(36).slice(-5)+"."+(f.type.split("/")[1]||"png"),{type:f.type}):f);
  if(fs.length)intake(fs);
});
run(load).then(()=>{if($("#app").hidden)$("#login").hidden=false});
fpInit();
</script>
</body>
</html>
__APP__;
