{{-- Selector de estado del SAC (barra superior).
     Cada cambio se guarda en tbl_estado_log (EstadoSacController). La señal de vida es la
     revisión de sesión de cada minuto (sac_shell_js): si se corta varios minutos, el servidor
     pasa al usuario a Desconectado. --}}
@if (\App\Models\EstadoSac::mide(Session::get('rol')))
<div class="dropdown sac-est" id="sacEst">
  <button class="sac-est-btn is-off" id="sacEstBtn" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" aria-haspopup="true" aria-label="Mi estado: cargando">
    <i class="sac-est-dot" aria-hidden="true"></i>
    <span class="sac-est-lbl" id="sacEstLbl">…</span>
    <span class="sac-est-time" id="sacEstTime"></span>
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
    <div class="sac-est-day">
      <div class="d-flex align-items-baseline justify-content-between">
        <p class="sac-est-h m-0">Mi día</p>
        <span class="sac-est-eff"><strong id="sacEstEff">—</strong> efectivo</span>
      </div>
      <div class="sac-stack" id="sacEstStack" aria-hidden="true"></div>
      <dl class="sac-est-legend" id="sacEstLegend"></dl>
    </div>
    <p class="sac-est-note" id="sacEstNote" role="status">Si cierras el sistema o se apaga la máquina, pasas a Desconectado.</p>
  </div>
</div>
<script>
  // Se ejecuta al terminar de cargar la página: jQuery se incluye al final del layout
  document.addEventListener('DOMContentLoaded', function () {
    var NOMBRE = { on: 'En línea', afk: 'AFK', lunch: 'Descanso', off: 'Desconectado' };
    var NOTA = 'Si cierras el sistema o se apaga la máquina, pasas a Desconectado.';
    var URL_MIO = @json(route('estadoMio')), URL_CAMBIAR = @json(route('estadoCambiar'));
    var btn = document.getElementById('sacEstBtn');
    if (!btn || !window.jQuery) return;

    // Horas del servidor ("AAAA-MM-DD HH:MM:SS", hora de Managua) a milisegundos locales
    function ms(s) {
      var m = /^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2}):(\d{2})/.exec(s || '');
      return m ? new Date(+m[1], +m[2] - 1, +m[3], +m[4], +m[5], +m[6]).getTime() : null;
    }
    var data = null, desfase = 0, guardando = false;   // desfase: reloj del servidor - reloj local
    function ahora() { return Date.now() + desfase; }
    function inicioDia() { var d = new Date(ahora()); d.setHours(0, 0, 0, 0); return d.getTime(); }

    function cargar(r) {
      var srv = ms(r.ahora);
      if (srv) desfase = srv - Date.now();
      data = {
        estado: r.estado || 'off',
        desde: ms(r.desde),
        tramos: (r.tramos || []).map(function (t) { return { e: t.estado, a: ms(t.inicio), b: t.fin ? ms(t.fin) : null }; })
      };
      pintar();
    }
    function pedir() { jQuery.ajax({ url: URL_MIO, cache: false, global: false }).done(cargar); }

    function dur(x) {
      var m = Math.floor(x / 60000);
      if (m < 1) return '<1 min';
      if (m < 60) return m + ' min';
      var h = Math.floor(m / 60), r = m % 60;
      return h + ' h ' + (r < 10 ? '0' : '') + r + ' min';
    }

    function pintar() {
      if (!data) return;
      var e = data.estado, t0 = ahora(), dia0 = inicioDia();
      btn.className = 'sac-est-btn is-' + e;
      document.getElementById('sacEstLbl').textContent = NOMBRE[e];
      var lleva = e !== 'off' && data.desde ? t0 - data.desde : 0;
      document.getElementById('sacEstTime').textContent = e === 'off' ? '' : dur(lleva);
      btn.setAttribute('aria-label', 'Mi estado: ' + NOMBRE[e] + (e === 'off' ? '' : ', desde hace ' + dur(lleva)));
      document.querySelectorAll('#sacEst .sac-est-opt').forEach(function (o) { o.setAttribute('aria-checked', o.getAttribute('data-est') === e ? 'true' : 'false'); });

      // Mi día: tramos de hoy en orden; los huecos entre tramos son tiempo Desconectado
      var tot = { on: 0, afk: 0, lunch: 0 }, segs = [], prev = null;
      data.tramos.forEach(function (x) {
        var a = Math.max(x.a, dia0), b = x.b == null ? t0 : x.b;
        if (b <= a) return;
        if (prev != null && a > prev) segs.push({ e: 'off', w: a - prev });
        segs.push({ e: x.e, w: b - a });
        tot[x.e] += b - a;
        prev = b;
      });
      if (prev != null && e === 'off' && t0 > prev) segs.push({ e: 'off', w: t0 - prev });
      var base = tot.on + tot.afk + tot.lunch;
      document.getElementById('sacEstEff').textContent = base ? Math.round(tot.on * 100 / base) + '%' : '—';
      document.getElementById('sacEstStack').innerHTML = segs.map(function (s) { return '<i class="is-' + s.e + '" style="flex-grow:' + s.w + '"></i>'; }).join('');
      document.getElementById('sacEstLegend').innerHTML = ['on', 'afk', 'lunch'].map(function (k) {
        return '<div class="is-' + k + '"><dt><i class="sac-est-dot" aria-hidden="true"></i>' + NOMBRE[k] + '</dt><dd>' + (tot[k] ? dur(tot[k]) : '—') + '</dd></div>';
      }).join('');
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
            nota.textContent = NOTA; nota.classList.remove('is-err');
            if (window.bootstrap) bootstrap.Dropdown.getOrCreateInstance(btn).hide();
            btn.focus();
          })
          .fail(function (xhr) {
            if (xhr && xhr.status === 401) return; // sesión cerrada: el shell lleva al login
            nota.textContent = 'No se pudo guardar el estado. Revisa la conexión e intenta de nuevo.';
            nota.classList.add('is-err');
          })
          .always(function () { guardando = false; document.getElementById('sacEst').classList.remove('is-saving'); });
      });
    });

    // La señal de cada minuto trae el estado vigente: si cambió en el servidor
    // (p. ej. se cerró por falta de señal), se recarga "Mi día"
    document.addEventListener('sac:estado', function (ev) {
      var r = ev.detail || {};
      if (!data || r.estado !== data.estado || ms(r.desde) !== data.desde) pedir();
    });

    pedir();
    setInterval(pintar, 30000);
  });
</script>
@endif
