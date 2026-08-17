/**
 * AMET 220 — Editor de código con protección anti-pegado
 * Usa CodeMirror 5. Bloquea: Ctrl+V, Cmd+V, drag-drop, paste desde contexto.
 */
const EDITOR = (function(){
  let cm = null;
  const toast = document.createElement('div');
  toast.className = 'paste-toast';
  toast.textContent = '🚫 Pegado deshabilitado — el ejercicio requiere escritura manual';
  document.body.appendChild(toast);
  let toastTimer = null;

  function showPasteWarning(){
    if(typeof TRACKER !== 'undefined') TRACKER.registrarPaste();
    clearTimeout(toastTimer);
    toast.style.display='block';
    toastTimer=setTimeout(()=>{ toast.style.display='none'; },3000);
  }

  function init(elementId, options={}){
    const textarea=document.getElementById(elementId);
    if(!textarea) return;

    const mode = options.mode || 'htmlmixed';
    const template = options.template || '';
    if(template) textarea.value = template;

    cm = CodeMirror.fromTextArea(textarea, {
      mode: mode,
      theme: 'default',
      lineNumbers: true,
      indentUnit: 2,
      tabSize: 2,
      indentWithTabs: false,
      lineWrapping: false,
      autofocus: true,
      extraKeys: {
        // Block Ctrl+V / Cmd+V
        'Ctrl-V': () => { showPasteWarning(); return false; },
        'Cmd-V':  () => { showPasteWarning(); return false; },
        'Ctrl-Shift-V': () => { showPasteWarning(); return false; },
        // Útiles permitidos
        'Ctrl-Z': 'undo',
        'Ctrl-Y': 'redo',
        'Ctrl-A': 'selectAll',
        'Tab': (cm) => { cm.replaceSelection('  '); }
      }
    });

    // Block paste via beforeChange
    cm.on('beforeChange', function(instance, change){
      if(change.origin === 'paste'){
        change.cancel();
        showPasteWarning();
      }
    });

    // Block drag-and-drop into editor
    cm.on('drop', function(instance, e){
      e.preventDefault();
      e.stopPropagation();
      showPasteWarning();
      return false;
    });

    // Block right-click paste in the editor wrapper
    cm.getWrapperElement().addEventListener('contextmenu', function(e){
      e.preventDefault();
    });

    // Preview functionality
    const previewBtn=document.getElementById('btn-preview');
    const previewFrame=document.getElementById('preview-frame');
    if(previewBtn && previewFrame){
      previewBtn.addEventListener('click',function(){
        const code=cm.getValue();
        previewFrame.style.display='block';
        previewFrame.srcdoc=code;
      });
    }

    // Sync CodeMirror back to textarea on form submit
    const form=textarea.closest('form');
    if(form){
      form.addEventListener('submit',function(){
        cm.save();
      });
    }

    return cm;
  }

  function getValue(){ return cm ? cm.getValue() : ''; }
  function setValue(v){ if(cm) cm.setValue(v); }

  return { init, getValue, setValue, showPasteWarning };
})();
