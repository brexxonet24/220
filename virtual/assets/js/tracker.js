/**
 * AMET 220 — Motor de tracking de interacción
 * Mide tiempo activo real: detecta idle, cambios de tab y salida de pantalla completa
 */
const TRACKER = (function(){
  let state = {
    actividadId: null,
    usuarioId: null,
    minimoSeg: 0,
    inicioSesion: Date.now(),
    segundosActivos: 0,
    segundosIdle: 0,
    tabChanges: 0,
    fsExits: 0,
    pasteAttempts: 0,
    lastInteraction: Date.now(),
    status: 'active', // active | idle | paused
    hbInterval: null,
    clockInterval: null,
    IDLE_THRESHOLD: 60000, // 60s sin interacción = idle
    HB_INTERVAL: 30000,    // heartbeat cada 30s
  };

  function fmt(s){ const m=Math.floor(s/60); return m+':'+(s%60).toString().padStart(2,'0'); }

  function tick(){
    if(state.status==='paused') return;
    const idle = (Date.now()-state.lastInteraction) > state.IDLE_THRESHOLD;
    if(idle){ state.status='idle'; state.segundosIdle++; }
    else { state.status='active'; state.segundosActivos++; }
    updateUI();
  }

  function updateUI(){
    const sa=state.segundosActivos, si=state.segundosIdle;
    const req=state.minimoSeg, pct=req>0?Math.min(100,Math.round(sa/req*100)):0;

    const dot=document.getElementById('t-dot');
    const sts=document.getElementById('t-status');
    const tmr=document.getElementById('t-timer');
    if(dot){
      dot.className='track-dot '+{active:'dot-active',idle:'dot-idle',paused:'dot-paused'}[state.status];
    }
    if(sts){
      sts.textContent={active:'Seguimiento activo',idle:'Sin actividad — tiempo pausado',paused:'Pestaña oculta — pausado'}[state.status];
    }
    if(tmr) tmr.textContent=fmt(sa+si);

    const ma=document.getElementById('m-activo');
    const mi=document.getElementById('m-idle');
    const mint=document.getElementById('m-int');
    if(ma) ma.textContent=fmt(sa);
    if(mi) mi.textContent=fmt(si);
    if(mint) mint.textContent=state.tabChanges+state.fsExits+state.pasteAttempts;

    const pb=document.getElementById('p-bar');
    const pl=document.getElementById('p-label');
    if(pb){
      pb.style.width=pct+'%';
      pb.className='prog-fill '+(pct>=100?'suc':pct>50?'':'war');
    }
    if(pl) pl.textContent=pct+'%';

    let alerts='';
    if(state.status==='idle')
      alerts+='<div class="alert alert-warning">Sin interacción por más de 60s — el tiempo activo está pausado. Mové el mouse para reanudar.</div>';
    if(state.tabChanges>0)
      alerts+=`<div class="alert alert-danger">${state.tabChanges} cambio(s) de pestaña registrados — el tiempo no se acumuló durante la ausencia.</div>`;
    if(state.fsExits>0)
      alerts+=`<div class="alert alert-warning">${state.fsExits} salida(s) de pantalla completa registradas.</div>`;
    if(state.pasteAttempts>0)
      alerts+=`<div class="alert alert-danger">${state.pasteAttempts} intento(s) de pegado bloqueados.</div>`;
    if(pct>=100)
      alerts='<div class="alert alert-success">¡Tiempo mínimo alcanzado! Podés continuar o finalizar.</div>'+alerts;

    const ab=document.getElementById('alert-box');
    if(ab) ab.innerHTML=alerts;
  }

  function heartbeat(){
    if(state.status==='paused') return;
    const data={
      actividad_id:state.actividadId,
      usuario_id:state.usuarioId,
      sa:state.segundosActivos,
      si:state.segundosIdle,
      tc:state.tabChanges,
      fe:state.fsExits,
      pa:state.pasteAttempts,
      ts:new Date().toISOString()
    };
    fetch(window.AMET_BASE+'/api/heartbeat.php',{
      method:'POST',
      headers:{'Content-Type':'application/json'},
      body:JSON.stringify(data)
    }).catch(()=>{});
  }

  function resetIdle(){
    state.lastInteraction=Date.now();
    if(state.status==='idle') state.status='active';
  }

  function onVisibility(){
    if(!state.actividadId) return;
    if(document.hidden){ state.status='paused'; state.tabChanges++; }
    else { state.status='active'; state.lastInteraction=Date.now(); }
    updateUI();
  }

  function onFullscreen(){
    if(!state.actividadId) return;
    if(!document.fullscreenElement&&!document.webkitFullscreenElement){
      state.fsExits++; updateUI();
    }
  }

  function init(config){
    state.actividadId=config.actividadId;
    state.usuarioId=config.usuarioId;
    state.minimoSeg=(config.minimoMinutos||0)*60;
    state.lastInteraction=Date.now();

    // Event listeners
    ['mousemove','keydown','click','scroll','touchstart'].forEach(ev=>
      document.addEventListener(ev,resetIdle,{passive:true})
    );
    document.addEventListener('visibilitychange',onVisibility);
    ['fullscreenchange','webkitfullscreenchange'].forEach(ev=>
      document.addEventListener(ev,onFullscreen)
    );

    state.clockInterval=setInterval(tick,1000);
    state.hbInterval=setInterval(heartbeat,state.HB_INTERVAL);

    // Request fullscreen on activity start
    const el=document.documentElement;
    setTimeout(()=>{
      try{
        if(el.requestFullscreen) el.requestFullscreen().catch(()=>{});
        else if(el.webkitRequestFullscreen) el.webkitRequestFullscreen();
      }catch(e){}
    },500);

    updateUI();
  }

  function registrarPaste(){
    state.pasteAttempts++;
    updateUI();
  }

  function stop(callback){
    clearInterval(state.clockInterval);
    clearInterval(state.hbInterval);
    try{ if(document.exitFullscreen) document.exitFullscreen(); }catch(e){}
    const payload={
      actividad_id:state.actividadId,
      usuario_id:state.usuarioId,
      sa:state.segundosActivos,
      si:state.segundosIdle,
      tc:state.tabChanges,
      fe:state.fsExits,
      pa:state.pasteAttempts,
      completada:state.segundosActivos>=state.minimoSeg,
      duracion_total:Math.floor((Date.now()-state.inicioSesion)/1000)
    };
    fetch(window.AMET_BASE+'/api/guardar_sesion.php',{
      method:'POST',
      headers:{'Content-Type':'application/json'},
      body:JSON.stringify(payload)
    }).then(()=>{ if(callback) callback(); }).catch(()=>{ if(callback) callback(); });
  }

  return { init, stop, registrarPaste, getState:()=>({...state}) };
})();
