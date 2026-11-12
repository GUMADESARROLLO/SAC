{{-- Selector de estado del SAC (barra superior).
     VISTA PREVIA: el estado y "Mi día" se guardan solo en este navegador (localStorage).
     La versión real registrará cada cambio en tbl_estado_log. --}}
@if (in_array((string) Session::get('rol'), ['1', '2', '9'], true))
<div class="dropdown sac-est" id="sacEst">
  <button class="sac-est-btn is-off" id="sacEstBtn" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" aria-haspopup="true" aria-label="Mi estado: Desconectado">
    <i class="sac-est-dot" aria-hidden="true"></i>
    <span class="sac-est-lbl" id="sacEstLbl">Desconectado</span>
    <span class="sac-est-time" id="sacEstTime"></span>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"></path></svg>
  </button>
  <div class="dropdown-menu dropdown-menu-end sac-est-menu" aria-labelledby="sacEstBtn">
    <p class="sac-est-h">Mi estado</p>
    <div role="radiogroup" aria-label="Cambiar mi estado" class="sac-est-opts">
      <button type="button" role="radio" class="sac-est-opt is-on" data-est="on" aria-checked="false"><i class="sac-est-dot" aria-hidden="true"></i><span><strong>En línea</strong><small>Atendiendo pedidos</small></span></button>
      <button type="button" role="radio" class="sac-est-opt is-afk" data-est="afk" aria-checked="false"><i class="sac-est-dot" aria-hidden="true"></i><span><strong>AFK</strong><small>Ausente un momento</small></span></button>
      <button type="button" role="radio" class="sac-est-opt is-lunch" data-est="lunch" aria-checked="false"><i class="sac-est-dot" aria-hidden="true"></i><span><strong>Almuerzo</strong><small>Pausa de comida</small></span></button>
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
    <p class="sac-est-note">Vista previa · se guarda solo en este navegador</p>
  </div>
</div>
<script>
  (function () {
    var KEY = 'sacEstadoV1';
    var NOMBRE = { on: 'En línea', afk: 'AFK', lunch: 'Almuerzo', off: 'Desconectado' };
    var btn = document.getElementById('sacEstBtn');
    if (!btn) return;

    function hoy() { var d = new Date(); return d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate(); }
    function hm(h, m) { var d = new Date(); d.setHours(h, m, 0, 0); return d.getTime(); }

    // Día de ejemplo para la vista previa: solo los cambios que ya ocurrieron
    function semilla() {
      var ejemplo = [['on', 8, 4], ['afk', 10, 12], ['on', 10, 27], ['lunch', 12, 6], ['on', 12, 58], ['afk', 15, 2], ['on', 15, 14]];
      var ahora = Date.now(), log = [];
      ejemplo.forEach(function (x) { var t = hm(x[1], x[2]); if (t <= ahora) log.push({ e: x[0], t: t }); });
      if (!log.length) log.push({ e: 'off', t: ahora });
      return { fecha: hoy(), log: log };
    }
    function leer() {
      try { var d = JSON.parse(localStorage.getItem(KEY) || 'null'); if (d && d.fecha === hoy() && d.log && d.log.length) return d; } catch (e) {}
      return semilla();
    }
    function guardar(d) { try { localStorage.setItem(KEY, JSON.stringify(d)); } catch (e) {} }

    var data = leer();
    guardar(data);

    function dur(ms) {
      var m = Math.floor(ms / 60000);
      if (m < 1) return '<1 min';
      if (m < 60) return m + ' min';
      var h = Math.floor(m / 60), r = m % 60;
      return h + ' h ' + (r < 10 ? '0' : '') + r + ' min';
    }
    function totales() {
      var t = { on: 0, afk: 0, lunch: 0, off: 0 }, ahora = Date.now();
      data.log.forEach(function (x, i) { var fin = i + 1 < data.log.length ? data.log[i + 1].t : ahora; t[x.e] += Math.max(0, fin - x.t); });
      return t;
    }

    function pintar() {
      var actual = data.log[data.log.length - 1], ahora = Date.now();
      btn.className = 'sac-est-btn is-' + actual.e;
      document.getElementById('sacEstLbl').textContent = NOMBRE[actual.e];
      document.getElementById('sacEstTime').textContent = actual.e === 'off' ? '' : dur(ahora - actual.t);
      btn.setAttribute('aria-label', 'Mi estado: ' + NOMBRE[actual.e] + (actual.e === 'off' ? '' : ', desde hace ' + dur(ahora - actual.t)));
      document.querySelectorAll('#sacEst .sac-est-opt').forEach(function (o) { o.setAttribute('aria-checked', o.getAttribute('data-est') === actual.e ? 'true' : 'false'); });

      // Mi día: barra por tramos en orden de tiempo + totales
      var t = totales(), base = t.on + t.afk + t.lunch;
      document.getElementById('sacEstEff').textContent = base ? Math.round(t.on * 100 / base) + '%' : '—';
      var ini = data.log[0].t, total = Math.max(1, ahora - ini), html = '';
      data.log.forEach(function (x, i) {
        var fin = i + 1 < data.log.length ? data.log[i + 1].t : ahora;
        var w = (fin - x.t) * 100 / total;
        if (w > 0) html += '<i class="is-' + x.e + '" style="flex-grow:' + w.toFixed(3) + '"></i>';
      });
      document.getElementById('sacEstStack').innerHTML = html;
      document.getElementById('sacEstLegend').innerHTML = ['on', 'afk', 'lunch'].map(function (k) {
        return '<div class="is-' + k + '"><dt><i class="sac-est-dot" aria-hidden="true"></i>' + NOMBRE[k] + '</dt><dd>' + (t[k] ? dur(t[k]) : '—') + '</dd></div>';
      }).join('');
    }

    document.querySelectorAll('#sacEst .sac-est-opt').forEach(function (o) {
      o.addEventListener('click', function () {
        var e = o.getAttribute('data-est'), actual = data.log[data.log.length - 1];
        if (actual.e !== e) { data.log.push({ e: e, t: Date.now() }); guardar(data); }
        pintar();
        if (window.bootstrap) bootstrap.Dropdown.getOrCreateInstance(btn).hide();
        btn.focus();
      });
    });

    pintar();
    setInterval(pintar, 30000);
  })();
</script>
@endif
