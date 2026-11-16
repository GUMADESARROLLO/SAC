{{-- Selector de estado del SAC (barra superior).
     Cada cambio se guarda en tbl_estado_log (EstadoSacController). La señal de vida es la
     revisión de sesión de cada minuto (sac_shell_js): si se corta varios minutos, el servidor
     pasa al usuario a Desconectado. --}}
@if (\App\Models\EstadoSac::mide(Session::get('rol')))
<div class="dropdown sac-est" id="sacEst">
  <button class="sac-est-btn is-off" id="sacEstBtn" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" aria-haspopup="true" aria-label="Mi estado: cargando">
    <i class="sac-est-dot" aria-hidden="true"></i>
    <span class="sac-est-lbl" id="sacEstLbl">…</span>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"></path></svg>
  </button>
  <div class="dropdown-menu dropdown-menu-end sac-est-menu" aria-labelledby="sacEstBtn">
    <p class="sac-est-h">Mi estado</p>
    <div role="radiogroup" aria-label="Cambiar mi estado" class="sac-est-opts">
      <button type="button" role="radio" class="sac-est-opt is-on" data-est="on" aria-checked="false"><i class="sac-est-dot" aria-hidden="true"></i><span><strong>En línea</strong><small>Atendiendo pedidos</small></span></button>
      <button type="button" role="radio" class="sac-est-opt is-afk" data-est="afk" aria-checked="false"><i class="sac-est-dot" aria-hidden="true"></i><span><strong>AFK</strong><small>Ausente un momento</small></span></button>
      <button type="button" role="radio" class="sac-est-opt is-lunch" data-est="lunch" aria-checked="false"><i class="sac-est-dot" aria-hidden="true"></i><span><strong>Descanso</strong><small>Pausa programada</small></span></button>
      <button type="button" role="radio" class="sac-est-opt is-off" data-est="off" aria-checked="false"><i class="sac-est-dot" aria-hidden="true"></i><span><strong>Desconectado</strong><small>Fuera de turno</small></span></button>
    </div>
    <p class="sac-est-note is-err" id="sacEstNote" role="status" hidden></p>
  </div>
</div>
<script>
  // Se ejecuta al terminar de cargar la página: jQuery se incluye al final del layout
  document.addEventListener('DOMContentLoaded', function () {
    var NOMBRE = { on: 'En línea', afk: 'AFK', lunch: 'Descanso', off: 'Desconectado' };
    var URL_MIO = @json(route('estadoMio')), URL_CAMBIAR = @json(route('estadoCambiar'));
    var btn = document.getElementById('sacEstBtn');
    if (!btn || !window.jQuery) return;

    // Horas del servidor ("AAAA-MM-DD HH:MM:SS", hora de Managua) a milisegundos locales
    function ms(s) {
      var m = /^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2}):(\d{2})/.exec(s || '');
      return m ? new Date(+m[1], +m[2] - 1, +m[3], +m[4], +m[5], +m[6]).getTime() : null;
    }
    var data = null, guardando = false;

    function cargar(r) { data = { estado: r.estado || 'off', desde: ms(r.desde) }; pintar(); }
    function pedir() { jQuery.ajax({ url: URL_MIO, cache: false, global: false }).done(cargar); }

    function pintar() {
      if (!data) return;
      var e = data.estado;
      btn.className = 'sac-est-btn is-' + e;
      document.getElementById('sacEstLbl').textContent = NOMBRE[e];
      btn.setAttribute('aria-label', 'Mi estado: ' + NOMBRE[e]);
      document.querySelectorAll('#sacEst .sac-est-opt').forEach(function (o) { o.setAttribute('aria-checked', o.getAttribute('data-est') === e ? 'true' : 'false'); });
    }

    document.querySelectorAll('#sacEst .sac-est-opt').forEach(function (o) {
      o.addEventListener('click', function () {
        var e = o.getAttribute('data-est'), nota = document.getElementById('sacEstNote');
        if (guardando) return;
        if (data && data.estado === e) { if (window.bootstrap) bootstrap.Dropdown.getOrCreateInstance(btn).hide(); return; }
        guardando = true;
        document.getElementById('sacEst').classList.add('is-saving');
        jQuery.ajax({ url: URL_CAMBIAR, method: 'POST', data: { estado: e }, headers: { 'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content') } })
          .done(function (r) {
            cargar(r);
            nota.hidden = true;
            if (window.bootstrap) bootstrap.Dropdown.getOrCreateInstance(btn).hide();
            btn.focus();
          })
          .fail(function (xhr) {
            if (xhr && xhr.status === 401) return; // sesión cerrada: el shell lleva al login
            nota.textContent = 'No se pudo guardar el estado. Revisa la conexión e intenta de nuevo.';
            nota.hidden = false;
          })
          .always(function () { guardando = false; document.getElementById('sacEst').classList.remove('is-saving'); });
      });
    });

    // La señal de cada minuto trae el estado vigente: si cambió en el servidor
    // (p. ej. se cerró por falta de señal), se actualiza el botón
    document.addEventListener('sac:estado', function (ev) {
      var r = ev.detail || {};
      if (!data || r.estado !== data.estado || ms(r.desde) !== data.desde) pedir();
    });

    pedir();
  });
</script>
@endif
